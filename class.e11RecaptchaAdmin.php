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

class e11RecaptchaAdmin {
  private static $initialized = false;

  /**
   * Plugin initialization.
   */
  public static function init() {

    // Ensure function is called only once.

    if (self::$initialized) {
      return;
    }

    self::$initialized = true;

    // Load stylesheet for plugin.

    wp_register_style('e11-recaptcha-admin.css', plugin_dir_url( __FILE__ ) . 'css/e11-recaptcha-admin.css', array(), E11_RECAPTCHA_VERSION);
    wp_enqueue_style('e11-recaptcha-admin.css');

    // Add plugin to WordPress hooks.

    add_action('admin_init', array('e11RecaptchaAdmin', 'settings_init'));
    add_action('admin_menu', array('e11RecaptchaAdmin', 'admin_menu_options_page'));
  }

  /**
   * Callback to register plugin settings and build its settings page.
   */
  public static function settings_init() {

    // Register settings for plugin.

    register_setting('e11_recaptcha', 'e11_recaptcha_options');

    // Build settings page for plugin.

    // Section: reCAPTCHA API Keys

    add_settings_section(
      'e11_recaptcha_section_api',
      __('reCAPTCHA API Keys', 'e11-recaptcha'),
      array('e11RecaptchaAdmin', 'section_api_cb'),
      'e11_recaptcha'
    );

    add_settings_field(
      'e11_recaptcha_field_site_key',
      __('Site key', 'e11-recaptcha'),
      array('e11RecaptchaAdmin', 'field_site_key_cb'),
      'e11_recaptcha',
      'e11_recaptcha_section_api',
      [
        'label_for' => 'e11_recaptcha_field_site_key',
        'class' => 'e11-recaptcha-row'
      ]
    );

    add_settings_field(
      'e11_recaptcha_field_secret_key',
      __('Secret key', 'e11-recaptcha'),
      array('e11RecaptchaAdmin', 'field_secret_key_cb'),
      'e11_recaptcha',
      'e11_recaptcha_section_api',
      [
        'label_for' => 'e11_recaptcha_field_secret_key',
        'class' => 'e11-recaptcha-row'
      ]
    );

    // Section: Behavior

    add_settings_section(
      'e11_recaptcha_section_behavior',
      __('Behavior', 'e11-recaptcha'),
      array('e11RecaptchaAdmin', 'section_behavior_cb'),
      'e11_recaptcha'
    );

    add_settings_field(
      'e11_recaptcha_field_behavior_comments',
      __('Comments', 'e11-recaptcha'),
      array('e11RecaptchaAdmin', 'field_behavior_comments_cb'),
      'e11_recaptcha',
      'e11_recaptcha_section_behavior',
      [
        'label_for' => 'e11_recaptcha_field_behavior_comments',
        'class' => 'e11-recaptcha-row'
      ]
    );

    add_settings_field(
      'e11_recaptcha_field_behavior_new_users',
      __('New users', 'e11-recaptcha'),
      array('e11RecaptchaAdmin', 'field_behavior_new_users_cb'),
      'e11_recaptcha',
      'e11_recaptcha_section_behavior',
      [
        'label_for' => 'e11_recaptcha_field_behavior_new_users',
        'class' => 'e11-recaptcha-row'
      ]
    );
  }

