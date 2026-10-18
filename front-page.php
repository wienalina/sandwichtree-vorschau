<?php
/**
 * Startseite im Magazin-Layout:
 * Einstieg mit Bildern, Infozeile, Kategorien als große Liste, Laufband,
 * versetzte Reihe mit neuen Teilen, Fragen-Kacheln.
 *
 * @package Sandwichtree
 */

defined( 'ABSPATH' ) || exit;
get_header();

$has_wc    = class_exists( 'WooCommerce' );
$shop_url  = $has_wc ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$instagram = sandwichtree_opt( 'instagram_url' );
$vinted    = sandwichtree_opt( 'vinted_url' );

// Neueste verfügbare Teile – für die Reihe und als Bildquelle, solange keine eigenen Bilder gesetzt sind.
$fresh = $has_wc ? wc_get_products( array(
	'limit'        => 8,
	'orderby'      => 'date',
	'order'        => 'DESC',
	'status'       => 'publish',
	'stock_status' => 'instock',
) ) : array();

$pool = array();
foreach ( $fresh as $p ) {
	if ( $p->get_image_id() ) {
		$pool[] = (int) $p->get_image_id();
	}
}
$pick = function ( $setting, $index ) use ( $pool ) {
	$id = (int) sandwichtree_opt( $setting );
	if ( $id ) {
		return $id;
	}
	return isset( $pool[ $index ] ) ? $pool[ $index ] : 0;
};
?>

<section class="intro wrap" aria-labelledby="intro-title">
	<figure class="intro__main">
		<?php echo sandwichtree_image_or_placeholder( $pick( 'hero_image_1', 0 ), 'large', '' ); // phpcs:ignore ?>
	</figure>

	<div class="intro__text">
		<p class="label"><?php esc_html_e( 'Vintage & Second Hand', 'sandwichtree' ); ?></p>
		<h1 id="intro-title" class="intro__title"><?php echo esc_html( sandwichtree_opt( 'hero_title' ) ); ?></h1>
		<p class="intro__lead"><?php echo esc_html( sandwichtree_opt( 'hero_text' ) ); ?></p>
		<a class="arrow-link" href="<?php echo esc_url( add_query_arg( 'orderby', 'date', $shop_url ) ); ?>">
			<span><?php echo esc_html( sandwichtree_opt( 'hero_button' ) ); ?></span>
			<?php echo sandwichtree_icon( 'arrow' ); // phpcs:ignore ?>
		</a>
	</div>

	<figure class="intro__side">
		<?php echo sandwichtree_image_or_placeholder( $pick( 'hero_image_2', 1 ), 'sandwichtree-portrait', '' ); // phpcs:ignore ?>
		<?php if ( sandwichtree_opt( 'hero_caption' ) ) : ?>
			<figcaption><?php echo esc_html( sandwichtree_opt( 'hero_caption' ) ); ?></figcaption>
		<?php endif; ?>
	</figure>
</section>

<?php if ( $has_wc ) : ?>
	<?php
	$available = (int) ( new WP_Query( array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'meta_query'     => array( array( 'key' => '_stock_status', 'value' => 'instock' ) ), // phpcs:ignore
	) ) )->found_posts;
	?>
	<div class="infobar wrap">
		<p>
			<?php
			/* translators: %d: Anzahl verfügbarer Teile */
			printf( esc_html( _n( '%d Teil gerade da', '%d Teile gerade da', $available, 'sandwichtree' ) ), (int) $available );
			?>
		</p>
		<p class="infobar__center"><?php echo esc_html( sandwichtree_opt( 'footer_text' ) ); ?></p>
		<p class="infobar__links">
			<?php if ( $instagram ) : ?>
				<a href="<?php echo esc_url( $instagram ); ?>" rel="noopener" target="_blank"><?php esc_html_e( 'Instagram', 'sandwichtree' ); ?></a>
			<?php endif; ?>
			<?php if ( $vinted ) : ?>
				<a href="<?php echo esc_url( $vinted ); ?>" rel="noopener" target="_blank"><?php esc_html_e( 'Vinted', 'sandwichtree' ); ?></a>
			<?php endif; ?>
		</p>
	</div>

	<?php
	$cats = get_terms( array(
		'taxonomy'   => 'product_cat',
		'parent'     => 0,
		'hide_empty' => false,
		'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
		'orderby'    => 'term_order',
	) );
	?>
	<?php if ( ! is_wp_error( $cats ) && $cats ) : ?>
		<nav class="cat-index wrap" aria-labelledby="cats-title">
			<h2 id="cats-title" class="screen-reader-text"><?php esc_html_e( 'Kategorien', 'sandwichtree' ); ?></h2>
			<ul>
				<?php foreach ( $cats as $cat ) : ?>
					<li>
						<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?><sup><?php echo (int) $cat->count; ?></sup></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>
	<?php endif; ?>
<?php endif; ?>

<?php if ( sandwichtree_opt( 'marquee' ) ) : ?>
	<?php
	$house = '<svg class="marquee__mark" viewBox="0 0 60 64" aria-hidden="true" focusable="false"><path d="M30 2 L57 26 L3 26 Z M3 27 L57 27 L53 62 L7 62 Z M12 34 H48 M12 38 H48"/></svg>';
	$item  = '<span class="marquee__item">' . esc_html( sandwichtree_opt( 'marquee' ) ) . '</span>' . $house;
	?>
	<div class="marquee" aria-hidden="true">
		<div class="marquee__track">
			<?php
			// Zwei identische Hälften für ein nahtloses Laufband.
			for ( $half = 0; $half < 2; $half++ ) {
				echo '<div class="marquee__half">' . str_repeat( $item, 6 ) . '</div>'; // phpcs:ignore
			}
			?>
		</div>
	</div>
