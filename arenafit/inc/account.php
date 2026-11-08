<?php
/**
 * Üye alanı: giriş, kayıt, profil, üye paketleri, rezervasyon geçmişi.
 *
 * Üyeler WordPress kullanıcısıdır (rol: "Üye"). Yönetim paneline giremezler.
 * Üye paketlerini site yöneticisi "Üye Paketleri" menüsünden tanımlar.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ARENAFIT_CANCEL_HOURS' ) ) {
	define( 'ARENAFIT_CANCEL_HOURS', 12 ); // Üye, rezervasyondan en az bu kadar saat önce iptal edebilir.
}

/* ------------------------------------------------------------------
 * Rol ve içerik türü
 * ----------------------------------------------------------------*/

function arenafit_ensure_role() {
	if ( ! get_role( 'af_member' ) ) {
		add_role( 'af_member', 'Üye', array( 'read' => true ) );
	}
}
add_action( 'init', 'arenafit_ensure_role', 5 );

function arenafit_register_sub_type() {
	register_post_type(
		'af_sub',
		array(
			'labels'          => array(
				'name'          => 'Üye Paketleri',
				'singular_name' => 'Üye Paketi',
				'menu_name'     => 'Üye Paketleri',
				'all_items'     => 'Tüm Üye Paketleri',
				'add_new'       => 'Pakete Üye Tanımla',
				'add_new_item'  => 'Üyeye Paket Tanımla',
				'edit_item'     => 'Üye Paketini Düzenle',
				'search_items'  => 'Üye Paketi Ara',
				'not_found'     => 'Kayıt bulunamadı',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-groups',
			'menu_position'   => 25,
			'capability_type' => 'post',
			'supports'        => array( 'title' ),
		)
	);
}
add_action( 'init', 'arenafit_register_sub_type' );

function arenafit_sub_fields( $fields ) {
	$fields['af_sub'] = array(
		'_af_user'     => array( 'label' => 'Üye', 'type' => 'user', 'help' => 'Üye önce siteden kayıt olmalı (veya Kullanıcılar > Yeni Ekle ile rolü "Üye" olarak oluşturun).' ),
		'_af_pkg'      => array( 'label' => 'Paket', 'type' => 'pkg' ),
		'_af_sessions' => array( 'label' => 'Toplam seans hakkı', 'type' => 'number', 'help' => 'Boş bırakırsanız paketteki seans sayısı alınır.' ),
		'_af_used'     => array( 'label' => 'Kullanılan seans', 'type' => 'number', 'help' => 'Üye seansa geldikçe bu sayıyı artırın.' ),
		'_af_start'    => array( 'label' => 'Başlangıç tarihi', 'type' => 'date', 'help' => 'Boşsa bugün.' ),
		'_af_end'      => array( 'label' => 'Bitiş tarihi', 'type' => 'date', 'help' => 'Boşsa süresiz.' ),
		'_af_status'   => array(
			'label'   => 'Durum',
			'type'    => 'select',
			'options' => array(
				'active'    => 'Aktif',
				'cancelled' => 'İptal / dondurulmuş',
			),
		),
		'_af_note'     => array( 'label' => 'Üyeye görünen not', 'type' => 'textarea', 'help' => 'Örn: Salı ve Perşembe 19:00 seansları.' ),
	);
	return $fields;
}
add_filter( 'arenafit_fields', 'arenafit_sub_fields' );

function arenafit_sub_titles( $titles ) {
	$titles['af_sub'] = 'Paket tanımı';
	return $titles;
}
add_filter( 'arenafit_meta_box_titles', 'arenafit_sub_titles' );

/**
 * Kayıttan sonra: boş alanları doldur, başlığı "Üye – Paket" yap.
 */
function arenafit_sync_sub( $post_id, $post ) {
	if ( 'af_sub' !== $post->post_type || wp_is_post_revision( $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
		return;
	}
	if ( ! isset( $_POST['arenafit_nonce'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	$uid = (int) get_post_meta( $post_id, '_af_user', true );
	$pkg = (int) get_post_meta( $post_id, '_af_pkg', true );

	if ( '' === get_post_meta( $post_id, '_af_sessions', true ) && $pkg ) {
		$n = get_post_meta( $pkg, '_af_sessions', true );
		if ( '' !== $n ) {
			update_post_meta( $post_id, '_af_sessions', $n );
		}
	}
	if ( '' === get_post_meta( $post_id, '_af_used', true ) ) {
		update_post_meta( $post_id, '_af_used', 0 );
	}
	if ( '' === get_post_meta( $post_id, '_af_start', true ) ) {
		update_post_meta( $post_id, '_af_start', wp_date( 'Y-m-d' ) );
	}

	$u     = $uid ? get_userdata( $uid ) : false;
	$title = ( $u ? $u->display_name : 'Üye seçilmedi' ) . ' – ' . ( $pkg ? get_the_title( $pkg ) : 'Paket seçilmedi' );
	if ( $post->post_title !== $title ) {
		// Başlık güncellenirken save_post yeniden tetiklenir; alanların üzerine yazılmaması için iki işlevi geçici çıkar.
		remove_action( 'save_post', 'arenafit_sync_sub', 20 );
		remove_action( 'save_post', 'arenafit_save_meta', 10 );
		wp_update_post(
			array(
				'ID'         => $post_id,
				'post_title' => $title,
			)
		);
		add_action( 'save_post', 'arenafit_save_meta', 10, 2 );
		add_action( 'save_post', 'arenafit_sync_sub', 20, 2 );
	}
}
add_action( 'save_post', 'arenafit_sync_sub', 20, 2 );

/**
 * Üye paketinin güncel durumu.
 */
function arenafit_sub_state( $id ) {
	$total  = (int) get_post_meta( $id, '_af_sessions', true );
	$used   = (int) get_post_meta( $id, '_af_used', true );
	$end    = (string) get_post_meta( $id, '_af_end', true );
	$start  = (string) get_post_meta( $id, '_af_start', true );
	$status = 'cancelled' === get_post_meta( $id, '_af_status', true ) ? 'cancelled' : 'active';
	if ( 'active' === $status && $end && $end < wp_date( 'Y-m-d' ) ) {
		$status = 'expired';
	} elseif ( 'active' === $status && $total > 0 && $used >= $total ) {
		$status = 'finished';
	}
	return array(
		'total'  => $total,
		'used'   => $used,
		'left'   => $total > 0 ? max( 0, $total - $used ) : null,
		'start'  => $start,
		'end'    => $end,
		'status' => $status,
		'pct'    => $total > 0 ? min( 100, round( $used / $total * 100 ) ) : 0,
	);
}

function arenafit_sub_status_label( $s ) {
	$l = array(
		'active'    => 'Aktif',
		'expired'   => 'Süresi doldu',
		'finished'  => 'Seanslar bitti',
		'cancelled' => 'Dondurulmuş / iptal',
	);
	return isset( $l[ $s ] ) ? $l[ $s ] : $s;
}

function arenafit_sub_columns( $cols ) {
	return array(
		'cb'      => $cols['cb'],
		'title'   => 'Üye – Paket',
		'af_left' => 'Kalan seans',
		'af_end'  => 'Bitiş',
		'af_st'   => 'Durum',
	);
}
add_filter( 'manage_af_sub_posts_columns', 'arenafit_sub_columns' );

function arenafit_sub_column_data( $col, $post_id ) {
	$s = arenafit_sub_state( $post_id );
	switch ( $col ) {
		case 'af_left':
			echo esc_html( null === $s['left'] ? '—' : $s['left'] . ' / ' . $s['total'] );
			break;
		case 'af_end':
			echo esc_html( $s['end'] ? wp_date( 'd.m.Y', strtotime( $s['end'] . ' 12:00' ) ) : 'Süresiz' );
			break;
		case 'af_st':
			echo esc_html( arenafit_sub_status_label( $s['status'] ) );
			break;
	}
}
add_action( 'manage_af_sub_posts_custom_column', 'arenafit_sub_column_data', 10, 2 );

/* ------------------------------------------------------------------
 * Üyeler yönetim paneline girmez
 * ----------------------------------------------------------------*/

function arenafit_block_admin() {
	global $pagenow;
	if ( ! is_user_logged_in() || wp_doing_ajax() || wp_doing_cron() || 'admin-post.php' === $pagenow ) {
		return;
	}
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_safe_redirect( arenafit_account_url() );
		exit;
	}
}
add_action( 'admin_init', 'arenafit_block_admin' );

function arenafit_hide_admin_bar( $show ) {
	return current_user_can( 'edit_posts' ) ? $show : false;
}
add_filter( 'show_admin_bar', 'arenafit_hide_admin_bar' );

function arenafit_login_redirect( $redirect_to, $request, $user ) {
	if ( $user instanceof WP_User && ! user_can( $user, 'edit_posts' ) ) {
		return arenafit_account_url();
	}
	return $redirect_to;
}
add_filter( 'login_redirect', 'arenafit_login_redirect', 10, 3 );

/* ------------------------------------------------------------------
 * Bildirim mesajları (adres çubuğundaki ?af_msg=kod)
 * ----------------------------------------------------------------*/

function arenafit_account_notices() {
	return array(
		'login_failed'   => array( 'err', 'Şifre hatalı. Lütfen tekrar deneyin veya "Şifremi unuttum" bağlantısını kullanın.' ),
		'login_nouser'   => array( 'err', 'Bu e-posta ile kayıtlı bir hesap bulunamadı. Adresi kontrol edin veya yandaki formdan üye olun.' ),
		'bad_request'    => array( 'err', 'İşlem doğrulanamadı. Sayfayı yenileyip tekrar deneyin.' ),
		'too_many'       => array( 'err', 'Çok fazla hatalı giriş denemesi yaptınız. Güvenliğiniz için 15 dakika sonra tekrar deneyin veya "Şifremi unuttum" bağlantısını kullanın.' ),
		'reg_name'       => array( 'err', 'Lütfen adınızı ve soyadınızı yazın.' ),
		'reg_email'      => array( 'err', 'Lütfen geçerli bir e-posta adresi yazın.' ),
		'reg_exists'     => array( 'err', 'Bu e-posta ile kayıtlı bir hesap var. Lütfen giriş yapın veya "Şifremi unuttum" bağlantısını kullanın.' ),
		'reg_phone'      => array( 'err', 'Lütfen geçerli bir telefon numarası girin.' ),
		'reg_pass'       => array( 'err', 'Şifreniz en az 8 karakter olmalı.' ),
		'reg_kvkk'       => array( 'err', 'Üye olmak için KVKK aydınlatma metnini onaylamanız gerekir.' ),
		'reg_fail'       => array( 'err', 'Hesap oluşturulamadı. Lütfen tekrar deneyin.' ),
		'registered'     => array( 'ok', 'Hesabınız oluşturuldu, hoş geldiniz!' ),
		'profile_ok'     => array( 'ok', 'Bilgileriniz güncellendi.' ),
		'profile_name'   => array( 'err', 'Lütfen adınızı ve soyadınızı yazın.' ),
		'profile_phone'  => array( 'err', 'Lütfen geçerli bir telefon numarası girin.' ),
		'pass_wrong'     => array( 'err', 'Mevcut şifreniz hatalı.' ),
		'pass_short'     => array( 'err', 'Yeni şifre en az 8 karakter olmalı.' ),
		'pass_match'     => array( 'err', 'Yeni şifre ve tekrarı aynı değil.' ),
		'cancel_ok'      => array( 'ok', 'Rezervasyonunuz iptal edildi.' ),
		'cancel_late'    => array( 'err', 'Başlangıca ' . ARENAFIT_CANCEL_HOURS . ' saatten az kaldığı için online iptal edilemiyor. Lütfen bizi arayın.' ),
		'cancel_invalid' => array( 'err', 'Bu rezervasyon iptal edilemedi.' ),
		'logged_out'     => array( 'ok', 'Çıkış yaptınız.' ),
	);
}

function arenafit_back( $code ) {
	wp_safe_redirect( add_query_arg( 'af_msg', $code, arenafit_account_url() ) );
	exit;
}

function arenafit_check_nonce( $action ) {
	return isset( $_POST['_afn'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_afn'] ) ), $action );
}

function arenafit_valid_phone( $phone ) {
	$d = preg_replace( '/\D/', '', (string) $phone );
	return strlen( $d ) >= 10 && strlen( $d ) <= 13;
}

/* ------------------------------------------------------------------
 * İşleyiciler: giriş, kayıt, profil, iptal
 * ----------------------------------------------------------------*/

/**
 * Giriş denemesi sınırı: yalnızca HATALI denemeler sayılır (IP + e-posta başına),
 * başarılı girişte sayaç sıfırlanır. Böylece doğru şifreyi giren kimse kilitlenmez.
 */
if ( ! defined( 'ARENAFIT_LOGIN_MAX' ) ) {
	define( 'ARENAFIT_LOGIN_MAX', 8 ); // Bu kadar hatalı denemeden sonra 15 dakika beklenir.
}
if ( ! defined( 'ARENAFIT_LOGIN_WINDOW' ) ) {
	define( 'ARENAFIT_LOGIN_WINDOW', 15 * MINUTE_IN_SECONDS );
}

function arenafit_login_keys( $login ) {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'x';
	return array(
		'one' => 'af_lf_' . md5( $ip . '|' . strtolower( $login ) ),
		'ip'  => 'af_lf_ip_' . md5( $ip ),
	);
}

function arenafit_login_locked( $login ) {
	$k = arenafit_login_keys( $login );
	return (int) get_transient( $k['one'] ) >= ARENAFIT_LOGIN_MAX || (int) get_transient( $k['ip'] ) >= ARENAFIT_LOGIN_MAX * 4;
}

function arenafit_login_failed( $login ) {
	foreach ( arenafit_login_keys( $login ) as $key ) {
		set_transient( $key, (int) get_transient( $key ) + 1, ARENAFIT_LOGIN_WINDOW );
	}
}

function arenafit_login_ok( $login ) {
	$k = arenafit_login_keys( $login );
	delete_transient( $k['one'] );
}

function arenafit_do_login() {
	if ( ! arenafit_check_nonce( 'arenafit_login' ) || ! empty( $_POST['af_website'] ) ) {
		arenafit_back( 'bad_request' );
	}
	$login = isset( $_POST['log'] ) ? sanitize_text_field( wp_unslash( $_POST['log'] ) ) : '';
	$pass  = isset( $_POST['pwd'] ) ? (string) wp_unslash( $_POST['pwd'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	if ( arenafit_login_locked( $login ) ) {
		arenafit_back( 'too_many' );
	}
	$user  = wp_signon(
		array(
			'user_login'    => $login,
			'user_password' => $pass,
			'remember'      => ! empty( $_POST['remember'] ),
		),
		is_ssl()
	);
	if ( is_wp_error( $user ) ) {
		arenafit_login_failed( $login );
		$code = $user->get_error_code();
		arenafit_back( in_array( $code, array( 'invalid_username', 'invalid_email' ), true ) ? 'login_nouser' : 'login_failed' );
	}
	arenafit_login_ok( $login );
	wp_safe_redirect( user_can( $user, 'edit_posts' ) ? admin_url() : arenafit_account_url() );
	exit;
}
add_action( 'admin_post_nopriv_arenafit_login', 'arenafit_do_login' );
add_action( 'admin_post_arenafit_login', 'arenafit_do_login' );

function arenafit_do_register() {
	if ( ! arenafit_check_nonce( 'arenafit_register' ) || ! empty( $_POST['af_website'] ) ) {
		arenafit_back( 'bad_request' );
	}
	if ( arenafit_rate_limited( 'register', 10 ) ) {
		arenafit_back( 'too_many' );
	}
	$name  = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$pass  = isset( $_POST['pass'] ) ? (string) wp_unslash( $_POST['pass'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

	if ( mb_strlen( $name ) < 3 || mb_strlen( $name ) > 80 ) {
		arenafit_back( 'reg_name' );
	}
	if ( ! is_email( $email ) ) {
		arenafit_back( 'reg_email' );
	}
	if ( ! arenafit_valid_phone( $phone ) ) {
		arenafit_back( 'reg_phone' );
	}
	if ( strlen( $pass ) < 8 ) {
		arenafit_back( 'reg_pass' );
	}
	if ( empty( $_POST['kvkk'] ) ) {
		arenafit_back( 'reg_kvkk' );
	}
	if ( email_exists( $email ) || username_exists( $email ) ) {
		arenafit_back( 'reg_exists' );
	}

	$parts = preg_split( '/\s+/u', $name, 2 );
	$uid   = wp_insert_user(
		array(
			'user_login'   => $email,
			'user_email'   => $email,
			'user_pass'    => $pass,
			'display_name' => $name,
			'nickname'     => $name,
			'first_name'   => $parts[0],
			'last_name'    => isset( $parts[1] ) ? $parts[1] : '',
			'role'         => 'af_member',
		)
	);
	if ( is_wp_error( $uid ) ) {
		arenafit_back( 'reg_fail' );
	}
	update_user_meta( $uid, 'af_phone', $phone );

	$site = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	wp_mail(
		arenafit_notify_address(),
		'[' . $site . '] Yeni üye kaydı: ' . $name,
		"Siteye yeni bir üye kayıt oldu.\n\nAd soyad: " . $name . "\nE-posta: " . $email . "\nTelefon: " . $phone . "\n\nÜyeye paket tanımlamak için: " . admin_url( 'post-new.php?post_type=af_sub' )
	);
	wp_mail( $email, $site . ' – Hoş geldiniz', 'Merhaba ' . $name . ",\n\nÜyeliğiniz oluşturuldu. Giriş yapıp rezervasyonlarınızı ve paketinizi görebilirsiniz:\n" . arenafit_account_url() . "\n\n" . $site );

	wp_set_current_user( $uid );
	wp_set_auth_cookie( $uid, true );
	arenafit_back( 'registered' );
}
add_action( 'admin_post_nopriv_arenafit_register', 'arenafit_do_register' );
add_action( 'admin_post_arenafit_register', 'arenafit_do_register' );

function arenafit_do_profile() {
	if ( ! is_user_logged_in() || ! arenafit_check_nonce( 'arenafit_profile' ) ) {
		arenafit_back( 'bad_request' );
	}
	$user  = wp_get_current_user();
	$name  = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$cur   = isset( $_POST['pass_current'] ) ? (string) wp_unslash( $_POST['pass_current'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$new   = isset( $_POST['pass_new'] ) ? (string) wp_unslash( $_POST['pass_new'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$new2  = isset( $_POST['pass_new2'] ) ? (string) wp_unslash( $_POST['pass_new2'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

	if ( mb_strlen( $name ) < 3 || mb_strlen( $name ) > 80 ) {
		arenafit_back( 'profile_name' );
	}
	if ( ! arenafit_valid_phone( $phone ) ) {
		arenafit_back( 'profile_phone' );
	}

	$data = array(
		'ID'           => $user->ID,
		'display_name' => $name,
		'nickname'     => $name,
	);
	$parts              = preg_split( '/\s+/u', $name, 2 );
	$data['first_name'] = $parts[0];
	$data['last_name']  = isset( $parts[1] ) ? $parts[1] : '';

	$change_pass = ( '' !== $new || '' !== $new2 || '' !== $cur );
	if ( $change_pass ) {
		if ( ! wp_check_password( $cur, $user->user_pass, $user->ID ) ) {
			arenafit_back( 'pass_wrong' );
		}
		if ( strlen( $new ) < 8 ) {
			arenafit_back( 'pass_short' );
		}
		if ( $new !== $new2 ) {
			arenafit_back( 'pass_match' );
		}
		$data['user_pass'] = $new;
	}

	wp_update_user( $data );
	update_user_meta( $user->ID, 'af_phone', $phone );
	if ( $change_pass ) {
		wp_set_current_user( $user->ID );
		wp_set_auth_cookie( $user->ID, true ); // Şifre değişince oturum yenilenir.
	}
	arenafit_back( 'profile_ok' );
}
add_action( 'admin_post_arenafit_profile', 'arenafit_do_profile' );

/**
 * Rezervasyon başlangıcının zaman damgası.
 */
function arenafit_booking_ts( $id ) {
	$d = (string) get_post_meta( $id, '_af_date', true );
	$t = (string) get_post_meta( $id, '_af_start', true );
	$o = DateTimeImmutable::createFromFormat( 'Y-m-d H:i', $d . ' ' . $t, wp_timezone() );
	return $o ? $o->getTimestamp() : 0;
}

function arenafit_do_cancel() {
	$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
	if ( ! is_user_logged_in() || ! $id || ! arenafit_check_nonce( 'arenafit_cancel_' . $id ) ) {
		arenafit_back( 'bad_request' );
	}
	if ( 'af_booking' !== get_post_type( $id ) || (int) get_post_meta( $id, '_af_user', true ) !== get_current_user_id() || 'cancelled' === get_post_meta( $id, '_af_status', true ) ) {
		arenafit_back( 'cancel_invalid' );
	}
	$ts = arenafit_booking_ts( $id );
	if ( ! $ts || $ts < time() ) {
		arenafit_back( 'cancel_invalid' );
	}
	if ( $ts - time() < ARENAFIT_CANCEL_HOURS * HOUR_IN_SECONDS ) {
		arenafit_back( 'cancel_late' );
	}

	update_post_meta( $id, '_af_status', 'cancelled' );
	arenafit_mail_status( $id, 'cancelled' ); // Üyeye bilgi.

	$site = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	wp_mail(
		arenafit_notify_address(),
		'[' . $site . '] Üye rezervasyonunu iptal etti: ' . get_post_meta( $id, '_af_name', true ),
		"Bir üye rezervasyonunu panelden iptal etti. Saat tekrar boşa çıktı.\n\n" . implode( "\n", arenafit_booking_lines( $id ) ) . "\n\nÜye: " . get_post_meta( $id, '_af_name', true ) . "\nTelefon: " . get_post_meta( $id, '_af_phone', true )
	);
	arenafit_back( 'cancel_ok' );
}
add_action( 'admin_post_arenafit_cancel', 'arenafit_do_cancel' );

/* ------------------------------------------------------------------
 * Yardımcılar
 * ----------------------------------------------------------------*/

/**
 * Giriş yapmış üyenin bilgisini form alanlarına doldurmak için.
 */
function arenafit_prefill( $key ) {
	if ( ! is_user_logged_in() ) {
		return '';
	}
	$u = wp_get_current_user();
	switch ( $key ) {
		case 'name':
			return $u->display_name;
		case 'email':
			return $u->user_email;
		case 'phone':
			return (string) get_user_meta( $u->ID, 'af_phone', true );
	}
	return '';
}

function arenafit_user_subs( $uid ) {
	return get_posts(
		array(
			'post_type'   => 'af_sub',
			'post_status' => 'publish',
			'numberposts' => 50,
			'orderby'     => 'date',
			'order'       => 'DESC',
			'meta_query'  => array(
				array(
					'key'   => '_af_user',
					'value' => (int) $uid,
					'type'  => 'NUMERIC',
				),
			),
		)
	);
}

function arenafit_user_bookings( $uid ) {
	return get_posts(
		array(
			'post_type'   => 'af_booking',
			'post_status' => 'publish',
			'numberposts' => 100,
			'meta_key'    => '_af_date',
			'orderby'     => 'meta_value',
			'order'       => 'DESC',
			'meta_query'  => array(
				array(
					'key'   => '_af_user',
					'value' => (int) $uid,
					'type'  => 'NUMERIC',
				),
			),
		)
	);
}

/* ------------------------------------------------------------------
 * Görünüm
 * ----------------------------------------------------------------*/

function arenafit_account_notice() {
	if ( empty( $_GET['af_msg'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return '';
	}
	$code = sanitize_key( wp_unslash( $_GET['af_msg'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
	$all  = arenafit_account_notices();
	if ( ! isset( $all[ $code ] ) ) {
		return '';
	}
	return '<div class="af-notice af-notice--' . esc_attr( $all[ $code ][0] ) . '" role="status">' . esc_html( $all[ $code ][1] ) . '</div>';
}

function arenafit_auth_forms() {
	ob_start();
	?>
	<div class="af-auth">
		<div class="af-panel">
			<h2 class="af-panel__title"><?php echo arenafit_icon( 'user' ); // phpcs:ignore ?> Üye Girişi</h2>
			<form class="af-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="arenafit_login">
				<input type="hidden" name="_afn" value="<?php echo esc_attr( wp_create_nonce( 'arenafit_login' ) ); ?>">
				<input type="text" name="af_website" class="af-hp" tabindex="-1" autocomplete="off" aria-hidden="true">
				<div class="af-field">
					<label for="afl-log">E-posta</label>
					<input type="text" name="log" id="afl-log" autocomplete="username" required>
				</div>
				<div class="af-field">
					<label for="afl-pwd">Şifre</label>
					<input type="password" name="pwd" id="afl-pwd" autocomplete="current-password" required>
				</div>
				<label class="af-check"><input type="checkbox" name="remember" value="1"> <span>Beni hatırla</span></label>
				<button type="submit" class="af-btn af-btn--accent af-btn--block">Giriş Yap</button>
				<p class="af-fine"><a href="<?php echo esc_url( wp_lostpassword_url( arenafit_account_url() ) ); ?>">Şifremi unuttum</a></p>
			</form>
		</div>

		<div class="af-panel">
			<h2 class="af-panel__title"><?php echo arenafit_icon( 'users' ); // phpcs:ignore ?> Yeni Üye Ol</h2>
			<form class="af-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="arenafit_register">
				<input type="hidden" name="_afn" value="<?php echo esc_attr( wp_create_nonce( 'arenafit_register' ) ); ?>">
				<input type="text" name="af_website" class="af-hp" tabindex="-1" autocomplete="off" aria-hidden="true">
				<div class="af-field">
					<label for="afr-name">Ad soyad</label>
					<input type="text" name="name" id="afr-name" autocomplete="name" required>
				</div>
				<div class="af-row">
					<div class="af-field">
						<label for="afr-email">E-posta</label>
						<input type="email" name="email" id="afr-email" autocomplete="email" required>
					</div>
					<div class="af-field">
						<label for="afr-phone">Telefon</label>
						<input type="tel" name="phone" id="afr-phone" autocomplete="tel" inputmode="tel" placeholder="05xx xxx xx xx" required>
					</div>
				</div>
				<div class="af-field">
					<label for="afr-pass">Şifre <small>(en az 8 karakter)</small></label>
					<input type="password" name="pass" id="afr-pass" autocomplete="new-password" minlength="8" required>
				</div>
				<label class="af-check"><input type="checkbox" name="kvkk" value="1" required> <span><?php echo wp_kses( arenafit_kvkk_label(), array( 'a' => array( 'href' => array(), 'target' => array(), 'rel' => array() ) ) ); ?></span></label>
				<button type="submit" class="af-btn af-btn--block">Hesap Oluştur</button>
				<p class="af-fine">Üye olunca rezervasyonlarınızı ve paketinizi bu sayfadan takip edersiniz.</p>
			</form>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

function arenafit_account_dashboard() {
	$user     = wp_get_current_user();
	$subs     = arenafit_user_subs( $user->ID );
	$bookings = arenafit_user_bookings( $user->ID );
	$now      = time();
	$upcoming = array();
	$past     = array();
	foreach ( $bookings as $b ) {
		$ts = arenafit_booking_ts( $b->ID );
		if ( 'cancelled' !== get_post_meta( $b->ID, '_af_status', true ) && $ts >= $now ) {
			$upcoming[] = $b;
		} else {
			$past[] = $b;
		}
	}
	$upcoming = array_reverse( $upcoming ); // En yakın tarih üstte.
	$labels   = arenafit_status_labels();

	$row = function ( $b, $can_cancel ) use ( $labels ) {
		$id     = $b->ID;
		$item   = (int) get_post_meta( $id, '_af_item', true );
		$date   = (string) get_post_meta( $id, '_af_date', true );
		$start  = (string) get_post_meta( $id, '_af_start', true );
		$dur    = (int) get_post_meta( $id, '_af_duration', true );
		$end    = $start ? arenafit_m2t( arenafit_t2m( $start ) + $dur ) : '';
		$status = (string) get_post_meta( $id, '_af_status', true );
		$ts     = strtotime( $date . ' 12:00' );
		?>
		<li class="af-brow">
			<div class="af-brow__date">
				<strong><?php echo esc_html( wp_date( 'j', $ts ) ); ?></strong>
				<span><?php echo esc_html( wp_date( 'M', $ts ) ); ?></span>
			</div>
			<div class="af-brow__main">
				<h3><?php echo esc_html( $item ? get_the_title( $item ) : 'Rezervasyon' ); ?></h3>
				<p><?php echo esc_html( wp_date( 'l', $ts ) . ' · ' . $start . ' – ' . $end ); ?><?php echo '1' === get_post_meta( $id, '_af_referee', true ) ? ' · Hakem hizmeti' : ''; ?></p>
			</div>
			<div class="af-brow__side">
				<span class="af-status af-status--<?php echo esc_attr( $status ); ?>"><?php echo esc_html( isset( $labels[ $status ] ) ? $labels[ $status ] : $status ); ?></span>
				<?php if ( $can_cancel && 'cancelled' !== $status ) : ?>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('Bu rezervasyonu iptal etmek istiyor musunuz?');">
						<input type="hidden" name="action" value="arenafit_cancel">
						<input type="hidden" name="id" value="<?php echo (int) $id; ?>">
						<input type="hidden" name="_afn" value="<?php echo esc_attr( wp_create_nonce( 'arenafit_cancel_' . $id ) ); ?>">
						<button type="submit" class="af-linkbtn">İptal et</button>
					</form>
				<?php endif; ?>
			</div>
		</li>
		<?php
	};

	ob_start();
	?>
	<div class="af-detail">
		<div class="af-detail__main">
			<h2 class="af-h3">Paketlerim</h2>
			<?php if ( $subs ) : ?>
				<div class="af-subs">
					<?php
					foreach ( $subs as $s ) :
						$st  = arenafit_sub_state( $s->ID );
						$pkg = (int) get_post_meta( $s->ID, '_af_pkg', true );
						$nt  = get_post_meta( $s->ID, '_af_note', true );
						?>
						<article class="af-card af-sub af-sub--<?php echo esc_attr( $st['status'] ); ?>">
							<div class="af-sub__top">
								<h3><?php echo esc_html( $pkg ? get_the_title( $pkg ) : 'Paket' ); ?></h3>
								<span class="af-status af-status--sub-<?php echo esc_attr( $st['status'] ); ?>"><?php echo esc_html( arenafit_sub_status_label( $st['status'] ) ); ?></span>
							</div>
							<?php if ( $st['total'] > 0 ) : ?>
								<div class="af-progress" role="progressbar" aria-valuemin="0" aria-valuemax="<?php echo (int) $st['total']; ?>" aria-valuenow="<?php echo (int) $st['used']; ?>" aria-label="Kullanılan seans">
									<span style="width:<?php echo (int) $st['pct']; ?>%"></span>
								</div>
								<p class="af-sub__count"><strong><?php echo (int) $st['left']; ?> seans kaldı</strong> <span>(<?php echo (int) $st['used']; ?> / <?php echo (int) $st['total']; ?> kullanıldı)</span></p>
							<?php endif; ?>
							<ul class="af-pkg__facts">
								<?php if ( $st['start'] ) : ?>
									<li><?php echo arenafit_icon( 'calendar' ); // phpcs:ignore ?> Başlangıç: <?php echo esc_html( wp_date( 'd.m.Y', strtotime( $st['start'] . ' 12:00' ) ) ); ?></li>
								<?php endif; ?>
								<li><?php echo arenafit_icon( 'clock' ); // phpcs:ignore ?> Bitiş: <?php echo esc_html( $st['end'] ? wp_date( 'd.m.Y', strtotime( $st['end'] . ' 12:00' ) ) : 'Süresiz' ); ?></li>
							</ul>
							<?php if ( $nt ) : ?>
								<p class="af-sub__note"><?php echo esc_html( $nt ); ?></p>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<div class="af-empty">
					<p>Hesabınıza henüz tanımlı bir paket yok.</p>
					<p><a class="af-btn af-btn--sm" href="<?php echo esc_url( get_post_type_archive_link( 'af_package' ) ); ?>">PT Paketlerini İncele</a></p>
				</div>
			<?php endif; ?>

			<h2 class="af-h3">Yaklaşan rezervasyonlarım</h2>
			<?php if ( $upcoming ) : ?>
				<ul class="af-blist">
					<?php
					foreach ( $upcoming as $b ) {
						$row( $b, true );
					}
					?>
				</ul>
				<p class="af-fine af-left">Başlangıca <?php echo (int) ARENAFIT_CANCEL_HOURS; ?> saatten fazla varsa buradan iptal edebilirsiniz.</p>
			<?php else : ?>
				<div class="af-empty">
					<p>Yaklaşan rezervasyonunuz yok.</p>
					<p><a class="af-btn af-btn--accent af-btn--sm" href="<?php echo esc_url( arenafit_booking_url() ); ?>">Rezervasyon Yap</a></p>
				</div>
			<?php endif; ?>

			<?php if ( $past ) : ?>
				<h2 class="af-h3">Geçmiş rezervasyonlar</h2>
				<ul class="af-blist af-blist--past">
					<?php
					foreach ( array_slice( $past, 0, 20 ) as $b ) {
						$row( $b, false );
					}
					?>
				</ul>
			<?php endif; ?>
		</div>

		<aside class="af-detail__aside">
			<div class="af-sticky">
				<div class="af-panel">
					<h2 class="af-panel__title"><?php echo arenafit_icon( 'user' ); // phpcs:ignore ?> Profilim</h2>
					<form class="af-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="arenafit_profile">
						<input type="hidden" name="_afn" value="<?php echo esc_attr( wp_create_nonce( 'arenafit_profile' ) ); ?>">
						<div class="af-field">
							<label for="afp-name">Ad soyad</label>
							<input type="text" name="name" id="afp-name" value="<?php echo esc_attr( $user->display_name ); ?>" required>
						</div>
						<div class="af-field">
							<label for="afp-email">E-posta</label>
							<input type="text" id="afp-email" value="<?php echo esc_attr( $user->user_email ); ?>" disabled>
						</div>
						<div class="af-field">
							<label for="afp-phone">Telefon</label>
							<input type="tel" name="phone" id="afp-phone" value="<?php echo esc_attr( get_user_meta( $user->ID, 'af_phone', true ) ); ?>" inputmode="tel" required>
						</div>
						<details class="af-details">
							<summary>Şifremi değiştir</summary>
							<div class="af-field">
								<label for="afp-cur">Mevcut şifre</label>
								<input type="password" name="pass_current" id="afp-cur" autocomplete="current-password">
							</div>
							<div class="af-field">
								<label for="afp-new">Yeni şifre</label>
								<input type="password" name="pass_new" id="afp-new" autocomplete="new-password" minlength="8">
							</div>
							<div class="af-field">
								<label for="afp-new2">Yeni şifre (tekrar)</label>
								<input type="password" name="pass_new2" id="afp-new2" autocomplete="new-password" minlength="8">
							</div>
						</details>
						<button type="submit" class="af-btn af-btn--block">Bilgilerimi Kaydet</button>
					</form>
				</div>
				<div class="af-panel af-panel--soft">
					<h3 class="af-panel__title">Hızlı işlemler</h3>
					<div class="af-actions">
						<a class="af-btn af-btn--accent af-btn--block" href="<?php echo esc_url( arenafit_booking_url() ); ?>"><?php echo arenafit_icon( 'calendar' ); // phpcs:ignore ?> Yeni Rezervasyon</a>
						<a class="af-btn af-btn--block" href="<?php echo esc_url( arenafit_contact_url() ); ?>"><?php echo arenafit_icon( 'mail' ); // phpcs:ignore ?> Mesaj / Şikayet Gönder</a>
						<a class="af-btn af-btn--block" href="<?php echo esc_url( wp_logout_url( add_query_arg( 'af_msg', 'logged_out', arenafit_account_url() ) ) ); ?>">Çıkış Yap</a>
					</div>
				</div>
			</div>
		</aside>
	</div>
	<?php
	return ob_get_clean();
}

function arenafit_account_shortcode() {
	return arenafit_account_notice() . ( is_user_logged_in() ? arenafit_account_dashboard() : arenafit_auth_forms() );
}
add_shortcode( 'arenafit_account', 'arenafit_account_shortcode' );
