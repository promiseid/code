<?php
/** Native theme settings. @package NavidCommerce */
defined( 'ABSPATH' ) || exit;

function navid_commerce_customize( $customizer ) {
	$customizer->add_section( 'navid_store', array( 'title' => __( 'تنظیمات نویدشاپ', 'navid-commerce' ), 'priority' => 30 ) );
	$customizer->add_setting( 'navid_home_layout', array( 'default' => 'market', 'sanitize_callback' => function( $v ) { return 'studio' === $v ? 'studio' : 'market'; } ) );
	$customizer->add_control( 'navid_home_layout', array( 'label' => 'چیدمان فروشگاه', 'section' => 'navid_store', 'type' => 'select', 'choices' => array( 'market' => 'بازارگاهی', 'studio' => 'استودیو سه‌بعدی' ) ) );
	$fields = array(
		'navid_announcement' => array( __( 'نوار اطلاع‌رسانی', 'navid-commerce' ), __( 'انتخاب‌های تازه برای زندگی امروز', 'navid-commerce' ) ),
		'navid_hero_label' => array( __( 'برچسب بالای بنر', 'navid-commerce' ), 'THE NEXT EVERYDAY / NAVID' ),
		'navid_hero_title' => array( __( 'عنوان بنر اصلی', 'navid-commerce' ), __( 'فراتر از یک انتخاب', 'navid-commerce' ) ),
		'navid_hero_text' => array( __( 'توضیح بنر اصلی', 'navid-commerce' ), __( 'صدا را احساس کن. جزئیات را کشف کن. همراه بعدی زندگی‌ات را از زاویه‌ای تازه ببین.', 'navid-commerce' ) ),
		'navid_hero_button' => array( __( 'متن دکمه بنر', 'navid-commerce' ), __( 'وارد دنیای نوید شو', 'navid-commerce' ) ),
		'navid_support' => array( __( 'متن پشتیبانی', 'navid-commerce' ), __( 'همراه شما، از انتخاب تا خرید', 'navid-commerce' ) ),
		'navid_featured_title' => array( __( 'عنوان بنر کناری', 'navid-commerce' ), __( 'همراه کوچک، تجربه بزرگ.', 'navid-commerce' ) ),
		'navid_featured_text' => array( __( 'توضیح بنر کناری', 'navid-commerce' ), __( 'یک قدم هوشمندتر در ریتم زندگی.', 'navid-commerce' ) ),
	);
	foreach ( $fields as $key => $field ) {
		$customizer->add_setting( $key, array( 'default' => $field[1], 'sanitize_callback' => 'sanitize_text_field' ) );
		$customizer->add_control( $key, array( 'label' => $field[0], 'section' => 'navid_store', 'type' => 'text' ) );
	}
	$customizer->add_setting( 'navid_accent', array( 'default' => '#d92846', 'sanitize_callback' => 'sanitize_hex_color' ) );
	$customizer->add_control( new WP_Customize_Color_Control( $customizer, 'navid_accent', array( 'label' => __( 'رنگ اصلی برند', 'navid-commerce' ), 'section' => 'navid_store' ) ) );
	$customizer->add_setting( 'navid_hero_image', array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
	$customizer->add_control( new WP_Customize_Media_Control( $customizer, 'navid_hero_image', array( 'label' => __( 'تصویر بنر اصلی', 'navid-commerce' ), 'mime_type' => 'image', 'section' => 'navid_store' ) ) );
	$customizer->add_setting( 'navid_featured_image', array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
	$customizer->add_control( new WP_Customize_Media_Control( $customizer, 'navid_featured_image', array( 'label' => __( 'تصویر بنر کناری', 'navid-commerce' ), 'mime_type' => 'image', 'section' => 'navid_store' ) ) );
	$choices = array( 0 => __( 'انتخاب خودکار', 'navid-commerce' ) );
	if ( taxonomy_exists( 'product_cat' ) ) {
		$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'number' => 100 ) );
		if ( ! is_wp_error( $terms ) ) { foreach ( $terms as $term ) { $choices[ $term->term_id ] = $term->name; } }
	}
	$customizer->add_setting( 'navid_featured_category', array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
	$customizer->add_control( 'navid_featured_category', array( 'label' => __( 'دسته مقصد بنر کناری', 'navid-commerce' ), 'section' => 'navid_store', 'type' => 'select', 'choices' => $choices ) );
	foreach ( array( 'navid_3d_scene' => __( 'صحنه سه‌بعدی تعاملی صفحه نخست', 'navid-commerce' ), 'navid_shopping_tools' => __( 'علاقه‌مندی، مقایسه و جستجوی زنده', 'navid-commerce' ), 'navid_sticky_buy' => __( 'نوار دسترسی سریع به خرید محصول', 'navid-commerce' ), 'navid_show_benefits' => __( 'نمایش راهنمای خدمات', 'navid-commerce' ), 'navid_show_categories' => __( 'نمایش دسته‌بندی‌های تصویری', 'navid-commerce' ), 'navid_show_collections' => __( 'نمایش بنرهای کالکشن', 'navid-commerce' ), 'navid_show_journal' => __( 'نمایش نوشته‌های مجله', 'navid-commerce' ), 'navid_enable_quickview' => __( 'فعال بودن پیش‌نمایش سریع محصول', 'navid-commerce' ), 'navid_mobile_dock' => __( 'نمایش نوار دسترسی موبایل', 'navid-commerce' ) ) as $key => $label ) {
		$customizer->add_setting( $key, array( 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean' ) );
		$customizer->add_control( $key, array( 'label' => $label, 'section' => 'navid_store', 'type' => 'checkbox' ) );
	}
	$customizer->add_setting( 'navid_light_home_scripts', array( 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean' ) );
	$customizer->add_control( 'navid_light_home_scripts', array( 'label' => __( 'بارگذاری سبک صفحه نخست', 'navid-commerce' ), 'description' => __( 'اسکریپت‌های استاندارد صفحه نخست در پایین صفحه قرار می‌گیرند. اگر افزونه‌ای به اجرای زودهنگام اسکریپت وابسته است، می‌توانید این گزینه را خاموش کنید.', 'navid-commerce' ), 'section' => 'navid_store', 'type' => 'checkbox' ) );
}
add_action( 'customize_register', 'navid_commerce_customize' );
