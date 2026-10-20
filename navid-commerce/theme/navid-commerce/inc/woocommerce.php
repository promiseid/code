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
	add_action( 'woocommerce_shop_loop_item_title', 'navid_commerce_card_category', 5 );
	add_action( 'woocommerce_after_shop_loop_item', 'navid_commerce_quickview_button', 7 );
	add_action( 'woocommerce_after_single_product_summary', 'navid_commerce_product_assurance', 5 );
	add_action( 'wp_footer', 'navid_commerce_quickview_dialog', 5 );
	add_action( 'woocommerce_after_cart_table', 'navid_commerce_cart_state' );
	add_action( 'woocommerce_cart_is_empty', 'navid_commerce_cart_state' );
}
add_action( 'after_setup_theme', 'navid_commerce_woo_hooks', 20 );

function navid_commerce_woo_start() {
	echo '<main id="main-content" class="container shop-main"><div class="shop-layout">';
	if ( ! is_product() ) {
		echo '<aside class="shop-filters" aria-label="' . esc_attr__( 'فیلترهای محصولات', 'navid-commerce' ) . '">';
		navid_commerce_catalog_filters();
		if ( is_active_sidebar( 'shop-sidebar' ) ) { dynamic_sidebar( 'shop-sidebar' ); }
		echo '</aside>';
	}
	echo '<div class="shop-content">';
}

function navid_commerce_card_category() {
	global $product;
	if ( ! $product instanceof WC_Product ) { return; }
	$terms = get_the_terms( $product->get_id(), 'product_cat' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		echo '<span class="product-category-label">' . esc_html( $terms[0]->name ) . '</span>';
	}
}

/** All preview data is public, escaped display data; there is no custom endpoint. */
function navid_commerce_quickview_button() {
	global $product;
	if ( ! $product instanceof WC_Product || ! get_theme_mod( 'navid_enable_quickview', true ) ) { return; }
	$data = navid_commerce_public_product( $product );

	echo '<button type="button" class="nc-quickview" aria-haspopup="dialog" aria-controls="nc-product-preview" aria-label="' . esc_attr( sprintf( __( 'پیش‌نمایش سریع: %s', 'navid-commerce' ), $product->get_name() ) ) . '" data-product-preview="' . esc_attr( wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) . '">';
	navid_commerce_icon( 'eye' );
	echo '</button>';
}

function navid_commerce_quickview_dialog() {
	if ( ! get_theme_mod( 'navid_enable_quickview', true ) ) { return; }
	?>
	<dialog class="nc-preview" id="nc-product-preview" aria-labelledby="nc-preview-title">
		<button type="button" class="nc-preview-close" aria-label="<?php esc_attr_e( 'بستن پیش‌نمایش', 'navid-commerce' ); ?>"><?php navid_commerce_icon( 'close' ); ?></button>
		<div class="nc-preview-layout"><div class="nc-preview-visual"><img class="nc-preview-image" width="600" height="600" alt=""></div><div class="nc-preview-copy"><span class="eyebrow"><?php esc_html_e( 'نگاهی نزدیک‌تر به انتخاب شما', 'navid-commerce' ); ?></span><h2 id="nc-preview-title"></h2><span class="nc-preview-stock"></span><p class="nc-preview-description"></p><div class="nc-preview-prices"><del class="nc-preview-regular"></del><strong class="nc-preview-price"></strong></div><a class="button button-dark nc-preview-link"><?php esc_html_e( 'مشخصات و خرید محصول', 'navid-commerce' ); ?><?php navid_commerce_icon( 'arrow' ); ?></a><small><?php esc_html_e( 'انتخاب ویژگی‌ها و افزودن به سبد در صفحه محصول', 'navid-commerce' ); ?></small></div></div>
	</dialog>
	<?php
}

function navid_commerce_product_assurance() {
	echo '<div class="product-assurance">';
	foreach ( array( array( 'shield', __( 'جزئیات شفاف محصول', 'navid-commerce' ) ), array( 'truck', __( 'روش ارسال در تسویه‌حساب', 'navid-commerce' ) ), array( 'headset', __( 'پیگیری از حساب کاربری', 'navid-commerce' ) ) ) as $item ) {
		echo '<span>'; navid_commerce_icon( $item[0] ); echo esc_html( $item[1] ) . '</span>';
	}
	echo '</div>';
}

function navid_commerce_sale_badge( $html, $post, $product ) {
	$discount = 0;
	if ( $product->is_type( 'variable' ) ) {
		$prices = $product->get_variation_prices();
		foreach ( $prices['regular_price'] as $key => $regular ) {
			if ( (float) $regular > 0 && isset( $prices['price'][ $key ] ) ) {
				$discount = max( $discount, (int) round( 100 * ( (float) $regular - (float) $prices['price'][ $key ] ) / (float) $regular ) );
			}
		}
	} elseif ( (float) $product->get_regular_price() > 0 ) {
		$discount = (int) round( 100 * ( (float) $product->get_regular_price() - (float) $product->get_price() ) / (float) $product->get_regular_price() );
	}
	return $discount > 0 ? '<span class="onsale">' . esc_html( sprintf( __( '%s٪ تخفیف', 'navid-commerce' ), number_format_i18n( $discount ) ) ) . '</span>' : $html;
}
add_filter( 'woocommerce_sale_flash', 'navid_commerce_sale_badge', 10, 3 );

function navid_commerce_catalog_categories() {
	if ( ! ( is_shop() || is_product_taxonomy() ) ) { return; }
	$categories = navid_commerce_categories();
	if ( ! $categories ) { return; }
	echo '<nav class="catalog-categories" aria-label="' . esc_attr__( 'مرور دسته‌های فروشگاه', 'navid-commerce' ) . '"><a href="' . esc_url( navid_commerce_shop_url() ) . '" ' . ( is_shop() ? 'aria-current="page"' : '' ) . '>' . esc_html__( 'همه محصولات', 'navid-commerce' ) . '</a>';
	foreach ( $categories as $term ) {
		$url = get_term_link( $term ); if ( is_wp_error( $url ) ) { continue; }
		echo '<a href="' . esc_url( $url ) . '" ' . ( is_product_category( $term->term_id ) ? 'aria-current="page"' : '' ) . '>' . esc_html( $term->name ) . '</a>';
	}
	echo '</nav>';
}
add_action( 'woocommerce_before_shop_loop', 'navid_commerce_catalog_categories', 15 );

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
function navid_commerce_light_home() {
	if ( ! is_front_page() ) { return false; }
	$content = (string) get_post_field( 'post_content', get_queried_object_id() );
	foreach ( array( 'woocommerce_cart', 'woocommerce_checkout', 'woocommerce_my_account', 'product_page' ) as $shortcode ) {
		if ( has_shortcode( $content, $shortcode ) ) {
			return false;
		}
	}
	return false === strpos( $content, '<!-- wp:woocommerce/' );
}

function navid_commerce_woo_styles( $styles ) {
	if ( ! navid_commerce_light_home() ) { return $styles; }
	unset( $styles['woocommerce-general'], $styles['woocommerce-layout'], $styles['woocommerce-smallscreen'] );
	return $styles;
}
add_filter( 'woocommerce_enqueue_styles', 'navid_commerce_woo_styles' );
