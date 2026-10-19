<?php
/** Child theme overrides. @package NavidCommerceChild */
defined( 'ABSPATH' ) || exit;
function navid_commerce_child_assets() {
	wp_enqueue_style( 'navid-commerce-child', get_stylesheet_uri(), array( 'navid-commerce' ), '0.1.0' );
}
add_action( 'wp_enqueue_scripts', 'navid_commerce_child_assets', 30 );