  /**
   * Callback to build and display HTML for "reCAPTCHA API Keys" section.
   *
   * @param array $args Associative array of field arguments
   */
  public static function section_api_cb() {
    echo __(
      '<p>Please enter your reCAPTCHA API keys below.  If you do 
            not yet have a Google reCAPTCHA account, 
            <a href="https://www.google.com/recaptcha">please create one</a>
            to obtain the necessary credentials to continue.</p>
      ', 'e11-recaptcha');
  }

  /**
   * Callback to build and display HTML for "Behavior" section.
   *
   * @param array $args Associative array of field arguments
   */
  public static function section_behavior_cb() {
    echo __(
      '<p>The following settings determine when users will be required to
          solve reCAPTCHAs.</p>
      ', 'e11-recaptcha');
  }

  /**
   * Callback to build and display HTML for API site key text input.
   *
   * @param array $args Associative array of field arguments
   */
  public static function field_site_key_cb($args) {
    $options = get_option('e11_recaptcha_options', array());

    $siteKey = '';

    if (isset($options[$args['label_for']])) {
      $siteKey = $options[$args['label_for']];
    }

    echo '
      <input id="' 
        . esc_attr($args['label_for']) 
        . '" type="text" name="e11_recaptcha_options[' 
        . esc_attr($args['label_for']) 
        . ']" value="'
        . esc_attr($siteKey)
        . '" />
      <p class="description">'
      . esc_html_x('Site key as provided by Google for your reCAPTCHA account', 'e11-recaptcha')
      . '</p>
    ';
  }

  /**
   * Callback to build and display HTML for API secret key text input.
   *
   * @param array $args Associative array of field arguments
   */
  public static function field_secret_key_cb($args) {
    $options = get_option('e11_recaptcha_options', array());

    $secretKey = '';

    if (isset($options[$args['label_for']])) {
      $secretKey = $options[$args['label_for']];
    }

    echo '
      <input id="'
      . esc_attr($args['label_for'])
      . '" type="text" name="e11_recaptcha_options['
      . esc_attr($args['label_for'])
      . ']" value="'
      . esc_attr($secretKey)
      . '" />
      <p class="description">'
      . esc_html_x('Secret key as provided by Google for your reCAPTCHA account', 'e11-recaptcha')
      . '</p>
    ';
  }

  /**
   * Callback to build and display HTML for "behavior with comments" select
   * box.
   *
   * @param array $args Associative array of field arguments
   */
  public static function field_behavior_comments_cb($args) {
    $options = get_option('e11_recaptcha_options', array());

    // Comment behavior may be one of "all_comments", "not_logged_in",
    // or "disabled".  Default to "not_logged_in".

    $behavior = 'not_logged_in';

    if (isset($options[$args['label_for']])) {
      $behavior = $options[$args['label_for']];

      switch($behavior) {
        case 'all_comments':
        case 'not_logged_in':
        case 'disabled':
          break;

        default:
          $behavior = 'not_logged_in';
          break;
      }
    }

    echo '
      <select id="'
      . esc_attr($args['label_for'])
      . '" name="e11_recaptcha_options['
      . esc_attr($args['label_for'])
      . ']">
        <option value="all_comments" '
      . selected($behavior, 'all_comments', false)
      . '>'
      . esc_html_x('Enabled for all comments', 'e11-recaptcha')
      . '</option>
        <option value="not_logged_in" '
      . selected($behavior, 'not_logged_in', false)
      . '>'
      . esc_html_x('Enabled for comments by users not logged in', 'e11-recaptcha')
      . '</option>
        <option value="disabled" '
          . selected($behavior, 'disabled', false)
      . '>'
      . esc_html_x('Disabled', 'e11-recaptcha')
      . '</option>
      </select>
      <p class="description">'
      . esc_html_x('Require users to solve a reCAPTCHA to post a comment', 'e11-recaptcha')
      . '</p>
    ';
  }

  /**
   * Callback to build and display HTML for "behavior with new users" select
   * box.
   *
   * @param array $args Associative array of field arguments
   */
  public static function field_behavior_new_users_cb($args) {
    $options = get_option('e11_recaptcha_options', array());

    // New user behavior may be one of "enabled" or "disabled".
    // Defaults to "enabled".

    $behavior = 'enabled';

    if (isset($options[$args['label_for']])) {
      $behavior = $options[$args['label_for']];

      switch($behavior) {
        case 'enabled':
        case 'disabled':
          break;

        default:
          $behavior = 'enabled';
          break;
      }
    }

    echo '
      <select id="'
      . esc_attr($args['label_for'])
      . '" name="e11_recaptcha_options['
      . esc_attr($args['label_for'])
      . ']">
        <option value="enabled" '
      . selected($behavior, 'enabled', false)
      . '>'
      . esc_html_x('Enabled for user registrations', 'e11-recaptcha')
      . '</option>
        <option value="disabled" '
      . selected($behavior, 'disabled', false)
      . '>'
      . esc_html_x('Disabled', 'e11-recaptcha')
      . '</option>
      </select>
      <p class="description">'
      . esc_html_x('Require new users to solve a reCAPTCHA to create an account', 'e11-recaptcha')
      . '</p>
    ';
  }


  /**
   * Callback to add plugin options page under "Settings" in admin menu.
   */
  public static function admin_menu_options_page() {
    add_submenu_page(
      'options-general.php',
      'e11 reCAPTCHA',
      'e11 reCAPTCHA',
      'manage_options',
      'e11_recaptcha',
      array('e11RecaptchaAdmin', 'options_page_html')
    );
  }

  /**
   * Callback to build and display options page for plugin.
   */
  public static function options_page_html() {

    // Block access unless user has adequate permissions.

    if (!current_user_can('manage_options')) {
      return;
    }

    // Display status messages to user.

    settings_errors('e11_recaptcha_messages');

    // Output settings HTML.

    echo '
      <div class="wrap">
        <h1>' . esc_html(get_admin_page_title()) . '</h1>
        <form action="options.php" method="post">
    ';

    // Write WordPress hidden fields for form input.
    settings_fields('e11_recaptcha');

    // Write settings HTML for plugin.
    do_settings_sections('e11_recaptcha');

    // Write submit button.
    submit_button('Save changes');

    echo '
        </form>
      </div>
    ';
  }

}
