<?php
 /**
 * Content Revalidation Tracker
 *
 * Content Revalidation Tracker builds post after creation or edition.
 *
 * @package content_revalidation_tracker
 * @author Dropndot Solutions
 * @since 1.0.0
 * @license GPL-2.0+
 */

/**
 * Plugin Name: Content Revalidation Tracker
 * Plugin URI: https://wordpress.org/plugins/content-revalidation-tracker/
 * Description: Content Revalidation Tracker builds post after creation or edition
 * Author: Dropndot Solutions
 * Version: 1.0.0
 * Author URI: https://dropndot.com/
 * Text Domain: content-revalidation-tracker
 * License: GPL-2.0+
 * Requires at least: 6.3
 * Requires PHP: 7.4
 */

/*
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with This program. If not, see https://www.gnu.org/licenses/gpl-3.0.html.
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hit URL when new post created, updated or deleted.
 *
 * @param mixed $post_ID     Current Post ID.
 *
 * @return void
 */
function content_revalidation_tracker_check_post_update( $post_ID ) {

	if ( ! empty( get_the_title( $post_ID ) ) && ( 'publish' === get_post_status( $post_ID ) || 'trash' === get_post_status( $post_ID ) ) ) {

		$secret_key = get_option( 'content_revalidation_tracker_secret_key', '' ); // Retrieve the secret key
		$domain     = rtrim( get_option( 'content_revalidation_tracker_domain', '' ), '/' ); // Retrieve the domain
		$post_slug  = str_replace( home_url(), '', get_permalink( $post_ID ) );

		// Make the GET request
		$response = wp_remote_get(
			"{$domain}/api/revalidate?path={$post_slug}&secret={$secret_key}",
			array(
				'timeout'   => 10,
				'sslverify' => false,
			)
		);

		// Check for errors
		if ( is_wp_error( $response ) ) {
			// Log the error
			error_log( 'API request failed: ' . $response->get_error_message() );
		}

		// Check the response code
		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $response_code ) {
			// Log unexpected response codes
			error_log( 'Unexpected response code: ' . $response_code );
		}
	}
}

// Hook into the appropriate WordPress actions.
add_action( 'post_updated', 'content_revalidation_tracker_check_post_update', 10, 3 );
add_action( 'save_post', 'content_revalidation_tracker_check_post_update', 10, 3 );
add_action( 'delete_post', 'content_revalidation_tracker_check_post_update', 10, 1 ); // delete_post provides only 1 argument.


/**
 * Check User Profile Update.
 *
 * @param mixed $user_id Author's ID.
 *
 * @return void
 */
function content_revalidation_tracker_check_user_profile_update( $user_id ) {
	// Get the updated user data.
	$user = get_userdata( $user_id );

	// Get the user slug (nicename).
	$user_slug = $user->user_nicename;

	$secret_key = get_option( 'content_revalidation_tracker_secret_key', '' ); // Retrieve the secret key
	$domain     = rtrim( get_option( 'content_revalidation_tracker_domain', '' ), '/' ); // Retrieve the domain

	// Make the GET request.
	$response = wp_remote_get(
		"{$domain}/api/revalidate?path=/author/{$user_slug}/&secret={$secret_key}",
		array(
			'timeout'   => 10,
			'sslverify' => false,
		)
	);

	// Check for errors.
	if ( is_wp_error( $response ) ) {
		// Log the error.
		error_log( 'API request failed: ' . $response->get_error_message() );
	}

	// Check the response code.
	$response_code = wp_remote_retrieve_response_code( $response );
	if ( 200 !== $response_code ) {
		// Log unexpected response codes.
		error_log( 'Unexpected response code: ' . $response_code );
	}
}

// Hook into the appropriate WordPress actions
add_action( 'profile_update', 'content_revalidation_tracker_check_user_profile_update' );
add_action( 'edit_user_profile_update', 'content_revalidation_tracker_check_user_profile_update' );
add_action( 'user_register', 'content_revalidation_tracker_check_user_profile_update' );
add_action( 'delete_user', 'content_revalidation_tracker_check_user_profile_update' );


