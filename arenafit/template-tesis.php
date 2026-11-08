<?php
/**
 * Template Name: Tesis Olanakları
 * Template Post Type: page
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

arenafit_page_hero( 'Tesis Olanakları', 'Antrenman öncesi ve sonrası her şey düşünüldü', 'Soyunma odaları, duşlar, otopark ve kafe ile tesisimizde rahat edin.' );
?>
<section class="af-section">
	<div class="af-container">
		<div class="af-grid af-grid--4">
			<?php foreach ( arenafit_facilities() as $af_f ) { arenafit_facility_card( $af_f, true ); } ?>
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

		<div class="af-grid af-grid--3 af-mt">
			<div class="af-panel af-panel--soft">
				<h2 class="af-panel__title"><?php echo arenafit_icon( 'clock' ); // phpcs:ignore ?> Açık olduğumuz saatler</h2>
				<p><?php echo esc_html( arenafit_opt( 'hours' ) ); ?></p>
				<p class="af-fine">Soyunma odası, duş ve kafe tesis çalışma saatlerinde kullanılabilir.</p>
			</div>
			<div class="af-panel af-panel--soft">
				<h2 class="af-panel__title"><?php echo arenafit_icon( 'shield' ); // phpcs:ignore ?> Hijyen ve güvenlik</h2>
				<p>Alanlar gün içinde düzenli temizlenir. Değerli eşyalarınız için kilitli dolap kullanmanızı öneririz.</p>
			</div>
			<div class="af-panel af-panel--soft">
				<h2 class="af-panel__title"><?php echo arenafit_icon( 'pin' ); // phpcs:ignore ?> Ulaşım</h2>
				<p><?php echo arenafit_opt( 'address' ) ? esc_html( arenafit_opt( 'address' ) ) : 'Adres bilgisi için bizi arayın.'; ?></p>
				<a class="af-link" href="<?php echo esc_url( arenafit_contact_url() ); ?>">Yol tarifi ve iletişim <?php echo arenafit_icon( 'arrow' ); // phpcs:ignore ?></a>
			</div>
		</div>

		<div class="af-note af-mt">
			<?php echo arenafit_icon( 'ball' ); // phpcs:ignore ?>
			<p><strong>Spor salonlarımız.</strong> Basketbol ve voleybol salonlarımızı saatlik kiralayabilirsiniz.</p>
			<a class="af-btn af-btn--sm af-btn--accent" href="<?php echo esc_url( get_post_type_archive_link( 'af_hall' ) ); ?>">Salonları Gör</a>
		</div>
	</div>
</section>
<?php
get_footer();
