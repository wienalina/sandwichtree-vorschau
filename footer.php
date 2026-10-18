<?php
/**
 * Fußbereich mit großem Schriftzug.
 *
 * @package Sandwichtree
 */

defined( 'ABSPATH' ) || exit;
$instagram = sandwichtree_opt( 'instagram_url' );
$vinted    = sandwichtree_opt( 'vinted_url' );
$email     = sandwichtree_opt( 'contact_email' );
$address   = sandwichtree_opt( 'contact_address' );
?>
</main>

<footer class="site-footer">
	<div class="wrap site-footer__grid">
		<div class="site-footer__about">
			<img class="site-footer__tree" src="<?php echo esc_url( SANDWICHTREE_URI . '/assets/img/logo-tree.png' ); ?>" width="730" height="1014" alt="" loading="lazy">
			<div>
				<p class="site-footer__lead"><?php echo esc_html( sandwichtree_opt( 'footer_text' ) ); ?></p>
				<?php if ( $address ) : ?>
					<p><?php echo nl2br( esc_html( $address ) ); // phpcs:ignore ?></p>
				<?php endif; ?>
				<?php if ( $email ) : ?>
					<p><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></p>
				<?php endif; ?>
			</div>
		</div>

		<div>
			<h2 class="site-footer__heading"><?php esc_html_e( 'Service', 'sandwichtree' ); ?></h2>
			<?php
			if ( has_nav_menu( 'footer' ) ) {
				wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'menu_class' => 'footer__list', 'depth' => 1 ) );
			} else {
				sandwichtree_page_links( array( 'ueber-uns', 'groessen-zustand', 'versand-rueckgabe', 'faq', 'kontakt' ) );
			}
			?>
		</div>

		<div>
			<h2 class="site-footer__heading"><?php esc_html_e( 'Rechtliches', 'sandwichtree' ); ?></h2>
			<?php
			if ( has_nav_menu( 'legal' ) ) {
				wp_nav_menu( array( 'theme_location' => 'legal', 'container' => false, 'menu_class' => 'footer__list', 'depth' => 1 ) );
			} else {
				sandwichtree_page_links( array( 'impressum', 'datenschutz', 'agb', 'widerruf' ) );
			}
			?>
		</div>

		<div>
			<h2 class="site-footer__heading"><?php esc_html_e( 'Auch hier zu finden', 'sandwichtree' ); ?></h2>
			<ul class="footer__list">
				<?php if ( $instagram ) : ?>
					<li><a href="<?php echo esc_url( $instagram ); ?>" rel="noopener" target="_blank"><?php esc_html_e( 'Instagram', 'sandwichtree' ); ?></a></li>
				<?php endif; ?>
				<?php if ( $vinted ) : ?>
					<li><a href="<?php echo esc_url( $vinted ); ?>" rel="noopener" target="_blank"><?php esc_html_e( 'Vinted', 'sandwichtree' ); ?></a></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>

	<div class="wrap site-footer__mark" aria-hidden="true">
		<span class="site-footer__word"><?php bloginfo( 'name' ); ?></span>
	</div>

	<div class="wrap site-footer__bottom">
		<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>
		<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
			<p><?php esc_html_e( 'Alle Preise inkl. gesetzlicher USt., zzgl. Versand.', 'sandwichtree' ); ?></p>
		<?php endif; ?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
