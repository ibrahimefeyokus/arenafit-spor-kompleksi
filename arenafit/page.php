<?php
/**
 * Standart sayfa.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

while ( have_posts() ) :
	the_post();
	arenafit_page_hero( '', get_the_title() );
	?>
	<section class="af-section">
		<div class="af-container af-narrow">
			<div class="af-prose"><?php the_content(); ?></div>
		</div>
	</section>
	<?php
endwhile;

get_footer();
