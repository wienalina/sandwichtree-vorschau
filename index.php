<?php
/**
 * Standard-Template (Blog, Archive, Suche).
 *
 * @package Sandwichtree
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<div class="wrap page-head">
	<h1 class="page-title">
		<?php
		if ( is_search() ) {
			/* translators: %s: Suchbegriff */
			printf( esc_html__( 'Suche: %s', 'sandwichtree' ), esc_html( get_search_query() ) );
		} elseif ( is_archive() ) {
			echo wp_kses_post( get_the_archive_title() );
		} elseif ( is_home() && ! is_front_page() ) {
			echo esc_html( get_the_title( (int) get_option( 'page_for_posts' ) ) );
		} else {
			esc_html_e( 'Neuigkeiten', 'sandwichtree' );
		}
		?>
	</h1>
</div>

<div class="wrap post-list">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class( 'post-teaser' ); ?>>
				<?php if ( has_post_thumbnail() ) : ?>
					<a class="post-teaser__image" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'sandwichtree-portrait' ); ?></a>
				<?php endif; ?>
				<h2 class="post-teaser__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<div class="post-teaser__excerpt"><?php the_excerpt(); ?></div>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination( array( 'prev_text' => __( 'Zurück', 'sandwichtree' ), 'next_text' => __( 'Weiter', 'sandwichtree' ) ) ); ?>
	<?php else : ?>
		<div class="empty-state">
			<p><?php esc_html_e( 'Dazu haben wir nichts gefunden. Probier einen anderen Suchbegriff oder schau bei allen Teilen vorbei.', 'sandwichtree' ); ?></p>
			<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
				<a class="button" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Alle Teile ansehen', 'sandwichtree' ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
<?php
get_footer();
