<?php
/**
 * WooCommerce-Darstellung: Wrapper, Produktkarten, Produktseite, Warenkorb.
 *
 * @package Sandwichtree
 */

defined( 'ABSPATH' ) || exit;

/* ------------------------------------------------------------------------
 * Allgemein
 * --------------------------------------------------------------------- */

// Eigene Stile übernehmen das Layout, WooCommerce liefert nur die Grundlagen.
add_filter( 'woocommerce_enqueue_styles', function ( $styles ) {
	unset( $styles['woocommerce-layout'], $styles['woocommerce-smallscreen'] );
	return $styles;
} );

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

add_action( 'woocommerce_before_main_content', function () {
	echo '<div class="wrap shop-wrap">';
}, 10 );
add_action( 'woocommerce_after_main_content', function () {
	echo '</div>';
}, 10 );

add_filter( 'woocommerce_breadcrumb_defaults', function ( $args ) {
	$args['delimiter']   = '<span class="breadcrumb__sep" aria-hidden="true">/</span>';
	$args['wrap_before'] = '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Brotkrümelnavigation', 'sandwichtree' ) . '">';
	$args['wrap_after']  = '</nav>';
	$args['home']        = __( 'Start', 'sandwichtree' );
	return $args;
} );

// Produkte pro Seite & Spalten.
add_filter( 'loop_shop_per_page', function () {
	return 24;
} );
add_filter( 'loop_shop_columns', function () {
	return 4;
} );

/* ------------------------------------------------------------------------
 * Warenkorb-Link im Header (aktualisiert sich per AJAX)
 * --------------------------------------------------------------------- */

/**
 * Warenkorb-Link mit Anzahl.
 */
function sandwichtree_cart_link() {
	$count = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
	?>
	<a class="text-link cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
		<span class="cart-link__icon"><?php echo sandwichtree_icon( 'bag' ); // phpcs:ignore ?></span>
		<span class="cart-link__label"><?php esc_html_e( 'Warenkorb', 'sandwichtree' ); ?></span>
		<span class="cart-link__count<?php echo $count ? '' : ' is-empty'; ?>" aria-hidden="true"><?php echo (int) $count; ?></span>
		<span class="screen-reader-text">
			<?php
			/* translators: %d: Anzahl Teile im Warenkorb */
			printf( esc_html( _n( '%d Teil', '%d Teile', $count, 'sandwichtree' ) ), (int) $count );
			?>
		</span>
	</a>
	<?php
}

add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	ob_start();
	sandwichtree_cart_link();
	$fragments['a.cart-link'] = ob_get_clean();
	return $fragments;
} );

/* ------------------------------------------------------------------------
 * Shop-Übersicht
 * --------------------------------------------------------------------- */

// Titel + Beschreibung + Filter-Leiste.
add_filter( 'woocommerce_show_page_title', '__return_false' );

add_action( 'woocommerce_before_main_content', function () {
	if ( ! ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}
	echo '<header class="shop-head">';
	echo '<h1 class="page-title">' . esc_html( woocommerce_page_title( false ) ) . '</h1>';
	echo '</header>';
}, 25 );

// Unterkategorien als Leiste (z. B. Damen → Kleider, Jacken …).
add_action( 'woocommerce_archive_description', function () {
	$parent = is_product_category() ? get_queried_object_id() : 0;
	$terms  = get_terms( array(
		'taxonomy'   => 'product_cat',
		'parent'     => $parent,
		'hide_empty' => true,
		'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
	) );
	if ( is_wp_error( $terms ) || ! $terms ) {
		return;
	}
	echo '<nav class="chip-row" aria-label="' . esc_attr__( 'Kategorien', 'sandwichtree' ) . '"><ul>';
	foreach ( $terms as $term ) {
		printf( '<li><a class="chip" href="%s">%s</a></li>', esc_url( get_term_link( $term ) ), esc_html( $term->name ) );
	}
	echo '</ul></nav>';
}, 20 );

// Filter-Widgets ausklappbar über dem Raster.
add_action( 'woocommerce_before_shop_loop', function () {
	if ( ! is_active_sidebar( 'shop-filters' ) ) {
		return;
	}
	echo '<details class="shop-filters"><summary class="button button--ghost">' . esc_html__( 'Filtern', 'sandwichtree' ) . '</summary><div class="shop-filters__body">';
	dynamic_sidebar( 'shop-filters' );
	echo '</div></details>';
}, 15 );

