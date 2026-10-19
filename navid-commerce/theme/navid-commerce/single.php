<?php
/** Single article. @package NavidCommerce */
defined( 'ABSPATH' ) || exit;
get_header(); ?>
<main id="main-content" class="container page-main article-main"><?php while ( have_posts() ) : the_post(); ?><article <?php post_class(); ?>><header class="page-heading"><span class="post-meta"><?php echo esc_html( get_the_date() ); ?></span><h1><?php the_title(); ?></h1></header><?php the_post_thumbnail( 'large', array( 'class' => 'article-cover' ) ); ?><div class="entry-content"><?php the_content(); wp_link_pages(); ?></div><footer class="post-tags"><?php the_tags(); ?></footer></article><?php the_post_navigation(); if ( comments_open() || get_comments_number() ) { comments_template(); } endwhile; ?></main>
<?php get_footer(); ?>
