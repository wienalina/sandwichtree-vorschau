<?php
/**
 * Einrichtung: legt per Knopfdruck Kategorien, Eigenschaften, Beispielprodukte
 * mit Platzhalterbildern, Seiten, Menüs und sinnvolle Shop-Einstellungen an.
 * Zu finden unter Design → Sandwichtree einrichten.
 *
 * @package Sandwichtree
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', function () {
	add_theme_page(
		__( 'Sandwichtree einrichten', 'sandwichtree' ),
		__( 'Sandwichtree einrichten', 'sandwichtree' ),
		'manage_options',
		'sandwichtree-setup',
		'sandwichtree_setup_page'
	);
} );

// Nach der Theme-Aktivierung auf die Einrichtung hinweisen.
add_action( 'admin_notices', function () {
	if ( get_option( 'sandwichtree_setup_done' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'appearance_page_sandwichtree-setup' === $screen->id ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p>%s <a class="button button-primary" href="%s">%s</a></p></div>',
		esc_html__( 'Sandwichtree ist aktiv. Leg mit einem Klick Kategorien, Beispielprodukte und alle Seiten an.', 'sandwichtree' ),
		esc_url( admin_url( 'themes.php?page=sandwichtree-setup' ) ),
		esc_html__( 'Zur Einrichtung', 'sandwichtree' )
	);
} );

/**
 * Admin-Seite.
 */
