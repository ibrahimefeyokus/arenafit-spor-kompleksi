<?php
/**
 * Antrenör listesi (ve uzmanlık alanı arşivi).
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$af_terms   = get_terms(
	array(
		'taxonomy'   => 'af_specialty',
		'hide_empty' => true,
	)
);
$af_current = is_tax( 'af_specialty' ) ? get_queried_object_id() : 0;

arenafit_page_hero( 'Antrenörlerimiz', is_tax( 'af_specialty' ) ? single_term_title( '', false ) : 'Uzman ve belgeli antrenör kadromuz', 'Özgeçmişleri, sertifikaları ve uzmanlık alanlarını inceleyin; size uygun antrenörden online randevu alın.' );
?>
<section class="af-section">
	<div class="af-container">
		<?php if ( $af_terms && ! is_wp_error( $af_terms ) ) : ?>
			<nav class="af-filters" aria-label="Uzmanlık alanına göre filtrele">
				<a class="af-filter<?php echo $af_current ? '' : ' is-active'; ?>"<?php echo $af_current ? '' : ' aria-current="page"'; ?> href="<?php echo esc_url( get_post_type_archive_link( 'af_trainer' ) ); ?>">Tümü</a>
				<?php foreach ( $af_terms as $t ) : ?>
					<a class="af-filter<?php echo $af_current === $t->term_id ? ' is-active' : ''; ?>"<?php echo $af_current === $t->term_id ? ' aria-current="page"' : ''; ?> href="<?php echo esc_url( get_term_link( $t ) ); ?>"><?php echo esc_html( $t->name ); ?></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div class="af-search" role="search">
				<label for="af-tsearch" class="screen-reader-text">Antrenör ara</label>
				<span class="af-search__ic" aria-hidden="true"><svg class="af-i" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg></span>
				<input type="search" id="af-tsearch" data-af-search=".af-trainer" placeholder="İsim, unvan veya uzmanlık ara… (örn. voleybol, HIIT)" autocomplete="off">
				<output class="af-search__count" aria-live="polite" data-af-search-count></output>
			</div>
			<div class="af-grid af-grid--4">
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/card', 'trainer' );
				}
				?>
			</div>
			<p class="af-empty" data-af-search-empty hidden>Aramanıza uygun antrenör bulunamadı. Farklı bir kelime deneyin.</p>
			<?php the_posts_pagination( array( 'prev_text' => '&larr;', 'next_text' => '&rarr;' ) ); ?>
		<?php else : ?>
			<?php arenafit_empty( 'Henüz antrenör eklenmedi.', 'Panel > Antrenörler > Yeni Antrenör ile ekleyin.' ); ?>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
