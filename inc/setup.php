<?php
/**
 * Grundeinstellungen: Theme-Support, Menüs, Skripte.
 *
 * @package Sandwichtree
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', function () {
	load_theme_textdomain( 'sandwichtree', SANDWICHTREE_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 320,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	// WooCommerce: Galerie mit Zoom, Lightbox und Wischen auf dem Handy.
	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 600,
		'single_image_width'    => 1000,
		'product_grid'          => array(
			'default_rows'    => 4,
			'min_rows'        => 1,
			'default_columns' => 4,
			'min_columns'     => 2,
			'max_columns'     => 4,
		),
	) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus( array(
		'primary' => __( 'Hauptmenü', 'sandwichtree' ),
		'footer'  => __( 'Footer: Service', 'sandwichtree' ),
		'legal'   => __( 'Footer: Rechtliches', 'sandwichtree' ),
	) );

	// Hochformat-Bilder (3:4) für Kategorien und Startseite.
	add_image_size( 'sandwichtree-portrait', 750, 1000, true );
} );

/**
 * Produktbilder einheitlich im Hochformat 3:4 zuschneiden,
 * damit das Raster ruhig bleibt, egal welche Fotos hochgeladen werden.
 */
add_filter( 'woocommerce_get_image_size_thumbnail', function ( $size ) {
	return array( 'width' => 600, 'height' => 800, 'crop' => 1 );
} );
add_filter( 'woocommerce_get_image_size_gallery_thumbnail', function ( $size ) {
	return array( 'width' => 225, 'height' => 300, 'crop' => 1 );
} );

add_action( 'wp_enqueue_scripts', function () {
	// Nach den WooCommerce-Grundstilen laden, damit unsere Regeln gewinnen.
	$deps = wp_style_is( 'woocommerce-general', 'registered' ) ? array( 'woocommerce-general' ) : array();
	wp_enqueue_style( 'sandwichtree', SANDWICHTREE_URI . '/assets/css/main.css', $deps, SANDWICHTREE_VERSION );
	wp_enqueue_script( 'sandwichtree', SANDWICHTREE_URI . '/assets/js/main.js', array(), SANDWICHTREE_VERSION, array( 'strategy' => 'defer', 'in_footer' => true ) );
} );

/** Schrift vorladen – liegt lokal im Theme, kein Kontakt zu Google (DSGVO). */
add_action( 'wp_head', function () {
	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( SANDWICHTREE_URI . '/assets/fonts/inter-tight-latin-wght-normal.woff2' )
	);
}, 1 );

/** Gutenberg-Inhalte (Seiten wie Über uns, FAQ) im selben Stil darstellen. */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );
	add_theme_support( 'disable-custom-colors' );
	add_theme_support( 'editor-color-palette', array(
		array( 'name' => 'Tinte', 'slug' => 'ink', 'color' => '#0D0F12' ),
		array( 'name' => 'Meeresblau', 'slug' => 'navy', 'color' => '#29395E' ),
		array( 'name' => 'Himmelblau', 'slug' => 'sky', 'color' => '#A9BAC5' ),
		array( 'name' => 'Rot', 'slug' => 'red', 'color' => '#AA2317' ),
		array( 'name' => 'Stein', 'slug' => 'stone', 'color' => '#A3998B' ),
		array( 'name' => 'Weiß', 'slug' => 'white', 'color' => '#FFFFFF' ),
		array( 'name' => 'Hellgrau', 'slug' => 'paper', 'color' => '#E6E4E1' ),
	) );
} );

add_action( 'widgets_init', function () {
	register_sidebar( array(
		'name'          => __( 'Shop-Filter', 'sandwichtree' ),
		'id'            => 'shop-filters',
		'description'   => __( 'Filter in der Shop-Übersicht, z. B. nach Größe, Marke oder Preis.', 'sandwichtree' ),
		'before_widget' => '<div id="%1$s" class="filter-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="filter-widget__title">',
		'after_title'   => '</h3>',
	) );
} );

/** Ausschnittslänge für Blog/Suche. */
add_filter( 'excerpt_length', function () {
	return 24;
} );

/** Favicon aus dem runden Logo, solange unter Design → Customizer kein Website-Icon gesetzt ist. */
add_action( 'wp_head', function () {
	if ( has_site_icon() ) {
		return;
	}
	$base = SANDWICHTREE_URI . '/assets/img/';
	printf( '<link rel="icon" href="%s" sizes="32x32">' . "\n", esc_url( $base . 'favicon-32.png' ) );
	printf( '<link rel="icon" href="%s" sizes="192x192">' . "\n", esc_url( $base . 'favicon-192.png' ) );
	printf( '<link rel="apple-touch-icon" href="%s">' . "\n", esc_url( $base . 'favicon-180.png' ) );
}, 2 );
