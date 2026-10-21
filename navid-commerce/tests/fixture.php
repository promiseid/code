<?php
/**
 * DISPOSABLE TEST FIXTURE ONLY. Never install on a production site.
 * Copy to wp-content/mu-plugins/navid-test-tools.php for the integration suite.
 * The recovery constant is enabled before regular plugins using a test-only option.
 * This avoids file synchronization races between PHP-WASM workers.
 */
defined( 'ABSPATH' ) || exit;
if ( get_option( 'navid_test_recovery_enabled', false ) && ! defined( 'NAVID_COMMERCE_DISABLE_LOGIN_ROUTE' ) ) {
 define( 'NAVID_COMMERCE_DISABLE_LOGIN_ROUTE', true );
}
add_action( 'admin_footer', static function() {
 if ( current_user_can( 'manage_options' ) ) { echo '<div hidden id="nc-test-fixture" data-nonce="' . esc_attr( wp_create_nonce( 'navid_test_recovery' ) ) . '"></div>'; }
} );
add_action( 'admin_post_navid_test_recovery', static function() {
 if ( ! current_user_can( 'manage_options' ) ) { wp_die( 'Forbidden', '', array( 'response' => 403 ) ); }
 check_admin_referer( 'navid_test_recovery' );
 update_option( 'navid_test_recovery_enabled', isset( $_POST['mode'] ) && 'on' === $_POST['mode'], false );
 wp_safe_redirect( admin_url( 'options-general.php?page=navid-login' ) ); exit;
} );
