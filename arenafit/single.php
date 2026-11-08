<?php
/**
 * Yazı detayı.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

while ( have_posts() ) :
	the_post();
	arenafit_page_hero( get_the_date(), get_the_title() );
	?>
	<section class="af-section">
		<div class="af-container af-narrow">
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="af-post__hero"><?php the_post_thumbnail( 'arenafit-wide' ); ?></div>
			<?php endif; ?>
			<div class="af-prose"><?php the_content(); ?></div>
		</div>
	</section>
	<?php
endwhile;

get_footer();
