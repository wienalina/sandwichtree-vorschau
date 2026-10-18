<?php
/**
 * Kopfbereich: Menü links, Logo mittig, Suche/Konto/Warenkorb rechts.
 *
 * @package Sandwichtree
 */

defined( 'ABSPATH' ) || exit;
$announcement = sandwichtree_opt( 'announcement' );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#content"><?php esc_html_e( 'Zum Inhalt springen', 'sandwichtree' ); ?></a>

<?php if ( $announcement ) : ?>
	<div class="announcement"><p><?php echo esc_html( $announcement ); ?></p></div>
<?php endif; ?>

<header class="site-header" id="site-header">
	<div class="site-header__inner wrap">
		<button class="icon-button nav-toggle" type="button" aria-controls="site-nav" aria-expanded="false">
			<span class="nav-toggle__open"><?php echo sandwichtree_icon( 'menu' ); // phpcs:ignore ?></span>
			<span class="nav-toggle__close"><?php echo sandwichtree_icon( 'close' ); // phpcs:ignore ?></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Menü', 'sandwichtree' ); ?></span>
		</button>

		<nav class="site-nav" id="site-nav" aria-label="<?php esc_attr_e( 'Hauptmenü', 'sandwichtree' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'nav__list',
				'depth'          => 2,
				'fallback_cb'    => 'sandwichtree_menu_fallback',
			) );
			?>
		</nav>

		<div class="site-brand"><?php sandwichtree_brand(); ?></div>

		<div class="site-actions">
			<button class="text-link search-toggle" type="button" aria-controls="site-search" aria-expanded="false">
				<span class="search-toggle__icon"><?php echo sandwichtree_icon( 'search' ); // phpcs:ignore ?></span>
				<span class="search-toggle__label"><?php esc_html_e( 'Suche', 'sandwichtree' ); ?></span>
			</button>
			<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
				<a class="text-link account-link" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'Konto', 'sandwichtree' ); ?></a>
				<?php sandwichtree_cart_link(); ?>
			<?php endif; ?>
		</div>
	</div>

	<div class="site-search" id="site-search" hidden>
		<div class="wrap">
			<?php get_search_form(); ?>
		</div>
	</div>
</header>

<main id="content" class="site-main" tabindex="-1">
