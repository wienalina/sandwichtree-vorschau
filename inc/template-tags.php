<?php
/**
 * Kleine Hilfsfunktionen für Templates.
 *
 * @package Sandwichtree
 */

defined( 'ABSPATH' ) || exit;

/**
 * Inline-SVG-Icons (Strichstärke 1.5, erben die Textfarbe).
 *
 * @param string $name Icon-Name.
 * @return string
 */
function sandwichtree_icon( $name ) {
	$paths = array(
		'bag'       => '<path d="M5 8h14l-1 12H6L5 8Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/>',
		'search'    => '<circle cx="11" cy="11" r="6.5"/><path d="m20 20-4.2-4.2"/>',
		'user'      => '<circle cx="12" cy="8.5" r="3.5"/><path d="M5 20c1-3.6 3.8-5.5 7-5.5s6 1.9 7 5.5"/>',
		'menu'      => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'     => '<path d="m6 6 12 12M18 6 6 18"/>',
		'instagram' => '<rect x="4" y="4" width="16" height="16" rx="4.5"/><circle cx="12" cy="12" r="3.6"/><circle cx="16.8" cy="7.2" r=".6" fill="currentColor"/>',
		'arrow'     => '<path d="M3 12h18M15 6l6 6-6 6"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return '<svg class="icon icon--' . esc_attr( $name ) . '" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>';
}

/**
 * Logo ausgeben: eigenes Logo aus dem Customizer, sonst die mitgelieferte Wortmarke.
 */
function sandwichtree_brand() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	printf(
		'<a class="wordmark" href="%s" rel="home"><img src="%s" width="790" height="108" alt="%s"></a>',
		esc_url( home_url( '/' ) ),
		esc_url( SANDWICHTREE_URI . '/assets/img/logo-wordmark.png' ),
		esc_attr( get_bloginfo( 'name' ) )
	);
}

/**
 * Fallback fürs Hauptmenü, solange kein Menü angelegt ist:
 * zeigt die Produktkategorien.
 */
function sandwichtree_menu_fallback() {
	echo '<ul class="nav__list">';
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		printf( '<li class="menu-item-has-children"><a href="%s">%s</a><ul class="sub-menu">', esc_url( wc_get_page_permalink( 'shop' ) ), esc_html__( 'Shop', 'sandwichtree' ) );
		$cats = get_terms( array(
			'taxonomy'   => 'product_cat',
			'parent'     => 0,
			'hide_empty' => false,
			'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
			'orderby'    => 'term_order',
		) );
		if ( ! is_wp_error( $cats ) ) {
			foreach ( $cats as $cat ) {
				printf( '<li><a href="%s">%s</a></li>', esc_url( get_term_link( $cat ) ), esc_html( $cat->name ) );
			}
		}
		echo '</ul></li>';
		printf( '<li><a href="%s">%s</a></li>', esc_url( add_query_arg( 'orderby', 'date', wc_get_page_permalink( 'shop' ) ) ), esc_html__( 'Neu', 'sandwichtree' ) );
	}
	$about = get_page_by_path( 'ueber-uns' );
	if ( $about ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( get_permalink( $about ) ), esc_html( get_the_title( $about ) ) );
	}
	echo '</ul>';
}

/**
 * Menü-Fallback im Footer (Seiten nach Slug, falls vorhanden).
 *
 * @param array $slugs Seiten-Slugs.
 */
function sandwichtree_page_links( $slugs ) {
	echo '<ul class="footer__list">';
	foreach ( $slugs as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			printf( '<li><a href="%s">%s</a></li>', esc_url( get_permalink( $page ) ), esc_html( get_the_title( $page ) ) );
		}
	}
	echo '</ul>';
}

/**
 * Bild aus der Mediathek (Customizer) oder neutraler Platzhalter.
 *
 * @param int    $id    Attachment-ID.
 * @param string $size  Bildgröße.
 * @param string $label Alternativtext / Platzhalter-Beschriftung.
 * @param string $class CSS-Klasse.
 * @return string
 */
function sandwichtree_image_or_placeholder( $id, $size, $label, $class = '' ) {
	if ( $id && wp_attachment_is_image( $id ) ) {
		return wp_get_attachment_image( $id, $size, false, array( 'class' => $class, 'alt' => $label ) );
	}
	return sprintf(
		'<div class="placeholder %s" role="img" aria-label="%s"><span>%s</span></div>',
		esc_attr( $class ),
		esc_attr( $label ),
		esc_html__( 'Bild folgt', 'sandwichtree' )
	);
}
