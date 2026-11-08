<?php
/**
 * Ana sayfa.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$af_img = arenafit_opt( 'hero_image' );
$af_map = arenafit_opt( 'map' );
?>

<section class="af-hero">
	<div class="af-hero__bg"<?php echo $af_img ? ' style="background-image:url(' . esc_url( $af_img ) . ')"' : ''; ?>></div>
	<div class="af-container af-hero__grid">
		<div class="af-hero__copy">
			<span class="af-eyebrow"><?php echo arenafit_icon( 'shield' ); // phpcs:ignore ?> Güvenilir &bull; Sertifikalı &bull; Online Rezervasyon</span>
			<h1><?php echo esc_html( arenafit_opt( 'hero_title' ) ); ?></h1>
			<p class="af-hero__lead"><?php echo esc_html( arenafit_opt( 'hero_text' ) ); ?></p>
			<div class="af-hero__cta">
				<a class="af-btn af-btn--accent af-btn--lg" href="<?php echo esc_url( arenafit_booking_url() ); ?>"><?php echo arenafit_icon( 'calendar' ); // phpcs:ignore ?> Online Rezervasyon</a>
				<a class="af-btn af-btn--ghost af-btn--lg" href="<?php echo esc_url( get_post_type_archive_link( 'af_package' ) ); ?>">PT Paketlerini İncele</a>
			</div>
			<ul class="af-hero__points">
				<li><?php echo arenafit_icon( 'check' ); // phpcs:ignore ?> Belgeli antrenörler</li>
				<li><?php echo arenafit_icon( 'check' ); // phpcs:ignore ?> Anlık müsaitlik takvimi</li>
				<li><?php echo arenafit_icon( 'check' ); // phpcs:ignore ?> Şeffaf seans ücretleri</li>
			</ul>
		</div>

		<div class="af-hero__panel">
			<h2>Hızlı İşlemler</h2>
			<a class="af-quick" href="<?php echo esc_url( get_post_type_archive_link( 'af_trainer' ) ); ?>">
				<span class="af-quick__ic"><?php echo arenafit_icon( 'user' ); // phpcs:ignore ?></span>
				<span><strong>Antrenör Randevusu</strong><small>Antrenörünü seç, saatini ayırt</small></span>
				<?php echo arenafit_icon( 'arrow' ); // phpcs:ignore ?>
			</a>
			<a class="af-quick" href="<?php echo esc_url( get_post_type_archive_link( 'af_hall' ) ); ?>">
				<span class="af-quick__ic"><?php echo arenafit_icon( 'ball' ); // phpcs:ignore ?></span>
				<span><strong>Salon Kirala</strong><small>Basketbol &amp; voleybol salonları</small></span>
				<?php echo arenafit_icon( 'arrow' ); // phpcs:ignore ?>
			</a>
			<a class="af-quick" href="<?php echo esc_url( get_post_type_archive_link( 'af_package' ) ); ?>">
				<span class="af-quick__ic"><?php echo arenafit_icon( 'dumbbell' ); // phpcs:ignore ?></span>
				<span><strong>PT Paketleri</strong><small>Bireysel ve grup programlar</small></span>
				<?php echo arenafit_icon( 'arrow' ); // phpcs:ignore ?>
			</a>
			<a class="af-quick" href="<?php echo esc_url( arenafit_member_url() ); ?>">
				<span class="af-quick__ic"><?php echo arenafit_icon( 'users' ); // phpcs:ignore ?></span>
				<span><strong>Üyelik Başvurusu</strong><small>Online başvur, seni arayalım</small></span>
				<?php echo arenafit_icon( 'arrow' ); // phpcs:ignore ?>
			</a>
		</div>
	</div>
</section>

<section class="af-trust">
	<div class="af-container af-trust__grid">
		<div><?php echo arenafit_icon( 'award' ); // phpcs:ignore ?><span><strong>Sertifikalı Kadro</strong>Belge ve özgeçmişleri açık</span></div>
		<div><?php echo arenafit_icon( 'calendar' ); // phpcs:ignore ?><span><strong>Online Takvim</strong>7/24 randevu talebi</span></div>
		<div><?php echo arenafit_icon( 'ball' ); // phpcs:ignore ?><span><strong>Profesyonel Salonlar</strong>Basketbol ve voleybol</span></div>
		<div><?php echo arenafit_icon( 'coffee' ); // phpcs:ignore ?><span><strong>Konforlu Tesis</strong>Duş, otopark ve kafe</span></div>
	</div>
</section>

<section class="af-section">
	<div class="af-container">
		<?php arenafit_head( 'Hizmetlerimiz', 'İhtiyacınıza uygun programı seçin', 'Bireysel çalışmadan grup derslerine, salon kiralamadan üyeliğe kadar her şey tek yerde.' ); ?>
		<div class="af-grid af-grid--4">
			<a class="af-card af-service reveal" href="<?php echo esc_url( get_post_type_archive_link( 'af_package' ) . '?tip=individual' ); ?>">
				<span class="af-service__ic"><?php echo arenafit_icon( 'user' ); // phpcs:ignore ?></span>
				<h3>Bireysel Antrenman</h3>
				<p>Hedefinize özel program, birebir takip ve esnek seans saatleri.</p>
				<span class="af-link">Paketleri gör <?php echo arenafit_icon( 'arrow' ); // phpcs:ignore ?></span>
			</a>
			<a class="af-card af-service reveal" href="<?php echo esc_url( get_post_type_archive_link( 'af_package' ) . '?tip=group' ); ?>">
				<span class="af-service__ic"><?php echo arenafit_icon( 'users' ); // phpcs:ignore ?></span>
				<h3>Grup Antrenman</h3>
				<p>Motivasyonu yüksek, kontenjanlı ve uygun fiyatlı grup dersleri.</p>
				<span class="af-link">Gruplara bak <?php echo arenafit_icon( 'arrow' ); // phpcs:ignore ?></span>
			</a>
			<a class="af-card af-service reveal" href="<?php echo esc_url( get_post_type_archive_link( 'af_hall' ) ); ?>">
				<span class="af-service__ic"><?php echo arenafit_icon( 'ball' ); // phpcs:ignore ?></span>
				<h3>Salon Kiralama</h3>
				<p>Basketbol ve voleybol salonları, istenirse hakem ayarlama hizmetiyle.</p>
				<span class="af-link">Salonları gör <?php echo arenafit_icon( 'arrow' ); // phpcs:ignore ?></span>
			</a>
			<a class="af-card af-service reveal" href="<?php echo esc_url( arenafit_member_url() ); ?>">
				<span class="af-service__ic"><?php echo arenafit_icon( 'shield' ); // phpcs:ignore ?></span>
				<h3>Üyelik</h3>
				<p>Online başvurun, ekibimiz sizi arayıp işlemleri tamamlasın.</p>
				<span class="af-link">Başvur <?php echo arenafit_icon( 'arrow' ); // phpcs:ignore ?></span>
			</a>
		</div>
	</div>
</section>

<?php
// PT paketleri: önce öne çıkanlar, yoksa son eklenenler.
$af_pk = new WP_Query(
	array(
		'post_type'      => 'af_package',
		'posts_per_page' => 3,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'meta_query'     => array( array( 'key' => '_af_featured', 'value' => '1' ) ),
	)
);
if ( ! $af_pk->have_posts() ) {
	$af_pk = new WP_Query(
		array(
			'post_type'      => 'af_package',
			'posts_per_page' => 3,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		)
	);
}
?>
<section class="af-section af-section--tint">
	<div class="af-container">
		<?php arenafit_head( 'PT Paketleri', 'Şeffaf fiyat, net kapsam', 'Seans sayısı, süre ve eğitim içeriği paket kartlarında açıkça yer alır.', array( 'url' => get_post_type_archive_link( 'af_package' ), 'label' => 'Tüm paketler' ) ); ?>
		<?php if ( $af_pk->have_posts() ) : ?>
			<div class="af-grid af-grid--3">
				<?php
				while ( $af_pk->have_posts() ) {
					$af_pk->the_post();
					get_template_part( 'template-parts/card', 'package' );
				}
				?>
			</div>
		<?php else : ?>
			<?php arenafit_empty( 'Paketler yakında burada olacak.', 'Panel > PT Paketleri > Yeni Paket ile ekleyin.' ); ?>
		<?php endif; ?>
		<?php wp_reset_postdata(); ?>
	</div>
</section>

<?php
$af_tr = new WP_Query(
	array(
		'post_type'      => 'af_trainer',
		'posts_per_page' => 4,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
	)
);
?>
<section class="af-section">
	<div class="af-container">
		<?php arenafit_head( 'Antrenörlerimiz', 'Uzman ve belgeli kadro', 'Özgeçmiş, sertifika ve uzmanlık alanlarını inceleyin; size uygun antrenörü seçip online randevu alın.', array( 'url' => get_post_type_archive_link( 'af_trainer' ), 'label' => 'Tüm antrenörler' ) ); ?>
		<?php if ( $af_tr->have_posts() ) : ?>
			<div class="af-grid af-grid--4">
				<?php
				while ( $af_tr->have_posts() ) {
					$af_tr->the_post();
					get_template_part( 'template-parts/card', 'trainer' );
				}
				?>
			</div>
		<?php else : ?>
			<?php arenafit_empty( 'Antrenör profilleri yakında eklenecek.', 'Panel > Antrenörler > Yeni Antrenör ile ekleyin.' ); ?>
		<?php endif; ?>
		<?php wp_reset_postdata(); ?>
	</div>
</section>

<?php
$af_hl = new WP_Query(
	array(
		'post_type'      => 'af_hall',
		'posts_per_page' => 3,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
	)
);
?>
<section class="af-section af-section--dark">
	<div class="af-container">
		<div class="af-head af-head--light reveal">
			<div>
				<span class="af-eyebrow"><?php echo arenafit_icon( 'ball' ); // phpcs:ignore ?> Salon Kiralama</span>
				<h2>Maçınız için hazır salonlar</h2>
				<p>Basketbol ve voleybol salonlarımızı saatlik kiralayın. İsterseniz hakem ayarlama hizmeti de sunuyoruz.</p>
			</div>
			<a class="af-link af-link--light" href="<?php echo esc_url( get_post_type_archive_link( 'af_hall' ) ); ?>">Tüm salonlar <?php echo arenafit_icon( 'arrow' ); // phpcs:ignore ?></a>
		</div>
		<?php if ( $af_hl->have_posts() ) : ?>
			<div class="af-grid af-grid--3">
				<?php
				while ( $af_hl->have_posts() ) {
					$af_hl->the_post();
					get_template_part( 'template-parts/card', 'hall' );
				}
				?>
			</div>
		<?php else : ?>
			<?php arenafit_empty( 'Salon bilgileri yakında eklenecek.', 'Panel > Salonlar > Yeni Salon ile ekleyin.' ); ?>
		<?php endif; ?>
		<?php wp_reset_postdata(); ?>
	</div>
</section>

<section class="af-section">
	<div class="af-container">
		<?php arenafit_head( 'Tesis Olanakları', 'Antrenman öncesi ve sonrası konfor', '', array( 'url' => arenafit_facility_url(), 'label' => 'Tesisi keşfet' ) ); ?>
		<div class="af-grid af-grid--4">
			<?php foreach ( arenafit_facilities() as $af_f ) { arenafit_facility_card( $af_f, false ); } ?>
		</div>
	</div>
</section>

<section class="af-section af-section--tint">
	<div class="af-container">
		<?php arenafit_head( 'Nasıl Çalışır?', '3 adımda rezervasyon', 'Telefonla uğraşmadan, birkaç dakikada talebinizi iletin.' ); ?>
		<ol class="af-steps">
			<li class="reveal"><span>1</span><h3>Seçin</h3><p>Antrenörü veya salonu seçin.</p></li>
			<li class="reveal"><span>2</span><h3>Tarih ve saat</h3><p>Takvimden boş saatlerden birini işaretleyin.</p></li>
			<li class="reveal"><span>3</span><h3>Onay alın</h3><p>Talebiniz ekibimize ulaşır, onay size bildirilir.</p></li>
		</ol>
		<p class="af-center"><a class="af-btn af-btn--accent af-btn--lg" href="<?php echo esc_url( arenafit_booking_url() ); ?>">Hemen Rezervasyon Yap</a></p>
	</div>
</section>

<section class="af-cta">
	<div class="af-container af-cta__in">
		<div>
			<h2>Aramıza katılmaya hazır mısınız?</h2>
			<p>Üyelik başvurusunu doldurun, ekibimiz sizi arayıp size uygun planı birlikte seçsin.</p>
		</div>
		<div class="af-cta__btns">
			<a class="af-btn af-btn--accent af-btn--lg" href="<?php echo esc_url( arenafit_member_url() ); ?>">Üyelik Başvurusu</a>
			<?php if ( arenafit_wa_link() ) : ?>
				<a class="af-btn af-btn--ghost af-btn--lg" href="<?php echo esc_url( arenafit_wa_link( 'Merhaba, üyelik hakkında bilgi almak istiyorum.' ) ); ?>" target="_blank" rel="noopener"><?php echo arenafit_icon( 'whatsapp' ); // phpcs:ignore ?> WhatsApp</a>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php if ( arenafit_opt( 'address' ) || arenafit_opt( 'phone' ) || $af_map ) : ?>
<section class="af-section" id="iletisim">
	<div class="af-container af-contactgrid">
		<div class="reveal">
			<span class="af-eyebrow af-eyebrow--dark">İletişim</span>
			<h2>Bize ulaşın</h2>
			<ul class="af-contact af-contact--dark">
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
		<?php if ( $af_map && ( 0 === strpos( $af_map, 'https://www.google.com/maps/embed' ) || 0 === strpos( $af_map, 'https://maps.google.com/' ) ) ) : ?>
			<div class="af-map reveal">
				<iframe src="<?php echo esc_url( $af_map ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Harita" allowfullscreen></iframe>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php
get_footer();
