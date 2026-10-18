<?php
/**
 * Sandwichtree – Theme-Funktionen.
 *
 * @package Sandwichtree
 */

defined( 'ABSPATH' ) || exit;

define( 'SANDWICHTREE_VERSION', '1.7.2' );
define( 'SANDWICHTREE_DIR', get_template_directory() );
define( 'SANDWICHTREE_URI', get_template_directory_uri() );

require SANDWICHTREE_DIR . '/inc/setup.php';
require SANDWICHTREE_DIR . '/inc/template-tags.php';
require SANDWICHTREE_DIR . '/inc/customizer.php';

if ( class_exists( 'WooCommerce' ) ) {
	require SANDWICHTREE_DIR . '/inc/woocommerce.php';
	require SANDWICHTREE_DIR . '/inc/vintage.php';
}

if ( is_admin() ) {
	require SANDWICHTREE_DIR . '/inc/placeholder-images.php';
	require SANDWICHTREE_DIR . '/inc/demo-import.php';
}
