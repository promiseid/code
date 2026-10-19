<?php
/** Store navigation. @package NavidCommerce */
defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head><meta charset="<?php bloginfo( 'charset' ); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main-content"><?php esc_html_e( 'رفتن به محتوای اصلی', 'navid-commerce' ); ?></a>
<?php $announcement = get_theme_mod( 'navid_announcement', __( 'انتخاب‌های تازه برای زندگی امروز', 'navid-commerce' ) ); ?>
<?php if ( $announcement ) : ?><div class="announcement"><div class="container"><span class="announcement-brand">NAVID / EVERYDAY BETTER</span><span><?php echo esc_html( $announcement ); ?></span><a href="<?php echo esc_url( navid_commerce_shop_url() ); ?>"><?php esc_html_e( 'کشف فروشگاه', 'navid-commerce' ); ?><?php navid_commerce_icon( 'arrow' ); ?></a></div></div><?php endif; ?>
<header class="site-header">
 <div class="header-main container">
  <div class="site-branding">
  <?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
  <a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><span class="brand-mark"><?php navid_commerce_icon( 'bag' ); ?></span><span><?php bloginfo( 'name' ); ?><small>NAVID COMMERCE</small></span></a>
  <?php endif; ?>
  </div>
  <div class="header-search"><?php get_search_form(); ?></div>
  <div class="header-actions">
   <a class="account-link" aria-label="<?php esc_attr_e( 'خوش آمدید حساب کاربری', 'navid-commerce' ); ?>" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>"><?php navid_commerce_icon( 'user' ); ?><span><small><?php esc_html_e( 'خوش آمدید', 'navid-commerce' ); ?></small> <?php esc_html_e( 'حساب کاربری', 'navid-commerce' ); ?></span></a>
   <?php if ( function_exists( 'wc_get_cart_url' ) ) : $cart_quantity = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?><a class="cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>" data-cart-label="<?php esc_attr_e( 'مشاهده سبد خرید؛ %s کالا', 'navid-commerce' ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'مشاهده سبد خرید؛ %s کالا', 'navid-commerce' ), number_format_i18n( $cart_quantity ) ) ); ?>"><?php navid_commerce_icon( 'bag' ); navid_commerce_cart_count(); ?></a><?php endif; ?>
  </div>
 </div>
 <div class="nav-border"><div class="header-nav container">
  <?php $nav_categories = navid_commerce_categories(); if ( $nav_categories ) : ?>
  <details class="category-menu"><summary><?php navid_commerce_icon( 'grid' ); ?><span><?php esc_html_e( 'دسته‌بندی محصولات', 'navid-commerce' ); ?></span><?php navid_commerce_icon( 'chevron' ); ?></summary><div class="category-dropdown"><span class="dropdown-label"><?php esc_html_e( 'دنیای انتخاب‌های شما', 'navid-commerce' ); ?></span><?php foreach ( $nav_categories as $term ) : $url = get_term_link( $term ); if ( is_wp_error( $url ) ) { continue; } ?><a href="<?php echo esc_url( $url ); ?>"><span><?php echo esc_html( $term->name ); ?></span><small><?php echo esc_html( number_format_i18n( $term->count ) ); ?></small><?php navid_commerce_icon( 'arrow' ); ?></a><?php endforeach; ?><a class="dropdown-all" href="<?php echo esc_url( navid_commerce_shop_url() ); ?>"><?php esc_html_e( 'همه محصولات', 'navid-commerce' ); ?><?php navid_commerce_icon( 'arrow' ); ?></a></div></details>
  <?php endif; ?>
  <button class="menu-toggle" aria-controls="site-navigation" aria-expanded="false"><?php navid_commerce_icon( 'menu' ); ?><span><?php esc_html_e( 'فهرست فروشگاه', 'navid-commerce' ); ?></span></button>
  <nav id="site-navigation" class="site-navigation" aria-label="<?php esc_attr_e( 'فهرست اصلی', 'navid-commerce' ); ?>"><?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'primary-menu', 'fallback_cb' => 'navid_commerce_fallback_menu', 'depth' => 2 ) ); ?></nav>
  <span class="nav-note"><?php navid_commerce_icon( 'headset' ); ?><?php echo esc_html( get_theme_mod( 'navid_support', __( 'همراه شما، از انتخاب تا خرید', 'navid-commerce' ) ) ); ?></span>
 </div></div>
</header>
