<?php
/**
 * Yönetim paneli alanları (meta kutuları).
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function arenafit_status_labels() {
	return array(
		'pending'   => 'Onay bekliyor',
		'confirmed' => 'Onaylandı',
		'cancelled' => 'İptal edildi',
	);
}

function arenafit_fields() {
	return apply_filters( 'arenafit_fields', arenafit_fields_base() );
}

function arenafit_fields_base() {
	return array(
		'af_trainer' => array(
			'_af_role'          => array( 'label' => 'Unvan / Uzmanlık', 'type' => 'text', 'help' => 'Örn: Fitness ve Kuvvet Antrenörü' ),
			'_af_exp'           => array( 'label' => 'Deneyim (yıl)', 'type' => 'number' ),
			'_af_edu'           => array( 'label' => 'Eğitim / Mezuniyet', 'type' => 'textarea', 'help' => 'Her satıra bir bilgi.' ),
			'_af_certs'         => array( 'label' => 'Sertifika ve belgeler', 'type' => 'textarea', 'help' => 'Her satıra bir sertifika. Örn: NASM-CPT (2022)' ),
			'_af_official'      => array( 'label' => 'Resmi bilgiler', 'type' => 'textarea', 'help' => 'Her satıra bir bilgi. "Başlık: değer" biçimi önerilir. Örn: Antrenör Belgesi No: 12345' ),
			'_af_cert_docs'     => array( 'label' => 'Sertifika / belge görselleri', 'type' => 'gallery', 'help' => 'Sertifika, diploma veya antrenörlük belgesinin taranmış görsellerini ekleyin. Profilde büyütülebilir küçük resimler olarak görünür.' ),
			'_af_days'          => array( 'label' => 'Çalışma günleri', 'type' => 'days', 'help' => 'Hiçbiri seçilmezse her gün çalışıyor kabul edilir.' ),
			'_af_start'         => array( 'label' => 'Mesai başlangıcı', 'type' => 'time', 'help' => 'Boşsa tesis saatleri kullanılır.' ),
			'_af_end'           => array( 'label' => 'Mesai bitişi', 'type' => 'time' ),
			'_af_session_min'   => array( 'label' => 'Seans süresi (dakika)', 'type' => 'number', 'help' => 'Boşsa 60 dakika.' ),
			'_af_instagram'     => array( 'label' => 'Instagram adresi', 'type' => 'url' ),
		),
		'af_package' => array(
			'_af_pkg_type'  => array(
				'label'   => 'Paket türü',
				'type'    => 'select',
				'options' => array(
					'individual' => 'Bireysel antrenman',
					'group'      => 'Grup antrenman',
				),
			),
			'_af_sessions'  => array( 'label' => 'Seans sayısı', 'type' => 'number' ),
			'_af_price'     => array( 'label' => 'Paket toplam ücreti', 'type' => 'number', 'help' => 'Seans başı ücret otomatik hesaplanır.' ),
			'_af_duration'  => array( 'label' => 'Seans süresi (dakika)', 'type' => 'number' ),
			'_af_capacity'  => array( 'label' => 'Grup kontenjanı (kişi)', 'type' => 'number', 'help' => 'Yalnızca grup paketleri için.' ),
			'_af_when'      => array( 'label' => 'Eğitim günleri / saatleri', 'type' => 'text', 'help' => 'Örn: Pazartesi - Çarşamba - Cuma, 18:00' ),
			'_af_content'   => array( 'label' => 'Eğitim kapsamı ve içeriği', 'type' => 'textarea', 'help' => 'Her satıra bir madde.' ),
			'_af_level'     => array(
				'label'   => 'Seviye',
				'type'    => 'select',
				'options' => array(
					'all'          => 'Her seviyeye uygun',
					'beginner'     => 'Başlangıç',
					'intermediate' => 'Orta',
					'advanced'     => 'İleri',
				),
			),
			'_af_pkg_days'  => array( 'label' => 'Eğitim günleri (takvim)', 'type' => 'days', 'help' => 'Paket sayfasındaki haftalık takvimde işaretlenir. Bireysel paketlerde boş bırakılırsa "randevuyla, her gün" kabul edilir.' ),
			'_af_target'    => array( 'label' => 'Kimler için?', 'type' => 'textarea', 'help' => 'Her satıra bir madde. Örn: Spora yeni başlayanlar' ),
			'_af_program'   => array( 'label' => 'Haftalık program / ders akışı', 'type' => 'textarea', 'help' => 'Her satıra bir adım. "Başlık: açıklama" biçimi önerilir. Örn: 1. hafta: Hareket analizi ve temel teknik' ),
			'_af_trainers'  => array( 'label' => 'Bu paketi veren antrenörler', 'type' => 'trainers', 'help' => 'Paket sayfasında seçilebilir antrenör olarak listelenir.' ),
			'_af_featured'  => array( 'label' => 'Ana sayfada öne çıkar', 'type' => 'checkbox' ),
		),
		'af_hall'    => array(
			'_af_sport'         => array(
				'label'   => 'Spor türü',
				'type'    => 'select',
				'options' => arenafit_sports(),
			),
			'_af_hour_price'    => array( 'label' => 'Saatlik kiralama ücreti', 'type' => 'number' ),
			'_af_capacity'      => array( 'label' => 'Kapasite (kişi)', 'type' => 'number' ),
			'_af_size'          => array( 'label' => 'Salon ölçüsü', 'type' => 'text', 'help' => 'Örn: 28 x 15 m' ),
			'_af_floor'         => array( 'label' => 'Zemin', 'type' => 'text', 'help' => 'Örn: Parke' ),
			'_af_features'      => array( 'label' => 'Salon özellikleri', 'type' => 'textarea', 'help' => 'Her satıra bir özellik.' ),
			'_af_referee'       => array( 'label' => 'Hakem ayarlama hizmeti sunuluyor', 'type' => 'checkbox' ),
			'_af_referee_price' => array( 'label' => 'Hakem hizmeti ücreti', 'type' => 'number', 'help' => 'Rezervasyon başına. Boşsa "bilgi alınız" gösterilir.' ),
			'_af_gallery'       => array( 'label' => 'Fotoğraf galerisi', 'type' => 'gallery' ),
		),
		'af_booking' => array(
			'_af_item'     => array( 'label' => 'Antrenör / Salon', 'type' => 'item' ),
			'_af_date'     => array( 'label' => 'Tarih', 'type' => 'date' ),
			'_af_start'    => array( 'label' => 'Başlangıç saati', 'type' => 'time' ),
			'_af_duration' => array( 'label' => 'Süre (dakika)', 'type' => 'number' ),
			'_af_name'     => array( 'label' => 'Ad soyad', 'type' => 'text' ),
			'_af_phone'    => array( 'label' => 'Telefon', 'type' => 'text' ),
			'_af_email'    => array( 'label' => 'E-posta', 'type' => 'text' ),
			'_af_referee'  => array( 'label' => 'Hakem talep edildi', 'type' => 'checkbox' ),
			'_af_note'     => array( 'label' => 'Not', 'type' => 'textarea' ),
			'_af_status'   => array(
				'label'   => 'Durum',
				'type'    => 'select',
				'options' => arenafit_status_labels(),
			),
		),
		'af_member'  => array(
			'_af_name'     => array( 'label' => 'Ad soyad', 'type' => 'text' ),
			'_af_phone'    => array( 'label' => 'Telefon', 'type' => 'text' ),
			'_af_email'    => array( 'label' => 'E-posta', 'type' => 'text' ),
			'_af_interest' => array( 'label' => 'İlgilendiği hizmetler', 'type' => 'text' ),
			'_af_plan'     => array( 'label' => 'Seçilen plan / paket', 'type' => 'text' ),
			'_af_note'     => array( 'label' => 'Not', 'type' => 'textarea' ),
			'_af_status'   => array(
				'label'   => 'Durum',
				'type'    => 'select',
				'options' => array(
					'new'       => 'Yeni',
					'contacted' => 'Görüşüldü',
					'done'      => 'Tamamlandı',
				),
			),
		),
	);
}

function arenafit_add_meta_boxes() {
	$titles = array(
		'af_trainer' => 'Antrenör bilgileri',
		'af_package' => 'Paket detayları',
		'af_hall'    => 'Salon detayları',
		'af_booking' => 'Rezervasyon detayı',
		'af_member'  => 'Başvuru detayı',
	);
	$titles = apply_filters( 'arenafit_meta_box_titles', $titles );
	foreach ( $titles as $pt => $t ) {
		add_meta_box( 'arenafit_' . $pt, $t, 'arenafit_render_meta_box', $pt, 'normal', 'high' );
	}
}
add_action( 'add_meta_boxes', 'arenafit_add_meta_boxes' );

function arenafit_render_meta_box( $post ) {
	$fields = arenafit_fields();
	$pt     = $post->post_type;
	if ( empty( $fields[ $pt ] ) ) {
		return;
	}
	wp_nonce_field( 'arenafit_save_' . $pt, 'arenafit_nonce' );
	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( $fields[ $pt ] as $key => $def ) {
		$val  = get_post_meta( $post->ID, $key, true );
		$name = 'arenafit_meta[' . $key . ']';
		$id   = 'af_' . sanitize_key( $key );
		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $def['label'] ) . '</label></th><td>';
		switch ( $def['type'] ) {
			case 'user':
				echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '"><option value="">— Seçin —</option>';
				foreach ( get_users( array( 'orderby' => 'display_name', 'number' => 500 ) ) as $u ) {
					echo '<option value="' . (int) $u->ID . '"' . selected( (int) $val, $u->ID, false ) . '>' . esc_html( $u->display_name . ' (' . $u->user_email . ')' ) . '</option>';
				}
				echo '</select>';
				break;
			case 'pkg':
				echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '"><option value="">— Seçin —</option>';
				foreach ( get_posts( array( 'post_type' => 'af_package', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ) ) as $p ) {
					echo '<option value="' . (int) $p->ID . '"' . selected( (int) $val, $p->ID, false ) . '>' . esc_html( get_the_title( $p ) ) . '</option>';
				}
				echo '</select>';
				break;
			case 'textarea':
				echo '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" rows="4" class="large-text">' . esc_textarea( $val ) . '</textarea>';
				break;
			case 'select':
				echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">';
				foreach ( $def['options'] as $ov => $ol ) {
					echo '<option value="' . esc_attr( $ov ) . '"' . selected( $val, $ov, false ) . '>' . esc_html( $ol ) . '</option>';
				}
				echo '</select>';
				break;
			case 'checkbox':
				echo '<input type="hidden" name="' . esc_attr( $name ) . '" value="0"><label><input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="1"' . checked( $val, '1', false ) . '> Evet</label>';
				break;
			case 'days':
				$sel = is_array( $val ) ? array_map( 'intval', $val ) : array();
				foreach ( arenafit_days_labels() as $n => $l ) {
					echo '<label style="margin-right:14px;display:inline-block"><input type="checkbox" name="' . esc_attr( $name ) . '[]" value="' . (int) $n . '"' . checked( in_array( $n, $sel, true ), true, false ) . '> ' . esc_html( $l ) . '</label>';
				}
				break;
			case 'trainers':
				$sel = is_array( $val ) ? array_map( 'intval', $val ) : array();
				$tps = get_posts( array( 'post_type' => 'af_trainer', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
				if ( ! $tps ) {
					echo '<em>Önce Antrenörler menüsünden antrenör ekleyin.</em>';
				}
				foreach ( $tps as $tp ) {
					echo '<label style="margin-right:14px;display:inline-block"><input type="checkbox" name="' . esc_attr( $name ) . '[]" value="' . (int) $tp->ID . '"' . checked( in_array( $tp->ID, $sel, true ), true, false ) . '> ' . esc_html( get_the_title( $tp ) ) . '</label>';
				}
				break;
			case 'gallery':
				$ids = array_filter( array_map( 'absint', explode( ',', (string) $val ) ) );
				echo '<div class="af-gallery"><input type="hidden" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( implode( ',', $ids ) ) . '">';
				echo '<div class="af-gallery-prev">';
				foreach ( $ids as $gid ) {
					echo wp_get_attachment_image( $gid, 'thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput
				}
				echo '</div><p><button type="button" class="button af-gallery-add">Fotoğraf seç / düzenle</button> <button type="button" class="button-link af-gallery-clear">Temizle</button></p></div>';
				break;
			case 'item':
				echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '"><option value="">— Seçin —</option>';
				foreach ( array( 'af_trainer' => 'Antrenörler', 'af_hall' => 'Salonlar' ) as $ipt => $ilabel ) {
					$posts = get_posts(
						array(
							'post_type'   => $ipt,
							'numberposts' => -1,
							'orderby'     => 'title',
							'order'       => 'ASC',
						)
					);
					if ( $posts ) {
						echo '<optgroup label="' . esc_attr( $ilabel ) . '">';
						foreach ( $posts as $p ) {
							echo '<option value="' . (int) $p->ID . '"' . selected( (int) $val, $p->ID, false ) . '>' . esc_html( get_the_title( $p ) ) . '</option>';
						}
						echo '</optgroup>';
					}
				}
				echo '</select>';
				break;
			default:
				$t = in_array( $def['type'], array( 'number', 'time', 'url', 'date' ), true ) ? $def['type'] : 'text';
				echo '<input type="' . esc_attr( $t ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $val ) . '" class="regular-text"' . ( 'number' === $t ? ' min="0" step="any"' : '' ) . '>';
		}
		if ( ! empty( $def['help'] ) ) {
			echo '<p class="description">' . esc_html( $def['help'] ) . '</p>';
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
}

function arenafit_sanitize_field( $def, $raw ) {
	$raw = is_string( $raw ) ? $raw : '';
	switch ( $def['type'] ) {
		case 'number':
			return ( '' !== $raw && is_numeric( $raw ) ) ? max( 0, 0 + $raw ) : '';
		case 'time':
			return arenafit_sanitize_time( $raw );
		case 'date':
			return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw ) ? $raw : '';
		case 'url':
			return esc_url_raw( $raw );
		case 'textarea':
			return sanitize_textarea_field( $raw );
		case 'select':
			return array_key_exists( $raw, $def['options'] ) ? $raw : (string) key( $def['options'] );
		case 'checkbox':
			return '1' === $raw ? '1' : '';
		case 'gallery':
			return preg_replace( '/[^\d,]/', '', $raw );
		case 'item':
		case 'user':
		case 'pkg':
			return absint( $raw );
		default:
			return sanitize_text_field( $raw );
	}
}

function arenafit_save_meta( $post_id, $post ) {
	$fields = arenafit_fields();
	$pt     = $post->post_type;
	if ( empty( $fields[ $pt ] ) ) {
		return;
	}
	if ( ! isset( $_POST['arenafit_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['arenafit_nonce'] ) ), 'arenafit_save_' . $pt ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$data       = isset( $_POST['arenafit_meta'] ) && is_array( $_POST['arenafit_meta'] ) ? wp_unslash( $_POST['arenafit_meta'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$old_status = get_post_meta( $post_id, '_af_status', true );

	foreach ( $fields[ $pt ] as $key => $def ) {
		if ( 'days' === $def['type'] ) {
			$days = array();
			if ( isset( $data[ $key ] ) && is_array( $data[ $key ] ) ) {
				foreach ( $data[ $key ] as $d ) {
					$d = absint( $d );
					if ( $d >= 1 && $d <= 7 ) {
						$days[] = $d;
					}
				}
			}
			update_post_meta( $post_id, $key, array_values( array_unique( $days ) ) );
			continue;
		}
		if ( 'trainers' === $def['type'] ) {
			$ids = array();
			if ( isset( $data[ $key ] ) && is_array( $data[ $key ] ) ) {
				foreach ( $data[ $key ] as $tid ) {
					$tid = absint( $tid );
					if ( $tid && 'af_trainer' === get_post_type( $tid ) ) {
						$ids[] = $tid;
					}
				}
			}
			update_post_meta( $post_id, $key, array_values( array_unique( $ids ) ) );
			continue;
		}
		$raw = isset( $data[ $key ] ) ? $data[ $key ] : '';
		update_post_meta( $post_id, $key, arenafit_sanitize_field( $def, $raw ) );
	}

	if ( 'af_booking' === $pt ) {
		arenafit_sync_booking( $post_id );
		$new_status = get_post_meta( $post_id, '_af_status', true );
		if ( $new_status !== $old_status && in_array( $new_status, array( 'confirmed', 'cancelled' ), true ) ) {
			arenafit_mail_status( $post_id, $new_status );
		}
	}
}
add_action( 'save_post', 'arenafit_save_meta', 10, 2 );

/**
 * Yönetim paneli betik ve stilleri (galeri seçici).
 */