function sandwichtree_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$message = '';
	if ( isset( $_POST['sandwichtree_action'] ) && check_admin_referer( 'sandwichtree_setup' ) ) {
		$action = sanitize_key( wp_unslash( $_POST['sandwichtree_action'] ) );
		if ( 'import' === $action ) {
			$message = sandwichtree_run_setup( ! empty( $_POST['with_products'] ) );
		} elseif ( 'remove_demo' === $action ) {
			$message = sandwichtree_remove_demo_products();
		}
	}
	$has_wc = class_exists( 'WooCommerce' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Sandwichtree einrichten', 'sandwichtree' ); ?></h1>
		<?php if ( $message ) : ?>
			<div class="notice notice-success"><p><?php echo wp_kses_post( $message ); ?></p></div>
		<?php endif; ?>

		<?php if ( ! $has_wc ) : ?>
			<div class="notice notice-warning"><p><?php esc_html_e( 'Bitte zuerst das Plugin WooCommerce installieren und aktivieren.', 'sandwichtree' ); ?></p></div>
		<?php else : ?>
			<div class="card" style="max-width:720px">
				<h2><?php esc_html_e( 'Shop einrichten', 'sandwichtree' ); ?></h2>
				<p><?php esc_html_e( 'Legt an bzw. stellt ein:', 'sandwichtree' ); ?></p>
				<ul style="list-style:disc;padding-left:20px">
					<li><?php esc_html_e( 'Kategorien: Damen, Herren, Kids, Accessoires & Taschen, Schuhe (mit Unterkategorien)', 'sandwichtree' ); ?></li>
					<li><?php esc_html_e( 'Filterbare Eigenschaften: Marke, Größe, Zustand, Material, Farbe, Epoche', 'sandwichtree' ); ?></li>
					<li><?php esc_html_e( 'Seiten: Start, Über uns, Größen & Zustand, Versand & Rückgabe, FAQ, Kontakt, Impressum, Datenschutz, AGB, Widerruf', 'sandwichtree' ); ?></li>
					<li><?php esc_html_e( 'Menüs, Shop-Filter, Euro, Österreich als Standort, Bewertungen aus', 'sandwichtree' ); ?></li>
				</ul>
				<form method="post">
					<?php wp_nonce_field( 'sandwichtree_setup' ); ?>
					<input type="hidden" name="sandwichtree_action" value="import">
					<p><label><input type="checkbox" name="with_products" value="1" checked> <?php esc_html_e( '16 Beispielprodukte mit je 3–5 Platzhalterbildern anlegen', 'sandwichtree' ); ?></label></p>
					<p><button class="button button-primary button-hero"><?php esc_html_e( 'Jetzt einrichten', 'sandwichtree' ); ?></button></p>
					<p class="description"><?php esc_html_e( 'Kann gefahrlos mehrmals ausgeführt werden: Vorhandenes wird nicht doppelt angelegt. Dauert etwa eine Minute.', 'sandwichtree' ); ?></p>
				</form>
			</div>

			<div class="card" style="max-width:720px">
				<h2><?php esc_html_e( 'Beispielprodukte entfernen', 'sandwichtree' ); ?></h2>
				<p><?php esc_html_e( 'Löscht nur die Beispielprodukte und ihre Platzhalterbilder. Eigene Produkte, Seiten und Kategorien bleiben.', 'sandwichtree' ); ?></p>
				<form method="post" onsubmit="return confirm('<?php echo esc_js( __( 'Alle Beispielprodukte endgültig löschen?', 'sandwichtree' ) ); ?>');">
					<?php wp_nonce_field( 'sandwichtree_setup' ); ?>
					<input type="hidden" name="sandwichtree_action" value="remove_demo">
					<p><button class="button"><?php esc_html_e( 'Beispielprodukte löschen', 'sandwichtree' ); ?></button></p>
				</form>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Kategorien: Name => Unterkategorien.
 *
 * @return array
 */
function sandwichtree_demo_categories() {
	return array(
		'Damen'                 => array( 'Kleider', 'Jacken & Mäntel', 'Oberteile', 'Hosen & Röcke' ),
		'Herren'                => array( 'Jacken', 'Hemden', 'Pullover', 'Hosen' ),
		'Kids'                  => array(),
		'Accessoires & Taschen' => array(),
		'Schuhe'                => array(),
	);
}

/**
 * Beispielprodukte.
 *
 * @return array
 */
function sandwichtree_demo_products() {
	return array(
		array( 'Wollmantel mit Fischgrätmuster', 'Damen/Jacken & Mäntel', 89, 0, 'Maison Lune', '38', 'Sehr gut', 'Wolle', 'Braun', '80er', 'coat', 5, "Achsel zu Achsel: 54 cm\nGesamtlänge: 98 cm\nÄrmellänge: 60 cm", '' ),
		array( 'Midikleid mit Blumendruck', 'Damen/Kleider', 45, 0, 'Ostwind', '36', 'Sehr gut', 'Viskose', 'Grün', '90er', 'dress', 4, "Achsel zu Achsel: 44 cm\nTaille: 35 cm\nGesamtlänge: 118 cm", '' ),
		array( 'Strickpullover Zopfmuster', 'Damen/Oberteile', 38, 29, 'Nordlicht', 'M', 'Gut', 'Wolle-Mix', 'Creme', '90er', 'top', 3, "Achsel zu Achsel: 56 cm\nGesamtlänge: 64 cm", 'Leichte Knötchenbildung unter den Armen, siehe Foto 3.' ),
		array( 'High-Waist-Jeans', 'Damen/Hosen & Röcke', 49, 0, 'Kettner Denim', 'W28 L30', 'Sehr gut', 'Baumwolle', 'Blau', '90er', 'pants', 4, "Bund flach: 36 cm\nLeibhöhe: 31 cm\nInnenbein: 76 cm", '' ),
		array( 'Seidenbluse mit Schluppe', 'Damen/Oberteile', 42, 0, 'Alba', '38', 'Neu ohne Etikett', 'Seide', 'Bordeaux', '70er', 'top', 4, "Achsel zu Achsel: 50 cm\nGesamtlänge: 66 cm", '' ),
		array( 'Lederjacke im Bikerstil', 'Herren/Jacken', 120, 0, 'Kettner', 'L', 'Gut', 'Leder', 'Schwarz', '80er', 'coat', 5, "Achsel zu Achsel: 58 cm\nGesamtlänge: 70 cm\nÄrmellänge: 65 cm", 'Patina am Kragen und an den Ärmelenden, typisch für getragenes Leder.' ),
		array( 'Flanellhemd kariert', 'Herren/Hemden', 32, 0, 'Ostwind', 'L', 'Sehr gut', 'Baumwolle', 'Rot', '90er', 'top', 3, "Achsel zu Achsel: 60 cm\nGesamtlänge: 78 cm", '' ),
		array( 'Strickpullover mit Rundhals', 'Herren/Pullover', 44, 0, 'Nordlicht', 'XL', 'Sehr gut', 'Lammwolle', 'Grau', '80er', 'top', 4, "Achsel zu Achsel: 62 cm\nGesamtlänge: 72 cm", '' ),
		array( 'Cordhose mit geradem Bein', 'Herren/Hosen', 39, 0, 'Kettner', 'W32 L32', 'Gut', 'Cord', 'Beige', '70er', 'pants', 3, "Bund flach: 41 cm\nInnenbein: 81 cm", 'Cord an den Knien leicht abgetragen.' ),
		array( 'Kinder-Regenjacke', 'Kids', 24, 0, 'Alba Kids', '110/116', 'Sehr gut', 'Polyester', 'Gelb', '2000er', 'kids', 3, "Achsel zu Achsel: 38 cm\nGesamtlänge: 46 cm", '' ),
		array( 'Strickjacke für Kinder', 'Kids', 18, 0, 'Nordlicht', '98/104', 'Gut', 'Baumwolle', 'Blau', '90er', 'kids', 3, "Achsel zu Achsel: 33 cm\nGesamtlänge: 38 cm", '' ),
		array( 'Lederhandtasche mit Henkel', 'Accessoires & Taschen', 65, 0, 'Maison Lune', 'Einheitsgröße', 'Sehr gut', 'Leder', 'Cognac', '70er', 'bag', 5, "Breite: 30 cm\nHöhe: 22 cm\nTiefe: 10 cm", '' ),
		array( 'Umhängetasche aus Canvas', 'Accessoires & Taschen', 28, 0, 'Ostwind', 'Einheitsgröße', 'Gut', 'Canvas', 'Oliv', '90er', 'bag', 3, "Breite: 34 cm\nHöhe: 26 cm", 'Leichte Verfärbung am Riemen.' ),
		array( 'Chelsea Boots', 'Schuhe', 75, 0, 'Kettner', '39', 'Gut', 'Leder', 'Braun', '90er', 'shoe', 4, "Innensohle: 25,5 cm\nSchafthöhe: 14 cm", 'Absätze etwas abgelaufen, Sohlen in gutem Zustand.' ),
		array( 'Ledersneaker in Weiß', 'Schuhe', 48, 0, 'Alba', '42', 'Sehr gut', 'Leder', 'Weiß', '2000er', 'shoe', 3, "Innensohle: 27 cm", '' ),
		array( 'Jeansjacke mit Lammfellkragen', 'Damen/Jacken & Mäntel', 79, 0, 'Kettner Denim', 'S', 'Sehr gut', 'Baumwolle', 'Blau', '80er', 'coat', 4, "Achsel zu Achsel: 50 cm\nGesamtlänge: 58 cm\nÄrmellänge: 58 cm", '', true ),
	);
}

/**
 * Platzhalter-Seiten: slug => [Titel, Inhalt].
 *
 * @return array
 */
function sandwichtree_demo_pages() {
	$note = function ( $text ) {
		return '<!-- wp:paragraph {"className":"placeholder-note"} --><p class="placeholder-note">' . $text . '</p><!-- /wp:paragraph -->';
	};
	$p    = function ( $text ) {
		return '<!-- wp:paragraph --><p>' . $text . '</p><!-- /wp:paragraph -->';
	};
	$h    = function ( $text, $anchor = '' ) {
		$attr = $anchor ? ' {"anchor":"' . $anchor . '"}' : '';
		$id   = $anchor ? ' id="' . $anchor . '"' : '';
		return '<!-- wp:heading' . $attr . ' --><h2 class="wp-block-heading"' . $id . '>' . $text . '</h2><!-- /wp:heading -->';
	};
	$list = function ( $items ) {
		$out = '<!-- wp:list --><ul class="wp-block-list">';
		foreach ( $items as $item ) {
			$out .= '<!-- wp:list-item --><li>' . $item . '</li><!-- /wp:list-item -->';
		}
		return $out . '</ul><!-- /wp:list -->';
	};
	$legal = $note( 'Entwurf: Dieser Rechtstext muss noch erstellt bzw. geprüft werden (z. B. WKO-Vorlage, Händlerbund oder Anwaltskanzlei). Nicht ungeprüft veröffentlichen.' );
	$plan  = 'In Planung, noch nicht freigegeben: ';

	return array(
		'start'             => array( 'Start', '' ),

		'ueber-uns'         => array( 'Über uns',
			$p( 'Wir sind ein junges, innovatives und kreatives Paar mit einer gemeinsamen Leidenschaft: Mode, die eine zweite Chance verdient. ♻️' )
			. $p( 'Mit Sandwich Tree möchten wir hochwertige Kleidung nachhaltig weitergeben und zeigen, dass bewusster Konsum nicht langweilig sein muss.' )
			. $p( 'Dabei sind uns vier Dinge besonders wichtig: Nachhaltigkeit, Sauberkeit, Offenheit und Klarheit. Jedes Kleidungsstück soll sorgfältig ausgewählt, ehrlich beschrieben und sauber bei euch ankommen.' )
			. $p( 'Wir glauben daran, dass gute Kleidung nicht nach einmaligem Tragen aussortiert werden muss. Gemeinsam möchten wir Mode wieder in den Kreislauf bringen – mit Herz, Kreativität und einem offenen Blick für neue Ideen. 🌱' )
			. $p( '<strong>Sandwich Tree – Kleidung mit einer zweiten Chance.</strong> ♻️' ) ),

		'groessen-zustand'  => array( 'Größen & Zustand',
			$h( 'Maße' )
			. $p( 'Die Maße jedes Teils stehen in der Artikelbeschreibung. Am einfachsten vergleichst du sie mit einem Kleidungsstück aus deinem Schrank, das dir gut passt. Ältere Größenangaben fallen oft anders aus als heutige, verlass dich deshalb lieber auf die Maße als auf das Etikett.' )
			. $h( 'Zustand', 'zustand' )
			. $note( 'Entwurf: Die Stufen orientieren sich an Vinted. Die genauen Formulierungen werden noch abgeglichen.' )
			. $list( array(
				'<strong>Neu mit Etikett:</strong> ungetragen, das Originaletikett ist noch dran.',
				'<strong>Neu ohne Etikett:</strong> ungetragen oder nur anprobiert, ohne Etikett.',
				'<strong>Sehr gut:</strong> wenig getragen, kaum sichtbare Spuren.',
				'<strong>Gut:</strong> getragen, mit leichten Gebrauchsspuren.',
				'<strong>Zufriedenstellend:</strong> deutlich getragen, mit sichtbaren Spuren.',
			) )
			. $h( 'Mängel' )
			. $p( 'Unabhängig vom Zustand beschreiben wir jeden konkreten Mangel in der Artikelbeschreibung und zeigen ihn zusätzlich auf einem eigenen Foto. Wir bearbeiten keine Mängel aus den Bildern heraus.' ) ),

		'versand-rueckgabe' => array( 'Versand & Rückgabe',
			$note( 'Entwurf: Versanddienstleister, Versandkosten unterhalb von 60 € und Lieferzeiten stehen noch nicht fest. Rechtliche Formulierungen werden noch geprüft.' )
			. $h( 'Versand' )
			. $p( 'Wir liefern innerhalb Europas, insbesondere nach Österreich, Deutschland, Italien, Frankreich und in die Nachbarländer Österreichs.' )
			. $list( array(
				'Ab 60 € Einkaufswert versenden wir kostenlos nach Österreich, Deutschland, Italien und Frankreich.',
				'Für andere Lieferländer gelten gesonderte Versandkosten, die du an der Kasse siehst.',
				'Lieferzeit: folgt.',
			) )
			. $h( 'Rücktritt' )
			. $p( 'Du kannst innerhalb von 14 Tagen nach Erhalt ohne Angabe von Gründen vom Kauf zurücktreten. Danach hast du weitere 14 Tage Zeit, das Teil zurückzuschicken. Schreib uns dazu einfach eine E-Mail.' )
			. $p( 'Bei einem Rücktritt trägst du die Kosten für die Rücksendung. Einen allgemeinen Umtausch bieten wir derzeit nicht an.' )
			. $h( 'Wenn etwas nicht stimmt' )
			. $p( 'Hat ein Teil einen Mangel, den wir nicht beschrieben haben, melde dich bitte bei uns. Solche Fälle klären wir persönlich und unkompliziert, deine gesetzlichen Ansprüche bleiben selbstverständlich unberührt.' ) ),

		'faq'               => array( 'Häufige Fragen',
			$h( 'Wo finde ich die Maße?' )
			. $p( 'In der Beschreibung jedes Teils. Mehr dazu unter <a href="../groessen-zustand/">Größen & Zustand</a>.' )
			. $h( 'Wie lange dauert die Lieferung?' )
			. $note( 'Folgt, sobald Versanddienstleister und Abläufe feststehen.' )
			. $h( 'Was bedeuten die Zustandsangaben?' )
			. $p( 'Wir verwenden fünf Stufen von „Neu mit Etikett“ bis „Zufriedenstellend“. Mängel beschreiben und fotografieren wir immer zusätzlich.' )
			. $h( 'Gibt es ein Teil mehrmals?' )
			. $p( 'Nein. Jedes Teil ist ein Einzelstück. Ist es verkauft, ist es weg.' )
			. $h( 'Wie kann ich bezahlen?' )
			. $note( 'Geplant: Kartenzahlung, PayPal, Apple Pay und weitere gängige Zahlungsarten. Die endgültige Auswahl folgt.' )
			. $h( 'Kann ich zurückgeben?' )
			. $p( 'Ja, innerhalb von 14 Tagen nach Erhalt. Alle Details stehen unter <a href="../versand-rueckgabe/">Versand & Rückgabe</a>.' )
			. $h( 'Gibt es Rabatte?' )
			. $note( $plan . 'Rabatt für Instagram-Follower und ein Abo-Modell. Bedingungen folgen.' )
			. $h( 'Repariert ihr Kleidung?' )
			. $note( $plan . 'Reparatur- und Pflegeservice für bei uns gekaufte Teile (kleine Risse, lose Nähte, Fussel). Ablauf: Fotos schicken, wir prüfen und vereinbaren Leistung und Preis vorab.' )
			. $h( 'Nehmt ihr Kleidung zurück?' )
			. $note( $plan . 'Rücknahme gut erhaltener Kleidung gegen einen Rabatt auf den nächsten Einkauf, nach vorheriger Absprache. Bedingungen folgen.' ) ),

		'kontakt'           => array( 'Kontakt',
			$p( 'Am liebsten beantworten wir deine Fragen per E-Mail. Bei konkreten Anliegen rufen wir dich auch gerne an.' )
			. $p( 'E-Mail: <a href="mailto:info@sandwichtree.com">info@sandwichtree.com</a><br>Instagram: <a href="https://www.instagram.com/sandwichtree2026/">@sandwichtree2026</a>' )
			. $p( 'Sandwich Tree<br>Seefeldgasse 11<br>7100 Neusiedl am See<br>Österreich' )
			. $note( 'Offen: E-Mail-Adresse bestätigen, öffentliche Telefonnummer festlegen, Kontaktformular (z. B. Contact Form 7) einbinden.' ) ),

		'impressum'         => array( 'Impressum',
			$legal
			. $p( 'Sandwich Tree<br>Inhaber: Alexander Janovszky und Margarita Werzowa<br>Rechtsform und Firmenwortlaut: folgen' )
			. $p( 'Seefeldgasse 11<br>7100 Neusiedl am See<br>Österreich' )
			. $p( 'E-Mail: info@sandwichtree.com<br>Telefon: folgt' )
			. $p( 'UID-Nummer: folgt<br>Firmenbuchnummer und -gericht: folgen<br>Gewerbe und Gewerbebehörde: folgen<br>Kammerzugehörigkeit: folgt' ) ),
		'datenschutz'       => array( 'Datenschutzerklärung', $legal ),
		'agb'               => array( 'AGB', $legal ),
		'widerruf'          => array( 'Widerrufsbelehrung', $legal ),
	);
}

/**
 * Seite nach Slug holen oder anlegen.
 *
 * @param string $slug    Slug.
 * @param string $title   Titel.
 * @param string $content Inhalt.
 * @return int
 */
function sandwichtree_ensure_page( $slug, $title, $content ) {
	$page = get_page_by_path( $slug );
	if ( $page ) {
		return (int) $page->ID;
	}
	return (int) wp_insert_post( array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_content' => $content,
	) );
}

