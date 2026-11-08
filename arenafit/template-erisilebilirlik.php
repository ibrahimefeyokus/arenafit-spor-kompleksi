<?php
/**
 * Template Name: Erişilebilirlik Beyanı
 * Template Post Type: page
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

arenafit_page_hero( 'Erişilebilirlik', 'Erişilebilirlik beyanı', 'Sitemizi ve tesisimizi herkes için kullanılabilir kılmak için yaptıklarımız.' );
?>
<section class="af-section">
	<div class="af-container af-narrow">
		<div class="af-note af-note--top">
			<svg class="af-i" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="4.5" r="2"/><path d="M4 8.5c2.6.9 5.3 1.3 8 1.3s5.4-.4 8-1.3M12 9.8V15m0 0-3.5 6.5M12 15l3.5 6.5"/></svg>
			<p><strong>Erişilebilirlik paneli.</strong> Sağ alttaki düğmeden yazı boyutunu, kontrastı ve diğer görüntü ayarlarını kendinize göre değiştirebilirsiniz.</p>
			<button type="button" class="af-btn af-btn--sm af-btn--accent" data-af-a11y-open>Paneli aç</button>
		</div>
		<div class="af-prose af-mt">
			<?php
			$af_has = false;
			while ( have_posts() ) {
				the_post();
				if ( trim( get_the_content() ) ) {
					$af_has = true;
					the_content();
				}
			}
			if ( ! $af_has ) {
				echo arenafit_a11y_default_statement(); // phpcs:ignore WordPress.Security.EscapeOutput
			}
			?>
		</div>
	</div>
</section>
<?php
get_footer();