function arenafit_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && in_array( $screen->post_type, array( 'af_hall', 'af_trainer' ), true ) ) {
		wp_enqueue_media();
		wp_enqueue_script( 'arenafit-admin', ARENAFIT_URI . '/assets/js/admin.js', array( 'jquery' ), ARENAFIT_VERSION, true );
		wp_add_inline_style( 'wp-admin', '.af-gallery-prev img{width:80px;height:80px;object-fit:cover;margin:0 6px 6px 0;border-radius:6px}' );
	}
}
add_action( 'admin_enqueue_scripts', 'arenafit_admin_assets' );

/* ------------------------------------------------------------------
 * Yönetim listeleri: sütunlar
 * ----------------------------------------------------------------*/

function arenafit_booking_columns( $cols ) {
	return array(
		'cb'       => $cols['cb'],
		'title'    => 'Rezervasyon',
		'af_item'  => 'Hizmet',
		'af_when'  => 'Tarih / Saat',
		'af_who'   => 'Müşteri',
		'af_state' => 'Durum',
	);
}
add_filter( 'manage_af_booking_posts_columns', 'arenafit_booking_columns' );

function arenafit_status_badge( $status ) {
	$labels = arenafit_status_labels();
	$colors = array(
		'pending'   => '#b7791f',
		'confirmed' => '#0e9f5b',
		'cancelled' => '#d14343',
	);
	$label  = isset( $labels[ $status ] ) ? $labels[ $status ] : $status;
	$color  = isset( $colors[ $status ] ) ? $colors[ $status ] : '#666';
	return '<span style="display:inline-block;padding:2px 10px;border-radius:99px;color:#fff;background:' . esc_attr( $color ) . ';font-size:12px">' . esc_html( $label ) . '</span>';
}

