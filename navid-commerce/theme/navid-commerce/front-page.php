<?php
/** Shop landing page. @package NavidCommerce */
defined( 'ABSPATH' ) || exit;
get_header();
$shop_url = navid_commerce_shop_url();
?>
<main id="main-content">
	<section class="hero container" aria-labelledby="hero-title">
		<div class="hero-copy"><span class="hero-tag"><span class="status-dot"></span><?php echo esc_html( get_theme_mod( 'navid_hero_label', __( 'کالکشن تازه نویدشاپ', 'navid-commerce' ) ) ); ?></span>
		<h1 id="hero-title"><?php echo esc_html( get_theme_mod( 'navid_hero_title', __( 'تکنولوژی خوب، حالِ خوب.', 'navid-commerce' ) ) ); ?></h1>
		<p><?php echo esc_html( get_theme_mod( 'navid_hero_text', __( 'از اولین آهنگ صبح تا آخرین کار روز؛ همراه‌های تازه‌ات را اینجا پیدا کن.', 'navid-commerce' ) ) ); ?></p>
		<a class="button button-dark" href="<?php echo esc_url( $shop_url ); ?>"><?php echo esc_html( get_theme_mod( 'navid_hero_button', __( 'کشف محصولات', 'navid-commerce' ) ) ); ?><?php navid_commerce_icon( 'arrow' ); ?></a><span class="hero-footnote"><?php esc_html_e( 'انتخاب‌های کوچک، تجربه‌های متفاوت', 'navid-commerce' ); ?></span></div>
		<div class="hero-art"><span class="hero-orbit" aria-hidden="true"></span>
		<?php $hero_image = absint( get_theme_mod( 'navid_hero_image', 0 ) ); ?>
		<?php if ( $hero_image ) : echo wp_get_attachment_image( $hero_image, 'large', false, array( 'class' => 'hero-product', 'loading' => 'eager', 'fetchpriority' => 'high' ) ); else : ?>
		<img class="hero-product" src="<?php echo esc_url( get_theme_file_uri( '/assets/images/headphones.svg' ) ); ?>" width="650" height="560" alt="<?php esc_attr_e( 'تصویر مفهومی هدفون بی‌سیم', 'navid-commerce' ); ?>" fetchpriority="high">
		<?php endif; ?>
		<div class="floating-note"><span class="note-icon"><?php navid_commerce_icon( 'headset' ); ?></span><span><?php esc_html_e( 'صدایی که همراهت می‌ماند', 'navid-commerce' ); ?><small>WIRELESS COLLECTION</small></span></div><span class="hero-index" aria-hidden="true">NAVID / 01</span></div>
	</section>
	<section class="benefits container" aria-label="<?php esc_attr_e( 'راهنمای خرید', 'navid-commerce' ); ?>">
	<?php foreach ( array( array( 'truck', __( 'ارسال سفارش', 'navid-commerce' ), __( 'طبق روش انتخابی شما', 'navid-commerce' ) ), array( 'shield', __( 'خرید با اطمینان', 'navid-commerce' ), __( 'جزئیات شفاف هر محصول', 'navid-commerce' ) ), array( 'return', __( 'شرایط بازگشت', 'navid-commerce' ), __( 'پیش از خرید مطالعه کنید', 'navid-commerce' ) ), array( 'headset', __( 'همراه شما', 'navid-commerce' ), __( 'از انتخاب تا دریافت', 'navid-commerce' ) ) ) as $benefit ) : ?>
		<div class="benefit"><?php navid_commerce_icon( $benefit[0] ); ?><div><strong><?php echo esc_html( $benefit[1] ); ?></strong><span><?php echo esc_html( $benefit[2] ); ?></span></div></div>
	<?php endforeach; ?>
	</section>
	<?php if ( taxonomy_exists( 'product_cat' ) ) :
		$categories = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0, 'number' => 6 ) );
		if ( ! is_wp_error( $categories ) && $categories ) : ?>
	<section class="categories container" aria-labelledby="categories-title"><div class="section-heading"><div><span class="eyebrow"><?php esc_html_e( 'از اینجا شروع کن', 'navid-commerce' ); ?></span><h2 id="categories-title"><?php esc_html_e( 'دنیای مورد علاقه‌ات', 'navid-commerce' ); ?></h2></div><span class="section-caption"><?php esc_html_e( 'برای کار، برای بازی، برای زندگی', 'navid-commerce' ); ?></span></div><div class="category-grid">
		<?php $category_icons = array( 'headphones' => 'headset', 'laptops' => 'laptop', 'mobiles' => 'phone', 'watches' => 'watch', 'gaming' => 'game', 'cameras' => 'camera' ); foreach ( $categories as $index => $category ) : $category_link = get_term_link( $category ); if ( is_wp_error( $category_link ) ) { continue; } ?>
		<a class="category-card" href="<?php echo esc_url( $category_link ); ?>"><span class="category-icon"><?php navid_commerce_icon( isset( $category_icons[ $category->slug ] ) ? $category_icons[ $category->slug ] : 'bag' ); ?></span><strong><?php echo esc_html( $category->name ); ?></strong><span><?php echo esc_html( sprintf( __( '%s محصول', 'navid-commerce' ), number_format_i18n( $category->count ) ) ); ?></span></a>
		<?php endforeach; ?>
	</div></section>
	<?php endif; endif; ?>
	<?php
	if ( function_exists( 'wc_get_product_ids_on_sale' ) ) {
		$sale_ids = wc_get_product_ids_on_sale();
		if ( $sale_ids ) {
			navid_commerce_product_section( __( 'پیشنهادهای دوست‌داشتنی', 'navid-commerce' ), array( 'include' => $sale_ids ), 'offers' );
		}
	}
	?>
	<section class="promo-grid container" aria-label="<?php esc_attr_e( 'کشف محصولات فروشگاه', 'navid-commerce' ); ?>"><a href="<?php echo esc_url( $shop_url ); ?>" class="promo-card promo-sage"><div><span class="eyebrow">MAKE IT YOURS</span><h2><?php esc_html_e( 'ریتم روزت را عوض کن.', 'navid-commerce' ); ?></h2><span class="text-link"><?php esc_html_e( 'دیدن محصولات', 'navid-commerce' ); ?><?php navid_commerce_icon( 'arrow' ); ?></span></div><?php navid_commerce_icon( 'headset', 'promo-icon' ); ?></a><a href="<?php echo esc_url( $shop_url ); ?>" class="promo-card promo-peach"><div><span class="eyebrow">A LITTLE SMARTER</span><h2><?php esc_html_e( 'یک قدم هوشمندتر.', 'navid-commerce' ); ?></h2><span class="text-link"><?php esc_html_e( 'کشف تازه‌ها', 'navid-commerce' ); ?><?php navid_commerce_icon( 'arrow' ); ?></span></div><?php navid_commerce_icon( 'watch', 'promo-icon' ); ?></a></section>
	<?php navid_commerce_product_section( __( 'تازه به جمع ما رسیدند', 'navid-commerce' ) ); ?>
	<?php if ( ! class_exists( 'WooCommerce' ) && current_user_can( 'manage_options' ) ) : ?><div class="container empty-state"><h2><?php esc_html_e( 'فروشگاهت را آماده کن', 'navid-commerce' ); ?></h2><p><?php esc_html_e( 'برای نمایش محصولات، ووکامرس را نصب و فعال کنید. این پیام فقط برای مدیر نمایش داده می‌شود.', 'navid-commerce' ); ?></p></div><?php endif; ?>
	<?php if ( 'page' === get_option( 'show_on_front' ) && have_posts() ) : while ( have_posts() ) : the_post(); if ( trim( get_the_content() ) ) : ?><section class="container entry-content front-content"><?php the_content(); ?></section><?php endif; endwhile; endif; ?>
	<section class="store-story container"><span class="eyebrow">CURATED FOR YOUR EVERYDAY</span><h2><?php esc_html_e( 'انتخاب خوب، از جزئیات شروع می‌شود.', 'navid-commerce' ); ?></h2><p><?php esc_html_e( 'محصولات را مقایسه کن، مشخصات را بخوان و چیزی را انتخاب کن که به زندگی‌ات می‌آید.', 'navid-commerce' ); ?></p><a class="text-link" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'گشتی در فروشگاه', 'navid-commerce' ); ?><?php navid_commerce_icon( 'arrow' ); ?></a></section>
</main>
<?php get_footer(); ?>
