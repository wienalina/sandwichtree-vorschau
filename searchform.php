<?php
/**
 * Suchformular – sucht in Produkten, wenn WooCommerce aktiv ist.
 *
 * @package Sandwichtree
 */

defined( 'ABSPATH' ) || exit;
$sw_id = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $sw_id ); ?>"><?php esc_html_e( 'Suchen nach', 'sandwichtree' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $sw_id ); ?>" class="search-form__input" placeholder="<?php esc_attr_e( 'Marke, Teil oder Farbe suchen', 'sandwichtree' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<input type="hidden" name="post_type" value="product">
	<?php endif; ?>
	<button type="submit" class="button search-form__submit"><?php esc_html_e( 'Suchen', 'sandwichtree' ); ?></button>
</form>
