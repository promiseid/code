<?php
/** Native administration, fixed schema, nonce and action-level permissions. */
defined( 'ABSPATH' ) || exit;
function navid_commerce_admin_tabs() { return array( 'general' => 'هویت و ظاهر', 'home' => 'صفحه نخست و بنرها', 'store' => 'ابزارهای فروشگاه', 'login' => 'ورود پیشخوان', 'status' => 'وضعیت و راهنما' ); }
function navid_commerce_admin_fields( $tab ) {
	$fields = array(
		'general' => array(
			'custom_logo' => array( 'لوگوی فروشگاه', 'media', 0 ),
			'navid_accent' => array( 'رنگ برند', 'color', '#d92846' ),
			'navid_home_layout' => array( 'چیدمان فروشگاه', 'select', 'market', array( 'market' => 'بازارگاهی — جست‌وجو و پیشنهادها', 'studio' => 'استودیو — ویترین سه‌بعدی' ) ),
			'navid_announcement' => array( 'متن نوار بالای سایت', 'text', 'یک دنیا انتخاب، همین‌جا در نویدشاپ' ),
			'navid_support' => array( 'متن همراهی مشتری', 'text', 'همراه شما، از انتخاب تا خرید' ),
		),
		'home' => array(
			'navid_slider_auto' => array( 'پخش خودکار اسلایدر با امکان توقف', 'checkbox', false ),
			'navid_show_benefits' => array( 'راهنمای خدمات', 'checkbox', true ),
			'navid_show_categories' => array( 'دسته‌های تصویری', 'checkbox', true ),
			'navid_show_collections' => array( 'بنرهای کالکشن', 'checkbox', true ),
			'navid_show_journal' => array( 'مجله فروشگاه', 'checkbox', true ),
			'navid_featured_title' => array( 'عنوان بنر کالکشن', 'text', 'همراه کوچک، تجربه بزرگ.' ),
			'navid_featured_text' => array( 'توضیح بنر کالکشن', 'text', 'یک قدم هوشمندتر در ریتم زندگی.' ),
			'navid_featured_image' => array( 'تصویر بنر کالکشن', 'media', 0 ),
			'navid_featured_category' => array( 'دسته مقصد کالکشن', 'category', 0 ),
			'navid_hero_title' => array( 'عنوان ویترین استودیو', 'text', 'فراتر از یک انتخاب' ),
			'navid_hero_text' => array( 'توضیح ویترین استودیو', 'textarea', 'صدا را احساس کن. جزئیات را کشف کن.' ),
			'navid_hero_button' => array( 'دکمه ویترین استودیو', 'text', 'وارد دنیای نوید شو' ),
			'navid_hero_label' => array( 'برچسب ویترین استودیو', 'text', 'THE NEXT EVERYDAY / NAVID' ),
			'navid_hero_image' => array( 'تصویر جایگزین مدل استودیو', 'media', 0 ),
		),
		'store' => array(
			'navid_shopping_tools' => array( 'علاقه‌مندی، مقایسه، بازدیدهای اخیر و جست‌وجوی زنده', 'checkbox', true ),
			'navid_enable_quickview' => array( 'پیش‌نمایش سریع محصول', 'checkbox', true ),
			'navid_sticky_buy' => array( 'نوار چسبان خرید محصول', 'checkbox', true ),
			'navid_mobile_dock' => array( 'دسترسی‌های پایین موبایل', 'checkbox', true ),
			'navid_3d_scene' => array( 'مدل سه‌بعدی در چیدمان استودیو', 'checkbox', true ),
			'navid_light_home_scripts' => array( 'بارگذاری سبک صفحه نخست', 'checkbox', true ),
		),
		'login' => array(
			'branding' => array( 'شخصی‌سازی صفحه ورود', 'checkbox', true ), 'title' => array( 'عنوان صفحه ورود', 'text', '' ),
			'message' => array( 'متن راهنما', 'textarea', '' ), 'color' => array( 'رنگ ورود', 'color', '#d92846' ),
			'logo' => array( 'لوگوی ورود', 'media', 0 ), 'background' => array( 'پس‌زمینه ورود', 'media', 0 ),
			'enabled' => array( 'مسیر سفارشی ورود و بستن فرم پیش‌فرض برای مهمان', 'checkbox', false ),
			'slug' => array( 'نام مسیر ورود', 'text', 'store-entrance' ),
		),
	);
	if ( 'home' === $tab ) { foreach ( navid_commerce_market_slides() as $i => $slide ) { foreach ( array( 'title' => array( 'عنوان', 'text' ), 'text' => array( 'توضیح', 'text' ), 'url' => array( 'لینک مقصد', 'url' ), 'image' => array( 'تصویر', 'media' ) ) as $key => $info ) { $fields['home'][ 'navid_slide_' . $i . '_' . $key ] = array( 'اسلاید ' . ( $i + 1 ) . ' — ' . $info[0], $info[1], $slide[ $key ] ); } } }
	return $fields[ $tab ] ?? array();
}
add_action( 'admin_menu', function() {
	add_menu_page( 'مدیریت نویدشاپ', 'مدیریت نویدشاپ', 'edit_theme_options', 'navid-commerce', 'navid_commerce_admin_page', 'dashicons-store', 58 );
	add_theme_page( 'مدیریت نویدشاپ', 'مدیریت نویدشاپ', 'edit_theme_options', 'navid-commerce', 'navid_commerce_admin_page' );
} );
add_action( 'admin_bar_menu', function( $bar ) { if ( current_user_can( 'edit_theme_options' ) ) { $bar->add_node( array( 'id' => 'navid-commerce-settings', 'title' => 'مدیریت نویدشاپ', 'href' => admin_url( 'admin.php?page=navid-commerce' ) ) ); } }, 80 );
add_action( 'admin_enqueue_scripts', function( $hook ) {
	if ( ! str_contains( $hook, 'navid-commerce' ) ) { return; }
	wp_enqueue_media();
	wp_enqueue_style( 'navid-commerce-fonts', get_theme_file_uri( '/assets/css/fonts.css' ), array(), NAVID_COMMERCE_VERSION );
	wp_enqueue_style( 'navid-commerce-admin', get_theme_file_uri( '/assets/css/admin.css' ), array( 'navid-commerce-fonts' ), NAVID_COMMERCE_VERSION );
	wp_enqueue_script( 'navid-commerce-admin', get_theme_file_uri( '/assets/js/admin.js' ), array( 'media-editor' ), NAVID_COMMERCE_VERSION, true );
} );
function navid_commerce_admin_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) { return; }
	$tabs = navid_commerce_admin_tabs(); $tab = isset( $_GET['tab'] ) && is_string( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general';
	if ( ! isset( $tabs[ $tab ] ) ) { $tab = 'general'; }
	?>
	<div class="wrap nc-admin" dir="rtl"><header class="nc-admin-header"><div><span>NAVID COMMERCE / <?php echo esc_html( NAVID_COMMERCE_VERSION ); ?></span><h1>مدیریت نویدشاپ</h1><p>فروشگاه شما، با تنظیمات دلخواه شما.</p></div><a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener">مشاهده فروشگاه ↗</a></header>
	<?php if ( isset( $_GET['saved'] ) ) : ?><div class="notice notice-success"><p>تنظیمات ذخیره شد.</p></div><?php endif; ?>
	<?php if ( isset( $_GET['nc-error'] ) ) : ?><div class="notice notice-error"><p>ذخیره نشد. مسیر ورود باید ۴ تا ۴۸ نویسه انگلیسی کوچک، عدد یا خط تیره باشد و با نام‌های رزروشده یا محتوای موجود تداخل نداشته باشد. تغییر مسیر در شبکه چندسایتی پشتیبانی نمی‌شود.</p></div><?php endif; ?>
	<nav class="nc-admin-tabs" aria-label="بخش‌های تنظیمات"><?php foreach ( $tabs as $key => $label ) : ?><a href="<?php echo esc_url( admin_url( 'admin.php?page=navid-commerce&tab=' . $key ) ); ?>" <?php echo $tab === $key ? 'aria-current="page"' : ''; ?>><?php echo esc_html( $label ); ?></a><?php endforeach; ?></nav>
	<div class="nc-admin-grid"><section class="nc-admin-card"><h2><?php echo esc_html( $tabs[ $tab ] ); ?></h2>
	<?php if ( 'status' === $tab ) : navid_commerce_status_panel(); elseif ( 'login' === $tab && ( ! function_exists( 'navid_login_options' ) || ! current_user_can( 'manage_options' ) ) ) : ?><p>مدیریت ورود نیازمند افزونه همراه Navid Commerce Core و دسترسی مدیریت تنظیمات است. فایل افزونه همراه بسته قالب است؛ آن را از «افزونه‌ها ← افزودن افزونه» نصب و فعال کنید.</p>
	<?php else : ?>
	<?php if ( 'login' === $tab ) : ?><div class="nc-admin-note"><strong>آدرس فعلی ورود:</strong><p><a class="nc-login-url" dir="ltr" href="<?php echo esc_url( wp_login_url() ); ?>" target="_blank" rel="noopener"><?php echo esc_html( wp_login_url() ); ?></a></p><p>نام کاربری و رمز تغییر نمی‌کند. نشانی جدید را نگه دارید و در پنجره خصوصی امتحان کنید. تغییر مسیر جایگزین رمز قوی یا احراز هویت دومرحله‌ای نیست.</p><p>بازیابی: «تنظیمات ← ورود نوید»؛ یا غیرفعال‌کردن افزونه همراه از فایل‌ها؛ یا افزودن این خط پیش از بارگذاری وردپرس در wp-config.php:</p><code dir="ltr">define( 'NAVID_COMMERCE_DISABLE_LOGIN_ROUTE', true );</code></div><?php endif; ?>
	<form class="nc-settings-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="navid_commerce_save"><input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>"><?php wp_nonce_field( 'navid_commerce_save_' . $tab ); ?>
	<?php $login = 'login' === $tab ? navid_login_options() : array(); foreach ( navid_commerce_admin_fields( $tab ) as $key => $field ) : $value = 'login' === $tab ? $login[ $key ] : get_theme_mod( $key, $field[2] ); $name = 'settings[' . $key . ']'; ?>
	<div class="nc-admin-field"><label for="nc-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field[0] ); ?></label>
	<?php if ( 'checkbox' === $field[1] ) : ?><label class="nc-admin-check"><input id="nc-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $name ); ?>" type="checkbox" value="1" <?php checked( (bool) $value ); ?>>فعال</label>
	<?php elseif ( 'textarea' === $field[1] ) : ?><textarea id="nc-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $name ); ?>" rows="3"><?php echo esc_textarea( $value ); ?></textarea>
	<?php elseif ( 'media' === $field[1] ) : ?><div class="nc-media-field"><input id="nc-<?php echo esc_attr( $key ); ?>" type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( absint( $value ) ); ?>"><div class="nc-media-preview"><?php if ( $value ) { echo wp_get_attachment_image( absint( $value ), 'thumbnail' ); } ?></div><button class="button nc-media-select" type="button">انتخاب از رسانه‌ها</button> <button class="button-link nc-media-clear" type="button">حذف تصویر</button></div>
	<?php elseif ( in_array( $field[1], array( 'select', 'category' ), true ) ) : $choices = $field[3] ?? array( 0 => 'انتخاب خودکار' ); if ( 'category' === $field[1] && taxonomy_exists( 'product_cat' ) ) { $terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'number' => 100 ) ); if ( ! is_wp_error( $terms ) ) { foreach ( $terms as $term ) { $choices[ $term->term_id ] = $term->name; } } } ?><select id="nc-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $name ); ?>"><?php foreach ( $choices as $val => $label ) : ?><option value="<?php echo esc_attr( $val ); ?>" <?php selected( $value, $val ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select>
	<?php else : ?><input id="nc-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $name ); ?>" type="<?php echo esc_attr( $field[1] ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php echo in_array( $field[1], array( 'url', 'color' ), true ) || 'slug' === $key ? 'dir="ltr"' : ''; ?>><?php endif; ?></div>
	<?php endforeach; ?><div class="nc-admin-save"><?php submit_button( 'ذخیره تنظیمات', 'primary', 'submit', false ); ?></div></form><?php endif; ?>
	</section><aside class="nc-admin-card nc-admin-aside"><h2>دسترسی‌های کاربردی</h2><?php foreach ( array( 'customize.php' => 'سفارشی‌سازی زنده', 'nav-menus.php' => 'فهرست‌ها و زیرمنوها', 'edit-tags.php?taxonomy=product_cat&post_type=product' => 'دسته‌ها و تصاویر دسته', 'options-general.php' => 'عنوان، زبان و نمادک سایت', 'admin.php?page=wc-settings' => 'حمل‌ونقل، پرداخت و مالیات', 'profile.php' => 'تغییر رمز حساب خودم', 'site-health.php' => 'سلامت سایت' ) as $path => $label ) : ?><a href="<?php echo esc_url( admin_url( $path ) ); ?>"><?php echo esc_html( $label ); ?></a><?php endforeach; ?><p>برای زیرمنو از «فهرست‌ها» استفاده کنید. داده محصولات و سفارش‌ها از ووکامرس خوانده می‌شود.</p></aside></div></div>
	<?php
}
function navid_commerce_status_panel() {
	$checks = array( 'پوسته' => NAVID_COMMERCE_VERSION, 'وردپرس' => get_bloginfo( 'version' ), 'PHP' => PHP_VERSION, 'ووکامرس' => defined( 'WC_VERSION' ) ? WC_VERSION : 'غیرفعال', 'افزونه همراه ورود' => function_exists( 'navid_login_options' ) ? 'فعال' : 'غیرفعال', 'HTTPS' => is_ssl() ? 'فعال' : 'غیرفعال در این محیط', 'مسیر سفارشی ورود' => function_exists( 'navid_login_route_active' ) && navid_login_route_active() ? 'فعال' : 'غیرفعال' );
	echo '<table class="widefat striped"><tbody>'; foreach ( $checks as $label => $value ) { echo '<tr><th>' . esc_html( $label ) . '</th><td>' . esc_html( $value ) . '</td></tr>'; } echo '</tbody></table><p>این صفحه گواهی امنیت نیست. قیمت و سفارش از ووکامرس است؛ علاقه‌مندی و مقایسه در همین مرورگر ذخیره می‌شود. درگاه، پیامک، چندفروشندگی و حمل اختصاصی به افزونه یا سرویس جدا نیاز دارند.</p>';
}
add_action( 'admin_post_navid_commerce_save', function() {
	if ( ! current_user_can( 'edit_theme_options' ) ) { wp_die( 'Forbidden', '', array( 'response' => 403 ) ); }
	$tab = isset( $_POST['tab'] ) && is_string( $_POST['tab'] ) ? sanitize_key( wp_unslash( $_POST['tab'] ) ) : '';
	$fields = navid_commerce_admin_fields( $tab );
	if ( ! $fields || ( 'login' === $tab && ( ! current_user_can( 'manage_options' ) || ! function_exists( 'navid_login_validate' ) ) ) ) { wp_die( 'Forbidden', '', array( 'response' => 403 ) ); }
	check_admin_referer( 'navid_commerce_save_' . $tab );
	$data = isset( $_POST['settings'] ) && is_array( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : array(); $url = admin_url( 'admin.php?page=navid-commerce&tab=' . $tab );
	if ( 'login' === $tab ) { $value = navid_login_validate( $data ); if ( is_wp_error( $value ) ) { wp_safe_redirect( add_query_arg( 'nc-error', 'login', $url ) ); exit; } update_option( 'navid_commerce_login', $value, false ); }
	else { foreach ( $fields as $key => $field ) { $raw = $data[ $key ] ?? ''; $value = is_scalar( $raw ) ? (string) $raw : ''; switch ( $field[1] ) {
		case 'checkbox': $value = '1' === $value; break;
		case 'media': $id = absint( $value ); $value = $id && wp_attachment_is_image( $id ) ? $id : 0; break;
		case 'color': $value = sanitize_hex_color( $value ) ?: $field[2]; break;
		case 'url': $value = esc_url_raw( $value, array( 'https', 'http' ) ); break;
		case 'category': $id = absint( $value ); $value = $id && term_exists( $id, 'product_cat' ) ? $id : 0; break;
		case 'select': $value = isset( $field[3][ $value ] ) ? $value : $field[2]; break;
		case 'textarea': $value = sanitize_textarea_field( $value ); break;
		default: $value = sanitize_text_field( $value );
	} set_theme_mod( $key, $value ); } }
	wp_safe_redirect( add_query_arg( 'saved', '1', $url ) ); exit;
} );
