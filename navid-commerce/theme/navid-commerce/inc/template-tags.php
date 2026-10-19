<?php
/** Presentation helpers. @package NavidCommerce */
defined( 'ABSPATH' ) || exit;

function navid_commerce_icon( $name, $class = '' ) {
	$paths = array(
		'bag' => '<path d="M6 8h12l1 12H5L6 8Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/>',
		'search' => '<circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 4.5 4.5"/>',
		'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/>',
		'arrow' => '<path d="M20 12H4m6-6-6 6 6 6"/>',
		'menu' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
		'truck' => '<path d="M2 5h13v12H2zM15 9h4l3 4v4h-7"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="18" r="2"/>',
		'shield' => '<path d="m12 3 8 3v6c0 5-8 9-8 9s-8-4-8-9V6l8-3Z"/><path d="m8 12 3 3 5-6"/>',
		'return' => '<path d="M4 10a8 8 0 1 1 1 8M4 4v6h6"/>',
		'headset' => '<path d="M4 13V11a8 8 0 0 1 16 0v2M4 12h3v8H4zM17 12h3v8h-3z"/>',
		'phone' => '<rect x="6" y="2" width="12" height="20" rx="3"/><path d="M10 18h4"/>',
		'laptop' => '<path d="M5 4h14v12H5zM2 20h20l-3-4H5l-3 4Z"/>',
		'watch' => '<rect x="6" y="6" width="12" height="12" rx="4"/><path d="M9 6V2h6v4M9 18v4h6v-4m-3-8v3l2 1"/>',
		'game' => '<path d="M7 7h10c5 0 6 12 3 13-2 0-4-4-5-4H9c-1 0-3 4-5 4C1 19 2 7 7 7Z"/><path d="M6 10v6m-3-3h6M16 11h.1M19 14h.1"/>',
		'camera' => '<path d="M3 7h4l2-3h6l2 3h4v13H3z"/><circle cx="12" cy="13" r="4"/>',
		'bolt' => '<path d="m14 2-10 12h7l-1 8 10-12h-7l1-8Z"/>',
		'close' => '<path d="m6 6 12 12M6 18 18 6"/>',
		'chevron' => '<path d="m6 9 6 6 6-6"/>',
		'eye' => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',
		'grid' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
		'spark' => '<path d="m12 2 2.7 7.3L22 12l-7.3 2.7L12 22l-2.7-7.3L2 12l7.3-2.7L12 2Z"/>',
		'check' => '<path d="m5 12 4 4L19 6"/>',
		'home' => '<path d="m3 10 9-7 9 7v11h-7v-7h-4v7H3V10Z"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return;
	}
	// SVG geometry comes exclusively from the fixed map above, never user input.
	echo '<svg class="nc-icon ' . esc_attr( $class ) . '" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">';
	echo wp_kses( $paths[ $name ], array( 'path' => array( 'd' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ), 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ) ) );
	echo '</svg>';
}

function navid_commerce_shop_url() {
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
}

/** Reuse a single bounded taxonomy query across the header and home page. */
function navid_commerce_categories() {
	static $categories = null;
	if ( null === $categories ) {
		$categories = taxonomy_exists( 'product_cat' ) ? get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0, 'number' => 8, 'exclude' => array( absint( get_option( 'default_product_cat' ) ) ) ) ) : array();
		if ( is_wp_error( $categories ) ) { $categories = array(); }
	}
	return $categories;
}

function navid_commerce_collection_url( $slug = '', $setting = '' ) {
	$id = $setting ? absint( get_theme_mod( $setting, 0 ) ) : 0;
	$term = $id ? get_term( $id, 'product_cat' ) : ( $slug && taxonomy_exists( 'product_cat' ) ? get_term_by( 'slug', $slug, 'product_cat' ) : false );
	if ( $term && ! is_wp_error( $term ) ) {
		$url = get_term_link( $term );
		if ( ! is_wp_error( $url ) ) { return $url; }
	}
	return navid_commerce_shop_url();
}

