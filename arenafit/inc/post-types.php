<?php
/**
 * Özel içerik türleri: Antrenör, PT Paketi, Salon, Rezervasyon, Üyelik Başvurusu.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function arenafit_register_types() {

	register_post_type(
		'af_trainer',
		array(
			'labels'        => array(
				'name'          => 'Antrenörler',
				'singular_name' => 'Antrenör',
				'menu_name'     => 'Antrenörler',
				'all_items'     => 'Tüm Antrenörler',
				'add_new'       => 'Yeni Antrenör',
				'add_new_item'  => 'Yeni Antrenör Ekle',
				'edit_item'     => 'Antrenörü Düzenle',
				'view_item'     => 'Antrenörü Görüntüle',
				'search_items'  => 'Antrenör Ara',
				'not_found'     => 'Antrenör bulunamadı',
			),
			'public'        => true,
			'has_archive'   => 'antrenorler',
			'rewrite'       => array( 'slug' => 'antrenor' ),
			'menu_icon'     => 'dashicons-awards',
			'menu_position' => 20,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
			'show_in_rest'  => true,
		)
	);

	register_taxonomy(
		'af_specialty',
		'af_trainer',
		array(
			'labels'       => array(
				'name'          => 'Uzmanlık Alanları',
				'singular_name' => 'Uzmanlık Alanı',
				'add_new_item'  => 'Yeni Uzmanlık Alanı Ekle',
				'menu_name'     => 'Uzmanlık Alanları',
			),
			'public'       => true,
			'hierarchical' => false,
			'rewrite'      => array( 'slug' => 'uzmanlik' ),
			'show_in_rest' => true,
		)
	);

	register_post_type(
		'af_package',
		array(
			'labels'        => array(
				'name'          => 'PT Paketleri',
				'singular_name' => 'PT Paketi',
				'menu_name'     => 'PT Paketleri',
				'all_items'     => 'Tüm Paketler',
				'add_new'       => 'Yeni Paket',
				'add_new_item'  => 'Yeni Paket Ekle',
				'edit_item'     => 'Paketi Düzenle',
				'view_item'     => 'Paketi Görüntüle',
				'search_items'  => 'Paket Ara',
				'not_found'     => 'Paket bulunamadı',
			),
			'public'        => true,
			'has_archive'   => 'pt-paketleri',
			'rewrite'       => array( 'slug' => 'paket' ),
			'menu_icon'     => 'dashicons-heart',
			'menu_position' => 21,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
			'show_in_rest'  => true,
		)
	);

	register_post_type(
		'af_hall',
		array(
			'labels'        => array(
				'name'          => 'Salonlar',
				'singular_name' => 'Salon',
				'menu_name'     => 'Salonlar',
				'all_items'     => 'Tüm Salonlar',
				'add_new'       => 'Yeni Salon',
				'add_new_item'  => 'Yeni Salon Ekle',
				'edit_item'     => 'Salonu Düzenle',
				'view_item'     => 'Salonu Görüntüle',
				'search_items'  => 'Salon Ara',
				'not_found'     => 'Salon bulunamadı',
			),
			'public'        => true,
			'has_archive'   => 'salonlar',
			'rewrite'       => array( 'slug' => 'salon' ),
			'menu_icon'     => 'dashicons-location-alt',
			'menu_position' => 22,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
			'show_in_rest'  => true,
		)
	);

	register_post_type(
		'af_booking',
		array(
			'labels'          => array(
				'name'          => 'Rezervasyonlar',
				'singular_name' => 'Rezervasyon',
				'menu_name'     => 'Rezervasyonlar',
				'all_items'     => 'Tüm Rezervasyonlar',
				'add_new'       => 'Manuel Rezervasyon',
				'add_new_item'  => 'Manuel Rezervasyon Ekle',
				'edit_item'     => 'Rezervasyonu Düzenle',
				'search_items'  => 'Rezervasyon Ara',
				'not_found'     => 'Rezervasyon bulunamadı',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-calendar-alt',
			'menu_position'   => 23,
			'capability_type' => 'post',
			'supports'        => array( 'title' ),
		)
	);

	register_post_type(
		'af_member',
		array(
			'labels'          => array(
				'name'          => 'Üyelik Başvuruları',
				'singular_name' => 'Üyelik Başvurusu',
				'menu_name'     => 'Üyelik Başvuruları',
				'all_items'     => 'Tüm Başvurular',
				'add_new'       => 'Yeni Başvuru',
				'add_new_item'  => 'Yeni Başvuru Ekle',
				'edit_item'     => 'Başvuruyu Düzenle',
				'search_items'  => 'Başvuru Ara',
				'not_found'     => 'Başvuru bulunamadı',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-id-alt',
			'menu_position'   => 24,
			'capability_type' => 'post',
			'supports'        => array( 'title' ),
		)
	);
}
add_action( 'init', 'arenafit_register_types' );

/**
 * Tema etkinleştirilince bağlantı yapılarını (permalink) yenile.
 */
function arenafit_flag_flush() {
	update_option( 'arenafit_flush', 1 );
}
add_action( 'after_switch_theme', 'arenafit_flag_flush' );

function arenafit_maybe_flush() {
	if ( get_option( 'arenafit_flush' ) ) {
		flush_rewrite_rules();
		delete_option( 'arenafit_flush' );
	}
}
add_action( 'init', 'arenafit_maybe_flush', 99 );

/**
 * Arşivlerde sıralama ve listeleme sayısı.
 */
function arenafit_archive_query( $q ) {
	if ( is_admin() || ! $q->is_main_query() ) {
		return;
	}
	if ( $q->is_post_type_archive( array( 'af_trainer', 'af_package', 'af_hall' ) ) || $q->is_tax( 'af_specialty' ) ) {
		$q->set( 'posts_per_page', 24 );
		$q->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
	}
}
add_action( 'pre_get_posts', 'arenafit_archive_query' );
