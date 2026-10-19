<?php
/** Blog and archive fallback. @package NavidCommerce */
defined( 'ABSPATH' ) || exit;
get_header(); ?>
<main id="main-content" class="container page-main"><header class="page-heading"><span class="eyebrow"><?php esc_html_e( 'بخوان، کشف کن، انتخاب کن', 'navid-commerce' ); ?></span><h1><?php if ( is_search() ) { printf( esc_html__( 'نتیجه جستجو: %s', 'navid-commerce' ), esc_html( get_search_query() ) ); } elseif ( is_archive() ) { the_archive_title(); } else { esc_html_e( 'مجله نوید', 'navid-commerce' ); } ?></h1><?php the_archive_description(); ?></header>
<?php if ( have_posts() ) : ?><div class="post-grid"><?php while ( have_posts() ) : the_post(); ?><article <?php post_class( 'post-card' ); ?>><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); ?><h2><?php the_title(); ?></h2></a><span class="post-meta"><?php echo esc_html( get_the_date() ); ?></span><?php the_excerpt(); ?></article><?php endwhile; ?></div><?php the_posts_pagination(); else : ?><div class="empty-state"><h2><?php esc_html_e( 'چیزی پیدا نشد', 'navid-commerce' ); ?></h2><?php get_search_form(); ?></div><?php endif; ?></main>
<?php get_footer(); ?>
