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
        <div id="comment-form-e11-recaptcha"></div>
      </p>
      ';

    return $comment_fields;
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

    // Tell WordPress to load reCAPTCHA API script.

    wp_register_script('recaptcha-api-js', 'https://www.google.com/recaptcha/api.js', '', '', true);
    wp_enqueue_script('recaptcha-api-js');

    // Add plugin to WordPress hooks.

    add_filter('comment_form_fields', array('e11Recaptcha', 'comment_form_captcha'));
  }
}
