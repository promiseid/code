<?php
/**
 * Plugin Name: Navid Commerce Core
 * Description: Optional login branding and recoverable custom login routing.
 * Version: 0.4.0
 * Requires at least: 6.6
 * Requires PHP: 8.0
 * License: GPL-2.0-or-later
 * Text Domain: navid-commerce-core
 */
defined( 'ABSPATH' ) || exit;
function navid_login_options() {
	return wp_parse_args( (array) get_option( 'navid_commerce_login', array() ), array( 'branding' => true, 'title' => 'به فروشگاه خود خوش آمدید', 'message' => 'برای مدیریت فروشگاه وارد حساب خود شوید.', 'color' => '#d92846', 'logo' => 0, 'background' => 0, 'enabled' => false, 'slug' => 'store-entrance' ) );
}
function navid_login_route_active() {
	return ! is_multisite() && ! ( defined( 'NAVID_COMMERCE_DISABLE_LOGIN_ROUTE' ) && NAVID_COMMERCE_DISABLE_LOGIN_ROUTE ) && ! empty( navid_login_options()['enabled'] );
}
function navid_login_custom_url() {
	$slug = navid_login_options()['slug'];
	return get_option( 'permalink_structure' ) ? home_url( '/' . $slug . '/' ) : add_query_arg( 'navid-login', $slug, home_url( '/' ) );
}
function navid_login_validate( $input ) {
	if ( ! is_array( $input ) ) { return new WP_Error( 'invalid', 'تنظیمات نامعتبر است.' ); }
	$slug = isset( $input['slug'] ) && is_string( $input['slug'] ) ? trim( $input['slug'] ) : '';
	$reserved = array( 'admin', 'wp-admin', 'wp-login', 'wp-json', 'wp-content', 'wp-includes', 'feed', 'embed', 'login', 'logout', 'register', 'checkout', 'cart', 'my-account', 'shop', 'robots', 'sitemap' );
	if ( ! preg_match( '/^[a-z0-9][a-z0-9-]{2,46}[a-z0-9]$/D', $slug ) || in_array( $slug, $reserved, true ) || get_page_by_path( $slug, OBJECT, get_post_types( array( 'public' => true ) ) ) || ( taxonomy_exists( 'product_cat' ) && term_exists( $slug, 'product_cat' ) ) ) { return new WP_Error( 'slug', 'مسیر ورود نامعتبر یا با مسیر موجود مشترک است.' ); }
	if ( is_multisite() && ! empty( $input['enabled'] ) ) { return new WP_Error( 'multisite', 'مسیر سفارشی در شبکه چندسایتی پشتیبانی نمی‌شود.' ); }
	$out = array( 'slug' => $slug );
	foreach ( array( 'enabled', 'branding' ) as $key ) { $out[ $key ] = ! empty( $input[ $key ] ); }
	foreach ( array( 'title', 'message' ) as $key ) { $out[ $key ] = sanitize_text_field( is_scalar( $input[ $key ] ?? '' ) ? $input[ $key ] : '' ); }
	$out['color'] = sanitize_hex_color( is_string( $input['color'] ?? null ) ? $input['color'] : '' ) ?: '#d92846';
	foreach ( array( 'logo', 'background' ) as $key ) { $id = is_scalar( $input[ $key ] ?? 0 ) ? absint( $input[ $key ] ?? 0 ) : 0; $out[ $key ] = $id && wp_attachment_is_image( $id ) ? $id : 0; }
	return $out;
}
function navid_login_filter_url( $url, $path = '', $scheme = null ) {
	if ( ! navid_login_route_active() ) { return $url; }
	$parts = wp_parse_url( $url );
	if ( empty( $parts['path'] ) || 'wp-login.php' !== basename( $parts['path'] ) ) { return $url; }
	$login = navid_login_custom_url();
	if ( ! empty( $parts['query'] ) ) { $login .= ( str_contains( $login, '?' ) ? '&' : '?' ) . $parts['query']; }
	if ( ! empty( $parts['fragment'] ) ) { $login .= '#' . $parts['fragment']; }
	return is_ssl() || 'https' === $scheme ? set_url_scheme( $login, 'https' ) : $login;
}
add_filter( 'site_url', 'navid_login_filter_url', 20, 3 );
add_filter( 'network_site_url', 'navid_login_filter_url', 20, 3 );
function navid_login_dispatch() {
	if ( ! navid_login_route_active() || wp_doing_ajax() || ( defined( 'WP_CLI' ) && WP_CLI ) ) { return; }
	$path = rawurldecode( (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_PATH ) );
	$slug = navid_login_options()['slug'];
	$home_path = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	$alias_path = (string) wp_parse_url( home_url( '/' . $slug . '/' ), PHP_URL_PATH );
	$query = isset( $_GET['navid-login'] ) && is_string( $_GET['navid-login'] ) ? wp_unslash( $_GET['navid-login'] ) : '';
	if ( untrailingslashit( $path ) === untrailingslashit( $alias_path ) || ( $query && hash_equals( $slug, $query ) && untrailingslashit( $path ) === untrailingslashit( $home_path ) ) ) {
		global $action, $error, $errors, $interim_login, $user_login, $user_email, $redirect_to, $requested_redirect_to;
		$GLOBALS['pagenow'] = 'wp-login.php';
		nocache_headers();
		require ABSPATH . 'wp-login.php';
		exit;
	}
	if ( 'wp-login.php' === basename( $path ) && ! is_user_logged_in() ) { nocache_headers(); wp_die( esc_html__( 'این صفحه در دسترس نیست.', 'navid-commerce-core' ), '', array( 'response' => 404 ) ); }
}
add_action( 'wp_loaded', 'navid_login_dispatch', 1 );
add_action( 'login_enqueue_scripts', function() {
	$o = navid_login_options(); if ( ! $o['branding'] ) { return; }
	wp_enqueue_style( 'navid-login', plugins_url( 'assets/login.css', __FILE__ ), array(), '0.4.0' );
	$css = ':root{--nc-login-accent:' . ( sanitize_hex_color( $o['color'] ) ?: '#d92846' ) . ';}';
	foreach ( array( 'logo' => '.login h1 a', 'background' => 'body.login' ) as $key => $selector ) {
		$image = $o[ $key ] ? wp_get_attachment_image_url( absint( $o[ $key ] ), 'large' ) : false;
		if ( $image ) { $css .= $selector . '{background-image:' . ( 'background' === $key ? 'linear-gradient(#fafafaee,#fafafaee),' : '' ) . 'url("' . esc_url( $image ) . '");' . ( 'logo' === $key ? 'font-size:0!important;' : 'background-size:cover;background-position:center;' ) . '}'; }
	}
	wp_add_inline_style( 'navid-login', $css );
} );
add_filter( 'login_headerurl', function( $url ) { return navid_login_options()['branding'] ? home_url( '/' ) : $url; } );
add_filter( 'login_headertext', function( $text ) { return navid_login_options()['branding'] ? get_bloginfo( 'name' ) : $text; } );
add_filter( 'login_message', function( $message ) { $o = navid_login_options(); return ! $o['branding'] ? $message : '<div class="nc-login-intro"><h2>' . esc_html( $o['title'] ) . '</h2><p>' . esc_html( $o['message'] ) . '</p></div>' . $message; } );
add_action( 'admin_menu', function() { add_options_page( 'ورود نوید', 'ورود نوید', 'manage_options', 'navid-login', 'navid_login_fallback_page' ); } );
function navid_login_fallback_page() {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	echo '<div class="wrap"><h1>تنظیمات ورود نوید</h1><p>مدیریت ظاهر و آدرس در مدیریت نویدشاپ انجام می‌شود. این بخش پس از تعویض پوسته نیز برای بازیابی دسترسی باقی می‌ماند.</p>';
	if ( function_exists( 'navid_commerce_admin_page' ) ) { echo '<a class="button" href="' . esc_url( admin_url( 'admin.php?page=navid-commerce&tab=login' ) ) . '">مدیریت نویدشاپ</a>'; }
	echo '<p>آدرس فعلی: <a href="' . esc_url( wp_login_url() ) . '">' . esc_html( wp_login_url() ) . '</a></p><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'navid_login_restore' );
	echo '<input type="hidden" name="action" value="navid_login_restore"><button class="button">بازگرداندن مسیر پیش‌فرض ورود</button></form><p>بازیابی از فایل‌ها: افزونه navid-commerce-core را غیرفعال کنید یا در wp-config.php اضافه کنید:</p><pre dir="ltr">define( \'NAVID_COMMERCE_DISABLE_LOGIN_ROUTE\', true );</pre></div>';
}
add_action( 'admin_post_navid_login_restore', function() {
	if ( ! current_user_can( 'manage_options' ) ) { wp_die( 'Forbidden', '', array( 'response' => 403 ) ); }
	check_admin_referer( 'navid_login_restore' );
	$o = navid_login_options(); $o['enabled'] = false; update_option( 'navid_commerce_login', $o, false );
	wp_safe_redirect( admin_url( 'options-general.php?page=navid-login' ) ); exit;
} );
