<?php
/**
 * Plugin Name: Navid Commerce Demo
 * Description: درون‌ریزی اختیاری محصولات و برگه‌های نمایشی نویدشاپ. پس از درون‌ریزی قابل غیرفعال‌سازی است.
 * Version: 0.2.0
 * Requires at least: 6.6
 * Requires PHP: 8.3
 * Author: Navid Bazrkar
 * License: GPL-2.0-or-later
 * Text Domain: navid-commerce-demo
 */
defined( 'ABSPATH' ) || exit;

function navid_demo_admin_menu() {
	add_theme_page( __( 'دموی نویدشاپ', 'navid-commerce-demo' ), __( 'دموی نویدشاپ', 'navid-commerce-demo' ), 'manage_options', 'navid-demo', 'navid_demo_admin_page' );
}
add_action( 'admin_menu', 'navid_demo_admin_menu' );

function navid_demo_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	?><div class="wrap" dir="rtl"><h1><?php esc_html_e( 'درون‌ریزی دموی نویدشاپ', 'navid-commerce-demo' ); ?></h1><p><?php esc_html_e( 'این ابزار محصولات فرضی، تصاویر طراحی‌شده و چند برگه نمونه ایجاد می‌کند. ابتدا روی نصب آزمایشی و با نسخه پشتیبان استفاده کنید. اجرای مجدد، محصولات قبلی را تکرار یا ویرایش نمی‌کند.', 'navid-commerce-demo' ); ?></p><p><?php esc_html_e( 'تنظیمات پرداخت، مالیات، واحد پول و ارسال تغییر نمی‌کند. قیمت‌های نمونه بر اساس ریال نوشته شده‌اند؛ پیش از فروش واقعی آن‌ها را اصلاح کنید.', 'navid-commerce-demo' ); ?></p>
	<?php if ( ! class_exists( 'WooCommerce' ) ) : ?><p><strong><?php esc_html_e( 'ابتدا ووکامرس را فعال کنید.', 'navid-commerce-demo' ); ?></strong></p><?php elseif ( ! current_user_can( 'manage_woocommerce' ) ) : ?><p><?php esc_html_e( 'دسترسی مدیریت فروشگاه لازم است.', 'navid-commerce-demo' ); ?></p><?php else : ?>
	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post"><input type="hidden" name="action" value="navid_demo_import"><?php wp_nonce_field( 'navid_demo_import', 'navid_demo_nonce' ); ?><p><label><input type="checkbox" name="configure_home" value="1"> <?php esc_html_e( 'صفحه نخست و فهرست‌های نمونه را تنظیم کن؛ فقط اگر جایگاه مربوطه خالی باشد.', 'navid-commerce-demo' ); ?></label></p><?php submit_button( __( 'ایجاد دموی نمونه', 'navid-commerce-demo' ) ); ?></form>
	<?php endif; ?>
	<?php $last_result = get_transient( 'navid_demo_result_' . get_current_user_id() ); if ( is_array( $last_result ) ) : ?><h2><?php esc_html_e( 'آخرین نتیجه', 'navid-commerce-demo' ); ?></h2><ul><?php foreach ( $last_result as $line ) : ?><li><?php echo esc_html( $line ); ?></li><?php endforeach; ?></ul><?php endif; ?>
	<p><?php esc_html_e( 'پس از درون‌ریزی می‌توانید این افزونه را غیرفعال و حذف کنید؛ تصاویر و محصولات در وردپرس می‌مانند.', 'navid-commerce-demo' ); ?></p></div><?php
}

function navid_demo_handle_import() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( esc_html__( 'دسترسی کافی نیست.', 'navid-commerce-demo' ), '', array( 'response' => 403 ) );
	}
	check_admin_referer( 'navid_demo_import', 'navid_demo_nonce' );
	$configure = isset( $_POST['configure_home'] ) && '1' === sanitize_text_field( wp_unslash( $_POST['configure_home'] ) );
	$result = navid_demo_seed( $configure );
	set_transient( 'navid_demo_result_' . get_current_user_id(), $result, 300 );
	wp_safe_redirect( admin_url( 'themes.php?page=navid-demo' ) );
	exit;
}
add_action( 'admin_post_navid_demo_import', 'navid_demo_handle_import' );