/* ------------------------------------------------------------------------
 * Produktkarte
 * --------------------------------------------------------------------- */

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
// Unikate: „Ansehen“ statt Warenkorb-Button direkt im Raster.
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

add_action( 'woocommerce_before_shop_loop_item_title', function () {
	global $product;
	echo '<div class="product-card__media">';
	echo woocommerce_get_product_thumbnail(); // phpcs:ignore

	// Zweites Bild beim Überfahren (zeigt z. B. die Rückseite).
	$gallery = $product->get_gallery_image_ids();
	if ( $gallery ) {
		echo wp_get_attachment_image( $gallery[0], 'woocommerce_thumbnail', false, array( 'class' => 'product-card__alt', 'alt' => '', 'loading' => 'lazy' ) );
	}

	echo sandwichtree_product_badges( $product ); // phpcs:ignore

	$size = sandwichtree_attr( $product, 'groesse' );
	if ( $size ) {
		echo sandwichtree_size_tag( $size ); // phpcs:ignore
	}
	echo '</div>';
}, 10 );

// Marke als Mono-Zeile unter dem Titel (wie die Bildunterschriften der Referenz).
add_action( 'woocommerce_after_shop_loop_item_title', function () {
	global $product;
	$brand = sandwichtree_attr( $product, 'marke' );
	if ( $brand ) {
		echo '<p class="product-card__brand">' . esc_html( $brand ) . '</p>';
	}
}, 5 );

add_filter( 'woocommerce_product_loop_title_classes', function ( $classes ) {
	return $classes . ' product-card__title';
} );

/**
 * Größen-Anhänger in der Form der Häuschen aus dem Logo.
 *
 * @param string $size  Größe.
 * @param string $class Zusatzklasse.
 * @return string
 */
function sandwichtree_size_tag( $size, $class = '' ) {
	return sprintf(
		'<span class="size-chip %s"><span class="screen-reader-text">%s </span>%s</span>',
		esc_attr( $class ),
		esc_html__( 'Größe', 'sandwichtree' ),
		esc_html( $size )
	);
}

/**
 * Kennzeichnungen „Verkauft“, „Neu“, „Reduziert“.
 *
 * @param WC_Product $product Produkt.
 * @return string
 */
function sandwichtree_product_badges( $product ) {
	$out = '';
	if ( ! $product->is_in_stock() ) {
		$out .= '<span class="badge badge--sold">' . esc_html__( 'Verkauft', 'sandwichtree' ) . '</span>';
	} elseif ( $product->is_on_sale() ) {
		$out .= '<span class="badge badge--sale">' . esc_html__( 'Reduziert', 'sandwichtree' ) . '</span>';
	} elseif ( sandwichtree_is_new( $product ) ) {
		$out .= '<span class="badge badge--new">' . esc_html__( 'Neu', 'sandwichtree' ) . '</span>';
	}
	return $out ? '<span class="badges">' . $out . '</span>' : '';
}

/**
 * Ist das Produkt innerhalb der eingestellten Tage veröffentlicht worden?
 *
 * @param WC_Product $product Produkt.
 * @return bool
 */
function sandwichtree_is_new( $product ) {
	$days = (int) sandwichtree_opt( 'new_badge_days' );
	$date = $product->get_date_created();
	if ( ! $days || ! $date ) {
		return false;
	}
	return ( time() - $date->getTimestamp() ) < $days * DAY_IN_SECONDS;
}

add_filter( 'post_class', function ( $classes, $class, $post_id ) {
	if ( 'product' === get_post_type( $post_id ) && ! is_singular( 'product' ) ) {
		$classes[] = 'product-card';
	}
	return $classes;
}, 10, 3 );

/* ------------------------------------------------------------------------
 * Produktseite
 * --------------------------------------------------------------------- */

remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );

// Marke über dem Titel.
add_action( 'woocommerce_single_product_summary', function () {
	global $product;
	$brand = sandwichtree_attr( $product, 'marke' );
	echo sandwichtree_product_badges( $product ); // phpcs:ignore
	if ( $brand ) {
		echo '<p class="product-brand">' . esc_html( $brand ) . '</p>';
	}
}, 4 );

