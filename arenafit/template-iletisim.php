<?php
/**
 * Template Name: İletişim / Şikayet
 * Template Post Type: page
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$af_map = arenafit_opt( 'map' );

arenafit_page_hero( 'İletişim', 'Bize ulaşın', 'Soru, öneri veya şikayetinizi yazın. Mesajınız kayıt altına alınır ve ekibimiz size dönüş yapar.' );
?>
<section class="af-section af-section--flush">
	<div class="af-container af-detail">
		<div class="af-detail__main">
			<div class="af-panel">
				<h2 class="af-panel__title"><?php echo arenafit_icon( 'mail' ); // phpcs:ignore ?> Mesaj / Şikayet Formu</h2>
				<?php echo arenafit_contact_form(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
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
					<h3 class="af-panel__title">İletişim bilgileri</h3>
					<ul class="af-contact af-contact--dark">
						<?php if ( arenafit_opt( 'phone' ) ) : ?>
							<li><?php echo arenafit_icon( 'phone' ); // phpcs:ignore ?> <a href="<?php echo esc_url( arenafit_tel_href() ); ?>"><?php echo esc_html( arenafit_opt( 'phone' ) ); ?></a></li>
						<?php endif; ?>
						<?php if ( arenafit_wa_link() ) : ?>
							<li><?php echo arenafit_icon( 'whatsapp' ); // phpcs:ignore ?> <a href="<?php echo esc_url( arenafit_wa_link( 'Merhaba, bilgi almak istiyorum.' ) ); ?>" target="_blank" rel="noopener">WhatsApp ile yazın</a></li>
						<?php endif; ?>
						<?php if ( arenafit_opt( 'email' ) ) : ?>
							<li><?php echo arenafit_icon( 'mail' ); // phpcs:ignore ?> <a href="mailto:<?php echo esc_attr( arenafit_opt( 'email' ) ); ?>"><?php echo esc_html( arenafit_opt( 'email' ) ); ?></a></li>
						<?php endif; ?>
						<?php if ( arenafit_opt( 'address' ) ) : ?>
							<li><?php echo arenafit_icon( 'pin' ); // phpcs:ignore ?> <span><?php echo nl2br( esc_html( arenafit_opt( 'address' ) ) ); ?></span></li>
						<?php endif; ?>
						<?php if ( arenafit_opt( 'hours' ) ) : ?>
							<li><?php echo arenafit_icon( 'clock' ); // phpcs:ignore ?> <span><?php echo esc_html( arenafit_opt( 'hours' ) ); ?></span></li>
						<?php endif; ?>
					</ul>
				</div>
				<?php if ( $af_map && ( 0 === strpos( $af_map, 'https://www.google.com/maps/embed' ) || 0 === strpos( $af_map, 'https://maps.google.com/' ) ) ) : ?>
					<div class="af-map">
						<iframe src="<?php echo esc_url( $af_map ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Harita" allowfullscreen></iframe>
					</div>
				<?php endif; ?>
			</div>
		</aside>
	</div>
</section>
<?php
get_footer();
