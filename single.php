<?php
/**
 * Einzelner Beitrag.
 *
 * @package Sandwichtree
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'page' ); ?>>
		<header class="wrap page-head">
			<p class="page-meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
			<h1 class="page-title"><?php the_title(); ?></h1>
		</header>
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="wrap page-hero-image"><?php the_post_thumbnail( 'large' ); ?></div>
		<?php endif; ?>
		<div class="wrap page-content prose"><?php the_content(); ?></div>
	</article>
	<?php
endwhile;

get_footer();
