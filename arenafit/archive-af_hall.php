<?php
/**
 * Salon listesi.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

arenafit_page_hero( 'Salon Kiralama', 'Basketbol ve voleybol salonları', 'Salon fotoğraflarını ve özelliklerini inceleyin, uygun saati seçip online rezervasyon yapın.' );
?>
<section class="af-section">
	<div class="af-container">
		<?php if ( have_posts() ) : ?>
			<div class="af-grid af-grid--3">
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/card', 'hall' );
				}
				?>
			</div>
			<?php the_posts_pagination( array( 'prev_text' => '&larr;', 'next_text' => '&rarr;' ) ); ?>
		<?php else : ?>
			<?php arenafit_empty( 'Henüz salon eklenmedi.', 'Panel > Salonlar > Yeni Salon ile ekleyin.' ); ?>
		<?php endif; ?>

		<div class="af-note">
			<?php echo arenafit_icon( 'flag' ); // phpcs:ignore ?>
			<p><strong>Hakem ayarlama hizmeti.</strong> Maçınız için hakem ihtiyacınız varsa rezervasyon sırasında hizmeti işaretlemeniz yeterli.</p>
			<a class="af-btn af-btn--sm af-btn--accent" href="<?php echo esc_url( arenafit_booking_url() ); ?>">Rezervasyon Yap</a>
		</div>
	</div>
</section>
<?php
get_footer();
