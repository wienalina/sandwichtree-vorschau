<?php
/**
 * Seiten (Über uns, FAQ, Impressum, Warenkorb, Kasse …).
 *
 * @package Sandwichtree
 */

defined( 'ABSPATH' ) || exit;
get_header();

$is_shop_page = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() );

while ( have_posts() ) :
	the_post();
	?>
	<?php if ( is_page( 'ueber-uns' ) ) : ?>
		<article <?php post_class( 'page page--about' ); ?>>
			<header class="wrap page-head">
				<h1 class="page-title"><?php the_title(); ?></h1>
			</header>
			<div class="wrap about">
				<figure class="about__logo">
					<img src="<?php echo esc_url( SANDWICHTREE_URI . '/assets/img/logo-rund.png' ); ?>" width="1000" height="1000" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				</figure>
				<div class="about__text prose">
					<?php the_content(); ?>
				</div>
			</div>
		</article>
	<?php else : ?>
		<article <?php post_class( $is_shop_page ? 'page page--wide' : 'page' ); ?>>
			<header class="wrap page-head">
				<h1 class="page-title"><?php the_title(); ?></h1>
			</header>
			<div class="wrap <?php echo $is_shop_page ? 'page-content--wide' : 'page-content prose'; ?>">
				<?php the_content(); ?>
			</div>
		</article>
	<?php endif; ?>
	<?php
endwhile;

get_footer();
