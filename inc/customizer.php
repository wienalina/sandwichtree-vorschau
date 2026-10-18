<?php
/**
 * Customizer: alle Texte und Links, die der Shop selbst ändern soll,
 * unter Design → Customizer → „Sandwichtree“.
 *
 * @package Sandwichtree
 */

defined( 'ABSPATH' ) || exit;

/**
 * Standardwerte (Platzhalter) an einer Stelle.
 */
function sandwichtree_defaults() {
	return array(
		'announcement'        => 'Kostenloser Versand ab 60 € nach Österreich, Deutschland, Italien und Frankreich',
		'hero_title'          => 'Kleidung mit einer zweiten Chance.',
		'hero_text'           => 'Hochwertige Second-Hand-Kleidung, sorgfältig ausgewählt, ehrlich beschrieben und sauber bei dir. Jedes Teil gibt es genau einmal.',
		'hero_button'         => 'Zu den neuen Teilen',
		'hero_caption'        => 'Neue Teile hängen regelmäßig am Baum.',
		'marquee'             => 'Jedes Teil gibt es nur einmal',
		'hero_image_1'        => 0,
		'hero_image_2'        => 0,
		'hero_image_3'        => 0,
		'usp_1_title'         => 'Passt mir das?',
		'usp_1_text'          => 'Die Maße stehen in der Beschreibung jedes Teils. Leg zum Vergleich ein eigenes Lieblingsstück daneben.',
		'usp_2_title'         => 'Und wenn nicht?',
		'usp_2_text'          => 'Du kannst innerhalb von 14 Tagen nach Erhalt vom Kauf zurücktreten.',
		'usp_3_title'         => 'Gibt es das noch mal?',
		'usp_3_text'          => 'Nein. Jedes Teil ist ein Einzelstück. Ist es weg, ist es weg.',
		'instagram_url'       => 'https://www.instagram.com/sandwichtree2026/',
		'vinted_url'          => '', // Profil-Link folgt (Profilname: sandwichtree)
		'instagram_handle'    => '@sandwichtree2026',
		'instagram_shortcode' => '',
		'contact_email'       => 'info@sandwichtree.com',
		'contact_address'     => "Seefeldgasse 11\n7100 Neusiedl am See\nÖsterreich",
		'footer_text'         => 'Kleidung mit einer zweiten Chance.',
		'new_badge_days'      => 14,
		'show_sold'           => true,
	);
}

/**
 * Einstellung lesen.
 *
 * @param string $key Schlüssel.
 * @return mixed
 */