function navid_commerce_category_art( $term ) {
	$thumbnail = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );
	if ( $thumbnail ) {
		echo wp_get_attachment_image( $thumbnail, 'woocommerce_thumbnail', false, array( 'loading' => 'lazy', 'alt' => '' ) );
		return;
	}
	$map = array( 'headphones' => 'headphones', 'laptops' => 'keyboard', 'mobiles' => 'phone', 'watches' => 'watch', 'gaming' => 'gamepad', 'cameras' => 'camera' );
	if ( isset( $map[ $term->slug ] ) ) {
		echo '<img src="' . esc_url( get_theme_file_uri( '/assets/images/' . $map[ $term->slug ] . '.svg' ) ) . '" width="120" height="110" loading="lazy" alt="">';
	} else {
		navid_commerce_icon( 'bag' );
	}
}

function navid_commerce_fallback_menu() {
	echo '<ul class="primary-menu">';
	$links = array( __( 'فروشگاه', 'navid-commerce' ) => navid_commerce_shop_url(), __( 'پیشنهادهای ویژه', 'navid-commerce' ) => home_url( '/#offers' ), __( 'مجله نوید', 'navid-commerce' ) => get_permalink( get_option( 'page_for_posts' ) ) );
	foreach ( $links as $label => $url ) {
		if ( $url ) {
			echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
		}
	}
	echo '</ul>';
}

function navid_commerce_product_section( $title, $args = array(), $id = '' ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return;
	}
	$products = wc_get_products( array_merge( array( 'status' => 'publish', 'limit' => 4, 'visibility' => 'catalog', 'orderby' => 'date', 'order' => 'DESC' ), $args ) );
	if ( ! $products ) {
		return;
	}
	?>
	<section class="product-section container woocommerce <?php echo 'offers' === $id ? 'offers-section' : ''; ?>" <?php echo $id ? 'id="' . esc_attr( $id ) . '"' : ''; ?> aria-label="<?php echo esc_attr( $title ); ?>">
		<div class="section-heading"><div><span class="eyebrow"><?php echo 'offers' === $id ? esc_html__( 'فرصتی برای یک انتخاب بهتر', 'navid-commerce' ) : esc_html__( 'تازه‌های فروشگاه', 'navid-commerce' ); ?></span><h2><?php echo esc_html( $title ); ?><span class="heading-dot" aria-hidden="true"></span></h2></div><a class="text-link" href="<?php echo esc_url( navid_commerce_shop_url() ); ?>"><?php esc_html_e( 'مشاهده همه محصولات', 'navid-commerce' ); ?> <?php navid_commerce_icon( 'arrow' ); ?></a></div>
		<div class="<?php echo 'offers' === $id ? 'offer-shell' : 'products-shell'; ?>">
		<?php if ( 'offers' === $id ) : ?><div class="offer-intro"><span class="offer-symbol" aria-hidden="true"><?php navid_commerce_icon( 'spark' ); ?></span><span class="eyebrow">THE GOOD FINDS</span><h3><?php esc_html_e( 'انتخاب خوب،', 'navid-commerce' ); ?><br><?php esc_html_e( 'قیمت بهتر.', 'navid-commerce' ); ?></h3><p><?php esc_html_e( 'محصولات تخفیف‌دار فروشگاه، یک‌جا برای شما.', 'navid-commerce' ); ?></p><a href="<?php echo esc_url( navid_commerce_shop_url() ); ?>"><?php esc_html_e( 'گشتی در فروشگاه', 'navid-commerce' ); ?><?php navid_commerce_icon( 'arrow' ); ?></a></div><?php endif; ?>
		<?php
		wc_set_loop_prop( 'columns', 4 );
		woocommerce_product_loop_start();
		foreach ( $products as $item ) {
			$post_object = get_post( $item->get_id() );
			$GLOBALS['post'] = $post_object; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Standard WooCommerce product loop context.
			setup_postdata( $post_object );
			wc_get_template_part( 'content', 'product' );
		}
		woocommerce_product_loop_end();
		wp_reset_postdata();
		wc_reset_loop();
		?>
		</div>
	</section>
	<?php
}