function arenafit_booking_column_data( $col, $post_id ) {
	switch ( $col ) {
		case 'af_item':
			$item = (int) get_post_meta( $post_id, '_af_item', true );
			echo esc_html( $item ? get_the_title( $item ) : '—' );
			if ( '1' === get_post_meta( $post_id, '_af_referee', true ) ) {
				echo ' <small>(+ hakem)</small>';
			}
			break;
		case 'af_when':
			$d = get_post_meta( $post_id, '_af_date', true );
			$s = (string) get_post_meta( $post_id, '_af_start', true );
			$m = (int) get_post_meta( $post_id, '_af_duration', true );
			$e = $s && $m ? arenafit_m2t( arenafit_t2m( $s ) + $m ) : '';
			echo esc_html( trim( ( $d ? wp_date( 'd.m.Y', strtotime( $d . ' 12:00' ) ) : '' ) . ' ' . $s . ( $e ? ' – ' . $e : '' ) ) );
			break;
		case 'af_who':
			echo esc_html( get_post_meta( $post_id, '_af_name', true ) ) . '<br><small>' . esc_html( get_post_meta( $post_id, '_af_phone', true ) ) . '</small>';
			break;
		case 'af_state':
			echo arenafit_status_badge( get_post_meta( $post_id, '_af_status', true ) ); // phpcs:ignore WordPress.Security.EscapeOutput
			break;
	}
}
add_action( 'manage_af_booking_posts_custom_column', 'arenafit_booking_column_data', 10, 2 );