function sandwichtree_opt( $key ) {
	$defaults = sandwichtree_defaults();
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ) {
	$d = sandwichtree_defaults();

	$wp_customize->add_panel( 'sandwichtree', array(
		'title'    => __( 'Sandwichtree', 'sandwichtree' ),
		'priority' => 30,
	) );

	$sections = array(
		'st_general' => __( 'Hinweisleiste & Kontakt', 'sandwichtree' ),
		'st_hero'    => __( 'Startseite: Einstieg', 'sandwichtree' ),
		'st_usp'     => __( 'Startseite: Fragen-Kacheln & Laufband', 'sandwichtree' ),
		'st_social'  => __( 'Instagram & Vinted', 'sandwichtree' ),
		'st_shop'    => __( 'Shop-Verhalten', 'sandwichtree' ),
	);
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section( $id, array( 'title' => $title, 'panel' => 'sandwichtree' ) );
	}

	$text = function ( $key, $label, $section, $type = 'text', $desc = '' ) use ( $wp_customize, $d ) {
		$sanitize = 'sanitize_text_field';
		if ( 'textarea' === $type ) {
			$sanitize = 'sanitize_textarea_field';
		} elseif ( 'url' === $type ) {
			$sanitize = 'esc_url_raw';
		} elseif ( 'email' === $type ) {
			$sanitize = 'sanitize_email';
		}
		$wp_customize->add_setting( $key, array( 'default' => $d[ $key ], 'sanitize_callback' => $sanitize ) );
		$wp_customize->add_control( $key, array(
			'label'       => $label,
			'section'     => $section,
			'type'        => $type,
			'description' => $desc,
		) );
	};

	$text( 'announcement', __( 'Text der Hinweisleiste (leer = ausblenden)', 'sandwichtree' ), 'st_general' );
	$text( 'contact_email', __( 'E-Mail-Adresse', 'sandwichtree' ), 'st_general', 'email' );
	$text( 'contact_address', __( 'Adresse (Footer)', 'sandwichtree' ), 'st_general', 'textarea' );
	$text( 'footer_text', __( 'Kurzbeschreibung im Footer', 'sandwichtree' ), 'st_general', 'textarea' );

	$text( 'hero_title', __( 'Überschrift', 'sandwichtree' ), 'st_hero', 'textarea' );
	$text( 'hero_text', __( 'Text', 'sandwichtree' ), 'st_hero', 'textarea' );
	$text( 'hero_button', __( 'Link-Text', 'sandwichtree' ), 'st_hero' );
	$text( 'hero_caption', __( 'Bildunterschrift rechts', 'sandwichtree' ), 'st_hero' );
	for ( $i = 1; $i <= 3; $i++ ) {
		$wp_customize->add_setting( 'hero_image_' . $i, array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'hero_image_' . $i, array(
			'label'       => sprintf( __( 'Bild %d', 'sandwichtree' ), $i ),
			'description' => 1 === $i ? __( 'Bild 1 groß links, Bild 2 klein rechts, Bild 3 in der Fragen-Kachel. Hochformat empfohlen (z. B. 1200 × 1500 px). Ohne eigene Bilder werden die neuesten Produktfotos verwendet.', 'sandwichtree' ) : '',
			'section'     => 'st_hero',
			'mime_type'   => 'image',
		) ) );
	}

	for ( $i = 1; $i <= 3; $i++ ) {
		$text( "usp_{$i}_title", sprintf( __( 'Kachel %d: Frage', 'sandwichtree' ), $i ), 'st_usp' );
		$text( "usp_{$i}_text", sprintf( __( 'Kachel %d: Antwort', 'sandwichtree' ), $i ), 'st_usp', 'textarea' );
	}
	$text( 'marquee', __( 'Text im Laufband (leer = ausblenden)', 'sandwichtree' ), 'st_usp' );

	$text( 'instagram_url', __( 'Instagram-Link', 'sandwichtree' ), 'st_social', 'url' );
	$text( 'instagram_handle', __( 'Instagram-Name (z. B. @sandwichtree)', 'sandwichtree' ), 'st_social' );
	$text( 'vinted_url', __( 'Vinted-Link', 'sandwichtree' ), 'st_social', 'url' );
	$text(
		'instagram_shortcode',
		__( 'Shortcode eines Instagram-Feed-Plugins (optional)', 'sandwichtree' ),
		'st_social',
		'text',
		__( 'Z. B. [instagram-feed] von Smash Balloon. Leer lassen = nur Links anzeigen. Achtung: Ein eingebetteter Feed braucht eine Cookie-Einwilligung.', 'sandwichtree' )
	);

	$wp_customize->add_setting( 'new_badge_days', array( 'default' => $d['new_badge_days'], 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'new_badge_days', array(
		'label'       => __( '„Neu“-Kennzeichnung für wie viele Tage?', 'sandwichtree' ),
		'description' => __( '0 = nie anzeigen.', 'sandwichtree' ),
		'section'     => 'st_shop',
		'type'        => 'number',
	) );
	$wp_customize->add_setting( 'show_sold', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean' ) );
	$wp_customize->add_control( 'show_sold', array(
		'label'       => __( 'Verkaufte Teile mit „Verkauft“ weiter zeigen', 'sandwichtree' ),
		'description' => __( 'Zeigt das bisherige Sortiment. Ausschalten blendet verkaufte Teile im Shop aus.', 'sandwichtree' ),
		'section'     => 'st_shop',
		'type'        => 'checkbox',
	) );
} );
