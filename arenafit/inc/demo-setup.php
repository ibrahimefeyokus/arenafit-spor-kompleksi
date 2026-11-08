<?php
/**
 * Tema etkinleştirildiğinde gerekli sayfaları ve menüyü oluşturur.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tema güncellemesiyle gelen yeni sayfalar (Hesabım, İletişim) yoksa oluşturur.
 * Bir kez çalışır; yönetim paneli açıldığında tetiklenir.
 */
function arenafit_ensure_pages() {
	if ( (int) get_option( 'arenafit_pages_v', 0 ) >= 3 || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$pages = array(
		'hesabim'  => array( 'Hesabım', 'template-hesabim.php' ),
		'iletisim' => array( 'İletişim', 'template-iletisim.php' ),
		'erisilebilirlik' => array( 'Erişilebilirlik', 'template-erisilebilirlik.php' ),
	);
	$made  = array();
	foreach ( $pages as $slug => $p ) {
		$has = get_posts(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'numberposts' => 1,
				'meta_key'    => '_wp_page_template',
				'meta_value'  => $p[1],
				'fields'      => 'ids',
			)
		);
		if ( $has ) {
			$made[ $slug ] = $has[0];
			continue;
		}
		$existing = get_page_by_path( $slug );
		if ( $existing ) { // Aynı adresli sayfa varsa içeriğine dokunmadan şablonu ata.
			update_post_meta( $existing->ID, '_wp_page_template', $p[1] );
			$made[ $slug ] = $existing->ID;
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => $p[0],
				'post_name'   => $slug,
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_wp_page_template', $p[1] );
			$made[ $slug ] = $id;
		}
	}

	// Ana menüye "İletişim" ekle (yoksa).
	$locations = get_nav_menu_locations();
	if ( ! empty( $locations['primary'] ) && ! empty( $made['iletisim'] ) ) {
		$menu_id = (int) $locations['primary'];
		$url     = get_permalink( $made['iletisim'] );
		$exists  = false;
		foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $mi ) {
			if ( untrailingslashit( $mi->url ) === untrailingslashit( $url ) ) {
				$exists = true;
			}
		}
		if ( ! $exists ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'  => 'İletişim',
					'menu-item-url'    => $url,
					'menu-item-type'   => 'custom',
					'menu-item-status' => 'publish',
				)
			);
		}
	}

	update_option( 'arenafit_pages_v', 3 );
	update_option( 'arenafit_flush', 1 );
}
add_action( 'admin_init', 'arenafit_ensure_pages' );

function arenafit_first_setup() {
	if ( get_option( 'arenafit_setup_done' ) ) {
		return;
	}

	$pages = array(
		'ana-sayfa'        => array( 'Ana Sayfa', '', '' ),
		'rezervasyon'      => array( 'Rezervasyon', 'template-rezervasyon.php', '' ),
		'uyelik'           => array( 'Üyelik', 'template-uyelik.php', '' ),
		'tesis-olanaklari' => array( 'Tesis Olanakları', 'template-tesis.php', '' ),
	);
	$ids   = array();
	foreach ( $pages as $slug => $p ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$ids[ $slug ] = $existing->ID;
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $p[0],
				'post_name'    => $slug,
				'post_content' => $p[2],
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			if ( $p[1] ) {
				update_post_meta( $id, '_wp_page_template', $p[1] );
			}
			$ids[ $slug ] = $id;
		}
	}

	if ( ! empty( $ids['ana-sayfa'] ) && 'posts' === get_option( 'show_on_front' ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['ana-sayfa'] );
	}

	// Menü: yalnızca "Ana menü" konumuna henüz menü atanmamışsa.
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( empty( $locations['primary'] ) ) {
		$menu_id = wp_create_nav_menu( 'Ana Menü' );
		if ( ! is_wp_error( $menu_id ) ) {
			$items = array(
				'PT Eğitimleri'  => home_url( '/pt-paketleri/' ),
				'Antrenörler'    => home_url( '/antrenorler/' ),
				'Salon Kiralama' => home_url( '/salonlar/' ),
			);
			if ( ! empty( $ids['tesis-olanaklari'] ) ) {
				$items['Tesis'] = get_permalink( $ids['tesis-olanaklari'] );
			}
			if ( ! empty( $ids['uyelik'] ) ) {
				$items['Üyelik'] = get_permalink( $ids['uyelik'] );
			}
			foreach ( $items as $title => $url ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'  => $title,
						'menu-item-url'    => $url,
						'menu-item-type'   => 'custom',
						'menu-item-status' => 'publish',
					)
				);
			}
			$locations['primary'] = $menu_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}

	update_option( 'arenafit_setup_done', 1 );
	update_option( 'arenafit_flush', 1 );
}
add_action( 'after_switch_theme', 'arenafit_first_setup' );
