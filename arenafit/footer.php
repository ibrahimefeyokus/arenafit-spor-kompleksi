<?php
/**
 * Alt bölüm.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$af_wa = arenafit_wa_link( 'Merhaba, bilgi almak istiyorum.' );
?>
</main>

<footer class="af-footer">
	<div class="af-container af-footer__grid">
		<div class="af-footer__col af-footer__about">
			<a class="af-brand__link af-brand__link--light" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="af-brand__mark"><?php echo arenafit_logo_mark(); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span class="af-brand__name"><?php echo arenafit_wordmark(); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			</a>
			<p><?php echo esc_html( get_bloginfo( 'description' ) ? get_bloginfo( 'description' ) : 'Sertifikalı antrenörler, profesyonel salonlar ve online rezervasyon ile hedeflerinize güvenle ulaşın.' ); ?></p>
			<ul class="af-social">
				<?php foreach ( array( 'instagram' => 'Instagram', 'facebook' => 'Facebook', 'youtube' => 'YouTube' ) as $k => $label ) : ?>
					<?php if ( arenafit_opt( $k ) ) : ?>
						<li><a href="<?php echo esc_url( arenafit_opt( $k ) ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $label . ' (yeni sekmede açılır)' ); ?>"><?php echo arenafit_icon( $k ); // phpcs:ignore ?></a></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>

		<div class="af-footer__col">
			<h3>Hizmetler</h3>
			<ul>
				<li><a href="<?php echo esc_url( get_post_type_archive_link( 'af_package' ) ); ?>">PT Paketleri ve Ücretler</a></li>
				<li><a href="<?php echo esc_url( get_post_type_archive_link( 'af_trainer' ) ); ?>">Antrenörlerimiz</a></li>
				<li><a href="<?php echo esc_url( get_post_type_archive_link( 'af_hall' ) ); ?>">Salon Kiralama</a></li>
				<li><a href="<?php echo esc_url( arenafit_facility_url() ); ?>">Tesis Olanakları</a></li>
				<li><a href="<?php echo esc_url( arenafit_member_url() ); ?>">Üyelik Başvurusu</a></li>
				<li><a href="<?php echo esc_url( arenafit_account_url() ); ?>"><?php echo is_user_logged_in() ? 'Hesabım' : 'Üye Girişi'; ?></a></li>
				<li><a href="<?php echo esc_url( arenafit_contact_url() ); ?>">İletişim / Şikayet</a></li>
			</ul>
		</div>

		<div class="af-footer__col">
			<h3>Hızlı Bağlantılar</h3>
			<?php
			if ( has_nav_menu( 'footer' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'depth'          => 1,
					)
				);
			} else {
				echo '<ul><li><a href="' . esc_url( arenafit_booking_url() ) . '">Online Rezervasyon</a></li>';
				if ( function_exists( 'get_privacy_policy_url' ) && get_privacy_policy_url() ) {
					echo '<li><a href="' . esc_url( get_privacy_policy_url() ) . '">Gizlilik Politikası</a></li>';
				}
				echo '</ul>';
			}
			?>
		</div>

		<div class="af-footer__col" id="iletisim-alt">
			<h3>İletişim</h3>
			<ul class="af-contact">
				<?php if ( arenafit_opt( 'address' ) ) : ?>
					<li><?php echo arenafit_icon( 'pin' ); // phpcs:ignore ?> <span><?php echo nl2br( esc_html( arenafit_opt( 'address' ) ) ); ?></span></li>
				<?php endif; ?>
				<?php if ( arenafit_opt( 'phone' ) ) : ?>
					<li><?php echo arenafit_icon( 'phone' ); // phpcs:ignore ?> <a href="<?php echo esc_url( arenafit_tel_href() ); ?>"><?php echo esc_html( arenafit_opt( 'phone' ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( arenafit_opt( 'email' ) ) : ?>
					<li><?php echo arenafit_icon( 'mail' ); // phpcs:ignore ?> <a href="mailto:<?php echo esc_attr( arenafit_opt( 'email' ) ); ?>"><?php echo esc_html( arenafit_opt( 'email' ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( arenafit_opt( 'hours' ) ) : ?>
					<li><?php echo arenafit_icon( 'clock' ); // phpcs:ignore ?> <span><?php echo esc_html( arenafit_opt( 'hours' ) ); ?></span></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
	<div class="af-footer__bar">
		<div class="af-container">
			<span>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. Tüm hakları saklıdır.</span>
			<nav class="af-footer__legal" aria-label="Yasal bağlantılar">
				<a href="<?php echo esc_url( arenafit_a11y_url() ); ?>">Erişilebilirlik</a>
				<?php if ( arenafit_kvkk_url() ) : ?>
					<a href="<?php echo esc_url( arenafit_kvkk_url() ); ?>">KVKK Aydınlatma Metni</a>
				<?php endif; ?>
			</nav>
		</div>
	</div>
</footer>

<?php if ( $af_wa ) : ?>
	<a class="af-wa" href="<?php echo esc_url( $af_wa ); ?>" target="_blank" rel="noopener" aria-label="WhatsApp ile yazın (yeni sekmede açılır)"><?php echo arenafit_icon( 'whatsapp' ); // phpcs:ignore ?></a>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
