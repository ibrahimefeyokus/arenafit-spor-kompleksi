<?php
/**
 * Template Name: Hesabım (Üye Alanı)
 * Template Post Type: page
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$af_in = is_user_logged_in();
if ( $af_in ) {
	arenafit_page_hero( 'Üye Alanı', 'Merhaba, ' . wp_get_current_user()->display_name, 'Paketlerinizi, rezervasyonlarınızı ve bilgilerinizi buradan yönetin.' );
} else {
	arenafit_page_hero( 'Üye Alanı', 'Giriş yapın veya üye olun', 'Üye olarak paketinizi, kalan seanslarınızı ve rezervasyonlarınızı takip edebilirsiniz.' );
}
?>
<section class="af-section af-section--flush">
	<div class="af-container">
		<?php
		echo arenafit_account_notice(); // phpcs:ignore WordPress.Security.EscapeOutput
		echo $af_in ? arenafit_account_dashboard() : arenafit_auth_forms(); // phpcs:ignore WordPress.Security.EscapeOutput
		?>
	</div>
</section>
<?php
get_footer();
