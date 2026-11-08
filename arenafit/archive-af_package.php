<?php
/**
 * PT paketleri ve seans ücretleri.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

arenafit_page_hero( 'PT Eğitimleri', 'Paketler ve seans ücretleri', 'Bireysel veya grup antrenman; seans sayısı, süre, günler ve eğitim kapsamı açıkça listelenir.' );

$af_active = ( isset( $_GET['tip'] ) && 'group' === $_GET['tip'] ) ? 'group' : ( ( isset( $_GET['tip'] ) && 'individual' === $_GET['tip'] ) ? 'individual' : 'all' ); // phpcs:ignore WordPress.Security.NonceVerification
?>
<section class="af-section">
	<div class="af-container">
		<?php if ( have_posts() ) : ?>
			<div class="af-filters" role="group" aria-label="Paket türü">
				<button type="button" class="af-filter<?php echo 'all' === $af_active ? ' is-active' : ''; ?>" data-af-filter="all" aria-pressed="<?php echo 'all' === $af_active ? 'true' : 'false'; ?>">Tümü</button>
				<button type="button" class="af-filter<?php echo 'individual' === $af_active ? ' is-active' : ''; ?>" data-af-filter="individual" aria-pressed="<?php echo 'individual' === $af_active ? 'true' : 'false'; ?>">Bireysel Antrenman</button>
				<button type="button" class="af-filter<?php echo 'group' === $af_active ? ' is-active' : ''; ?>" data-af-filter="group" aria-pressed="<?php echo 'group' === $af_active ? 'true' : 'false'; ?>">Grup Antrenman</button>
			</div>

			<div class="af-grid af-grid--3 af-filterable" data-active="<?php echo esc_attr( $af_active ); ?>">
				<?php
				$af_rows = array();
				while ( have_posts() ) {
					the_post();
					$af_rows[] = array(
						'title'    => get_the_title(),
						'url'      => get_permalink(),
						'type'     => arenafit_meta( get_the_ID(), '_af_pkg_type', 'individual' ),
						'sessions' => (int) arenafit_meta( get_the_ID(), '_af_sessions', 0 ),
						'price'    => arenafit_meta( get_the_ID(), '_af_price' ),
						'per'      => arenafit_per_session( get_the_ID() ),
						'dur'      => (int) arenafit_meta( get_the_ID(), '_af_duration', 0 ),
						'when'     => arenafit_meta( get_the_ID(), '_af_when' ),
					);
					get_template_part( 'template-parts/card', 'package' );
				}
				?>
			</div>

			<h2 class="af-h3 af-mt">Seans ücretleri tablosu</h2>
			<div class="af-tablewrap">
				<table class="af-table">
					<caption class="screen-reader-text">PT paketleri seans ücretleri karşılaştırması</caption>
					<thead>
						<tr><th>Paket</th><th>Tür</th><th>Seans</th><th>Süre</th><th>Günler / Saat</th><th>Paket ücreti</th><th>Seans başı</th></tr>
					</thead>
					<tbody>
						<?php foreach ( $af_rows as $r ) : ?>
							<tr>
								<td data-label="Paket"><a href="<?php echo esc_url( $r['url'] ); ?>"><strong><?php echo esc_html( $r['title'] ); ?></strong></a></td>
								<td data-label="Tür"><?php echo 'group' === $r['type'] ? 'Grup' : 'Bireysel'; ?></td>
								<td data-label="Seans"><?php echo $r['sessions'] ? esc_html( $r['sessions'] ) : '—'; ?></td>
								<td data-label="Süre"><?php echo $r['dur'] ? esc_html( $r['dur'] ) . ' dk' : '—'; ?></td>
								<td data-label="Günler / Saat"><?php echo $r['when'] ? esc_html( $r['when'] ) : '—'; ?></td>
								<td data-label="Paket ücreti"><?php echo '' !== $r['price'] ? esc_html( arenafit_money( $r['price'] ) ) : '—'; ?></td>
								<td data-label="Seans başı"><?php echo $r['per'] ? esc_html( arenafit_money( round( $r['per'] ) ) ) : '—'; ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php the_posts_pagination( array( 'prev_text' => '&larr;', 'next_text' => '&rarr;' ) ); ?>
		<?php else : ?>
			<?php arenafit_empty( 'Paketler yakında burada olacak.', 'Panel > PT Paketleri > Yeni Paket ile ekleyin.' ); ?>
		<?php endif; ?>

		<div class="af-note">
			<?php echo arenafit_icon( 'shield' ); // phpcs:ignore ?>
			<p><strong>Antrenörünü kendin seç.</strong> Paket sonrası seanslarınızı, antrenör profilindeki online takvimden dilediğiniz güne ve saate ayırtabilirsiniz.</p>
			<a class="af-btn af-btn--sm" href="<?php echo esc_url( get_post_type_archive_link( 'af_trainer' ) ); ?>">Antrenörleri Gör</a>
		</div>
	</div>
</section>
<?php
get_footer();
