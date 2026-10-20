<?php
/** Optional shopping tools. Only public product data; browser-local lists. @package NavidCommerce */
defined( 'ABSPATH' ) || exit;

/** Empty price inputs mean no bound; WooCommerce otherwise reads an empty maximum as zero. */
function navid_commerce_normalize_price_query() {
 if ( is_admin() ) { return; }
 foreach ( array( 'min_price', 'max_price' ) as $key ) {
  if ( isset( $_GET[ $key ] ) && ( ! is_scalar( $_GET[ $key ] ) || '' === trim( (string) wp_unslash( $_GET[ $key ] ) ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Stateless catalog query.
   unset( $_GET[ $key ] );
  }
 }
}
add_action( 'wp_loaded', 'navid_commerce_normalize_price_query', 0 );

function navid_commerce_public_product( $product ) {
 if ( ! $product instanceof WC_Product ) { return array(); }
 $format = static function ( $value ) use ( $product ) {
  return '' === $value ? '' : html_entity_decode( wp_strip_all_tags( wc_price( wc_get_price_to_display( $product, array( 'price' => (float) $value ) ) ) ), ENT_QUOTES, 'UTF-8' );
 };
 $attributes = array();
 foreach ( $product->get_attributes() as $attribute ) {
  if ( ! $attribute->get_visible() ) { continue; }
  $attributes[ wc_attribute_label( $attribute->get_name() ) ] = $product->get_attribute( $attribute->get_name() );
 }
 $terms = get_the_terms( $product->get_id(), 'product_cat' );
 return array(
  'id' => $product->get_id(), 'name' => $product->get_name(), 'url' => esc_url_raw( $product->get_permalink() ),
  'image' => esc_url_raw( wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_single' ) ?: wc_placeholder_img_src() ),
  'price' => ( $product->is_type( 'variable' ) ? __( 'از ', 'navid-commerce' ) : '' ) . $format( $product->is_type( 'variable' ) ? $product->get_variation_price( 'min' ) : $product->get_price() ),
  'regular' => $product->is_on_sale() && ! $product->is_type( 'variable' ) ? $format( $product->get_regular_price() ) : '',
  'description' => post_password_required( $product->get_id() ) ? '' : wp_trim_words( wp_strip_all_tags( $product->get_short_description() ), 45, '…' ),
  'stock' => $product->is_in_stock() ? __( 'موجود و قابل سفارش', 'navid-commerce' ) : __( 'در حال حاضر ناموجود', 'navid-commerce' ),
  'inStock' => $product->is_in_stock(), 'category' => $terms && ! is_wp_error( $terms ) ? implode( '، ', wp_list_pluck( $terms, 'name' ) ) : '',
  'attributes' => $attributes, 'type' => $product->get_type(),
 );
}

function navid_commerce_product_tools() {
 global $product;
 if ( ! $product instanceof WC_Product || ! get_theme_mod( 'navid_shopping_tools', true ) ) { return; }
 echo '<div class="nc-product-tools" data-nc-product="' . esc_attr( wp_json_encode( navid_commerce_public_product( $product ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) . '">';
 foreach ( array( 'wishlist' => array( 'heart', __( 'افزودن به علاقه‌مندی‌ها: %s', 'navid-commerce' ) ), 'compare' => array( 'compare', __( 'افزودن به مقایسه: %s', 'navid-commerce' ) ) ) as $tool => $item ) {
  echo '<button type="button" class="nc-save-product" data-save-tool="' . esc_attr( $tool ) . '" aria-pressed="false" aria-label="' . esc_attr( sprintf( $item[1], $product->get_name() ) ) . '">'; navid_commerce_icon( $item[0] ); echo '</button>';
 }
 echo '</div>';
}
add_action( 'woocommerce_after_shop_loop_item', 'navid_commerce_product_tools', 8 );
add_action( 'woocommerce_single_product_summary', 'navid_commerce_product_tools', 36 );

function navid_commerce_shopping_dialog() {
 if ( ! class_exists( 'WooCommerce' ) || ! get_theme_mod( 'navid_shopping_tools', true ) ) { return; }
 ?>
 <dialog id="nc-shopping-tools" class="nc-tools-dialog" aria-labelledby="nc-tools-title"><div class="nc-tools-heading"><div><span class="eyebrow">YOUR NEXT GOOD FIND</span><h2 id="nc-tools-title"><?php esc_html_e( 'انتخاب‌های شما', 'navid-commerce' ); ?></h2></div><button class="nc-tools-close" type="button" aria-label="<?php esc_attr_e( 'بستن انتخاب‌ها', 'navid-commerce' ); ?>"><?php navid_commerce_icon( 'close' ); ?></button></div><p class="nc-tools-note"><?php esc_html_e( 'فهرست‌ها در همین مرورگر ذخیره می‌شوند. قیمت و موجودی هنگام باز کردن فهرست تازه‌سازی می‌شود.', 'navid-commerce' ); ?></p><div class="nc-tools-content"></div><p class="nc-tools-status" role="status" aria-live="polite"></p></dialog>
 <div class="nc-toast" role="status" aria-live="polite" aria-atomic="true"></div>
 <?php
}
add_action( 'wp_footer', 'navid_commerce_shopping_dialog', 8 );

function navid_commerce_catalog_filters() {
 $base = is_product_taxonomy() ? get_term_link( get_queried_object() ) : navid_commerce_shop_url();
 if ( is_wp_error( $base ) ) { $base = navid_commerce_shop_url(); }
 // Read a strictly limited, scalar GET whitelist. The form makes no state changes.
 $value = static function ( $key ) { return isset( $_GET[ $key ] ) && is_scalar( $_GET[ $key ] ) ? sanitize_text_field( wp_unslash( $_GET[ $key ] ) ) : ''; }; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
 ?>
 <details class="nc-filter-panel" open><summary><?php navid_commerce_icon( 'filter' ); ?><?php esc_html_e( 'فیلتر محصولات', 'navid-commerce' ); ?></summary><form method="get" action="<?php echo esc_url( $base ); ?>" class="nc-filter-form">
 <label for="nc-filter-category"><?php esc_html_e( 'دسته‌بندی', 'navid-commerce' ); ?></label><select name="product_cat" id="nc-filter-category"><option value=""><?php esc_html_e( 'همه دسته‌ها', 'navid-commerce' ); ?></option><?php foreach ( navid_commerce_categories() as $term ) : ?><option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $value( 'product_cat' ), $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option><?php endforeach; ?></select>
 <fieldset><legend><?php echo esc_html( sprintf( __( 'بازه قیمت (%s)', 'navid-commerce' ), html_entity_decode( get_woocommerce_currency_symbol(), ENT_QUOTES, 'UTF-8' ) ) ); ?></legend><label for="nc-min-price"><?php esc_html_e( 'از قیمت', 'navid-commerce' ); ?></label><input id="nc-min-price" type="number" min="0" step="any" inputmode="decimal" name="min_price" value="<?php echo esc_attr( $value( 'min_price' ) ); ?>"><label for="nc-max-price"><?php esc_html_e( 'تا قیمت', 'navid-commerce' ); ?></label><input id="nc-max-price" type="number" min="0" step="any" inputmode="decimal" name="max_price" value="<?php echo esc_attr( $value( 'max_price' ) ); ?>"></fieldset>
 <label class="nc-check"><input type="checkbox" name="nc_stock" value="1" <?php checked( $value( 'nc_stock' ), '1' ); ?>><?php esc_html_e( 'فقط کالاهای موجود', 'navid-commerce' ); ?></label><label class="nc-check"><input type="checkbox" name="nc_sale" value="1" <?php checked( $value( 'nc_sale' ), '1' ); ?>><?php esc_html_e( 'فقط تخفیف‌دارها', 'navid-commerce' ); ?></label>
 <?php foreach ( array( 's', 'orderby' ) as $key ) : if ( $value( $key ) ) : ?><input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value( $key ) ); ?>"><?php endif; endforeach; ?>
 <button class="button button-dark" type="submit"><?php esc_html_e( 'اعمال فیلترها', 'navid-commerce' ); ?></button><a href="<?php echo esc_url( $base ); ?>" class="nc-filter-reset"><?php esc_html_e( 'پاک کردن فیلترها', 'navid-commerce' ); ?></a></form></details>
 <?php
}
function navid_commerce_apply_filters( $query ) {
 if ( is_admin() ) { return; }
 $flag = static function ( $name ) { return isset( $_GET[ $name ] ) && is_string( $_GET[ $name ] ) && '1' === wp_unslash( $_GET[ $name ] ); }; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
 if ( $flag( 'nc_stock' ) ) {
  $meta = $query->get( 'meta_query', array() );
  $query->set( 'meta_query', array( 'relation' => 'AND', $meta, array( 'key' => '_stock_status', 'value' => array( 'instock', 'onbackorder' ), 'compare' => 'IN' ) ) );
 }
 if ( $flag( 'nc_sale' ) ) {
  $ids = array_map( 'absint', wc_get_product_ids_on_sale() ); $existing = $query->get( 'post__in' );
  $query->set( 'post__in', $existing ? array_values( array_intersect( $ids, $existing ) ) ?: array( 0 ) : ( $ids ?: array( 0 ) ) );
 }
}
add_action( 'woocommerce_product_query', 'navid_commerce_apply_filters' );

function navid_commerce_sticky_product() {
 if ( ! function_exists( 'is_product' ) || ! is_product() || ! get_theme_mod( 'navid_sticky_buy', true ) ) { return; }
 global $product;
 if ( ! $product instanceof WC_Product || ! $product->is_purchasable() || ! $product->is_in_stock() || post_password_required() ) { return; }
 ?>
 <aside class="nc-sticky-buy" aria-label="<?php esc_attr_e( 'دسترسی سریع به خرید محصول', 'navid-commerce' ); ?>"><span><?php echo esc_html( $product->get_name() ); ?></span><strong><?php echo wp_kses_post( $product->get_price_html() ); ?></strong><a class="button button-dark" href="#nc-product-purchase"><?php esc_html_e( 'انتخاب و خرید', 'navid-commerce' ); ?><?php navid_commerce_icon( 'arrow' ); ?></a></aside>
 <?php
}
add_action( 'wp_footer', 'navid_commerce_sticky_product', 9 );
add_action( 'woocommerce_before_add_to_cart_form', static function () { echo '<span id="nc-product-purchase" class="nc-purchase-anchor"></span>'; } );
