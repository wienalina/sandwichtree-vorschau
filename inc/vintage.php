<?php
/**
 * Second-Hand-Logik: Unikate, Vintage-Details, Vinted-Erinnerung.
 *
 * @package Sandwichtree
 */

defined( 'ABSPATH' ) || exit;

/**
 * Vintage-Merkmale, die als WooCommerce-Eigenschaften (Attribute) gepflegt werden.
 * Als globale Eigenschaften sind sie im Shop filterbar.
 *
 * @return array slug => Bezeichnung
 */
function sandwichtree_detail_attributes() {
	return array(
		'marke'    => __( 'Marke', 'sandwichtree' ),
		'groesse'  => __( 'Größe', 'sandwichtree' ),
		'zustand'  => __( 'Zustand', 'sandwichtree' ),
		'material' => __( 'Material', 'sandwichtree' ),
		'farbe'    => __( 'Farbe', 'sandwichtree' ),
		'epoche'   => __( 'Epoche', 'sandwichtree' ),
	);
}

/**
 * Wert einer Eigenschaft lesen – globale (pa_…) oder produkteigene.
 *
 * @param WC_Product|null $product Produkt.
 * @param string          $slug    Schlüssel, z. B. „groesse“.
 * @return string
 */
function sandwichtree_attr( $product, $slug ) {
	if ( ! $product instanceof WC_Product ) {
		return '';
	}
	$value = $product->get_attribute( 'pa_' . $slug );
	if ( '' === $value ) {
		$value = $product->get_attribute( $slug );
	}
	if ( '' === $value ) {
		$labels = sandwichtree_detail_attributes();
		if ( isset( $labels[ $slug ] ) ) {
			$value = $product->get_attribute( sanitize_title( $labels[ $slug ] ) );
		}
	}
	return (string) $value;
}

/* ------------------------------------------------------------------------
 * Unikate: neue Produkte starten mit Lagerbestand 1 und „Einzeln verkaufen“
 * --------------------------------------------------------------------- */

add_action( 'wp_insert_post', function ( $post_id, $post ) {
	if ( 'product' !== $post->post_type || 'auto-draft' !== $post->post_status ) {
		return;
	}
	update_post_meta( $post_id, '_manage_stock', 'yes' );
	update_post_meta( $post_id, '_stock', 1 );
	update_post_meta( $post_id, '_stock_status', 'instock' );
	update_post_meta( $post_id, '_sold_individually', 'yes' );
	update_post_meta( $post_id, '_backorders', 'no' );
}, 10, 2 );

/* ------------------------------------------------------------------------
 * Eigener Reiter „Vintage-Details“ in den Produktdaten
 * --------------------------------------------------------------------- */

add_filter( 'woocommerce_product_data_tabs', function ( $tabs ) {
	$tabs['sandwichtree'] = array(
		'label'    => __( 'Vintage-Details', 'sandwichtree' ),
		'target'   => 'sandwichtree_product_data',
		'class'    => array(),
		'priority' => 15,
	);
	return $tabs;
} );

add_action( 'woocommerce_product_data_panels', function () {
	echo '<div id="sandwichtree_product_data" class="panel woocommerce_options_panel hidden">';

	echo '<div class="options_group"><p class="form-field" style="margin:0;padding-top:12px;padding-bottom:0">';
	esc_html_e( 'Marke, Größe, Zustand, Material, Farbe und Epoche pflegst du im Reiter „Eigenschaften“. Dann kann man im Shop danach filtern.', 'sandwichtree' );
	echo '</p></div>';

	echo '<div class="options_group">';
	woocommerce_wp_text_input( array(
		'id'          => '_st_size_note',
		'label'       => __( 'Hinweis zur Größe', 'sandwichtree' ),
		'placeholder' => __( 'z. B. Fällt eher klein aus, passt wie 36', 'sandwichtree' ),
		'desc_tip'    => true,
		'description' => __( 'Erscheint neben der Größe auf der Produktseite.', 'sandwichtree' ),
	) );
	woocommerce_wp_textarea_input( array(
		'id'          => '_st_measurements',
		'label'       => __( 'Maße', 'sandwichtree' ),
		'placeholder' => "Achsel zu Achsel: 52 cm\nGesamtlänge: 68 cm\nÄrmellänge: 60 cm",
		'rows'        => 5,
		'style'       => 'height:7em',
		'desc_tip'    => true,
		'description' => __( 'Eine Zeile pro Maß im Format „Bezeichnung: Wert“.', 'sandwichtree' ),
	) );
	woocommerce_wp_textarea_input( array(
		'id'          => '_st_flaws',
		'label'       => __( 'Gebrauchsspuren', 'sandwichtree' ),
		'placeholder' => __( 'z. B. Kleiner Fleck am linken Ärmel, siehe Foto 4', 'sandwichtree' ),
		'rows'        => 3,
		'style'       => 'height:4.5em',
	) );
	echo '</div>';

	echo '<div class="options_group">';
	woocommerce_wp_checkbox( array(
		'id'          => '_st_on_vinted',
		'label'       => __( 'Auch auf Vinted inseriert', 'sandwichtree' ),
		'description' => __( 'Bei einer Bestellung bekommst du eine Erinnerung, das Inserat auf Vinted zu entfernen.', 'sandwichtree' ),
	) );
	woocommerce_wp_text_input( array(
		'id'          => '_st_vinted_url',
		'label'       => __( 'Link zum Vinted-Inserat', 'sandwichtree' ),
		'type'        => 'url',
		'placeholder' => 'https://www.vinted.at/items/…',
	) );
	echo '</div>';

	echo '</div>';
} );

