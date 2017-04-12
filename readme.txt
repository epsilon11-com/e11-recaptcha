=== e11 reCAPTCHA ===
Contributors: er11
Tags: captcha, recaptcha, spam, comments, users, register
Requires at least: 4.7
Tested up to: 4.7.3
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WordPress plugin to use reCAPTCHA to help protect comments and new user signups against spam.


== Description ==

e11 reCAPTCHA is a no-frills way to add Google's reCAPTCHA widget to the standard WordPress comment form and/or the user registration page.  By default it will require only logged-out users to solve CAPTCHAs on comments and new users to solve a CAPTCHA to successfully register on the site; it's also possible to configure e11 reCAPTCHA to not require CAPTCHA solving in either of these two forms or to require it on comments by all users.


== Installation ==

1. The plugin needs two keys from your Google reCAPTCHA account in order to connect to the service.  If you don't have an account yet, [please create one now.](https://www.google.com/recaptcha)  Otherwise, log into your account.
2. Ensure the website you will be running this plugin on is registered under your Google reCAPTCHA account and set to use "reCAPTCHA V2" for validation.
3. Open the registration page for your website, and look for the section "Adding reCAPTCHA to your site", then expand the "Keys" section.  Note the "Site key" and "Secret key" fields -- the e11 reCAPTCHA plugin will require these.
4. On your WordPress site, upload the plugin files to the "/wp-content/plugins/e11-recaptcha" directory, or install the plugin through the WordPress plugins screen directly.
5. Activate the plugin through the "Plugins" screen in WordPress.
6. Use the "Settings->e11 reCAPTCHA screen" to configure the plugin, making sure to supply the "Site key" and "Secret key" parameters here.


== Screenshots ==

1. The plugin can be configured to define when reCAPTCHA forms will be presented to a user.
2. The reCAPTCHA widget on the registration page is scaled through CSS to fit the layout of the form.
3. The reCAPTCHA widget on the comment form appears after the comment <textarea> element.


== Changelog ==

= 1.0 =
* Initial release.

