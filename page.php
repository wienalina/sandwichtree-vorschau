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
	<article <?php post_class( $is_shop_page ? 'page page--wide' : 'page' ); ?>>
		<header class="wrap page-head">
			<h1 class="page-title"><?php the_title(); ?></h1>
		</header>
		<div class="wrap <?php echo $is_shop_page ? 'page-content--wide' : 'page-content prose'; ?>">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
