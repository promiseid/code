<?php
/** Review fixture for an isolated WordPress Playground, never a live store. */
require '/wordpress/wp-load.php';
wp_set_current_user( 1 );
wp_set_password( 'NavidDemo!2026', 1 );
update_option( 'blogname', 'نویدشاپ' );
update_option( 'blogdescription', 'تکنولوژی خوب، حال خوب. انتخاب‌های تازه برای زندگی امروز.' );
update_option( 'blog_public', 0 );
update_option( 'WPLANG', 'fa_IR' );
update_option( 'timezone_string', 'Asia/Tehran' );
update_option( 'woocommerce_currency', 'IRR' );
update_option( 'woocommerce_price_num_decimals', 0 );
update_option( 'woocommerce_default_country', 'IR:THR' );
update_option( 'woocommerce_store_city', 'تهران' );
update_option( 'woocommerce_store_postcode', '1111111111' );
update_option( 'woocommerce_store_address', 'فروشگاه آزمایشی' );
update_option( 'woocommerce_calc_taxes', 'no' );
update_option( 'woocommerce_enable_guest_checkout', 'yes' );
update_option( 'woocommerce_enable_signup_and_login_from_checkout', 'yes' );
update_option( 'woocommerce_enable_myaccount_registration', 'yes' );
update_option( 'woocommerce_cod_settings', array( 'enabled' => 'yes', 'title' => 'پرداخت آزمایشی در محل', 'description' => 'فقط برای آزمون؛ پول واقعی دریافت نمی‌شود.' ) );
update_option( 'woocommerce_ship_to_countries', 'disabled' );
update_option( 'woocommerce_onboarding_profile', array( 'completed' => true ) );
// Preview setup is complete: do not divert the first admin page to Woo onboarding.
delete_transient( '_wc_activation_redirect' );
update_option( 'woocommerce_task_list_hidden', 'yes' );
update_option( 'woocommerce_coming_soon', 'no' );
update_option( 'woocommerce_allow_tracking', 'no' );
if ( function_exists( 'wc_create_pages' ) ) {
    wc_create_pages();
}
$result = navid_demo_seed( true );
foreach ( array( 'cart' => '[woocommerce_cart]', 'checkout' => '[woocommerce_checkout]', 'myaccount' => '[woocommerce_my_account]' ) as $kind => $content ) {
    $id = wc_get_page_id( $kind );
    if ( $id > 0 ) {
        wp_update_post( array( 'ID' => $id, 'post_content' => $content ) );
    }
}
wp_mkdir_p( WPMU_PLUGIN_DIR );
file_put_contents( WPMU_PLUGIN_DIR . '/navid-playground.php', "<?php\n/** Isolated preview: do not send real emails. */\nadd_filter( 'pre_wp_mail', '__return_true' );\n" );
set_theme_mod( 'navid_home_layout', 'market' );
set_theme_mod( 'navid_accent', '#d92846' );
set_theme_mod( 'navid_announcement', 'یک دنیا انتخاب، همین‌جا در نویدشاپ' );
update_option( 'permalink_structure', '/%postname%/' );
flush_rewrite_rules( false );
wp_set_current_user( 0 );
echo wp_json_encode( array( 'theme' => get_stylesheet(), 'products' => count( wc_get_products( array( 'limit' => -1, 'return' => 'ids' ) ) ), 'demo' => $result ), JSON_UNESCAPED_UNICODE );