/**
 * #### Admin Page Development ####
 *
 * Register the admin menu
 *
 * @return void
 */
function content_revalidation_tracker_menu() {
	add_options_page(
		'Content Revalidation Tracker Settings',
		'Content Revalidation Tracker',
		'manage_options',
		'content-revalidation-tracker',
		'content_revalidation_tracker_settings_page'
	);
}
add_action( 'admin_menu', 'content_revalidation_tracker_menu' );

/**
 * Display the settings page
 *
 * @return void
 */
function content_revalidation_tracker_settings_page() {
	?>
	<div class="wrap">
		<h1>Content Revalidation Tracker Settings</h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'content_revalidation_tracker_settings' );
			do_settings_sections( 'content-revalidation-tracker' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Register the settings
 *
 * @return void
 */
function content_revalidation_tracker_settings_init() {
	register_setting( 'content_revalidation_tracker_settings', 'content_revalidation_tracker_secret_key' );
	register_setting( 'content_revalidation_tracker_settings', 'content_revalidation_tracker_domain' );

	add_settings_section(
		'content_revalidation_tracker_settings_section',
		__( 'API Settings', 'content-revalidation-tracker' ),
		null,
		'content-revalidation-tracker'
	);

	add_settings_field(
		'content_revalidation_tracker_domain',
		__( 'Domain', 'content-revalidation-tracker' ),
		'content_revalidation_tracker_domain_render',
		'content-revalidation-tracker',
		'content_revalidation_tracker_settings_section'
	);

	add_settings_field(
		'content_revalidation_tracker_secret_key',
		__( 'Secret Key', 'content-revalidation-tracker' ),
		'content_revalidation_tracker_secret_key_render',
		'content-revalidation-tracker',
		'content_revalidation_tracker_settings_section'
	);

	add_settings_field(
		'content_revalidation_tracker_instruction_field',
		__( 'Construction of the API:', 'content-revalidation-tracker' ),
		'content_revalidation_tracker_instruction_field_callback',
		'content-revalidation-tracker',
		'content_revalidation_tracker_settings_section'
	);
}
add_action( 'admin_init', 'content_revalidation_tracker_settings_init' );

/**
 * Render the domain input
 *
 * @return void
 */
function content_revalidation_tracker_domain_render() {
	$domain = get_option( 'content_revalidation_tracker_domain' );
	?>
	<input type="text" name="content_revalidation_tracker_domain" value="<?php echo esc_attr( $domain ); ?>" />
	<p class="cptui-field-description description"><?php esc_html_e( 'e.g. https://example.com', 'content-revalidation-tracker' ); ?></p>
	<?php
}

/**
 * Render the secret key input
 *
 * @return void
 */
function content_revalidation_tracker_secret_key_render() {
	$secret_key = get_option( 'content_revalidation_tracker_secret_key' );
	?>
	<input type="text" name="content_revalidation_tracker_secret_key" value="<?php echo esc_attr( $secret_key ); ?>" />
	<p class="cptui-field-description description"><?php esc_html_e( 'e.g. ad43d49b3f2e4847a6f6', 'content-revalidation-tracker' ); ?></p>
	<?php
}

/**
 * Field callback function.
 *
 * @return void
 */
function content_revalidation_tracker_instruction_field_callback() {

	$domain     = ( null !== get_option( 'content_revalidation_tracker_domain', '' ) && ! empty( get_option( 'content_revalidation_tracker_domain', '' ) ) ) ? rtrim( get_option( 'content_revalidation_tracker_domain', '' ), '/' ) : 'http://example.com'; // Retrieve the domain
	$secret_key = ( null !== get_option( 'content_revalidation_tracker_secret_key', '' ) && ! empty( get_option( 'content_revalidation_tracker_secret_key', '' ) ) ) ? get_option( 'content_revalidation_tracker_secret_key', '' ) : 'ad43d49b3f2e4847a6f6'; // Retrieve the domain

	echo wp_kses_post("<p>{$domain}/api/revalidate?path=/{post_slug}/&secret={$secret_key}</p>" );
}

