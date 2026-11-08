<?php
/**
 * Salon kartı.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$af_id     = get_the_ID();
$af_sport  = arenafit_meta( $af_id, '_af_sport', 'diger' );
$af_sports = arenafit_sports();
$af_price  = arenafit_meta( $af_id, '_af_hour_price' );
$af_size   = arenafit_meta( $af_id, '_af_size' );
$af_cap    = (int) arenafit_meta( $af_id, '_af_capacity', 0 );
$af_ref    = '1' === get_post_meta( $af_id, '_af_referee', true );
?>
<article class="af-card af-hall reveal">
	<a class="af-hall__img" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php arenafit_thumb( 'arenafit-card', 'ball' ); ?>
		<span class="af-badge af-badge--float"><?php echo esc_html( isset( $af_sports[ $af_sport ] ) ? $af_sports[ $af_sport ] : 'Salon' ); ?></span>
	</a>
	<div class="af-hall__body">
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<ul class="af-pkg__facts">
			<?php if ( $af_size ) : ?>
				<li><?php echo arenafit_icon( 'ball' ); // phpcs:ignore ?> <?php echo esc_html( $af_size ); ?></li>
			<?php endif; ?>
			<?php if ( $af_cap ) : ?>
				<li><?php echo arenafit_icon( 'users' ); // phpcs:ignore ?> <?php echo esc_html( $af_cap ); ?> kişi</li>
			<?php endif; ?>
			<?php if ( $af_ref ) : ?>
				<li><?php echo arenafit_icon( 'flag' ); // phpcs:ignore ?> Hakem hizmeti</li>
			<?php endif; ?>
		</ul>
		<div class="af-hall__foot">
			<?php if ( '' !== $af_price ) : ?>
				<p class="af-hall__price"><strong><?php echo esc_html( arenafit_money( $af_price ) ); ?></strong> <span>/ saat</span></p>
			<?php else : ?>
				<p class="af-hall__price"><span>Fiyat için arayın</span></p>
			<?php endif; ?>
			<a class="af-btn af-btn--sm af-btn--accent" href="<?php echo esc_url( arenafit_booking_url( 'hall', $af_id ) ); ?>">Rezervasyon<span class="screen-reader-text">: <?php the_title(); ?></span></a>
		</div>
	</div>
</article>