function arenafit_member_columns( $cols ) {
	return array(
		'cb'        => $cols['cb'],
		'title'     => 'Başvuru',
		'af_who'    => 'Telefon / E-posta',
		'af_int'    => 'İlgilendiği hizmetler',
		'af_mstate' => 'Durum',
		'date'      => 'Tarih',
	);
}
add_filter( 'manage_af_member_posts_columns', 'arenafit_member_columns' );

function arenafit_member_column_data( $col, $post_id ) {
	switch ( $col ) {
		case 'af_who':
			echo esc_html( get_post_meta( $post_id, '_af_phone', true ) ) . '<br><small>' . esc_html( get_post_meta( $post_id, '_af_email', true ) ) . '</small>';
			break;
		case 'af_int':
			echo esc_html( get_post_meta( $post_id, '_af_interest', true ) );
			$plan = get_post_meta( $post_id, '_af_plan', true );
			if ( $plan ) {
				echo '<br><small>' . esc_html( $plan ) . '</small>';
			}
			break;
		case 'af_mstate':
			$labels = array(
				'new'       => 'Yeni',
				'contacted' => 'Görüşüldü',
				'done'      => 'Tamamlandı',
			);
			$s      = get_post_meta( $post_id, '_af_status', true );
			echo esc_html( isset( $labels[ $s ] ) ? $labels[ $s ] : 'Yeni' );
			break;
	}
}
add_action( 'manage_af_member_posts_custom_column', 'arenafit_member_column_data', 10, 2 );