add_action( 'woocommerce_admin_process_product_object', function ( WC_Product $product ) {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- WooCommerce prüft die Nonce bereits.
	$product->update_meta_data( '_st_size_note', isset( $_POST['_st_size_note'] ) ? sanitize_text_field( wp_unslash( $_POST['_st_size_note'] ) ) : '' );
	$product->update_meta_data( '_st_measurements', isset( $_POST['_st_measurements'] ) ? sanitize_textarea_field( wp_unslash( $_POST['_st_measurements'] ) ) : '' );
	$product->update_meta_data( '_st_flaws', isset( $_POST['_st_flaws'] ) ? sanitize_textarea_field( wp_unslash( $_POST['_st_flaws'] ) ) : '' );
	$product->update_meta_data( '_st_on_vinted', isset( $_POST['_st_on_vinted'] ) ? 'yes' : 'no' );
	$product->update_meta_data( '_st_vinted_url', isset( $_POST['_st_vinted_url'] ) ? esc_url_raw( wp_unslash( $_POST['_st_vinted_url'] ) ) : '' );
	// phpcs:enable
} );

/* ------------------------------------------------------------------------
 * Vinted-Erinnerung, damit nichts doppelt verkauft wird
 * --------------------------------------------------------------------- */

/**
 * Bestellte Teile, die auch auf Vinted inseriert sind.
 *
 * @param WC_Order $order Bestellung.
 * @return array Liste aus [Name, Vinted-Link]
 */
function sandwichtree_order_vinted_items( $order ) {
	$items = array();
	foreach ( $order->get_items() as $item ) {
		$product = $item->get_product();
		if ( $product && 'yes' === $product->get_meta( '_st_on_vinted' ) ) {
			$items[] = array( $product->get_name(), $product->get_meta( '_st_vinted_url' ) );
		}
	}
	return $items;
}

add_action( 'woocommerce_order_status_changed', function ( $order_id, $from, $to, $order ) {
	if ( ! in_array( $to, array( 'processing', 'on-hold', 'completed' ), true ) || $order->get_meta( '_st_vinted_noted' ) ) {
		return;
	}
	$items = sandwichtree_order_vinted_items( $order );
	if ( ! $items ) {
		return;
	}
	$lines = array();
	foreach ( $items as $entry ) {
		$lines[] = '• ' . $entry[0] . ( $entry[1] ? ' (' . $entry[1] . ')' : '' );
	}
	$order->add_order_note( __( 'Achtung: Diese Teile sind auch auf Vinted inseriert. Bitte dort entfernen oder als verkauft markieren:', 'sandwichtree' ) . "\n" . implode( "\n", $lines ) );
	$order->update_meta_data( '_st_vinted_noted', 'yes' );
	$order->save();

	// Nach der Bestellung nicht mehr als „auf Vinted“ führen.
	foreach ( $order->get_items() as $item ) {
		$product = $item->get_product();
		if ( $product && 'yes' === $product->get_meta( '_st_on_vinted' ) ) {
			$product->update_meta_data( '_st_vinted_reminded', current_time( 'mysql' ) );
			$product->save_meta_data();
		}
	}
}, 10, 4 );

// Hinweis in der Bestell-E-Mail an den Shop (nicht an Kund:innen).
add_action( 'woocommerce_email_before_order_table', function ( $order, $sent_to_admin, $plain_text ) {
	if ( ! $sent_to_admin ) {
		return;
	}
	$items = sandwichtree_order_vinted_items( $order );
	if ( ! $items ) {
		return;
	}
	if ( $plain_text ) {
		echo "\n" . esc_html__( 'ACHTUNG – auch auf Vinted inseriert, bitte dort entfernen:', 'sandwichtree' ) . "\n";
		foreach ( $items as $entry ) {
			echo '- ' . esc_html( $entry[0] ) . ( $entry[1] ? ' ' . esc_url( $entry[1] ) : '' ) . "\n";
		}
		echo "\n";
		return;
	}
	echo '<div style="border:2px solid #B0734E;padding:12px 16px;margin:0 0 24px">';
	echo '<p style="margin:0 0 8px"><strong>' . esc_html__( 'Auch auf Vinted inseriert – bitte dort entfernen:', 'sandwichtree' ) . '</strong></p><ul style="margin:0;padding-left:18px">';
	foreach ( $items as $entry ) {
		echo '<li>' . ( $entry[1] ? '<a href="' . esc_url( $entry[1] ) . '">' . esc_html( $entry[0] ) . '</a>' : esc_html( $entry[0] ) ) . '</li>';
	}
	echo '</ul></div>';
}, 5, 3 );

// Spalte „Vinted“ in der Produktliste im Admin.
add_filter( 'manage_edit-product_columns', function ( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'is_in_stock' === $key ) {
			$new['st_vinted'] = __( 'Vinted', 'sandwichtree' );
		}
	}
	if ( ! isset( $new['st_vinted'] ) ) {
		$new['st_vinted'] = __( 'Vinted', 'sandwichtree' );
	}
	return $new;
} );

add_action( 'manage_product_posts_custom_column', function ( $column, $post_id ) {
	if ( 'st_vinted' !== $column ) {
		return;
	}
	if ( 'yes' === get_post_meta( $post_id, '_st_on_vinted', true ) ) {
		$url = get_post_meta( $post_id, '_st_vinted_url', true );
		echo $url ? '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html__( 'ja', 'sandwichtree' ) . '</a>' : esc_html__( 'ja', 'sandwichtree' );
	} else {
		echo '<span aria-hidden="true">–</span>';
	}
}, 10, 2 );
