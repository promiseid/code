<?php
/** Missing page. @package NavidCommerce */
defined( 'ABSPATH' ) || exit;
get_header(); ?>
<main id="main-content" class="container empty-state"><span class="error-number">404</span><h1><?php esc_html_e( 'این صفحه را پیدا نکردیم.', 'navid-commerce' ); ?></h1><p><?php esc_html_e( 'شاید محصول دلخواهت همین نزدیکی باشد.', 'navid-commerce' ); ?></p><?php get_search_form(); ?><a class="button button-dark" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'بازگشت به خانه', 'navid-commerce' ); ?></a></main>
<?php get_footer(); ?>
