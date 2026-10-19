<?php
/** Native theme settings. @package NavidCommerce */
defined( 'ABSPATH' ) || exit;

function navid_commerce_customize( $customizer ) {
	$customizer->add_section( 'navid_store', array( 'title' => __( 'تنظیمات نویدشاپ', 'navid-commerce' ), 'priority' => 30 ) );
	$fields = array(
		'navid_announcement' => array( __( 'نوار اطلاع‌رسانی', 'navid-commerce' ), __( 'یک انتخاب تازه، برای سبک زندگی امروز', 'navid-commerce' ) ),
		'navid_hero_label' => array( __( 'برچسب بالای بنر', 'navid-commerce' ), __( 'کالکشن تازه نویدشاپ', 'navid-commerce' ) ),
		'navid_hero_title' => array( __( 'عنوان بنر اصلی', 'navid-commerce' ), __( 'تکنولوژی خوب، حالِ خوب.', 'navid-commerce' ) ),
		'navid_hero_text' => array( __( 'توضیح بنر اصلی', 'navid-commerce' ), __( 'از اولین آهنگ صبح تا آخرین کار روز؛ همراه‌های تازه‌ات را اینجا پیدا کن.', 'navid-commerce' ) ),
		'navid_hero_button' => array( __( 'متن دکمه بنر', 'navid-commerce' ), __( 'کشف محصولات', 'navid-commerce' ) ),
		'navid_support' => array( __( 'متن پشتیبانی', 'navid-commerce' ), __( 'پاسخ‌گوی انتخاب شما هستیم', 'navid-commerce' ) ),
	);
	foreach ( $fields as $key => $field ) {
		$customizer->add_setting( $key, array( 'default' => $field[1], 'sanitize_callback' => 'sanitize_text_field' ) );
		$customizer->add_control( $key, array( 'label' => $field[0], 'section' => 'navid_store', 'type' => 'text' ) );
	}
	$customizer->add_setting( 'navid_accent', array( 'default' => '#087f76', 'sanitize_callback' => 'sanitize_hex_color' ) );
	$customizer->add_control( new WP_Customize_Color_Control( $customizer, 'navid_accent', array( 'label' => __( 'رنگ اصلی برند', 'navid-commerce' ), 'section' => 'navid_store' ) ) );
	$customizer->add_setting( 'navid_hero_image', array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
	$customizer->add_control( new WP_Customize_Media_Control( $customizer, 'navid_hero_image', array( 'label' => __( 'تصویر بنر اصلی', 'navid-commerce' ), 'mime_type' => 'image', 'section' => 'navid_store' ) ) );
}
add_action( 'customize_register', 'navid_commerce_customize' );
