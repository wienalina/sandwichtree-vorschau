<?php
/**
 * Seite nicht gefunden.
 *
 * @package Sandwichtree
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<div class="wrap page-head">
	<h1 class="page-title"><?php esc_html_e( 'Diese Seite gibt es nicht (mehr).', 'sandwichtree' ); ?></h1>
</div>
<div class="wrap empty-state">
	<p><?php esc_html_e( 'Vielleicht wurde das Teil schon verkauft oder der Link hat einen Tippfehler. Such nach etwas anderem oder schau dir an, was gerade da ist.', 'sandwichtree' ); ?></p>
	<?php get_search_form(); ?>
	<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
		<p><a class="button" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Alle Teile ansehen', 'sandwichtree' ); ?></a></p>
	<?php endif; ?>
</div>
<?php
get_footer();
