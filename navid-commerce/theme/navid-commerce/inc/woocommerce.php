<?php
/** WooCommerce integration without copied WooCommerce templates. @package NavidCommerce */
defined( 'ABSPATH' ) || exit;

function navid_commerce_woo_hooks() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
	add_action( 'woocommerce_before_main_content', 'navid_commerce_woo_start', 10 );
	add_action( 'woocommerce_after_main_content', 'navid_commerce_woo_end', 10 );
	add_action( 'woocommerce_before_shop_loop_item_title', 'navid_commerce_card_top', 5 );
	add_action( 'woocommerce_before_shop_loop_item_title', 'navid_commerce_card_top_end', 20 );
	add_action( 'woocommerce_after_shop_loop_item_title', 'navid_commerce_card_availability', 15 );
	add_action( 'woocommerce_after_cart_table', 'navid_commerce_cart_state' );
	add_action( 'woocommerce_cart_is_empty', 'navid_commerce_cart_state' );
}
add_action( 'after_setup_theme', 'navid_commerce_woo_hooks', 20 );

function navid_commerce_woo_start() {
	echo '<main id="main-content" class="container shop-main"><div class="shop-layout">';
	if ( ! is_product() && is_active_sidebar( 'shop-sidebar' ) ) {
		echo '<aside class="shop-filters" aria-label="' . esc_attr__( 'فیلترهای محصولات', 'navid-commerce' ) . '">';
		dynamic_sidebar( 'shop-sidebar' );
		echo '</aside>';
	}
	echo '<div class="shop-content">';
}

function navid_commerce_woo_end() {
	echo '</div></div></main>';
}

function navid_commerce_card_top() {
	echo '<div class="product-visual">';
}

function navid_commerce_card_top_end() {
	echo '</div>';
}

function navid_commerce_card_availability() {
	global $product;
	if ( $product instanceof WC_Product && ! $product->is_in_stock() ) {
		echo '<span class="stock-label">' . esc_html__( 'ناموجود', 'navid-commerce' ) . '</span>';
	}
}

function navid_commerce_cart_fragments( $fragments ) {
	ob_start();
	navid_commerce_cart_count();
	$fragments['span.nc-cart-count'] = ob_get_clean();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'navid_commerce_cart_fragments' );

function navid_commerce_cart_count() {
	$count = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	echo '<span class="nc-cart-count">' . esc_html( number_format_i18n( $count ) ) . '</span>';
}

/** Include the authoritative count in WooCommerce's refreshed cart markup. */
function navid_commerce_cart_state() {
	$count = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	echo '<span class="nc-cart-state" data-count="' . esc_attr( number_format_i18n( $count ) ) . '" hidden aria-hidden="true"></span>';
}

function navid_commerce_loop_columns() {
	return 4;
}
add_filter( 'loop_shop_columns', 'navid_commerce_loop_columns' );

/** The custom landing page supplies its own product grid styles. */
function navid_commerce_woo_styles( $styles ) {
	if ( ! is_front_page() ) {
		return $styles;
	}
	$content = (string) get_post_field( 'post_content', get_queried_object_id() );
	foreach ( array( 'woocommerce_cart', 'woocommerce_checkout', 'woocommerce_my_account' ) as $shortcode ) {
		if ( has_shortcode( $content, $shortcode ) ) {
			return $styles;
		}
	}
	foreach ( array( 'woocommerce/cart', 'woocommerce/checkout', 'woocommerce/product-reviews' ) as $block ) {
		if ( has_block( $block, $content ) ) {
			return $styles;
		}
	}
	unset( $styles['woocommerce-general'], $styles['woocommerce-layout'], $styles['woocommerce-smallscreen'] );
	return $styles;
}
add_filter( 'woocommerce_enqueue_styles', 'navid_commerce_woo_styles' );
