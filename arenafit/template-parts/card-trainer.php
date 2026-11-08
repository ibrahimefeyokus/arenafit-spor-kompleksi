<?php
/**
 * Antrenör kartı.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$af_id    = get_the_ID();
$af_role  = arenafit_meta( $af_id, '_af_role' );
$af_exp   = (int) arenafit_meta( $af_id, '_af_exp', 0 );
$af_certs = count( arenafit_lines( get_post_meta( $af_id, '_af_certs', true ) ) );
$af_terms = get_the_terms( $af_id, 'af_specialty' );
?>
<?php
$af_search = get_the_title() . ' ' . $af_role;
if ( $af_terms && ! is_wp_error( $af_terms ) ) {
	$af_search .= ' ' . implode( ' ', wp_list_pluck( $af_terms, 'name' ) );
}
?>
<article class="af-card af-trainer reveal" data-search="<?php echo esc_attr( function_exists( 'mb_strtolower' ) ? mb_strtolower( $af_search, 'UTF-8' ) : strtolower( $af_search ) ); ?>">
	<a class="af-trainer__img" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php arenafit_thumb( 'arenafit-portrait', 'user' ); ?>
	</a>
	<div class="af-trainer__body">
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php if ( $af_role ) : ?>
			<p class="af-trainer__role"><?php echo esc_html( $af_role ); ?></p>
		<?php endif; ?>
		<?php if ( $af_terms && ! is_wp_error( $af_terms ) ) : ?>
			<ul class="af-chips">
				<?php foreach ( array_slice( $af_terms, 0, 3 ) as $t ) : ?>
					<li><?php echo esc_html( $t->name ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<?php if ( $af_exp || $af_certs ) : ?>
			<p class="af-trainer__meta">
				<?php if ( $af_exp ) : ?>
					<span><?php echo arenafit_icon( 'clock' ); // phpcs:ignore ?> <?php echo esc_html( $af_exp ); ?> yıl deneyim</span>
				<?php endif; ?>
				<?php if ( $af_certs ) : ?>
					<span><?php echo arenafit_icon( 'award' ); // phpcs:ignore ?> <?php echo esc_html( $af_certs ); ?> sertifika</span>
				<?php endif; ?>
			</p>
		<?php endif; ?>
		<div class="af-trainer__actions">
			<a class="af-btn af-btn--sm" href="<?php the_permalink(); ?>">Profili Gör<span class="screen-reader-text">: <?php the_title(); ?></span></a>
			<a class="af-btn af-btn--sm af-btn--accent" href="<?php echo esc_url( arenafit_booking_url( 'trainer', $af_id ) ); ?>">Randevu Al<span class="screen-reader-text">: <?php the_title(); ?></span></a>
		</div>
	</div>
</article>