/**
 * Begriff einer Taxonomie holen oder anlegen.
 *
 * @param string $name     Name.
 * @param string $taxonomy Taxonomie.
 * @param int    $parent   Eltern-ID.
 * @return int
 */
function sandwichtree_ensure_term( $name, $taxonomy, $parent = 0 ) {
	$existing = term_exists( $name, $taxonomy, $parent ?: null );
	if ( $existing ) {
		return (int) ( is_array( $existing ) ? $existing['term_id'] : $existing );
	}
	$created = wp_insert_term( $name, $taxonomy, array( 'parent' => $parent ) );
	return is_wp_error( $created ) ? 0 : (int) $created['term_id'];
}

/**
 * Einrichtung ausführen.
 *
 * @param bool $with_products Beispielprodukte anlegen.
 * @return string Meldung.
 */
function sandwichtree_run_setup( $with_products ) {
	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 300 ); // phpcs:ignore
	}
	wp_raise_memory_limit( 'admin' );

	// 1. Grundeinstellungen.
	update_option( 'woocommerce_currency', 'EUR' );
	update_option( 'woocommerce_currency_pos', 'right_space' );
	update_option( 'woocommerce_price_thousand_sep', '.' );
	update_option( 'woocommerce_price_decimal_sep', ',' );
	if ( ! get_option( 'woocommerce_default_country' ) || 'US:CA' === get_option( 'woocommerce_default_country' ) ) {
		update_option( 'woocommerce_default_country', 'AT' );
	}
	update_option( 'woocommerce_enable_reviews', 'no' );
	update_option( 'woocommerce_manage_stock', 'yes' );
	update_option( 'woocommerce_hold_stock_minutes', '30' );
	update_option( 'woocommerce_notify_no_stock', 'yes' );
	update_option( 'woocommerce_enable_guest_checkout', 'yes' );
	update_option( 'timezone_string', 'Europe/Vienna' );
	update_option( 'date_format', 'j. F Y' );
	update_option( 'time_format', 'H:i' );
	update_option( 'start_of_week', 1 );
	update_option( 'permalink_structure', '/%postname%/' );

	$name = get_option( 'blogname' );
	if ( '' === $name || in_array( $name, array( 'My WordPress Site', 'Meine WordPress-Website', 'Sandwichtree' ), true ) ) {
		update_option( 'blogname', 'Sandwich Tree' );
	}

	// 2. Eigenschaften (Attribute).
	foreach ( sandwichtree_detail_attributes() as $slug => $label ) {
		if ( ! wc_attribute_taxonomy_id_by_name( $slug ) ) {
			wc_create_attribute( array(
				'name'         => $label,
				'slug'         => $slug,
				'type'         => 'select',
				'order_by'     => 'menu_order',
				'has_archives' => false,
			) );
		}
		$taxonomy = wc_attribute_taxonomy_name( $slug );
		if ( ! taxonomy_exists( $taxonomy ) ) {
			register_taxonomy( $taxonomy, array( 'product' ), array( 'hierarchical' => false, 'show_ui' => false, 'query_var' => true, 'rewrite' => false ) );
		}
	}
	// Zustände in sinnvoller Reihenfolge anlegen.
	$order = 0;
	foreach ( array( 'Neu mit Etikett', 'Neu ohne Etikett', 'Sehr gut', 'Gut', 'Zufriedenstellend' ) as $state ) {
		$term_id = sandwichtree_ensure_term( $state, 'pa_zustand' );
		if ( $term_id ) {
			update_term_meta( $term_id, 'order', $order++ );
		}
	}

	// 3. Kategorien.
	$cat_ids = array();
	$pos     = 0;
	foreach ( sandwichtree_demo_categories() as $parent => $children ) {
		$parent_id          = sandwichtree_ensure_term( $parent, 'product_cat' );
		$cat_ids[ $parent ] = $parent_id;
		update_term_meta( $parent_id, 'order', $pos++ );
		foreach ( $children as $child ) {
			$cat_ids[ $parent . '/' . $child ] = sandwichtree_ensure_term( $child, 'product_cat', $parent_id );
		}
	}

	// 4. Seiten.
	$page_ids = array();
	foreach ( sandwichtree_demo_pages() as $slug => $page ) {
		$page_ids[ $slug ] = sandwichtree_ensure_page( $slug, $page[0], $page[1] );
	}
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $page_ids['start'] );
	if ( ! empty( $page_ids['datenschutz'] ) ) {
		update_option( 'wp_page_for_privacy_policy', $page_ids['datenschutz'] );
	}
	if ( ! empty( $page_ids['agb'] ) ) {
		update_option( 'woocommerce_terms_page_id', $page_ids['agb'] );
	}

	// 5. Produkte.
	$created = 0;
	if ( $with_products ) {
		$created = sandwichtree_create_demo_products( $cat_ids );
	}

	// 6. Kategorie-Bilder aus dem ersten Produkt der Kategorie.
	foreach ( array_keys( sandwichtree_demo_categories() ) as $parent ) {
		$term_id = $cat_ids[ $parent ];
		if ( ! $term_id || get_term_meta( $term_id, 'thumbnail_id', true ) ) {
			continue;
		}
		$ids = wc_get_products( array( 'category' => array( get_term( $term_id )->slug ), 'limit' => 1, 'return' => 'ids', 'orderby' => 'date', 'order' => 'ASC' ) );
		if ( $ids ) {
			$thumb = get_post_thumbnail_id( $ids[0] );
			if ( $thumb ) {
				update_term_meta( $term_id, 'thumbnail_id', $thumb );
			}
		}
	}

	// 7. Versand (nur wenn noch keine Versandzonen existieren).
	sandwichtree_setup_shipping();

	// 8. Menüs.
	sandwichtree_setup_menus( $cat_ids, $page_ids );

	// 9. Shop-Filter.
	sandwichtree_setup_filter_widgets();

	flush_rewrite_rules();
	update_option( 'sandwichtree_setup_done', 1 );

	$msg = __( 'Fertig! Kategorien, Eigenschaften, Seiten, Menüs und Filter sind eingerichtet.', 'sandwichtree' );
	if ( $with_products ) {
		/* translators: %d: Anzahl Produkte */
		$msg .= ' ' . sprintf( __( '%d Beispielprodukte wurden neu angelegt.', 'sandwichtree' ), $created );
	}
	$msg .= ' <a href="' . esc_url( home_url( '/' ) ) . '">' . __( 'Shop ansehen', 'sandwichtree' ) . '</a>';
	return $msg;
}

