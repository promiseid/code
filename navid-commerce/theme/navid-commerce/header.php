<?php
/** Site header. @package NavidCommerce */
defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head><meta charset="<?php bloginfo( 'charset' ); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main-content"><?php esc_html_e( 'رفتن به محتوای اصلی', 'navid-commerce' ); ?></a>
<?php $announcement = get_theme_mod( 'navid_announcement', __( 'یک انتخاب تازه، برای سبک زندگی امروز', 'navid-commerce' ) ); ?>
<?php if ( $announcement ) : ?><div class="announcement"><span><?php echo esc_html( $announcement ); ?></span></div><?php endif; ?>
<header class="site-header">
	<div class="header-main container">
		<div class="site-branding">
		<?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><span class="brand-mark"><?php navid_commerce_icon( 'bag' ); ?></span><span><?php bloginfo( 'name' ); ?><small>NAVID COMMERCE</small></span></a>
		<?php endif; ?>
		</div>
		<div class="header-search"><?php get_search_form(); ?></div>
		<div class="header-actions">
			<a class="account-link" aria-label="<?php esc_attr_e( 'حساب کاربری', 'navid-commerce' ); ?>" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>"><?php navid_commerce_icon( 'user' ); ?><span><?php esc_html_e( 'حساب کاربری', 'navid-commerce' ); ?></span></a>
			<?php if ( function_exists( 'wc_get_cart_url' ) ) : $cart_quantity = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?><a class="cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>" data-cart-label="<?php esc_attr_e( 'مشاهده سبد خرید؛ %s کالا', 'navid-commerce' ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'مشاهده سبد خرید؛ %s کالا', 'navid-commerce' ), number_format_i18n( $cart_quantity ) ) ); ?>"><?php navid_commerce_icon( 'bag' ); navid_commerce_cart_count(); ?></a><?php endif; ?>
		</div>
	</div>
	<div class="nav-border"><div class="header-nav container">
		<button class="menu-toggle" aria-controls="site-navigation" aria-expanded="false"><?php navid_commerce_icon( 'menu' ); ?><span><?php esc_html_e( 'فهرست فروشگاه', 'navid-commerce' ); ?></span></button>
		<nav id="site-navigation" class="site-navigation" aria-label="<?php esc_attr_e( 'فهرست اصلی', 'navid-commerce' ); ?>"><?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'primary-menu', 'fallback_cb' => 'navid_commerce_fallback_menu', 'depth' => 2 ) ); ?></nav>
		<span class="nav-note"><span class="status-dot"></span><?php echo esc_html( get_theme_mod( 'navid_support', __( 'پاسخ‌گوی انتخاب شما هستیم', 'navid-commerce' ) ) ); ?></span>
	</div></div>
</header>
