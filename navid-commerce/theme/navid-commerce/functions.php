<?php
/** Theme bootstrap. @package NavidCommerce */
defined( 'ABSPATH' ) || exit;

define( 'NAVID_COMMERCE_VERSION', '0.1.0' );
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/woocommerce.php';

function navid_commerce_setup() {
	load_theme_textdomain( 'navid-commerce', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 64, 'width' => 220, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );
	add_theme_support( 'woocommerce', array( 'product_grid' => array( 'default_columns' => 4, 'default_rows' => 3 ) ) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
	register_nav_menus( array( 'primary' => __( 'فهرست اصلی', 'navid-commerce' ), 'footer' => __( 'فهرست پایین صفحه', 'navid-commerce' ) ) );
}
add_action( 'after_setup_theme', 'navid_commerce_setup' );

function navid_commerce_assets() {
	wp_enqueue_style( 'navid-commerce-fonts', get_theme_file_uri( '/assets/css/fonts.css' ), array(), NAVID_COMMERCE_VERSION );
	wp_enqueue_style( 'navid-commerce', get_theme_file_uri( '/assets/css/main.css' ), array( 'navid-commerce-fonts' ), NAVID_COMMERCE_VERSION );
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_style( 'navid-commerce-shop', get_theme_file_uri( '/assets/css/shop.css' ), array( 'navid-commerce' ), NAVID_COMMERCE_VERSION );
	}
	wp_enqueue_script( 'navid-commerce', get_theme_file_uri( '/assets/js/main.js' ), array(), NAVID_COMMERCE_VERSION, array( 'strategy' => 'defer', 'in_footer' => true ) );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	$accent = sanitize_hex_color( get_theme_mod( 'navid_accent', '#087f76' ) );
	if ( $accent ) {
		wp_add_inline_style( 'navid-commerce', ':root{--brand:' . $accent . ';}' );
	}
}
add_action( 'wp_enqueue_scripts', 'navid_commerce_assets' );

function navid_commerce_preload_fonts( $resources ) {
	foreach ( array( 'Regular', 'Bold' ) as $weight ) {
		$resources[] = array( 'href' => get_theme_file_uri( '/assets/fonts/Vazirmatn-' . $weight . '.woff2' ), 'as' => 'font', 'type' => 'font/woff2', 'crossorigin' => 'anonymous' );
	}
	return $resources;
}
add_filter( 'wp_preload_resources', 'navid_commerce_preload_fonts' );

function navid_commerce_content_width() {
	$GLOBALS['content_width'] = 1280;
}
add_action( 'after_setup_theme', 'navid_commerce_content_width', 0 );

function navid_commerce_widgets() {
	register_sidebar( array( 'name' => __( 'فیلترهای فروشگاه', 'navid-commerce' ), 'id' => 'shop-sidebar', 'before_widget' => '<section class="shop-widget">', 'after_widget' => '</section>', 'before_title' => '<h2 class="widget-title">', 'after_title' => '</h2>' ) );
}
add_action( 'widgets_init', 'navid_commerce_widgets' );
