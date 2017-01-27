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

/**
 * @package e11-reCAPTCHA
 */
/*
Plugin Name: e11 reCAPTCHA
Plugin URI: https://epsilon11.com/e11-reCAPTCHA
Description: Add reCAPTCHA support to WordPress.
Version: 1.0
Author: epsilon11
Author URI: https://epsilon11.com/wordpress-plugins/
License: BSD
Text Domain: recaptcha
*/

define('E11_RECAPTCHA_VERSION', '1.0');

// Don't run if called directly.

if (!function_exists('add_action')) {
  exit;
}

register_activation_hook(__FILE__, array('e11Recaptcha', 'plugin_activation'));
register_deactivation_hook(__FILE__, array('e11Recaptcha', 'plugin_deactivation'));

require_once(plugin_dir_path(__FILE__) . 'class.e11Recaptcha.php');

add_action('init', array('e11Recaptcha', 'init'));

if (is_admin()) {
  require_once(plugin_dir_path(__FILE__) . 'class.e11RecaptchaAdmin.php');

  add_action('init', array('e11RecaptchaAdmin', 'init'));
}