function navid_demo_seed( $configure = false ) {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'manage_woocommerce' ) || ! class_exists( 'WooCommerce' ) ) {
		return array( __( 'دسترسی یا ووکامرس در دسترس نیست.', 'navid-commerce-demo' ) );
	}
	$lock_key = 'navid_demo_import_lock';
	$old_lock = absint( get_option( $lock_key, 0 ) );
	if ( $old_lock && time() - $old_lock < 300 ) {
		return array( __( 'درون‌ریزی دیگری در حال اجراست.', 'navid-commerce-demo' ) );
	}
	if ( $old_lock ) { delete_option( $lock_key ); }
	if ( ! add_option( $lock_key, time(), '', false ) ) {
		return array( __( 'درون‌ریزی دیگری در حال اجراست.', 'navid-commerce-demo' ) );
	}
	try {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$categories = array( 'headphones' => 'هدفون و صدا', 'laptops' => 'کامپیوتر و لپ‌تاپ', 'mobiles' => 'موبایل و لوازم', 'watches' => 'ساعت هوشمند', 'gaming' => 'بازی و سرگرمی', 'cameras' => 'دوربین و تصویر' );
		$term_ids = array();
		foreach ( $categories as $slug => $name ) {
			$existing = get_term_by( 'slug', $slug, 'product_cat' );
			$term = $existing ? array( 'term_id' => $existing->term_id ) : wp_insert_term( $name, 'product_cat', array( 'slug' => $slug ) );
			if ( ! is_wp_error( $term ) ) { $term_ids[ $slug ] = absint( $term['term_id'] ); }
		}
		$data = array(
			array( 'wave', 'هدفون بی‌سیم مدل Wave', 'headphones', 'headphones', 59000000, 49000000, 'simple' ),
			array( 'flow', 'کیبورد مکانیکی مدل Flow', 'laptops', 'keyboard', 39000000, 34000000, 'simple' ),
			array( 'orbit', 'ساعت هوشمند مدل Orbit', 'watches', 'watch', 79000000, 65000000, 'variable' ),
			array( 'echo', 'اسپیکر همراه مدل Echo', 'headphones', 'speaker', 39000000, 29000000, 'simple' ),
			array( 'air', 'گوشی هوشمند مدل Air', 'mobiles', 'phone', 359000000, 0, 'simple' ),
			array( 'play', 'دسته بازی بی‌سیم مدل Play', 'gaming', 'gamepad', 49000000, 0, 'simple' ),
			array( 'focus', 'دوربین دیجیتال مدل Focus', 'cameras', 'camera', 189000000, 0, 'out' ),
			array( 'arc', 'ماوس بی‌سیم مدل Arc', 'laptops', 'mouse', 19000000, 15000000, 'simple' ),
		);
		$created = 0; $skipped = 0; $failed = 0;
		foreach ( $data as $row ) {
			$sku = 'NAVID-DEMO-' . strtoupper( $row[0] );
			if ( wc_get_product_id_by_sku( $sku ) ) { ++$skipped; continue; }
			$product = 'variable' === $row[6] ? new WC_Product_Variable() : new WC_Product_Simple();
			$product->set_name( $row[1] );
			$product->set_slug( 'navid-demo-' . $row[0] );
			$product->set_sku( $sku );
			$product->set_status( 'publish' );
			$product->set_catalog_visibility( 'visible' );
			$product->set_short_description( 'محصول فرضی برای نمایش قالب نویدشاپ؛ نام، مشخصات، قیمت و تصویر را پیش از فروش واقعی جایگزین کنید.' );
			$product->set_description( '<h2>همراهی برای هر روز</h2><p>این محصول برای آزمایش ظاهر فروشگاه و مسیر خرید ساخته شده است. تصاویر، طراحی مفهومی‌اند و به برند تجاری خاصی تعلق ندارند.</p><p>مشخصات واقعی، شرایط گارانتی و جزئیات ارسال را اینجا بنویسید.</p>' );
			$product->set_category_ids( isset( $term_ids[ $row[2] ] ) ? array( $term_ids[ $row[2] ] ) : array() );
			$product->set_weight( '0.5' );
			$product->set_stock_status( 'out' === $row[6] ? 'outofstock' : 'instock' );
			if ( 'variable' !== $row[6] ) {
				$product->set_regular_price( (string) $row[4] );
				if ( $row[5] ) { $product->set_sale_price( (string) $row[5] ); }
			} else {
				$attr = new WC_Product_Attribute();
				$attr->set_name( 'رنگ' ); $attr->set_options( array( 'سبز', 'مشکی' ) ); $attr->set_visible( true ); $attr->set_variation( true );
				$product->set_attributes( array( $attr ) );
			}
			$product->update_meta_data( '_navid_demo_seed', '0.2.0' );
			$id = $product->save();
			if ( ! $id ) { ++$failed; continue; }
			if ( 'variable' === $row[6] ) {
				foreach ( array( 'سبز', 'مشکی' ) as $color ) {
					$variation = new WC_Product_Variation(); $variation->set_parent_id( $id ); $variation->set_attributes( array( sanitize_title( 'رنگ' ) => $color ) ); $variation->set_regular_price( (string) $row[4] ); $variation->set_sale_price( (string) $row[5] ); $variation->set_stock_status( 'instock' ); $variation->save();
				}
				WC_Product_Variable::sync( $id );
			}
			$source = plugin_dir_path( __FILE__ ) . 'assets/' . $row[3] . '.webp';
			if ( is_readable( $source ) ) {
				$upload = wp_upload_bits( 'navid-demo-' . $row[3] . '.webp', null, file_get_contents( $source ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Fixed, bundled local image only.
				if ( ! $upload['error'] ) {
					$attachment = wp_insert_attachment( array( 'post_mime_type' => 'image/webp', 'post_title' => $row[1], 'post_status' => 'inherit' ), $upload['file'], $id, true );
					if ( ! is_wp_error( $attachment ) ) {
						wp_update_attachment_metadata( $attachment, wp_generate_attachment_metadata( $attachment, $upload['file'] ) ); update_post_meta( $attachment, '_wp_attachment_image_alt', $row[1] ); $product->set_image_id( $attachment ); $product->save();
					}
				}
			}
			++$created;
		}
		$pages = array( 'navid-home' => array( 'خانه', '' ), 'navid-journal' => array( 'مجله نوید', '' ), 'navid-about' => array( 'درباره ما', '<!-- wp:paragraph --><p>داستان فروشگاه و روش ارتباط با خود را اینجا بنویسید.</p><!-- /wp:paragraph -->' ), 'navid-delivery' => array( 'راهنمای ارسال و بازگشت', '<!-- wp:paragraph --><p>این متن نمونه است. روش‌های ارسال، هزینه‌ها، محدوده خدمت‌رسانی و سیاست بازگشت واقعی فروشگاه را پیش از انتشار درج کنید.</p><!-- /wp:paragraph -->' ) );
		// Original sample articles; existing posts are never changed on repeated imports.
		$guides = array(
			array( 'navid-guide-sound', 'هدفون مناسب روزهای شما کدام است؟', 'WAVE', 'ظاهر زیبا شروع ماجراست؛ راحتی و سازگاری را هم بررسی کنید.', '<p>برای انتخاب هدفون، ابتدا به شیوه استفاده خود فکر کنید: تماس، کار متمرکز یا شنیدن موسیقی در مسیر. راحتی روی گوش و تناسب اندازه، در استفاده طولانی به اندازه ظاهر اهمیت دارد.</p><h2>سه نکته پیش از انتخاب</h2><p>نوع اتصال و سازگاری با دستگاهتان را بررسی کنید. شرایط اعلام‌شده برای عمر باتری را بخوانید و به روش شارژ توجه کنید. در پایان، مشخصات و شرایط خدمات فروشنده را کنار هم بگذارید و گزینه مناسب خود را انتخاب کنید.</p>' ),
			array( 'navid-guide-watch', 'قبل از خرید ساعت هوشمند چه چیزهایی را بررسی کنیم؟', 'ORBIT', 'همراهی روزانه که باید با گوشی و نیازهای شما هماهنگ باشد.', '<p>ساعت هوشمند قرار است همراه روزمره شما باشد. پیش از خرید، سازگاری آن با سیستم‌عامل گوشی و امکاناتی را که واقعاً استفاده می‌کنید بررسی کنید.</p><h2>جزئیات کوچک، تفاوت‌های مهم</h2><p>اندازه و وزن ساعت، خوانایی نمایشگر، نوع بند و روش شارژ بر تجربه استفاده اثر می‌گذارند. مشخصات مقاومت در برابر آب و محدودیت‌های اعلام‌شده سازنده را دقیق بخوانید. ظاهر دلخواهتان را در کنار این جزئیات انتخاب کنید.</p>' ),
			array( 'navid-guide-workspace', 'میز کاری که با شما هماهنگ است', 'FLOW', 'چند جزئیات ساده برای انتخاب لوازم فضای کار.', '<p>یک فضای کار مناسب با شناخت کارهای روزانه شکل می‌گیرد. پیش از خرید لوازم، فضای میز، دستگاه‌های مورد استفاده و نوع اتصال آن‌ها را در نظر بگیرید.</p><h2>جای هر چیز را مشخص کنید</h2><p>برای صفحه‌کلید، اندازه و چیدمان کلیدها را بررسی کنید. برای ماوس، به تناسب اندازه و شیوه استفاده توجه کنید. تعداد درگاه‌ها و مدیریت کابل‌ها را هم در انتخاب‌هایتان لحاظ کنید تا ابزارهای تازه با فضای موجود هماهنگ باشند.</p>' ),
		);
		foreach ( $guides as $guide ) {
			if ( get_page_by_path( $guide[0], OBJECT, 'post' ) ) { continue; }
			$guide_id = wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_name' => $guide[0], 'post_title' => $guide[1], 'post_excerpt' => $guide[3], 'post_content' => $guide[4] ), true );
			if ( is_wp_error( $guide_id ) || ! $guide_id ) { continue; }
			update_post_meta( $guide_id, '_navid_demo_seed', '0.2.0' );
			$guide_product = wc_get_product( wc_get_product_id_by_sku( 'NAVID-DEMO-' . $guide[2] ) );
			if ( $guide_product && $guide_product->get_image_id() ) { set_post_thumbnail( $guide_id, $guide_product->get_image_id() ); }
		}
		$page_ids = array();
		foreach ( $pages as $slug => $values ) {
			$existing_page = get_page_by_path( $slug );
			$page_id = $existing_page ? $existing_page->ID : wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => $slug, 'post_title' => $values[0], 'post_content' => $values[1] ), true );
			if ( ! is_wp_error( $page_id ) ) { $page_ids[ $slug ] = absint( $page_id ); }
		}
		if ( $configure ) {
			if ( ! get_option( 'page_on_front' ) && isset( $page_ids['navid-home'] ) ) { update_option( 'show_on_front', 'page' ); update_option( 'page_on_front', $page_ids['navid-home'] ); }
			if ( ! get_option( 'page_for_posts' ) && isset( $page_ids['navid-journal'] ) ) { update_option( 'page_for_posts', $page_ids['navid-journal'] ); }
			$locations = get_nav_menu_locations();
			foreach ( array( 'primary' => 'فهرست اصلی نوید', 'footer' => 'راهنمای نوید' ) as $location => $label ) {
				if ( ! empty( $locations[ $location ] ) ) { continue; }
				$menu = wp_get_nav_menu_object( $label ); $menu_id = $menu ? $menu->term_id : wp_create_nav_menu( $label );
				if ( is_wp_error( $menu_id ) ) { continue; }
				if ( ! wp_get_nav_menu_items( $menu_id ) ) {
					wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'فروشگاه', 'menu-item-url' => wc_get_page_permalink( 'shop' ), 'menu-item-type' => 'custom', 'menu-item-status' => 'publish' ) );
					foreach ( array( 'navid-about', 'navid-delivery', 'navid-journal' ) as $slug ) {
						if ( isset( $page_ids[ $slug ] ) ) { wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-object-id' => $page_ids[ $slug ], 'menu-item-object' => 'page', 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) ); }
					}
				}
				$locations[ $location ] = $menu_id;
			}
			set_theme_mod( 'nav_menu_locations', $locations );
		}
		return array( sprintf( 'محصول ایجادشده: %d، محصول موجود و دست‌نخورده: %d، خطا: %d.', $created, $skipped, $failed ), 'برگه‌های نمونه آماده‌اند. تنظیمات واقعی فروشگاه را در ووکامرس بررسی کنید.' );
	} finally {
		delete_option( $lock_key );
	}
}
