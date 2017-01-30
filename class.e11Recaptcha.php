<?php

// e11-reCAPTCHA
// Copyright (C) 2017  Eric Adolfson
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License along
// with this program; if not, write to the Free Software Foundation, Inc.,
// 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.

class e11Recaptcha {
  private static $initialized = false;

  // Plugin options
  
  private static $siteKey;
  private static $secretKey;
  private static $behaviorComments;
  private static $behaviorNewUsers;
  
  public static function plugin_activation() {

  }

  public static function plugin_deactivation() {

  }

  /**
   * Attach a reCAPTCHA field to the area after the "comment" field on comment
   * forms.
   *
   * @param array $comment_fields Associative array of comment field HTML
   *                              keyed by field name
   * @return array Filtered $comment_fields with "comment" field altered to
   *               include reCAPTCHA HTML
   */
  public static function comment_form_captcha($comment_fields) {

    global $wp_query;

    // Check if comment fields were saved in cookies.  This is done to preserve
    // comment details if a reCAPTCHA solution fails to pass.

    //var_export($wp_query);

    if (isset($wp_query->e11_recaptcha_comment)) {

      $comment = $wp_query->e11_recaptcha_comment;

      // Set field values in comment form to the cookie-preserved values,
      // ignoring fields that aren't present in the set passed to the filter
      // by WordPress.

      if (isset($comment_fields['comment'])) {
        $comment_fields['comment'] = preg_replace('/<\/textarea>/i', $comment['comment'] . '</textarea>', $comment_fields['comment']);
      }

      if (isset($comment_fields['author'])) {
        $comment_fields['author'] = preg_replace('/value\s*=\s*[\'"].*[\'"]/i', 'value="' . $comment['author'] . '"', $comment_fields['author']);
      }

      if (isset($comment_fields['email'])) {
        $comment_fields['email'] = preg_replace('/value\s*=\s*[\'"].*[\'"]/i', 'value="' . $comment['email'] . '"', $comment_fields['email']);
      }

      if (isset($comment_fields['url'])) {
        $comment_fields['url'] = preg_replace('/value\s*=\s*[\'"].*[\'"]/i', 'value="' . $comment['url'] . '"', $comment_fields['url']);
      }
    }
    

    // Don't display reCAPTCHA if disabled on comments.

    if (self::$behaviorComments == 'disabled') {
      return $comment_fields;
    }

    // Don't display reCAPTCHA if only enabled when users are not logged
    // in and user is logged in.

    if (self::$behaviorComments == 'not_logged_in' && is_user_logged_in()) {
      return $comment_fields;
    }

    // Push reCAPTCHA field into the comment field code, to ensure it can
    // appear whether a user is logged in or not.

    $comment_fields['comment'] = $comment_fields['comment'] . '
      <p class="comment-form-e11-recaptcha">
        <label for="comment-form-e11-recaptcha">Recaptcha</label>
        <span id="comment-form-e11-recaptcha" class="g-recaptcha" data-sitekey="'
          . esc_html(self::$siteKey)
          . '" />
      </p>
      ';

    return $comment_fields;
  }

  public static function check_comment_captcha($postID) {

    // Don't check reCAPTCHA if disabled on comments.

    if (self::$behaviorComments == 'disabled') {
      return;
    }

    // Don't check reCAPTCHA if only enabled when users are not logged
    // in and user is logged in.

    if (self::$behaviorComments == 'not_logged_in' && is_user_logged_in()) {
      return;
    }

    // [TODO] Check reCAPTCHA.

    // reCAPTCHA verify failed.  Bounce back to comment area.

    $comment = array();

    foreach (array('comment', 'author', 'email', 'url') as $k) {
      if (isset($_POST[$k])) {
        $comment[$k] = $_POST[$k];
      } else {
        $comment[$k] = '';
      }
    }

    $comment = base64_encode(serialize($comment));

    if (parse_url(home_url(), PHP_URL_SCHEME) == 'https') {
      $secure = true;
    } else {
      $secure = false;
    }

    setcookie('e11_recaptcha_comment_' . COOKIEHASH, $comment, time() + 300, COOKIEPATH, COOKIE_DOMAIN, $secure);

    $postURL = get_permalink($postID) . '#respond';

    wp_safe_redirect($postURL);

    exit(0);
  }

