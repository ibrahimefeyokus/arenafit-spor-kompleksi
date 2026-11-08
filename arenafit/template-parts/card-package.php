<?php
/**
 * PT paketi kartı.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$af_id       = get_the_ID();
$af_type     = arenafit_meta( $af_id, '_af_pkg_type', 'individual' );
$af_price    = arenafit_meta( $af_id, '_af_price' );
$af_sessions = (int) arenafit_meta( $af_id, '_af_sessions', 0 );
$af_per      = arenafit_per_session( $af_id );
$af_dur      = (int) arenafit_meta( $af_id, '_af_duration', 0 );
$af_cap      = (int) arenafit_meta( $af_id, '_af_capacity', 0 );
$af_when     = arenafit_meta( $af_id, '_af_when' );
$af_content  = arenafit_lines( get_post_meta( $af_id, '_af_content', true ) );
$af_featured = '1' === get_post_meta( $af_id, '_af_featured', true );
?>
<article class="af-card af-pkg<?php echo $af_featured ? ' is-featured' : ''; ?> reveal" data-type="<?php echo esc_attr( $af_type ); ?>">
	<?php if ( $af_featured ) : ?>
		<span class="af-pkg__flag"><?php echo arenafit_icon( 'star' ); // phpcs:ignore ?> Öne çıkan</span>
	<?php endif; ?>
	<span class="af-badge"><?php echo 'group' === $af_type ? 'Grup antrenman' : 'Bireysel antrenman'; ?></span>
	<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	<?php if ( '' !== $af_price ) : ?>
		<p class="af-pkg__price"><strong><?php echo esc_html( arenafit_money( $af_price ) ); ?></strong>
			<?php if ( $af_sessions ) : ?>
				<span>/ <?php echo esc_html( $af_sessions ); ?> seans</span>
			<?php endif; ?>
		</p>
		<?php if ( $af_per && $af_sessions > 1 ) : ?>
			<p class="af-pkg__per">Seans başı <?php echo esc_html( arenafit_money( round( $af_per ) ) ); ?></p>
		<?php endif; ?>
	<?php endif; ?>

	<ul class="af-pkg__facts">
		<?php if ( $af_dur ) : ?>
			<li><?php echo arenafit_icon( 'clock' ); // phpcs:ignore ?> <?php echo esc_html( $af_dur ); ?> dk / seans</li>
		<?php endif; ?>
		<?php if ( $af_when ) : ?>
			<li><?php echo arenafit_icon( 'calendar' ); // phpcs:ignore ?> <?php echo esc_html( $af_when ); ?></li>
		<?php endif; ?>
		<?php if ( 'group' === $af_type && $af_cap ) : ?>
			<li><?php echo arenafit_icon( 'users' ); // phpcs:ignore ?> En fazla <?php echo esc_html( $af_cap ); ?> kişi</li>
		<?php endif; ?>
	</ul>

	<?php if ( $af_content ) : ?>
		<ul class="af-checklist">
			<?php foreach ( array_slice( $af_content, 0, 5 ) as $line ) : ?>
				<li><?php echo arenafit_icon( 'check' ); // phpcs:ignore ?> <?php echo esc_html( $line ); ?></li>
			<?php endforeach; ?>
		</ul>
		<?php if ( count( $af_content ) > 5 ) : ?>
			<p class="af-pkg__more">+<?php echo esc_html( count( $af_content ) - 5 ); ?> madde daha</p>
		<?php endif; ?>
	<?php endif; ?>

	<div class="af-pkg__actions">
		<?php if ( ! is_singular( 'af_package' ) ) : ?>
			<a class="af-btn af-btn--block" href="<?php the_permalink(); ?>">Detaylar<span class="screen-reader-text">: <?php the_title(); ?></span></a>
		<?php endif; ?>
		<a class="af-btn af-btn--accent af-btn--block" href="<?php echo esc_url( arenafit_member_url( $af_id ) ); ?>">Paketi Seç<span class="screen-reader-text">: <?php the_title(); ?></span></a>
	</div>
</article>
