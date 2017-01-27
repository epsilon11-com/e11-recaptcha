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

  public static function plugin_activation() {

  }

  public static function plugin_deactivation() {

  }

  /**
   * Add the reCAPTCHA script to the header.
   */
  public static function add_recaptcha_script() {
    // [TODO] Can this be replaced with wp_register_script()/wp_enqueue_script() calls?

    echo '<script src=\'https://www.google.com/recaptcha/api.js\'></script>' . "\n";
  }

  /**
   * Attach a reCAPTCHA field to the area after the "comment" field on comment
   * forms.
   *
   * [TODO] Add admin interface to control whether field is used for all
   * comments, for comments made without being logged in, or not at all.
   *
   * @param array $comment_fields Associative array of comment field HTML
   *                              keyed by field name
   * @return array Filtered $comment_fields with "comment" field altered to
   *               include reCAPTCHA HTML
   */
  public static function comment_form_captcha($comment_fields) {

    // Push reCAPTCHA field into the comment field code, to ensure it can
    // appear whether a user is logged in or not.

    $comment_fields['comment'] = $comment_fields['comment'] . '
      <p class="comment-form-e11-recaptcha">
        <label for="comment-form-e11-recaptcha">Recaptcha</label>
        <div id="comment-form-e11-recaptcha"></div>
      </p>';

    return $comment_fields;
  }

  /**
   * [TODO] Add admin interface to set Google ReCAPTCHA API key, and read it
   *        in from database here.
   */
  public static function init() {
    if (self::$initialized) {
      return;
    }

    self::$initialized = true;

    add_filter('wp_headers', array('e11Recaptcha', 'add_recaptcha_script'));

    add_filter('comment_form_fields', array('e11Recaptcha', 'comment_form_captcha'));
  }
}
