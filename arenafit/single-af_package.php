<?php
/**
 * PT paketi detayı: kapsam, içerik, günler, program, antrenör seçimi.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

while ( have_posts() ) :
	the_post();
	$af_id       = get_the_ID();
	$af_type     = arenafit_meta( $af_id, '_af_pkg_type', 'individual' );
	$af_price    = arenafit_meta( $af_id, '_af_price' );
	$af_sessions = (int) arenafit_meta( $af_id, '_af_sessions', 0 );
	$af_per      = arenafit_per_session( $af_id );
	$af_dur      = (int) arenafit_meta( $af_id, '_af_duration', 0 );
	$af_cap      = (int) arenafit_meta( $af_id, '_af_capacity', 0 );
	$af_when     = arenafit_meta( $af_id, '_af_when' );
	$af_level    = arenafit_meta( $af_id, '_af_level', 'all' );
	$af_content  = arenafit_lines( get_post_meta( $af_id, '_af_content', true ) );
	$af_target   = arenafit_lines( get_post_meta( $af_id, '_af_target', true ) );
	$af_program  = arenafit_lines( get_post_meta( $af_id, '_af_program', true ) );
	$af_days     = get_post_meta( $af_id, '_af_pkg_days', true );
	$af_days     = is_array( $af_days ) ? array_map( 'intval', $af_days ) : array();
	$af_tids     = get_post_meta( $af_id, '_af_trainers', true );
	$af_tids     = is_array( $af_tids ) ? array_filter( array_map( 'intval', $af_tids ) ) : array();
	$af_is_group = 'group' === $af_type;

	// Pakete antrenör atanmamışsa tüm kadro listelenir.
	$af_trainer_q = array(
		'post_type'      => 'af_trainer',
		'posts_per_page' => 8,
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
	);
	if ( $af_tids ) {
		$af_trainer_q['post__in'] = $af_tids;
		$af_trainer_q['orderby']  = 'post__in';
	}
	$af_trainers = new WP_Query( $af_trainer_q );
	?>
	<section class="af-pagehero af-pagehero--pkg">
		<div class="af-container">
			<a class="af-back" href="<?php echo esc_url( get_post_type_archive_link( 'af_package' ) ); ?>">&larr; Tüm paketler</a>
			<span class="af-eyebrow"><?php echo $af_is_group ? 'Grup antrenman' : 'Bireysel antrenman'; ?></span>
			<h1><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<section class="af-section af-section--flush">
		<div class="af-container af-detail">
			<div class="af-detail__main">

				<div class="af-specs af-specs--5">
					<div class="af-fact"><?php echo arenafit_icon( 'dumbbell' ); // phpcs:ignore ?><strong><?php echo $af_sessions ? esc_html( $af_sessions ) : '—'; ?></strong><span>Seans</span></div>
					<div class="af-fact"><?php echo arenafit_icon( 'clock' ); // phpcs:ignore ?><strong><?php echo $af_dur ? esc_html( $af_dur ) . ' dk' : '—'; ?></strong><span>Seans süresi</span></div>
					<div class="af-fact"><?php echo arenafit_icon( 'star' ); // phpcs:ignore ?><strong><?php echo $af_per ? esc_html( arenafit_money( round( $af_per ) ) ) : '—'; ?></strong><span>Seans başı</span></div>
					<div class="af-fact"><?php echo arenafit_icon( 'users' ); // phpcs:ignore ?><strong><?php echo $af_is_group ? ( $af_cap ? 'En fazla ' . esc_html( $af_cap ) : 'Grup' ) : 'Birebir'; ?></strong><span>Kişi</span></div>
					<div class="af-fact"><?php echo arenafit_icon( 'bolt' ); // phpcs:ignore ?><strong><?php echo esc_html( arenafit_level_label( $af_level ) ); ?></strong><span>Seviye</span></div>
				</div>

				<?php if ( get_the_content() ) : ?>
					<h2 class="af-h3">Eğitim hakkında</h2>
					<div class="af-prose"><?php the_content(); ?></div>
				<?php endif; ?>

				<?php if ( $af_content ) : ?>
					<h2 class="af-h3">Eğitim kapsamı ve içeriği</h2>
					<ul class="af-checklist af-checklist--cols">
						<?php foreach ( $af_content as $line ) : ?>
							<li><?php echo arenafit_icon( 'check' ); // phpcs:ignore ?> <?php echo esc_html( $line ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<h2 class="af-h3">Eğitim günleri</h2>
				<div class="af-week">
					<?php foreach ( arenafit_days_short() as $n => $label ) : ?>
						<?php $af_on = $af_days ? in_array( $n, $af_days, true ) : ! $af_is_group; ?>
						<div class="af-week__day<?php echo $af_on ? ' is-on' : ''; ?>">
							<strong><?php echo esc_html( $label ); ?></strong>
							<span><?php echo $af_on ? ( $af_days ? 'Ders var' : 'Randevuyla' ) : '—'; ?></span>
						</div>
					<?php endforeach; ?>
				</div>
				<?php if ( $af_when ) : ?>
					<p class="af-fine"><?php echo arenafit_icon( 'calendar' ); // phpcs:ignore ?> <?php echo esc_html( $af_when ); ?></p>
				<?php endif; ?>

				<?php if ( $af_program ) : ?>
					<h2 class="af-h3">Program akışı</h2>
					<ol class="af-timeline">
						<?php foreach ( $af_program as $i => $line ) : ?>
							<?php $parts = explode( ':', $line, 2 ); ?>
							<li>
								<span class="af-timeline__n" aria-hidden="true"><?php echo esc_html( $i + 1 ); ?></span>
								<div>
									<?php if ( 2 === count( $parts ) ) : ?>
										<strong><?php echo esc_html( trim( $parts[0] ) ); ?></strong>
										<p><?php echo esc_html( trim( $parts[1] ) ); ?></p>
									<?php else : ?>
										<p><?php echo esc_html( $line ); ?></p>
									<?php endif; ?>
								</div>
							</li>
						<?php endforeach; ?>
					</ol>
				<?php endif; ?>

				<?php if ( $af_target ) : ?>
					<h2 class="af-h3">Kimler için?</h2>
					<ul class="af-chips af-chips--lg">
						<?php foreach ( $af_target as $line ) : ?>
							<li><?php echo esc_html( $line ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $af_trainers->have_posts() ) : ?>
					<h2 class="af-h3" id="antrenor-sec">Antrenörünü seç</h2>
					<p class="af-fine af-fine--top">Paketinizi seçtiğiniz antrenörle sürdürürsünüz. Profilini inceleyin, takviminden ilk seansınızı ayırtın.</p>
					<div class="af-pick">
						<?php
						while ( $af_trainers->have_posts() ) :
							$af_trainers->the_post();
							$af_r = arenafit_meta( get_the_ID(), '_af_role' );
							$af_e = (int) arenafit_meta( get_the_ID(), '_af_exp', 0 );
							?>
							<article class="af-pick__item">
								<div class="af-pick__img"><?php arenafit_thumb( 'thumbnail', 'user', array( 'alt' => '' ) ); ?></div>
								<div class="af-pick__body">
									<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
									<p><?php echo esc_html( $af_r ); ?><?php echo $af_e ? ' · ' . esc_html( $af_e ) . ' yıl' : ''; ?></p>
								</div>
								<a class="af-btn af-btn--sm af-btn--accent" href="<?php echo esc_url( arenafit_booking_url( 'trainer', get_the_ID() ) ); ?>">Randevu<span class="screen-reader-text"> al: <?php the_title(); ?></span></a>
							</article>
						<?php endwhile; ?>
					</div>
				<?php endif; ?>
				<?php wp_reset_postdata(); ?>
			</div>

			<aside class="af-detail__aside">
				<div class="af-sticky af-panel af-buy">
					<span class="af-badge"><?php echo $af_is_group ? 'Grup antrenman' : 'Bireysel antrenman'; ?></span>
					<?php if ( '' !== $af_price ) : ?>
						<p class="af-pkg__price"><strong><?php echo esc_html( arenafit_money( $af_price ) ); ?></strong>
							<?php if ( $af_sessions ) : ?>
								<span>/ <?php echo esc_html( $af_sessions ); ?> seans</span>
							<?php endif; ?>
						</p>
						<?php if ( $af_per && $af_sessions > 1 ) : ?>
							<p class="af-pkg__per">Seans başı <?php echo esc_html( arenafit_money( round( $af_per ) ) ); ?></p>
						<?php endif; ?>
					<?php else : ?>
						<p class="af-pkg__price"><strong>Fiyat için arayın</strong></p>
					<?php endif; ?>
					<ul class="af-buy__list">
						<li><?php echo arenafit_icon( 'check' ); // phpcs:ignore ?> Ücretsiz ön görüşme ve hedef belirleme</li>
						<li><?php echo arenafit_icon( 'check' ); // phpcs:ignore ?> Antrenörünü kendin seç</li>
						<li><?php echo arenafit_icon( 'check' ); // phpcs:ignore ?> Kalan seansını Hesabım'dan takip et</li>
					</ul>
					<a class="af-btn af-btn--accent af-btn--block" href="<?php echo esc_url( arenafit_member_url( $af_id ) ); ?>">Paketi Seç</a>
					<a class="af-btn af-btn--block" href="#antrenor-sec">Antrenör Seç &amp; Randevu Al</a>
					<?php if ( arenafit_opt( 'phone' ) ) : ?>
						<p class="af-fine af-center">Sorunuz mu var? <a href="<?php echo esc_url( arenafit_tel_href() ); ?>"><?php echo esc_html( arenafit_opt( 'phone' ) ); ?></a></p>
					<?php endif; ?>
				</div>
			</aside>
		</div>
	</section>
	<?php
endwhile;

get_footer();