/**
 * Beispielprodukte anlegen.
 *
 * @param array $cat_ids Kategorie-IDs.
 * @return int Anzahl neu angelegter Produkte.
 */
function sandwichtree_create_demo_products( $cat_ids ) {
	$created = 0;
	$i       = 0;
	foreach ( sandwichtree_demo_products() as $row ) {
		list( $name, $cat, $price, $sale, $brand, $size, $state, $material, $color, $era, $shape, $img_count, $measurements, $flaws ) = $row;
		$sold = ! empty( $row[14] );
		$i++;

		$existing = get_posts( array( 'post_type' => 'product', 'title' => $name, 'post_status' => 'any', 'fields' => 'ids', 'numberposts' => 1 ) );
		if ( $existing ) {
			continue;
		}

		$product = new WC_Product_Simple();
		$product->set_name( $name );
		$product->set_status( 'publish' );
		$product->set_regular_price( (string) $price );
		if ( $sale ) {
			$product->set_sale_price( (string) $sale );
		}
		$product->set_short_description( sprintf( 'Platzhalter: Ein, zwei Sätze zum Teil, z. B. Schnitt, Passform und wozu es gut passt. Material: %s, aus den %s.', $material, $era ) );
		$product->set_description( 'Platzhalter für die ausführliche Beschreibung: Wie fühlt sich der Stoff an, wie fällt das Teil, wie würdest du es kombinieren? Gebrauchsspuren gehören in das Feld „Gebrauchsspuren“ im Reiter „Vintage-Details“.' );
		$product->set_manage_stock( true );
		$product->set_stock_quantity( $sold ? 0 : 1 );
		$product->set_stock_status( $sold ? 'outofstock' : 'instock' );
		$product->set_sold_individually( true );
		$product->set_reviews_allowed( false );
		$product->set_sku( sprintf( 'DEMO-%02d', $i ) );
		$product->set_weight( in_array( $shape, array( 'coat', 'shoe' ), true ) ? '1.2' : '0.5' );

		$cats = array();
		if ( ! empty( $cat_ids[ $cat ] ) ) {
			$cats[] = $cat_ids[ $cat ];
		}
		$top = strtok( $cat, '/' );
		if ( ! empty( $cat_ids[ $top ] ) ) {
			$cats[] = $cat_ids[ $top ];
		}
		$product->set_category_ids( array_unique( $cats ) );

		$attributes = array();
		$values     = array(
			'marke'    => $brand,
			'groesse'  => $size,
			'zustand'  => $state,
			'material' => $material,
			'farbe'    => $color,
			'epoche'   => $era,
		);
		$position   = 0;
		foreach ( $values as $slug => $value ) {
			$taxonomy = wc_attribute_taxonomy_name( $slug );
			$term_id  = sandwichtree_ensure_term( $value, $taxonomy );
			if ( ! $term_id ) {
				continue;
			}
			$attribute = new WC_Product_Attribute();
			$attribute->set_id( wc_attribute_taxonomy_id_by_name( $slug ) );
			$attribute->set_name( $taxonomy );
			$attribute->set_options( array( $term_id ) );
			$attribute->set_position( $position++ );
			$attribute->set_visible( true );
			$attribute->set_variation( false );
			$attributes[] = $attribute;
		}
		$product->set_attributes( $attributes );

		$product->update_meta_data( '_st_measurements', $measurements );
		$product->update_meta_data( '_st_flaws', $flaws );
		$product->update_meta_data( '_st_demo', 1 );
		if ( 0 === $i % 4 ) {
			$product->update_meta_data( '_st_on_vinted', 'yes' );
		}
		$product_id = $product->save();

		// Bilder: 3–5 Platzhalter je Produkt.
		$image_ids = array();
		for ( $n = 1; $n <= $img_count; $n++ ) {
			$labels      = array( 1 => 'vorne', 2 => 'hinten', 3 => 'Detail', 4 => 'Etikett', 5 => 'Stimmung' );
			$image_ids[] = sandwichtree_make_placeholder( $shape, $n, $img_count, $i, $name . ' – ' . $labels[ $n ], $product_id, $color );
		}
		$image_ids = array_values( array_filter( $image_ids ) );
		if ( $image_ids ) {
			$product->set_image_id( array_shift( $image_ids ) );
			$product->set_gallery_image_ids( $image_ids );
			$product->save();
		}

		// Etwas gestaffelte Datum-Werte, damit „Neu“ nicht bei allen erscheint.
		if ( $i > 6 ) {
			$date = gmdate( 'Y-m-d H:i:s', time() - ( $i * 3 * DAY_IN_SECONDS ) );
			wp_update_post( array( 'ID' => $product_id, 'post_date' => get_date_from_gmt( $date ), 'post_date_gmt' => $date ) );
		}
		$created++;
	}
	return $created;
}

