<?php
/**
 * Template Name: Rezervasyon
 * Template Post Type: page
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

arenafit_page_hero( 'Online Rezervasyon', 'Randevunuzu veya salonunuzu ayırtın', 'Hizmeti ve tarihi seçin, boş saatlerden birini işaretleyin. Talebiniz anında ekibimize ulaşır.' );
?>
<section class="af-section af-section--flush">
	<div class="af-container af-detail">
		<div class="af-detail__main">
			<div class="af-panel">
				<h2 class="af-panel__title"><?php echo arenafit_icon( 'calendar' ); // phpcs:ignore ?> Rezervasyon Formu</h2>
				<?php echo arenafit_booking_form(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
			<?php
			while ( have_posts() ) {
				the_post();
				if ( trim( get_the_content() ) ) {
					echo '<div class="af-prose af-mt">';
					the_content();
					echo '</div>';
				}
			}
			?>
		</div>
		<aside class="af-detail__aside">
			<div class="af-sticky">
				<div class="af-panel af-panel--soft">
					<h3 class="af-panel__title">Nasıl çalışır?</h3>
					<ol class="af-mini-steps">
						<li>Antrenörü veya salonu seçin.</li>
						<li>Tarihi seçince boş saatler listelenir.</li>
						<li>Bilgilerinizi yazıp talebi gönderin.</li>
						<li>Onay telefon / e-posta ile bildirilir.</li>
					</ol>
				</div>
				<div class="af-panel af-panel--soft">
					<h3 class="af-panel__title">Çalışma saatleri</h3>
					<p><?php echo esc_html( arenafit_opt( 'hours' ) ); ?></p>
					<?php if ( arenafit_opt( 'phone' ) ) : ?>
						<p><a class="af-btn af-btn--sm af-btn--block" href="<?php echo esc_url( arenafit_tel_href() ); ?>"><?php echo arenafit_icon( 'phone' ); // phpcs:ignore ?> <?php echo esc_html( arenafit_opt( 'phone' ) ); ?></a></p>
					<?php endif; ?>
				</div>
			</div>
		</aside>
	</div>
</section>
<?php
get_footer();