  public static function load_comment_from_cookie($wp_query) {
    if (isset($_COOKIE['e11_recaptcha_comment_' . COOKIEHASH])) {
      
      // Decode and unserialize comment from variable.
      
      $failedUnpack = false;
      
      $comment = base64_decode($_COOKIE['e11_recaptcha_comment_' . COOKIEHASH]);
      
      if ($comment === false) {
        $failedUnpack = true;
      } else {
        $comment = unserialize($comment);
        
        if ($comment === false) {
          $failedUnpack = true;
        } else {

          // Ensure required fields are present in saved comment.

          foreach (array('comment', 'author', 'email', 'url') as $k) {
            if (!isset($comment[$k])) {
              $failedUnpack = true;
            }
          }
        }
      }
      
      
      if (!$failedUnpack) {
        // Sanitize fields loaded from cookie.

        // [TODO] Is this the correct way to sanitize comment text?

        $comment['comment'] = apply_filters('pre_comment_content', $comment['comment']);
        $comment['comment'] = wp_unslash($comment['comment']);
        $comment['comment'] = esc_html($comment['comment']);

        $comment['author'] = apply_filters('pre_comment_author_name', $comment['author']);
        $comment['author'] = wp_unslash($comment['author']);
        $comment['author'] = esc_attr($comment['author']);

        $comment['email'] = apply_filters('pre_comment_author_email', $comment['email']);
        $comment['email'] = wp_unslash($comment['email']);
        $comment['email'] = esc_attr($comment['email']);

        $comment['url'] = apply_filters('pre_comment_author_url', $comment['url']);
        $comment['url'] = wp_unslash($comment['url']);
        $comment['url'] = esc_attr($comment['url']);

        $wp_query->e11_recaptcha_comment = $comment;
      }

      // Clear comment cookie.

      if (parse_url(home_url(), PHP_URL_SCHEME) == 'https') {
        $secure = true;
      } else {
        $secure = false;
      }

      unset($_COOKIE['e11_recaptcha_comment_' . COOKIEHASH]);
      setcookie('e11_recaptcha_comment_' . COOKIEHASH, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, $secure);
    }

    return $wp_query;
  }

  public static function init() {

    // Ensure function is called only once.

    if (self::$initialized) {
      return;
    }

    self::$initialized = true;

    // Read plugin options, and set class variables from them.

    $options = get_option('e11_recaptcha_options', array());

    if (!isset($options['e11_recaptcha_field_site_key'])) {
      self::$siteKey = '';
    } else {
      self::$siteKey = $options['e11_recaptcha_field_site_key'];
    }

    if (!isset($options['e11_recaptcha_field_secret_key'])) {
      self::$secretKey = '';
    } else {
      self::$secretKey = $options['e11_recaptcha_field_secret_key'];
    }

    if (!isset($options['e11_recaptcha_field_behavior_comments'])) {
      self::$behaviorComments = 'not_logged_in';
    } else {
      switch($options['e11_recaptcha_field_behavior_comments']) {
        case 'all_comments':
        case 'not_logged_in':
        case 'disabled':
          self::$behaviorComments =
            $options['e11_recaptcha_field_behavior_comments'];
          break;

        default:
          self::$behaviorComments = 'not_logged_in';
          break;
      }
    }

    if (!isset($options['e11_recaptcha_field_behavior_new_users'])) {
      self::$behaviorNewUsers = 'enabled';
    } else {
      switch($options['e11_recaptcha_field_behavior_new_users']) {
        case 'enabled':
        case 'disabled':
          self::$behaviorNewUsers =
            $options['e11_recaptcha_field_behavior_new_users'];
          break;

        default:
          self::$behaviorNewUsers = 'enabled';
          break;
      }
    }

    // Tell WordPress to load reCAPTCHA CSS.

    wp_register_style('e11-recaptcha.css', plugin_dir_url( __FILE__ ) . 'css/e11-recaptcha.css', array(), E11_RECAPTCHA_VERSION);
    wp_enqueue_style('e11-recaptcha.css');

    // Tell WordPress to load reCAPTCHA API script.

    wp_register_script('recaptcha-api-js', 'https://www.google.com/recaptcha/api.js', '', '', true);
    wp_enqueue_script('recaptcha-api-js');

    // Add plugin to WordPress hooks.

    add_filter('comment_form_fields', array('e11Recaptcha', 'comment_form_captcha'));
    add_action('parse_query', array('e11Recaptcha', 'load_comment_from_cookie'));
    add_action('pre_comment_on_post', array('e11Recaptcha', 'check_comment_captcha'));
  }
}