// Größe + Zustand direkt unter dem Preis.
add_action( 'woocommerce_single_product_summary', function () {
	global $product;
	$size      = sandwichtree_attr( $product, 'groesse' );
	$condition = sandwichtree_attr( $product, 'zustand' );
	$note      = $product->get_meta( '_st_size_note' );
	if ( ! $size && ! $condition ) {
		return;
	}
	echo '<div class="product-facts">';
	if ( $size ) {
		printf( '<p class="product-facts__size"><span class="product-facts__label">%s</span><strong>%s</strong>%s</p>', esc_html__( 'Größe', 'sandwichtree' ), esc_html( $size ), $note ? '<span class="product-facts__note">' . esc_html( $note ) . '</span>' : '' );
	}
	if ( $condition ) {
		$info = get_page_by_path( 'groessen-zustand' );
		printf(
			'<p class="product-facts__condition"><span class="product-facts__label">%s</span><strong>%s</strong>%s</p>',
			esc_html__( 'Zustand', 'sandwichtree' ),
			esc_html( $condition ),
			$info ? ' <a href="' . esc_url( get_permalink( $info ) ) . '#zustand">' . esc_html__( 'Was heißt das?', 'sandwichtree' ) . '</a>' : ''
		);
	}
	echo '</div>';
}, 15 );

// Details, Maße, Beschreibung, Versand als aufklappbare Abschnitte.
add_action( 'woocommerce_single_product_summary', 'sandwichtree_product_details', 35 );

/**
 * Detail-Abschnitte auf der Produktseite.
 */
function sandwichtree_product_details() {
	global $product;

	$rows = array();
	foreach ( sandwichtree_detail_attributes() as $slug => $label ) {
		$value = sandwichtree_attr( $product, $slug );
		if ( $value ) {
			$rows[ $label ] = $value;
		}
	}
	$measurements = trim( (string) $product->get_meta( '_st_measurements' ) );
	$flaws        = trim( (string) $product->get_meta( '_st_flaws' ) );
	$description  = $product->get_description();

	echo '<div class="product-details">';

	if ( $rows || $flaws ) {
		echo '<details class="disclosure" open><summary>' . esc_html__( 'Details', 'sandwichtree' ) . '</summary><div class="disclosure__body">';
		if ( $rows ) {
			echo '<dl class="spec-list">';
			foreach ( $rows as $label => $value ) {
				printf( '<div><dt>%s</dt><dd>%s</dd></div>', esc_html( $label ), esc_html( $value ) );
			}
			echo '</dl>';
		}
		if ( $flaws ) {
			echo '<p class="product-flaws"><strong>' . esc_html__( 'Gebrauchsspuren:', 'sandwichtree' ) . '</strong> ' . esc_html( $flaws ) . '</p>';
		}
		echo '</div></details>';
	}

	if ( $measurements ) {
		echo '<details class="disclosure" open><summary>' . esc_html__( 'Maße', 'sandwichtree' ) . '</summary><div class="disclosure__body">';
		echo '<dl class="spec-list">';
		foreach ( preg_split( '/\r\n|\r|\n/', $measurements ) as $line ) {
			$parts = array_map( 'trim', explode( ':', $line, 2 ) );
			if ( 2 === count( $parts ) && '' !== $parts[0] ) {
				printf( '<div><dt>%s</dt><dd>%s</dd></div>', esc_html( $parts[0] ), esc_html( $parts[1] ) );
			} elseif ( '' !== $parts[0] ) {
				printf( '<div><dt>%s</dt><dd></dd></div>', esc_html( $parts[0] ) );
			}
		}
		echo '</dl>';
		echo '<p class="hint">' . esc_html__( 'Flach liegend gemessen. Leg zum Vergleich ein Teil aus deinem Schrank daneben.', 'sandwichtree' ) . '</p>';
		echo '</div></details>';
	}

	if ( $description ) {
		echo '<details class="disclosure"><summary>' . esc_html__( 'Beschreibung', 'sandwichtree' ) . '</summary><div class="disclosure__body prose">';
		echo wp_kses_post( wpautop( $description ) );
		echo '</div></details>';
	}

	$shipping = get_page_by_path( 'versand-rueckgabe' );
	echo '<details class="disclosure"><summary>' . esc_html__( 'Versand & Rückgabe', 'sandwichtree' ) . '</summary><div class="disclosure__body">';
	echo '<p>' . esc_html__( 'Ab 60 € versandkostenfrei nach Österreich, Deutschland, Italien und Frankreich. Rücktritt innerhalb von 14 Tagen nach Erhalt.', 'sandwichtree' ) . '</p>';
	if ( $shipping ) {
		echo '<p><a href="' . esc_url( get_permalink( $shipping ) ) . '">' . esc_html__( 'Alle Infos zu Versand und Rückgabe', 'sandwichtree' ) . '</a></p>';
	}
	echo '</div></details>';

	echo '</div>';
}

