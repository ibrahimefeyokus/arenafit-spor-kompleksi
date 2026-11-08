<?php
/**
 * ArenaFit tema fonksiyonları.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ARENAFIT_VERSION', '1.3.2' );
define( 'ARENAFIT_DIR', get_template_directory() );
define( 'ARENAFIT_URI', get_template_directory_uri() );

foreach ( array( 'helpers', 'accessibility', 'setup', 'post-types', 'meta-boxes', 'booking', 'membership', 'account', 'contact', 'mail-test', 'demo-setup' ) as $arenafit_file ) {
	require_once ARENAFIT_DIR . '/inc/' . $arenafit_file . '.php';
}
