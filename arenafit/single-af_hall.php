<?php
/**
 * Salon detayı.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

while ( have_posts() ) :
	the_post();
	$af_id     = get_the_ID();
	$af_sports = arenafit_sports();
	$af_sport  = arenafit_meta( $af_id, '_af_sport', 'diger' );
	$af_price  = arenafit_meta( $af_id, '_af_hour_price' );
	$af_size   = arenafit_meta( $af_id, '_af_size' );
	$af_floor  = arenafit_meta( $af_id, '_af_floor' );
	$af_cap    = (int) arenafit_meta( $af_id, '_af_capacity', 0 );
	$af_feat   = arenafit_lines( get_post_meta( $af_id, '_af_features', true ) );
	$af_ref    = '1' === get_post_meta( $af_id, '_af_referee', true );
	$af_refp   = arenafit_meta( $af_id, '_af_referee_price' );

	$af_gallery = array_filter( array_map( 'absint', explode( ',', (string) get_post_meta( $af_id, '_af_gallery', true ) ) ) );
	if ( has_post_thumbnail() ) {
		array_unshift( $af_gallery, get_post_thumbnail_id() );
	}
	$af_gallery = array_values( array_unique( $af_gallery ) );
	?>
	<section class="af-pagehero af-pagehero--person">
		<div class="af-container">
			<a class="af-back" href="<?php echo esc_url( get_post_type_archive_link( 'af_hall' ) ); ?>">&larr; Tüm salonlar</a>
			<h1><?php the_title(); ?></h1>
			<p><?php echo esc_html( isset( $af_sports[ $af_sport ] ) ? $af_sports[ $af_sport ] : 'Salon' ); ?><?php echo '' !== $af_price ? ' · ' . esc_html( arenafit_money( $af_price ) ) . ' / saat' : ''; ?></p>
		</div>
	</section>

	<section class="af-section af-section--flush">
		<div class="af-container af-detail">
			<div class="af-detail__main">
				<?php if ( $af_gallery ) : ?>
					<div class="af-gallery" data-af-gallery>
						<div class="af-gallery__main">
							<?php echo wp_get_attachment_image( $af_gallery[0], 'arenafit-wide', false, array( 'data-af-main' => '1', 'alt' => get_the_title() . ' salon fotoğrafı' ) ); // phpcs:ignore ?>
						</div>
						<?php if ( count( $af_gallery ) > 1 ) : ?>
							<div class="af-gallery__thumbs">
								<?php foreach ( $af_gallery as $i => $gid ) : ?>
									<?php $af_full = wp_get_attachment_image_src( $gid, 'arenafit-wide' ); ?>
									<button type="button" class="af-gallery__thumb<?php echo 0 === $i ? ' is-active' : ''; ?>" data-src="<?php echo esc_url( $af_full ? $af_full[0] : '' ); ?>" aria-label="Fotoğraf <?php echo esc_attr( $i + 1 ); ?>" aria-pressed="<?php echo 0 === $i ? 'true' : 'false'; ?>">
										<?php echo wp_get_attachment_image( $gid, 'thumbnail' ); // phpcs:ignore ?>
									</button>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php else : ?>
					<div class="af-gallery__ph"><span class="af-ph"><?php echo arenafit_icon( 'ball' ); // phpcs:ignore ?></span></div>
				<?php endif; ?>

				<div class="af-specs">
					<div class="af-fact"><?php echo arenafit_icon( 'ball' ); // phpcs:ignore ?><strong><?php echo esc_html( isset( $af_sports[ $af_sport ] ) ? $af_sports[ $af_sport ] : '—' ); ?></strong><span>Spor türü</span></div>
					<?php if ( $af_size ) : ?>
						<div class="af-fact"><?php echo arenafit_icon( 'pin' ); // phpcs:ignore ?><strong><?php echo esc_html( $af_size ); ?></strong><span>Ölçü</span></div>
					<?php endif; ?>
					<?php if ( $af_floor ) : ?>
						<div class="af-fact"><?php echo arenafit_icon( 'dumbbell' ); // phpcs:ignore ?><strong><?php echo esc_html( $af_floor ); ?></strong><span>Zemin</span></div>
					<?php endif; ?>
					<?php if ( $af_cap ) : ?>
						<div class="af-fact"><?php echo arenafit_icon( 'users' ); // phpcs:ignore ?><strong><?php echo esc_html( $af_cap ); ?> kişi</strong><span>Kapasite</span></div>
					<?php endif; ?>
				</div>

				<?php if ( get_the_content() ) : ?>
					<h2 class="af-h3">Salon hakkında</h2>
					<div class="af-prose"><?php the_content(); ?></div>
				<?php endif; ?>

				<?php if ( $af_feat ) : ?>
					<h2 class="af-h3">Özellikler</h2>
					<ul class="af-checklist af-checklist--cols">
						<?php foreach ( $af_feat as $line ) : ?>
							<li><?php echo arenafit_icon( 'check' ); // phpcs:ignore ?> <?php echo esc_html( $line ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<h2 class="af-h3">Online takvim – önümüzdeki 14 gün</h2>
				<?php echo arenafit_calendar_strip( 'hall', $af_id ); // phpcs:ignore WordPress.Security.EscapeOutput ?>

				<?php if ( $af_ref ) : ?>
					<div class="af-note">
						<?php echo arenafit_icon( 'flag' ); // phpcs:ignore ?>
						<p><strong>Hakem ayarlama hizmeti.</strong> Bu salon için hakem temin ediyoruz<?php echo '' !== $af_refp ? ' (' . esc_html( arenafit_money( $af_refp ) ) . ' / rezervasyon)' : ''; ?>. Rezervasyon formunda hizmeti işaretlemeniz yeterli.</p>
					</div>
				<?php endif; ?>
			</div>

			<aside class="af-detail__aside">
				<div class="af-sticky af-panel">
					<h2 class="af-panel__title"><?php echo arenafit_icon( 'calendar' ); // phpcs:ignore ?> Salon Rezervasyonu</h2>
					<?php echo arenafit_booking_form( array( 'kind' => 'hall', 'item' => $af_id, 'lock' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</aside>
		</div>
	</section>
	<?php
endwhile;

get_footer();
