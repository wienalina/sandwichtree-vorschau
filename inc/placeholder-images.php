<?php
/**
 * Platzhalterbilder für die Beispielprodukte.
 *
 * Wechselt zwischen drei Motiv-Bildern (assets/demo/) und einfarbigen Flächen
 * in deren Farben. Alle Platzhalter werden mit „Beispielprodukte löschen“
 * wieder entfernt und im echten Shop durch Produktfotos ersetzt.
 *
 * @package Sandwichtree
 */

defined( 'ABSPATH' ) || exit;

/**
 * Reihenfolge der Platzhalter: Motiv-Datei oder Farbfläche [R, G, B].
 *
 * @return array
 */
function sandwichtree_placeholder_motifs() {
	// Reihenfolge so gewählt, dass die neuesten Beispielteile (oben auf der Startseite)
	// mit Figur, Blau und Ring beginnen.
	return array(
		array( 'color' => array( 13, 13, 13 ), 'label' => 'Schwarz' ),
		array( 'color' => array( 72, 64, 58 ), 'label' => 'Taupe' ),
		array( 'color' => array( 7, 56, 244 ), 'label' => 'Kobaltblau' ),
		array( 'file' => 'motiv-ring.jpg', 'label' => 'Motiv Ring' ),
		array( 'color' => array( 155, 157, 152 ), 'label' => 'Grau' ),
		array( 'file' => 'motiv-blau.jpg', 'label' => 'Motiv Blau' ),
		array( 'file' => 'motiv-figur.jpg', 'label' => 'Motiv Figur' ),
	);
}

/**
 * Ein Platzhalterbild in die Mediathek legen.
 *
 * Signatur bleibt kompatibel zur Einrichtung (Form und Farbe des Teils werden nicht mehr gebraucht).
 *
 * @param string $shape   Nicht mehr verwendet.
 * @param int    $variant Bildnummer innerhalb des Produkts (1 = Hauptbild).
 * @param int    $total   Anzahl Bilder dieses Produkts.
 * @param int    $palette Laufende Nummer des Produkts.
 * @param string $title   Titel des Bildes.
 * @param int    $parent  Produkt-ID.
 * @param string $color   Nicht mehr verwendet.
 * @return int Attachment-ID oder 0.
 */
function sandwichtree_make_placeholder( $shape, $variant, $total, $palette, $title, $parent = 0, $color = '' ) {
	$motifs = sandwichtree_placeholder_motifs();
	// Hauptbild je Beispielprodukt fest vergeben, damit im Shop-Raster (alphabetisch)
	// keine gleichen Flächen neben- oder untereinander stehen.
	$main  = array( 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 0, 8 => 6, 9 => 2, 10 => 1, 11 => 4, 12 => 3, 13 => 0, 14 => 3, 15 => 0, 16 => 2 );
	$start = isset( $main[ (int) $palette ] ) ? $main[ (int) $palette ] : (int) $palette;
	$motif = $motifs[ ( $start + (int) $variant - 1 ) % count( $motifs ) ];

	$upload = wp_upload_dir();
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}

	if ( isset( $motif['color'] ) && function_exists( 'imagecreatetruecolor' ) ) {
		// Einfarbige Fläche im Hochformat 3:4.
		$img = imagecreatetruecolor( 1000, 1333 );
		list( $r, $g, $b ) = $motif['color'];
		imagefilledrectangle( $img, 0, 0, 1000, 1333, imagecolorallocate( $img, $r, $g, $b ) );
		$filename = wp_unique_filename( $upload['path'], sanitize_title( $title ) . '.png' );
		$path     = trailingslashit( $upload['path'] ) . $filename;
		imagepng( $img, $path, 9 );
		imagedestroy( $img );
		$mime = 'image/png';
	} else {
		// Motiv-Bild kopieren (ohne GD: Farbflächen werden ebenfalls durch Motive ersetzt).
		if ( ! isset( $motif['file'] ) ) {
			$motif = $motifs[0];
		}
		$source = SANDWICHTREE_DIR . '/assets/demo/' . $motif['file'];
		if ( ! file_exists( $source ) ) {
			return 0;
		}
		$filename = wp_unique_filename( $upload['path'], sanitize_title( $title ) . '.jpg' );
		$path     = trailingslashit( $upload['path'] ) . $filename;
		if ( ! copy( $source, $path ) ) {
			return 0;
		}
		$mime = 'image/jpeg';
	}

	$attachment_id = wp_insert_attachment( array(
		'post_mime_type' => $mime,
		'post_title'     => $title,
		'post_status'    => 'inherit',
	), $path, $parent );
	if ( is_wp_error( $attachment_id ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $path ) );
	update_post_meta( $attachment_id, '_st_demo', 1 );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', $title );
	return (int) $attachment_id;
}
