<?php
/**
 * Antrenör profili.
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
	$af_role     = arenafit_meta( $af_id, '_af_role' );
	$af_exp      = (int) arenafit_meta( $af_id, '_af_exp', 0 );
	$af_edu      = arenafit_lines( get_post_meta( $af_id, '_af_edu', true ) );
	$af_certs    = arenafit_lines( get_post_meta( $af_id, '_af_certs', true ) );
	$af_official = arenafit_lines( get_post_meta( $af_id, '_af_official', true ) );
	$af_days     = get_post_meta( $af_id, '_af_days', true );
	$af_days     = is_array( $af_days ) && $af_days ? array_map( 'intval', $af_days ) : array( 1, 2, 3, 4, 5, 6, 7 );
	$af_start    = arenafit_meta( $af_id, '_af_start', arenafit_opt( 'open', '07:00' ) );
	$af_end      = arenafit_meta( $af_id, '_af_end', arenafit_opt( 'close', '23:00' ) );
	$af_min      = (int) arenafit_meta( $af_id, '_af_session_min', 60 );
	$af_ig       = arenafit_meta( $af_id, '_af_instagram' );
	$af_terms    = get_the_terms( $af_id, 'af_specialty' );
	$af_docs     = arenafit_ids( get_post_meta( $af_id, '_af_cert_docs', true ) );
	$af_pkgs     = array_values(
		array_filter(
			get_posts( array( 'post_type' => 'af_package', 'numberposts' => 50 ) ),
			function ( $p ) use ( $af_id ) {
				$t = get_post_meta( $p->ID, '_af_trainers', true );
				return is_array( $t ) && in_array( $af_id, array_map( 'intval', $t ), true );
			}
		)
	);
	?>
	<section class="af-pagehero af-pagehero--person">
		<div class="af-container">
			<a class="af-back" href="<?php echo esc_url( get_post_type_archive_link( 'af_trainer' ) ); ?>">&larr; Tüm antrenörler</a>
			<h1><?php the_title(); ?></h1>
			<?php if ( $af_role ) : ?>
				<p><?php echo esc_html( $af_role ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<section class="af-section af-section--flush">
		<div class="af-container af-detail">
			<div class="af-detail__main">
				<div class="af-person">
					<div class="af-person__photo">
						<?php arenafit_thumb( 'arenafit-portrait', 'user' ); ?>
					</div>
					<div class="af-person__facts">
						<?php if ( $af_exp ) : ?>
							<div class="af-fact"><?php echo arenafit_icon( 'clock' ); // phpcs:ignore ?><strong><?php echo esc_html( $af_exp ); ?> yıl</strong><span>Deneyim</span></div>
						<?php endif; ?>
						<?php if ( $af_certs ) : ?>
							<div class="af-fact"><?php echo arenafit_icon( 'award' ); // phpcs:ignore ?><strong><?php echo esc_html( count( $af_certs ) ); ?></strong><span>Sertifika</span></div>
						<?php endif; ?>
						<div class="af-fact"><?php echo arenafit_icon( 'calendar' ); // phpcs:ignore ?><strong><?php echo esc_html( $af_min ); ?> dk</strong><span>Seans süresi</span></div>
						<?php if ( $af_ig ) : ?>
							<a class="af-fact af-fact--link" href="<?php echo esc_url( $af_ig ); ?>" target="_blank" rel="noopener"><?php echo arenafit_icon( 'instagram' ); // phpcs:ignore ?><strong>Instagram</strong><span>Profili aç</span></a>
						<?php endif; ?>
					</div>
				</div>

				<?php if ( $af_terms && ! is_wp_error( $af_terms ) ) : ?>
					<h2 class="af-h3">Uzmanlık alanları</h2>
					<ul class="af-chips af-chips--lg">
						<?php foreach ( $af_terms as $t ) : ?>
							<li><a href="<?php echo esc_url( get_term_link( $t ) ); ?>"><?php echo esc_html( $t->name ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( get_the_content() ) : ?>
					<h2 class="af-h3">Özgeçmiş</h2>
					<div class="af-prose"><?php the_content(); ?></div>
				<?php endif; ?>

				<?php if ( $af_edu ) : ?>
					<h2 class="af-h3">Eğitim</h2>
					<ul class="af-checklist">
						<?php foreach ( $af_edu as $line ) : ?>
							<li><?php echo arenafit_icon( 'check' ); // phpcs:ignore ?> <?php echo esc_html( $line ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $af_certs ) : ?>
					<h2 class="af-h3">Sertifika ve belgeler</h2>
					<ul class="af-certs">
						<?php foreach ( $af_certs as $line ) : ?>
							<li><?php echo arenafit_icon( 'award' ); // phpcs:ignore ?> <?php echo esc_html( $line ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $af_docs ) : ?>
					<h2 class="af-h3">Belge görselleri</h2>
					<ul class="af-docs" data-af-lightbox>
						<?php foreach ( $af_docs as $af_doc ) : ?>
							<?php
							$af_full = wp_get_attachment_image_src( $af_doc, 'large' );
							$af_alt  = get_post_meta( $af_doc, '_wp_attachment_image_alt', true );
							$af_alt  = $af_alt ? $af_alt : get_the_title( $af_doc );
							if ( ! $af_full ) {
								continue;
							}
							?>
							<li>
								<a href="<?php echo esc_url( $af_full[0] ); ?>" data-caption="<?php echo esc_attr( $af_alt ); ?>">
									<?php echo wp_get_attachment_image( $af_doc, 'medium', false, array( 'alt' => $af_alt . ' – büyütmek için tıklayın' ) ); // phpcs:ignore ?>
									<span><?php echo arenafit_icon( 'file' ); // phpcs:ignore ?> <?php echo esc_html( $af_alt ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $af_official ) : ?>
					<h2 class="af-h3">Resmi bilgiler</h2>
					<dl class="af-official">
						<?php
						foreach ( $af_official as $line ) {
							$parts = explode( ':', $line, 2 );
							if ( 2 === count( $parts ) ) {
								echo '<div><dt>' . esc_html( trim( $parts[0] ) ) . '</dt><dd>' . esc_html( trim( $parts[1] ) ) . '</dd></div>';
							} else {
								echo '<div><dt>' . esc_html( $line ) . '</dt><dd>' . arenafit_icon( 'check' ) . '</dd></div>'; // phpcs:ignore
							}
						}
						?>
					</dl>
				<?php endif; ?>

				<h2 class="af-h3">Online takvim – önümüzdeki 14 gün</h2>
				<?php echo arenafit_calendar_strip( 'trainer', $af_id ); // phpcs:ignore WordPress.Security.EscapeOutput ?>

				<h2 class="af-h3">Haftalık çalışma düzeni</h2>
				<div class="af-week">
					<?php foreach ( arenafit_days_short() as $n => $label ) : ?>
						<div class="af-week__day<?php echo in_array( $n, $af_days, true ) ? ' is-on' : ''; ?>">
							<strong><?php echo esc_html( $label ); ?></strong>
							<span><?php echo in_array( $n, $af_days, true ) ? esc_html( $af_start . '–' . $af_end ) : 'Kapalı'; ?></span>
						</div>
					<?php endforeach; ?>
				</div>
				<?php if ( $af_pkgs ) : ?>
					<h2 class="af-h3">Verdiği PT paketleri</h2>
					<ul class="af-mini-pkgs">
						<?php foreach ( $af_pkgs as $af_p ) : ?>
							<li>
								<a href="<?php echo esc_url( get_permalink( $af_p ) ); ?>">
									<strong><?php echo esc_html( get_the_title( $af_p ) ); ?></strong>
									<span><?php echo esc_html( arenafit_money( get_post_meta( $af_p->ID, '_af_price', true ) ) ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<aside class="af-detail__aside">
				<div class="af-sticky af-panel">
					<h2 class="af-panel__title"><?php echo arenafit_icon( 'calendar' ); // phpcs:ignore ?> Online Randevu</h2>
					<?php echo arenafit_booking_form( array( 'kind' => 'trainer', 'item' => $af_id, 'lock' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</aside>
		</div>
	</section>
	<?php
endwhile;

get_footer();