add_filter( 'woocommerce_output_related_products_args', function ( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
} );

add_filter( 'woocommerce_product_related_products_heading', function () {
	return __( 'Das könnte dir auch gefallen', 'sandwichtree' );
} );

/* ------------------------------------------------------------------------
 * Texte (gelten auch, falls die deutsche WooCommerce-Übersetzung fehlt)
 * --------------------------------------------------------------------- */

add_filter( 'woocommerce_product_single_add_to_cart_text', function () {
	return __( 'In den Warenkorb', 'sandwichtree' );
} );
add_filter( 'woocommerce_product_add_to_cart_text', function ( $text, $product ) {
	return $product->is_in_stock() ? __( 'Ansehen', 'sandwichtree' ) : __( 'Verkauft', 'sandwichtree' );
}, 10, 2 );

add_filter( 'woocommerce_get_availability_text', function ( $text, $product ) {
	if ( ! $product->is_in_stock() ) {
		return __( 'Schon verkauft. Dieses Teil gibt es nicht mehr, schau dir ähnliche Teile weiter unten an.', 'sandwichtree' );
	}
	if ( $product->managing_stock() && 1 === (int) $product->get_stock_quantity() ) {
		return __( 'Einzelstück, nur 1× verfügbar', 'sandwichtree' );
	}
	return $text;
}, 10, 2 );

add_filter( 'woocommerce_get_availability_class', function ( $class, $product ) {
	return $product->is_in_stock() ? $class . ' availability--unique' : $class;
}, 10, 2 );

// Mengenfeld ausblenden – jedes Teil gibt es nur einmal.
add_filter( 'woocommerce_is_sold_individually', function ( $sold_individually, $product ) {
	if ( $product->managing_stock() && (int) $product->get_stock_quantity() <= 1 ) {
		return true;
	}
	return $sold_individually;
}, 10, 2 );

/* ------------------------------------------------------------------------
 * Verkaufte Teile zeigen oder ausblenden (Customizer)
 * --------------------------------------------------------------------- */

add_filter( 'pre_option_woocommerce_hide_out_of_stock_items', function ( $value ) {
	return sandwichtree_opt( 'show_sold' ) ? 'no' : 'yes';
} );

// Verkaufte Teile ans Ende der Liste sortieren.
add_filter( 'posts_clauses', function ( $clauses, $query ) {
	global $wpdb;
	if ( is_admin() || ! $query->is_main_query() || ! ( is_shop() || is_product_taxonomy() ) || ! sandwichtree_opt( 'show_sold' ) ) {
		return $clauses;
	}
	if ( false === strpos( $clauses['join'], 'st_stock' ) ) {
		$clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} st_stock ON ( {$wpdb->posts}.ID = st_stock.post_id AND st_stock.meta_key = '_stock_status' ) ";
	}
	$clauses['orderby'] = "CASE WHEN st_stock.meta_value = 'outofstock' THEN 1 ELSE 0 END ASC, " . $clauses['orderby'];
	return $clauses;
}, 20, 2 );

/* ------------------------------------------------------------------------
 * Gratis-Versand: wenn verfügbar, die kostenpflichtige Option ausblenden
 * --------------------------------------------------------------------- */

add_filter( 'woocommerce_package_rates', function ( $rates ) {
	$free = array();
	foreach ( $rates as $id => $rate ) {
		if ( in_array( $rate->get_method_id(), array( 'free_shipping', 'local_pickup', 'pickup_location' ), true ) ) {
			$free[ $id ] = $rate;
		}
	}
	foreach ( $free as $rate ) {
		if ( 'free_shipping' === $rate->get_method_id() ) {
			return $free;
		}
	}
	return $rates;
}, 100 );

/* ------------------------------------------------------------------------
 * Teile ohne Preis: „Preis folgt“ statt leerer Fläche
 * --------------------------------------------------------------------- */

add_filter( 'woocommerce_get_price_html', function ( $html, $product ) {
	if ( '' === $product->get_price() ) {
		return '<span class="price-tbd">' . esc_html__( 'Preis folgt', 'sandwichtree' ) . '</span>';
	}
	return $html;
}, 10, 2 );
