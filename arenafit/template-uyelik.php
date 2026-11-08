<?php
/**
 * Template Name: Üyelik
 * Template Post Type: page
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$af_plans = arenafit_plans();

arenafit_page_hero( 'Üyelik İşlemleri', 'Aramıza katılın', 'Başvuru formunu doldurun; ekibimiz sizi arayarak size uygun üyelik planını birlikte belirlesin.' );
?>
<section class="af-section af-section--flush">
	<div class="af-container af-detail">
		<div class="af-detail__main">
			<?php if ( $af_plans ) : ?>
				<h2 class="af-h3">Üyelik planları</h2>
				<div class="af-grid af-grid--2">
					<?php foreach ( $af_plans as $pl ) : ?>
						<article class="af-card af-pkg reveal">
							<h3><?php echo esc_html( $pl['name'] ); ?></h3>
							<?php if ( $pl['price'] ) : ?>
								<p class="af-pkg__price"><strong><?php echo esc_html( is_numeric( $pl['price'] ) ? arenafit_money( $pl['price'] ) : $pl['price'] ); ?></strong>
									<?php if ( $pl['period'] ) : ?>
										<span>/ <?php echo esc_html( $pl['period'] ); ?></span>
									<?php endif; ?>
								</p>
							<?php endif; ?>
							<?php if ( $pl['features'] ) : ?>
								<ul class="af-checklist">
									<?php foreach ( $pl['features'] as $f ) : ?>
										<li><?php echo arenafit_icon( 'check' ); // phpcs:ignore ?> <?php echo esc_html( $f ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

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

			<?php if ( ! $af_plans ) : ?>
				<?php
				$af_mp = new WP_Query(
					array(
						'post_type'      => 'af_package',
						'posts_per_page' => 4,
						'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
					)
				);
				?>
				<?php if ( $af_mp->have_posts() ) : ?>
					<h2 class="af-h3">Paket seçenekleri</h2>
					<ul class="af-mini-pkgs af-mini-pkgs--2">
						<?php
						while ( $af_mp->have_posts() ) :
							$af_mp->the_post();
							?>
							<li>
								<a href="<?php the_permalink(); ?>">
									<strong><?php the_title(); ?></strong>
									<span><?php echo esc_html( arenafit_money( get_post_meta( get_the_ID(), '_af_price', true ) ) ); ?></span>
								</a>
							</li>
						<?php endwhile; ?>
					</ul>
					<?php wp_reset_postdata(); ?>
				<?php endif; ?>
			<?php endif; ?>

			<h2 class="af-h3">Üyelik nasıl işler?</h2>
			<ol class="af-timeline">
				<li><span class="af-timeline__n" aria-hidden="true">1</span><div><strong>Online başvuru</strong><p>Yandaki formu doldurun; ilgilendiğiniz hizmetleri işaretleyin.</p></div></li>
				<li><span class="af-timeline__n" aria-hidden="true">2</span><div><strong>Sizi arıyoruz</strong><p>Ekibimiz en geç 1 iş günü içinde arar, hedefinizi dinler ve uygun planı önerir.</p></div></li>
				<li><span class="af-timeline__n" aria-hidden="true">3</span><div><strong>Kayıt ve paket tanımı</strong><p>Tesiste ödemenizi yaparsınız; paketiniz hesabınıza tanımlanır.</p></div></li>
				<li><span class="af-timeline__n" aria-hidden="true">4</span><div><strong>Hesabım'dan takip</strong><p>Kalan seanslarınızı, bitiş tarihini ve rezervasyonlarınızı online görürsünüz; iptal edebilirsiniz.</p></div></li>
			</ol>

			<h2 class="af-h3">Üyelerimize sunduklarımız</h2>
			<div class="af-perks">
				<div><?php echo arenafit_icon( 'calendar' ); // phpcs:ignore ?><strong>Online rezervasyon</strong><span>Antrenör ve salon takvimine 7/24 erişim</span></div>
				<div><?php echo arenafit_icon( 'dumbbell' ); // phpcs:ignore ?><strong>Seans takibi</strong><span>Kalan seans ve ilerleme çubuğu</span></div>
				<div><?php echo arenafit_icon( 'locker' ); // phpcs:ignore ?><strong>Tesis olanakları</strong><span>Soyunma odası, duş, otopark, kafe</span></div>
				<div><?php echo arenafit_icon( 'user' ); // phpcs:ignore ?><strong>Kişisel hesap</strong><span>Profil, şifre ve iletişim bilgileri</span></div>
			</div>

			<div class="af-account-cta">
				<div>
					<strong><?php echo is_user_logged_in() ? 'Hesabınız açık.' : 'Zaten üye misiniz?'; ?></strong>
					<span><?php echo is_user_logged_in() ? 'Paketinizi ve rezervasyonlarınızı Hesabım sayfasında görebilirsiniz.' : 'Giriş yapın veya e-postanızla hemen hesap oluşturun.'; ?></span>
				</div>
				<a class="af-btn af-btn--sm" href="<?php echo esc_url( arenafit_account_url() ); ?>"><?php echo arenafit_icon( 'user' ); // phpcs:ignore ?> <?php echo is_user_logged_in() ? 'Hesabım' : 'Giriş Yap / Üye Ol'; ?></a>
			</div>

			<h2 class="af-h3">Sık sorulan sorular</h2>
			<div class="af-faq">
				<details><summary>Başvuru ücretli mi?</summary><p>Hayır. Online başvuru ve ön görüşme ücretsizdir; ödeme yalnızca paket seçildiğinde tesiste yapılır.</p></details>
				<details><summary>Üyeliğimi dondurabilir miyim?</summary><p>Evet. Sağlık veya seyahat gibi durumlarda bizi arayarak paketinizi dondurabilirsiniz; kalan seanslarınız korunur.</p></details>
				<details><summary>Rezervasyonumu nasıl iptal ederim?</summary><p>Hesabım sayfasından, seans başlangıcına 12 saatten fazla varsa tek tıkla iptal edebilirsiniz. Daha yakın iptaller için bizi arayın.</p></details>
				<details><summary>Antrenörümü değiştirebilir miyim?</summary><p>Evet. Paketiniz süresince farklı bir antrenörün takviminden randevu alabilirsiniz.</p></details>
			</div>

			<div class="af-note">
				<?php echo arenafit_icon( 'shield' ); // phpcs:ignore ?>
				<p><strong>Kişisel verileriniz güvende.</strong> Başvuru bilgileriniz yalnızca üyelik işlemleri için kullanılır.</p>
			</div>
		</div>
		<aside class="af-detail__aside">
			<div class="af-sticky af-panel">
				<h2 class="af-panel__title"><?php echo arenafit_icon( 'users' ); // phpcs:ignore ?> Üyelik Başvurusu</h2>
				<?php echo arenafit_membership_form(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</aside>
	</div>
</section>
<?php
get_footer();
