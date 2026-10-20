<?php
/** Original marketplace layout and configurable campaigns. */
defined( 'ABSPATH' ) || exit;
add_filter( 'body_class', function( $classes ) { if ( 'studio' !== get_theme_mod( 'navid_home_layout', 'market' ) ) { $classes[] = 'nc-market'; } return $classes; } );
function navid_commerce_market_slides() {
	$slides = array(
		array( 'title' => 'خرید خوب، حال خوب.', 'text' => 'از اولین جست‌وجو تا انتخاب آخر، کنار شماییم.', 'url' => navid_commerce_collection_url( 'headphones' ), 'image' => 0, 'art' => 'hero-studio-1000.webp', 'tag' => 'انتخاب امروز، همراه هر روز', 'theme' => 'mint' ),
		array( 'title' => 'یک قدم جلوتر از روزمرگی', 'text' => 'همراه‌های هوشمند برای ریتم زندگی شما.', 'url' => navid_commerce_collection_url( 'watches', 'navid_featured_category' ), 'image' => 0, 'art' => 'watch.svg', 'tag' => 'وقت یک انتخاب تازه‌ست', 'theme' => 'violet' ),
		array( 'title' => 'برای لحظه‌های بازی', 'text' => 'جزئیات بیشتر، تجربه‌ای که دوستش دارید.', 'url' => navid_commerce_collection_url( 'gaming' ), 'image' => 0, 'art' => 'gamepad.svg', 'tag' => 'دنیای سرگرمی شما', 'theme' => 'peach' ),
	);
	foreach ( $slides as $i => &$slide ) { foreach ( array( 'title', 'text', 'url', 'image' ) as $key ) { $slide[ $key ] = get_theme_mod( 'navid_slide_' . $i . '_' . $key, $slide[ $key ] ); } $slide['url'] = $slide['url'] ?: navid_commerce_shop_url(); } unset( $slide ); return $slides;
}
function navid_commerce_market_category_panels() {
	if ( ! function_exists( 'wc_get_products' ) ) { return; }
	echo '<section class="nc-discovery container" aria-label="کشف دسته‌ها">';
	foreach ( array_slice( navid_commerce_categories(), 0, 4 ) as $term ) {
		$url = get_term_link( $term ); if ( is_wp_error( $url ) ) { continue; }
		$products = wc_get_products( array( 'status' => 'publish', 'visibility' => 'catalog', 'category' => array( $term->slug ), 'limit' => 4, 'orderby' => 'date' ) ); if ( ! $products ) { continue; }
		echo '<article><h2>' . esc_html( $term->name ) . '</h2><p>بر اساس انتخاب شما</p><div class="nc-discovery-images">';
		foreach ( $products as $item ) { echo '<a href="' . esc_url( $item->get_permalink() ) . '" aria-label="' . esc_attr( $item->get_name() ) . '">' . $item->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy', 'alt' => '' ) ) . '</a>'; }
		echo '</div><a class="text-link" href="' . esc_url( $url ) . '">مشاهده همه '; navid_commerce_icon( 'arrow' ); echo '</a></article>';
	} echo '</section>';
}