/**
 * Menüs anlegen und zuweisen.
 *
 * @param array $cat_ids  Kategorien.
 * @param array $page_ids Seiten.
 */
function sandwichtree_setup_menus( $cat_ids, $page_ids ) {
	$locations = get_theme_mod( 'nav_menu_locations', array() );

	$make = function ( $name, $location, $items ) use ( &$locations ) {
		$menu = wp_get_nav_menu_object( $name );
		if ( $menu ) {
			$locations[ $location ] = $menu->term_id;
			return;
		}
		$menu_id = wp_create_nav_menu( $name );
		if ( is_wp_error( $menu_id ) ) {
			return;
		}
		foreach ( $items as $item ) {
			wp_update_nav_menu_item( $menu_id, 0, array_merge( array( 'menu-item-status' => 'publish' ), $item ) );
		}
		$locations[ $location ] = $menu_id;
	};

	// Hauptmenü: Shop (mit Kategorien als Untermenü), Neu, Über uns.
	$main_menu = wp_get_nav_menu_object( 'Hauptmenü' );
	if ( ! $main_menu ) {
		$menu_id = wp_create_nav_menu( 'Hauptmenü' );
		if ( ! is_wp_error( $menu_id ) ) {
			$shop_item = wp_update_nav_menu_item( $menu_id, 0, array(
				'menu-item-title'     => 'Shop',
				'menu-item-object-id' => wc_get_page_id( 'shop' ),
				'menu-item-object'    => 'page',
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			) );
			foreach ( array_keys( sandwichtree_demo_categories() ) as $parent ) {
				if ( ! empty( $cat_ids[ $parent ] ) ) {
					wp_update_nav_menu_item( $menu_id, 0, array(
						'menu-item-object-id' => $cat_ids[ $parent ],
						'menu-item-object'    => 'product_cat',
						'menu-item-type'      => 'taxonomy',
						'menu-item-parent-id' => $shop_item,
						'menu-item-status'    => 'publish',
					) );
				}
			}
			wp_update_nav_menu_item( $menu_id, 0, array(
				'menu-item-title'  => 'Neu',
				'menu-item-url'    => add_query_arg( 'orderby', 'date', wc_get_page_permalink( 'shop' ) ),
				'menu-item-type'   => 'custom',
				'menu-item-status' => 'publish',
			) );
			if ( ! empty( $page_ids['ueber-uns'] ) ) {
				wp_update_nav_menu_item( $menu_id, 0, array(
					'menu-item-object-id' => $page_ids['ueber-uns'],
					'menu-item-object'    => 'page',
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				) );
			}
			$locations['primary'] = $menu_id;
		}
	} else {
		$locations['primary'] = $main_menu->term_id;
	}

	$service = array();
	foreach ( array( 'ueber-uns', 'groessen-zustand', 'versand-rueckgabe', 'faq', 'kontakt' ) as $slug ) {
		if ( ! empty( $page_ids[ $slug ] ) ) {
			$service[] = array( 'menu-item-object-id' => $page_ids[ $slug ], 'menu-item-object' => 'page', 'menu-item-type' => 'post_type' );
		}
	}
	$make( 'Service', 'footer', $service );

	$legal = array();
	foreach ( array( 'impressum', 'datenschutz', 'agb', 'widerruf' ) as $slug ) {
		if ( ! empty( $page_ids[ $slug ] ) ) {
			$legal[] = array( 'menu-item-object-id' => $page_ids[ $slug ], 'menu-item-object' => 'page', 'menu-item-type' => 'post_type' );
		}
	}
	$make( 'Rechtliches', 'legal', $legal );

	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * Versandzonen Österreich und Deutschland mit Beispielpreisen
 * (4,90 € / ab 60 € gratis in AT, 7,90 € nach DE). Platzhalter – bitte anpassen.
 */
function sandwichtree_setup_shipping() {
	if ( ! class_exists( 'WC_Shipping_Zone' ) || WC_Shipping_Zones::get_zones() ) {
		return;
	}
	// Vorläufig laut Briefing: Gratisversand ab 60 € nach AT, DE, IT, FR.
	// Die Versandpreise darunter sind Platzhalter und müssen noch kalkuliert werden.
	$zones = array(
		array( 'Österreich, Deutschland, Italien, Frankreich', array( 'AT', 'DE', 'IT', 'FR' ), '6.90', '60' ),
		array( 'Nachbarländer', array( 'CH', 'LI', 'CZ', 'SK', 'HU', 'SI' ), '12.90', '' ),
	);
	$order = 0;
	foreach ( $zones as $z ) {
		$zone = new WC_Shipping_Zone();
		$zone->set_zone_name( $z[0] );
		$zone->set_zone_order( $order++ );
		foreach ( (array) $z[1] as $country ) {
			$zone->add_location( $country, 'country' );
		}
		$zone->save();

		$instance = $zone->add_shipping_method( 'flat_rate' );
		update_option( 'woocommerce_flat_rate_' . $instance . '_settings', array(
			'title'      => 'Paketversand',
			'tax_status' => 'none',
			'cost'       => $z[2],
		) );
		if ( $z[3] ) {
			$free = $zone->add_shipping_method( 'free_shipping' );
			update_option( 'woocommerce_free_shipping_' . $free . '_settings', array(
				'title'      => 'Kostenloser Versand',
				'requires'   => 'min_amount',
				'min_amount' => $z[3],
			) );
		}
	}
	update_option( 'woocommerce_ship_to_countries', 'specific' );
	update_option( 'woocommerce_specific_ship_to_countries', array( 'AT', 'DE', 'IT', 'FR', 'CH', 'LI', 'CZ', 'SK', 'HU', 'SI' ) );
}

/**
 * Filter (Größe, Marke, Zustand, Preis) in den Shop-Filter-Bereich legen.
 */
function sandwichtree_setup_filter_widgets() {
	$sidebars = get_option( 'sidebars_widgets', array() );
	if ( ! empty( $sidebars['shop-filters'] ) ) {
		return; // Bereits eingerichtet – nichts überschreiben.
	}
	$nav     = get_option( 'widget_woocommerce_layered_nav', array() );
	$price   = get_option( 'widget_woocommerce_price_filter', array() );
	$ids     = array();
	$next    = max( array_merge( array( 0 ), array_filter( array_keys( $nav ), 'is_int' ) ) ) + 1;
	$filters = array(
		'groesse' => 'Größe',
		'marke'   => 'Marke',
		'zustand' => 'Zustand',
		'farbe'   => 'Farbe',
	);
	foreach ( $filters as $slug => $title ) {
		$nav[ $next ] = array( 'title' => $title, 'attribute' => $slug, 'display_type' => 'list', 'query_type' => 'or' );
		$ids[]        = 'woocommerce_layered_nav-' . $next;
		$next++;
	}
	$nav['_multiwidget'] = 1;
	update_option( 'widget_woocommerce_layered_nav', $nav );

	$pnext           = max( array_merge( array( 0 ), array_filter( array_keys( $price ), 'is_int' ) ) ) + 1;
	$price[ $pnext ] = array( 'title' => 'Preis' );
	$price['_multiwidget'] = 1;
	update_option( 'widget_woocommerce_price_filter', $price );
	$ids[] = 'woocommerce_price_filter-' . $pnext;

	$sidebars['shop-filters'] = $ids;
	update_option( 'sidebars_widgets', $sidebars );
}

/**
 * Beispielprodukte und deren Bilder löschen.
 *
 * @return string
 */
function sandwichtree_remove_demo_products() {
	$ids   = get_posts( array( 'post_type' => 'product', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids', 'meta_key' => '_st_demo', 'meta_value' => 1 ) ); // phpcs:ignore
	$count = 0;
	foreach ( $ids as $id ) {
		$product = wc_get_product( $id );
		if ( $product ) {
			$product->delete( true );
			$count++;
		}
	}
	$images = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids', 'meta_key' => '_st_demo', 'meta_value' => 1 ) ); // phpcs:ignore
	foreach ( $images as $image_id ) {
		wp_delete_attachment( $image_id, true );
	}
	// Kategorie-Bilder, die auf gelöschte Platzhalter zeigten, zurücksetzen.
	foreach ( get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'fields' => 'ids' ) ) as $term_id ) {
		$thumb = (int) get_term_meta( $term_id, 'thumbnail_id', true );
		if ( $thumb && ! get_post( $thumb ) ) {
			delete_term_meta( $term_id, 'thumbnail_id' );
		}
	}
	/* translators: %d: Anzahl */
	return sprintf( __( '%d Beispielprodukte und ihre Platzhalterbilder wurden gelöscht.', 'sandwichtree' ), $count );
}
