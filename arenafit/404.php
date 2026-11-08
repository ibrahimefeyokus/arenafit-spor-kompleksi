<?php
/**
 * 404 sayfası.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
arenafit_page_hero( '404', 'Sayfa bulunamadı', 'Aradığınız sayfa taşınmış veya kaldırılmış olabilir.' );
?>
<section class="af-section">
	<div class="af-container af-center">
		<a class="af-btn af-btn--accent" href="<?php echo esc_url( home_url( '/' ) ); ?>">Ana sayfaya dön</a>
		<a class="af-btn" href="<?php echo esc_url( arenafit_booking_url() ); ?>">Rezervasyon yap</a>
	</div>
</section>
<?php
get_footer();
