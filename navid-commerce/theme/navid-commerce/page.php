<?php
/** Standard page. @package NavidCommerce */
defined( 'ABSPATH' ) || exit;
get_header(); ?>
<main id="main-content" class="container page-main"><?php while ( have_posts() ) : the_post(); ?><article <?php post_class(); ?>><header class="page-heading"><h1><?php the_title(); ?></h1></header><div class="entry-content"><?php the_content(); wp_link_pages(); ?></div></article><?php if ( comments_open() || get_comments_number() ) { comments_template(); } endwhile; ?></main>
<?php get_footer(); ?>