<?php endif; ?>

<?php if ( $fresh ) : ?>
	<section class="fresh" aria-labelledby="fresh-title">
		<div class="wrap fresh__head">
			<h2 id="fresh-title" class="section-title"><?php esc_html_e( 'Neu eingetroffen', 'sandwichtree' ); ?></h2>
			<a class="arrow-link" href="<?php echo esc_url( add_query_arg( 'orderby', 'date', $shop_url ) ); ?>">
				<span><?php esc_html_e( 'Alle neuen Teile', 'sandwichtree' ); ?></span>
				<?php echo sandwichtree_icon( 'arrow' ); // phpcs:ignore ?>
			</a>
		</div>
		<ul class="fresh__row">
			<?php foreach ( $fresh as $p ) : ?>
				<?php $brand = sandwichtree_attr( $p, 'marke' ); ?>
				<li class="fresh__item">
					<a href="<?php echo esc_url( $p->get_permalink() ); ?>">
						<span class="fresh__label"><?php echo $brand ? esc_html( $brand ) : '&nbsp;'; ?></span>
						<?php echo $p->get_image( 'woocommerce_thumbnail', array( 'class' => 'fresh__image', 'loading' => 'lazy' ) ); // phpcs:ignore ?>
						<span class="fresh__name"><?php echo esc_html( $p->get_name() ); ?></span>
						<span class="fresh__price"><?php echo wp_kses_post( $p->get_price_html() ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>
<?php endif; ?>

<?php
// Fragen-Kacheln, abwechselnd mit Fotos.
$tile_links = array(
	1 => get_page_by_path( 'groessen-zustand' ) ? get_permalink( get_page_by_path( 'groessen-zustand' ) ) : $shop_url,
	2 => get_page_by_path( 'versand-rueckgabe' ) ? get_permalink( get_page_by_path( 'versand-rueckgabe' ) ) : $shop_url,
	3 => $shop_url,
);
$tile_photos = array();
foreach ( array_slice( $fresh, 2 ) as $p ) {
	$gallery       = $p->get_gallery_image_ids();
	$tile_photos[] = $gallery ? (int) $gallery[0] : (int) $p->get_image_id();
}
$custom_3 = (int) sandwichtree_opt( 'hero_image_3' );
if ( $custom_3 ) {
	array_unshift( $tile_photos, $custom_3 );
}
$photo = function ( $i ) use ( $tile_photos ) {
	$id = isset( $tile_photos[ $i ] ) ? $tile_photos[ $i ] : 0;
	return '<div class="tile tile--photo">' . sandwichtree_image_or_placeholder( $id, 'sandwichtree-portrait', '' ) . '</div>';
};
$tile = function ( $i, $tone ) use ( $tile_links ) {
	$title = sandwichtree_opt( "usp_{$i}_title" );
	if ( ! $title ) {
		return '';
	}
	return sprintf(
		'<a class="tile tile--%s" href="%s"><span class="tile__q">%s</span><span class="tile__a">%s</span>%s</a>',
		esc_attr( $tone ),
		esc_url( $tile_links[ $i ] ),
		esc_html( $title ),
		esc_html( sandwichtree_opt( "usp_{$i}_text" ) ),
		sandwichtree_icon( 'arrow' )
	);
};
?>
<section class="tiles wrap" aria-label="<?php esc_attr_e( 'Häufige Fragen', 'sandwichtree' ); ?>">
	<?php
	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Teile oben bereits escaped.
	echo $tile( 1, 'mist' );
	echo $photo( 0 );
	echo $tile( 2, 'olive' );
	echo $photo( 1 );
	echo $photo( 2 );
	echo $tile( 3, 'copper' );
	echo $photo( 3 );
	if ( $instagram ) {
		printf(
			'<a class="tile tile--ink" href="%s" rel="noopener" target="_blank"><span class="tile__q">%s</span><span class="tile__a">%s</span>%s</a>',
			esc_url( $instagram ),
			esc_html( sandwichtree_opt( 'instagram_handle' ) ? sandwichtree_opt( 'instagram_handle' ) : __( 'Instagram', 'sandwichtree' ) ),
			esc_html__( 'Neue Teile zeigen wir zuerst auf Instagram, bevor sie hier online gehen.', 'sandwichtree' ),
			sandwichtree_icon( 'arrow' )
		);
	} else {
		echo $photo( 4 );
	}
	// phpcs:enable
	?>
</section>

<?php
$shortcode = trim( (string) sandwichtree_opt( 'instagram_shortcode' ) );
if ( $shortcode ) {
	echo '<section class="wrap feed">' . do_shortcode( $shortcode ) . '</section>'; // phpcs:ignore
}

// Inhalt der als Startseite gewählten Seite (falls vorhanden) zusätzlich anzeigen.
if ( is_page() ) {
	while ( have_posts() ) {
		the_post();
		if ( trim( get_the_content() ) ) {
			echo '<section class="wrap page-content prose">';
			the_content();
			echo '</section>';
		}
	}
}

get_footer();
