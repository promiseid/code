<?php
/** Search using the native WordPress query. @package NavidCommerce */
defined( 'ABSPATH' ) || exit;
$search_id = wp_unique_id( 'nc-search-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $search_id ); ?>"><?php esc_html_e( 'جستجو در فروشگاه', 'navid-commerce' ); ?></label>
	<input id="<?php echo esc_attr( $search_id ); ?>" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'دنبال چی می‌گردی؟', 'navid-commerce' ); ?>" required>
	<?php if ( class_exists( 'WooCommerce' ) ) : ?><input type="hidden" name="post_type" value="product"><?php endif; ?>
	<button type="submit" aria-label="<?php esc_attr_e( 'جستجو', 'navid-commerce' ); ?>"><?php navid_commerce_icon( 'search' ); ?></button>
</form>
