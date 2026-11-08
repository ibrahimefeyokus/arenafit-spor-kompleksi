<?php
/**
 * Varsayılan liste şablonu (blog, arama, arşiv).
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

if ( is_search() ) {
	arenafit_page_hero( 'Arama', 'Arama sonuçları: ' . get_search_query() );
} elseif ( is_home() && ! is_front_page() ) {
	arenafit_page_hero( 'Blog', single_post_title( '', false ) );
} elseif ( is_archive() ) {
	arenafit_page_hero( 'Arşiv', wp_strip_all_tags( get_the_archive_title() ) );
} else {
	arenafit_page_hero( '', get_bloginfo( 'name' ) );
}
?>
<section class="af-section">
	<div class="af-container">
		<?php if ( have_posts() ) : ?>
			<div class="af-grid af-grid--3">
				<?php while ( have_posts() ) : ?>
					<?php the_post(); ?>
					<article <?php post_class( 'af-card af-post reveal' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="af-post__img" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'arenafit-card', array( 'loading' => 'lazy' ) ); ?></a>
						<?php endif; ?>
						<div class="af-post__body">
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
							<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
							<a class="af-link" href="<?php the_permalink(); ?>">Devamını oku <?php echo arenafit_icon( 'arrow' ); // phpcs:ignore ?></a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination( array( 'prev_text' => '&larr;', 'next_text' => '&rarr;' ) ); ?>
		<?php else : ?>
			<?php arenafit_empty( 'Aradığınız içerik bulunamadı.' ); ?>
			<p class="af-center"><a class="af-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">Ana sayfaya dön</a></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