function arenafit_package_columns( $cols ) {
	$cols['af_price'] = 'Ücret';
	return $cols;
}
add_filter( 'manage_af_package_posts_columns', 'arenafit_package_columns' );

function arenafit_package_column_data( $col, $post_id ) {
	if ( 'af_price' === $col ) {
		echo esc_html( arenafit_money( get_post_meta( $post_id, '_af_price', true ) ) );
	}
}
add_action( 'manage_af_package_posts_custom_column', 'arenafit_package_column_data', 10, 2 );

/**
 * Rezervasyon listesi: tarihe göre sırala (en yeni üstte).
 */
function arenafit_booking_admin_order( $q ) {
	if ( ! is_admin() || ! $q->is_main_query() || 'af_booking' !== $q->get( 'post_type' ) ) {
		return;
	}
	if ( ! $q->get( 'orderby' ) ) {
		$q->set( 'meta_key', '_af_date' );
		$q->set( 'orderby', 'meta_value' );
		$q->set( 'order', 'DESC' );
	}
}
add_action( 'pre_get_posts', 'arenafit_booking_admin_order' );

/**
 * Hızlı eylemler: Onayla / İptal et.
 */
function arenafit_booking_row_actions( $actions, $post ) {
	if ( 'af_booking' !== $post->post_type || ! current_user_can( 'edit_post', $post->ID ) ) {
		return $actions;
	}
	foreach ( array( 'confirmed' => 'Onayla', 'cancelled' => 'İptal et' ) as $st => $label ) {
		$url             = wp_nonce_url(
			admin_url( 'admin-post.php?action=arenafit_booking_status&post=' . (int) $post->ID . '&status=' . $st ),
			'arenafit_status_' . $post->ID
		);
		$actions[ 'af_' . $st ] = '<a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
	}
	return $actions;
}
add_filter( 'post_row_actions', 'arenafit_booking_row_actions', 10, 2 );

function arenafit_booking_status_action() {
	$id     = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
	$status = isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : '';
	if ( ! $id || ! current_user_can( 'edit_post', $id ) || 'af_booking' !== get_post_type( $id ) ) {
		wp_die( 'Yetkiniz yok.' );
	}
	check_admin_referer( 'arenafit_status_' . $id );
	if ( in_array( $status, array( 'confirmed', 'cancelled' ), true ) ) {
		$old = get_post_meta( $id, '_af_status', true );
		update_post_meta( $id, '_af_status', $status );
		if ( $old !== $status ) {
			arenafit_mail_status( $id, $status );
		}
	}
	wp_safe_redirect( admin_url( 'edit.php?post_type=af_booking' ) );
	exit;
}
add_action( 'admin_post_arenafit_booking_status', 'arenafit_booking_status_action' );
