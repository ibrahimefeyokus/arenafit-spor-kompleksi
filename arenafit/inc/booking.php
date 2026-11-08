<?php
/**
 * Online randevu ve rezervasyon sistemi.
 *
 * Antrenör seansları ve salon kiralamaları için dolu/boş saat takvimi,
 * çakışma kontrolü, e-posta bildirimleri.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ARENAFIT_HORIZON' ) ) {
	define( 'ARENAFIT_HORIZON', 60 ); // Kaç gün sonrasına kadar rezervasyon alınır.
}

/* ------------------------------------------------------------------
 * Zaman yardımcıları
 * ----------------------------------------------------------------*/

function arenafit_t2m( $t ) {
	if ( ! preg_match( '/^(\d{1,2}):(\d{2})$/', (string) $t, $m ) ) {
		return null;
	}
	return ( (int) $m[1] ) * 60 + (int) $m[2];
}

function arenafit_m2t( $m ) {
	$m = (int) $m;
	return sprintf( '%02d:%02d', floor( $m / 60 ), $m % 60 );
}

function arenafit_item_ok( $kind, $item ) {
	$item = (int) $item;
	if ( ! $item ) {
		return false;
	}
	$map = array(
		'trainer' => 'af_trainer',
		'hall'    => 'af_hall',
	);
	return isset( $map[ $kind ] ) && get_post_type( $item ) === $map[ $kind ] && 'publish' === get_post_status( $item );
}

function arenafit_allowed_duration( $kind, $item, $requested ) {
	if ( 'hall' === $kind ) {
		$r = (int) $requested;
		return in_array( $r, array( 60, 120, 180 ), true ) ? $r : 60;
	}
	$m = (int) get_post_meta( $item, '_af_session_min', true );
	return $m > 0 ? $m : 60;
}

/**
 * Seçilen gün için açılış/kapanış (dakika). Kapalıysa null.
 */
function arenafit_bounds( $kind, $item, DateTimeImmutable $day ) {
	$open = arenafit_t2m( arenafit_opt( 'open', '07:00' ) );
	if ( null === $open ) {
		$open = 420;
	}
	$close = arenafit_t2m( arenafit_opt( 'close', '23:00' ) );
	if ( null === $close ) {
		$close = 1380;
	}

	if ( 'trainer' === $kind ) {
		$days = get_post_meta( $item, '_af_days', true );
		if ( is_array( $days ) && $days && ! in_array( (int) $day->format( 'N' ), array_map( 'intval', $days ), true ) ) {
			return null;
		}
		$s = arenafit_t2m( get_post_meta( $item, '_af_start', true ) );
		$e = arenafit_t2m( get_post_meta( $item, '_af_end', true ) );
		if ( null !== $s ) {
			$open = max( $open, $s );
		}
		if ( null !== $e ) {
			$close = min( $close, $e );
		}
	}
	return $close > $open ? array( $open, $close ) : null;
}

/**
 * O günkü dolu aralıklar: [[başlangıç, bitiş], ...] (dakika).
 */
function arenafit_booked( $kind, $item, $date ) {
	$ids = get_posts(
		array(
			'post_type'   => 'af_booking',
			'post_status' => 'publish',
			'numberposts' => -1,
			'fields'      => 'ids',
			'meta_query'  => array(
				'relation' => 'AND',
				array( 'key' => '_af_kind', 'value' => $kind ),
				array( 'key' => '_af_item', 'value' => (int) $item, 'type' => 'NUMERIC' ),
				array( 'key' => '_af_date', 'value' => $date ),
				array( 'key' => '_af_status', 'value' => 'cancelled', 'compare' => '!=' ),
			),
		)
	);
	$out = array();
	foreach ( $ids as $id ) {
		$s = get_post_meta( $id, '_af_start_min', true );
		$e = get_post_meta( $id, '_af_end_min', true );
		if ( '' !== $s && '' !== $e ) {
			$out[] = array( (int) $s, (int) $e );
		}
	}
	return $out;
}

/**
 * Bir gün için saat dilimleri: [['t'=>'18:00','ok'=>true], ...]
 */
function arenafit_slots( $kind, $item, $date, $duration ) {
	$tz  = wp_timezone();
	$day = DateTimeImmutable::createFromFormat( '!Y-m-d', $date, $tz );
	if ( ! $day || $day->format( 'Y-m-d' ) !== $date ) {
		return new WP_Error( 'date', 'Geçersiz tarih.' );
	}
	$today = new DateTimeImmutable( 'today', $tz );
	if ( $day < $today ) {
		return new WP_Error( 'past', 'Geçmiş bir tarih seçilemez.' );
	}
	if ( $day > $today->modify( '+' . ARENAFIT_HORIZON . ' days' ) ) {
		return new WP_Error( 'far', 'Rezervasyon en fazla ' . ARENAFIT_HORIZON . ' gün sonrası için yapılabilir.' );
	}

	$bounds = arenafit_bounds( $kind, $item, $day );
	if ( null === $bounds ) {
		return array(); // O gün hizmet yok.
	}
	list( $open, $close ) = $bounds;

	$booked  = arenafit_booked( $kind, $item, $date );
	$now     = new DateTimeImmutable( 'now', $tz );
	$now_min = ( $day == $today ) ? ( (int) $now->format( 'H' ) * 60 + (int) $now->format( 'i' ) ) : -1; // phpcs:ignore

	$out = array();
	for ( $s = $open; $s + $duration <= $close; $s += 60 ) {
		$ok = $s > $now_min;
		if ( $ok ) {
			foreach ( $booked as $b ) {
				if ( $s < $b[1] && $s + $duration > $b[0] ) {
					$ok = false;
					break;
				}
			}
		}
		$out[] = array(
			't'  => arenafit_m2t( $s ),
			'ok' => $ok,
		);
	}
	return $out;
}

/**
 * Önümüzdeki günlerin müsaitlik özeti (online takvim şeridi).
 * [['date'=>'2026-11-04','free'=>5,'total'=>12,'closed'=>false], ...]
 */
function arenafit_availability( $kind, $item, $days = 14 ) {
	$tz    = wp_timezone();
	$today = new DateTimeImmutable( 'today', $tz );
	$dur   = arenafit_allowed_duration( $kind, $item, 60 );
	$out   = array();
	for ( $i = 0; $i < $days; $i++ ) {
		$d     = $today->modify( '+' . $i . ' days' );
		$slots = arenafit_slots( $kind, $item, $d->format( 'Y-m-d' ), $dur );
		if ( is_wp_error( $slots ) ) {
			continue;
		}
		$free  = count( array_filter( $slots, function ( $s ) { return $s['ok']; } ) );
		$out[] = array(
			'date'   => $d->format( 'Y-m-d' ),
			'dow'    => (int) $d->format( 'N' ),
			'day'    => (int) $d->format( 'j' ),
			'free'   => $free,
			'total'  => count( $slots ),
			'closed' => ! $slots,
		);
	}
	return $out;
}

/**
 * Online takvim şeridi: güne tıklanınca yandaki rezervasyon formunda o tarih seçilir.
 */
function arenafit_calendar_strip( $kind, $item, $days = 14 ) {
	$months = array( 1 => 'Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara' );
	$short  = arenafit_days_short();
	$long   = arenafit_days_labels();
	$rows   = arenafit_availability( $kind, $item, $days );
	if ( ! $rows ) {
		return '';
	}
	ob_start();
	?>
	<div class="af-cal" data-af-cal>
		<div class="af-cal__legend" aria-hidden="true">
			<span><i class="is-free"></i> Müsait</span><span><i class="is-few"></i> Az yer kaldı</span><span><i class="is-full"></i> Dolu / kapalı</span>
		</div>
		<ul class="af-cal__days">
			<?php
			foreach ( $rows as $r ) :
				$m     = (int) substr( $r['date'], 5, 2 );
				$state = $r['closed'] ? 'is-closed' : ( 0 === $r['free'] ? 'is-full' : ( $r['free'] <= 2 ? 'is-few' : 'is-free' ) );
				$txt   = $r['closed'] ? 'Kapalı' : ( 0 === $r['free'] ? 'Dolu' : $r['free'] . ' boş saat' );
				$off   = $r['closed'] || 0 === $r['free'];
				?>
				<li>
					<button type="button" class="af-cal__day <?php echo esc_attr( $state ); ?>" data-date="<?php echo esc_attr( $r['date'] ); ?>"<?php echo $off ? ' disabled' : ''; ?> aria-label="<?php echo esc_attr( $r['day'] . ' ' . $months[ $m ] . ' ' . $long[ $r['dow'] ] . ': ' . $txt ); ?>">
						<span class="af-cal__dow"><?php echo esc_html( $short[ $r['dow'] ] ); ?></span>
						<strong><?php echo esc_html( $r['day'] ); ?></strong>
						<span class="af-cal__mon"><?php echo esc_html( $months[ $m ] ); ?></span>
						<em><?php echo esc_html( $txt ); ?></em>
					</button>
				</li>
			<?php endforeach; ?>
		</ul>
		<p class="af-fine">Bir güne tıklayın; o günün boş saatleri rezervasyon formunda listelenir.</p>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Rezervasyon yazısının türetilmiş alanlarını günceller.
 */
function arenafit_sync_booking( $post_id ) {
	$item = (int) get_post_meta( $post_id, '_af_item', true );
	$kind = ( 'af_hall' === get_post_type( $item ) ) ? 'hall' : 'trainer';
	$s    = arenafit_t2m( get_post_meta( $post_id, '_af_start', true ) );
	$dur  = (int) get_post_meta( $post_id, '_af_duration', true );
	if ( $dur < 1 ) {
		$dur = 60;
		update_post_meta( $post_id, '_af_duration', $dur );
	}
	update_post_meta( $post_id, '_af_kind', $kind );
	if ( null !== $s ) {
		update_post_meta( $post_id, '_af_start_min', $s );
		update_post_meta( $post_id, '_af_end_min', $s + $dur );
	}
	if ( ! get_post_meta( $post_id, '_af_status', true ) ) {
		update_post_meta( $post_id, '_af_status', 'pending' );
	}

	$post = get_post( $post_id );
	if ( $post && '' === trim( $post->post_title ) ) {
		remove_action( 'save_post', 'arenafit_save_meta', 10 );
		wp_update_post(
			array(
				'ID'         => $post_id,
				'post_title' => trim( get_post_meta( $post_id, '_af_name', true ) . ' – ' . get_post_meta( $post_id, '_af_date', true ) . ' ' . get_post_meta( $post_id, '_af_start', true ) ),
			)
		);
		add_action( 'save_post', 'arenafit_save_meta', 10, 2 );
	}
}

/* ------------------------------------------------------------------
 * Koruma: hız sınırı ve kilit
 * ----------------------------------------------------------------*/

function arenafit_rate_limited( $bucket, $limit = 6, $window = HOUR_IN_SECONDS ) {
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'x';
	$key = 'af_rl_' . $bucket . '_' . md5( $ip );
	$n   = (int) get_transient( $key );
	if ( $n >= $limit ) {
		return true;
	}
	set_transient( $key, $n + 1, $window );
	return false;
}

function arenafit_lock( $name ) {
	$existing = get_option( $name );
	if ( $existing && ( time() - (int) $existing ) > 30 ) {
		delete_option( $name );
	}
	return add_option( $name, time(), '', 'no' );
}

function arenafit_unlock( $name ) {
	delete_option( $name );
}

/* ------------------------------------------------------------------
 * AJAX: boş saatler
 * ----------------------------------------------------------------*/

function arenafit_ajax_slots() {
	nocache_headers();
	$kind = isset( $_GET['kind'] ) ? sanitize_key( wp_unslash( $_GET['kind'] ) ) : '';
	$item = isset( $_GET['item'] ) ? absint( $_GET['item'] ) : 0;
	$date = isset( $_GET['date'] ) ? sanitize_text_field( wp_unslash( $_GET['date'] ) ) : '';
	$req  = isset( $_GET['duration'] ) ? absint( $_GET['duration'] ) : 60;

	if ( ! arenafit_item_ok( $kind, $item ) ) {
		wp_send_json_error( array( 'message' => 'Geçersiz seçim.' ) );
	}
	$dur   = arenafit_allowed_duration( $kind, $item, $req );
	$slots = arenafit_slots( $kind, $item, $date, $dur );
	if ( is_wp_error( $slots ) ) {
		wp_send_json_error( array( 'message' => $slots->get_error_message() ) );
	}
	wp_send_json_success(
		array(
			'slots'    => $slots,
			'duration' => $dur,
		)
	);
}
add_action( 'wp_ajax_arenafit_slots', 'arenafit_ajax_slots' );
add_action( 'wp_ajax_nopriv_arenafit_slots', 'arenafit_ajax_slots' );

/* ------------------------------------------------------------------
 * AJAX: rezervasyon oluştur
 * ----------------------------------------------------------------*/

function arenafit_fail( $message ) {
	wp_send_json_error( array( 'message' => $message ) );
}

function arenafit_ajax_book() {
	nocache_headers();

	if ( ! empty( $_POST['af_website'] ) ) { // Bal küpü (honeypot).
		arenafit_fail( 'İşlem tamamlanamadı.' );
	}
	if ( arenafit_rate_limited( 'book', 20 ) ) {
		arenafit_fail( 'Çok fazla deneme yaptınız. Lütfen bir süre sonra tekrar deneyin veya bizi arayın.' );
	}

	$target = isset( $_POST['target'] ) ? sanitize_text_field( wp_unslash( $_POST['target'] ) ) : '';
	$parts  = explode( ':', $target );
	$kind   = sanitize_key( isset( $parts[0] ) ? $parts[0] : '' );
	$item   = absint( isset( $parts[1] ) ? $parts[1] : 0 );
	$date   = isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '';
	$start  = isset( $_POST['start'] ) ? sanitize_text_field( wp_unslash( $_POST['start'] ) ) : '';
	$name   = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$phone  = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$email  = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$note   = isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '';
	$req    = isset( $_POST['duration'] ) ? absint( $_POST['duration'] ) : 60;
	$ref    = ! empty( $_POST['referee'] );
	$kvkk   = ! empty( $_POST['kvkk'] );

	if ( ! arenafit_item_ok( $kind, $item ) ) {
		arenafit_fail( 'Lütfen bir antrenör veya salon seçin.' );
	}
	if ( '' === $date || null === arenafit_t2m( $start ) ) {
		arenafit_fail( 'Lütfen tarih ve saat seçin.' );
	}
	if ( mb_strlen( $name ) < 3 || mb_strlen( $name ) > 80 ) {
		arenafit_fail( 'Lütfen adınızı ve soyadınızı yazın.' );
	}
	$digits = preg_replace( '/\D/', '', $phone );
	if ( strlen( $digits ) < 10 || strlen( $digits ) > 13 ) {
		arenafit_fail( 'Lütfen geçerli bir telefon numarası girin.' );
	}
	if ( ! empty( $_POST['email'] ) && ! is_email( $email ) ) {
		arenafit_fail( 'E-posta adresi geçersiz görünüyor.' );
	}
	if ( ! $kvkk ) {
		arenafit_fail( 'Devam etmek için KVKK aydınlatma metnini onaylamanız gerekir.' );
	}

	$dur  = arenafit_allowed_duration( $kind, $item, $req );
	$lock = 'af_lock_' . md5( $kind . $item . $date );
	if ( ! arenafit_lock( $lock ) ) {
		arenafit_fail( 'Sistem şu an yoğun, lütfen birkaç saniye sonra tekrar deneyin.' );
	}

	$slots = arenafit_slots( $kind, $item, $date, $dur );
	if ( is_wp_error( $slots ) ) {
		arenafit_unlock( $lock );
		arenafit_fail( $slots->get_error_message() );
	}
	$free = false;
	foreach ( $slots as $s ) {
		if ( $s['t'] === $start && $s['ok'] ) {
			$free = true;
			break;
		}
	}
	if ( ! $free ) {
		arenafit_unlock( $lock );
		arenafit_fail( 'Seçtiğiniz saat artık müsait değil. Lütfen başka bir saat seçin.' );
	}

	$total = '';
	if ( 'hall' === $kind ) {
		$total = (float) get_post_meta( $item, '_af_hour_price', true ) * ( $dur / 60 );
		if ( $ref && '1' === get_post_meta( $item, '_af_referee', true ) ) {
			$total += (float) get_post_meta( $item, '_af_referee_price', true );
		} else {
			$ref = false;
		}
	} else {
		$ref = false;
	}

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'af_booking',
			'post_status' => 'publish',
			'post_title'  => $name . ' – ' . $date . ' ' . $start,
		),
		true
	);
	if ( is_wp_error( $post_id ) ) {
		arenafit_unlock( $lock );
		arenafit_fail( 'Kayıt sırasında bir sorun oluştu. Lütfen tekrar deneyin.' );
	}

	$meta = array(
		'_af_item'     => $item,
		'_af_date'     => $date,
		'_af_start'    => $start,
		'_af_duration' => $dur,
		'_af_name'     => $name,
		'_af_phone'    => $phone,
		'_af_email'    => $email,
		'_af_note'     => $note,
		'_af_referee'  => $ref ? '1' : '',
		'_af_status'   => 'pending',
		'_af_total'    => $total,
	);
	foreach ( $meta as $k => $v ) {
		update_post_meta( $post_id, $k, $v );
	}
	if ( is_user_logged_in() ) {
		update_post_meta( $post_id, '_af_user', get_current_user_id() ); // Üyenin "Hesabım" sayfasında görünür.
	}
	arenafit_sync_booking( $post_id );
	arenafit_unlock( $lock );

	arenafit_mail_new_booking( $post_id );

	$end = arenafit_m2t( arenafit_t2m( $start ) + $dur );
	wp_send_json_success(
		array(
			'message' => 'Rezervasyon talebiniz alındı. Ekibimiz en kısa sürede telefon veya e-posta ile onay verecek.',
			'summary' => get_the_title( $item ) . ' · ' . wp_date( 'd.m.Y', strtotime( $date . ' 12:00' ) ) . ' · ' . $start . ' – ' . $end,
		)
	);
}
add_action( 'wp_ajax_arenafit_book', 'arenafit_ajax_book' );
add_action( 'wp_ajax_nopriv_arenafit_book', 'arenafit_ajax_book' );

/* ------------------------------------------------------------------
 * E-postalar
 * ----------------------------------------------------------------*/

function arenafit_notify_address() {
	$to = arenafit_opt( 'notify_email' );
	return is_email( $to ) ? $to : get_option( 'admin_email' );
}

function arenafit_booking_lines( $post_id ) {
	$item  = (int) get_post_meta( $post_id, '_af_item', true );
	$start = (string) get_post_meta( $post_id, '_af_start', true );
	$dur   = (int) get_post_meta( $post_id, '_af_duration', true );
	$date  = (string) get_post_meta( $post_id, '_af_date', true );
	$end   = $start ? arenafit_m2t( arenafit_t2m( $start ) + $dur ) : '';
	$lines = array(
		'Hizmet: ' . get_the_title( $item ),
		'Tarih: ' . ( $date ? wp_date( 'd.m.Y', strtotime( $date . ' 12:00' ) ) : '' ),
		'Saat: ' . $start . ' – ' . $end,
	);
	if ( '1' === get_post_meta( $post_id, '_af_referee', true ) ) {
		$lines[] = 'Hakem hizmeti: Talep edildi';
	}
	$total = get_post_meta( $post_id, '_af_total', true );
	if ( '' !== $total && (float) $total > 0 ) {
		$lines[] = 'Tahmini ücret: ' . arenafit_money( $total );
	}
	return $lines;
}

function arenafit_mail_new_booking( $post_id ) {
	$site  = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$lines = arenafit_booking_lines( $post_id );

	$admin_body = "Yeni bir rezervasyon talebi geldi.\n\n" . implode( "\n", $lines ) . "\n\n"
		. 'Ad soyad: ' . get_post_meta( $post_id, '_af_name', true ) . "\n"
		. 'Telefon: ' . get_post_meta( $post_id, '_af_phone', true ) . "\n"
		. 'E-posta: ' . get_post_meta( $post_id, '_af_email', true ) . "\n"
		. 'Not: ' . get_post_meta( $post_id, '_af_note', true ) . "\n\n"
		. 'Yönetim paneli: ' . admin_url( 'edit.php?post_type=af_booking' );
	$headers = array();
	$cust    = get_post_meta( $post_id, '_af_email', true );
	if ( is_email( $cust ) ) {
		$headers[] = 'Reply-To: ' . $cust; // "Yanıtla" doğrudan müşteriye gider.
	}
	wp_mail( arenafit_notify_address(), '[' . $site . '] Yeni rezervasyon talebi: ' . get_post_meta( $post_id, '_af_name', true ), $admin_body, $headers );

	$email = get_post_meta( $post_id, '_af_email', true );
	if ( is_email( $email ) ) {
		$body = 'Merhaba ' . get_post_meta( $post_id, '_af_name', true ) . ",\n\nRezervasyon talebinizi aldık. Onay durumu size ayrıca bildirilecektir.\n\n"
			. implode( "\n", $lines ) . "\n\n" . $site;
		wp_mail( $email, $site . ' – Rezervasyon talebiniz alındı', $body );
	}
}

function arenafit_mail_status( $post_id, $status ) {
	$email = get_post_meta( $post_id, '_af_email', true );
	if ( ! is_email( $email ) ) {
		return;
	}
	$site  = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$lines = arenafit_booking_lines( $post_id );
	if ( 'confirmed' === $status ) {
		$subject = $site . ' – Rezervasyonunuz onaylandı';
		$intro   = 'Rezervasyonunuz onaylandı. Sizi bekliyoruz!';
	} else {
		$subject = $site . ' – Rezervasyonunuz iptal edildi';
		$intro   = 'Rezervasyonunuz iptal edilmiştir. Yeni bir randevu için internet sitemizden tekrar rezervasyon yapabilirsiniz.';
	}
	$body = 'Merhaba ' . get_post_meta( $post_id, '_af_name', true ) . ",\n\n" . $intro . "\n\n" . implode( "\n", $lines ) . "\n\n" . $site;
	wp_mail( $email, $subject, $body );
}

/* ------------------------------------------------------------------
 * Ön yüz: form ve kısa kod
 * ----------------------------------------------------------------*/

function arenafit_target_data( $kind, $id ) {
	if ( 'hall' !== $kind ) {
		return ' data-kind="trainer"';
	}
	return ' data-kind="hall"'
		. ' data-price="' . esc_attr( (float) get_post_meta( $id, '_af_hour_price', true ) ) . '"'
		. ' data-referee="' . ( '1' === get_post_meta( $id, '_af_referee', true ) ? '1' : '0' ) . '"'
		. ' data-referee-price="' . esc_attr( (float) get_post_meta( $id, '_af_referee_price', true ) ) . '"';
}

function arenafit_kvkk_url() {
	$u = arenafit_opt( 'kvkk' );
	if ( ! $u && function_exists( 'get_privacy_policy_url' ) ) {
		$u = get_privacy_policy_url();
	}
	return $u;
}

function arenafit_kvkk_label() {
	$u = arenafit_kvkk_url();
	if ( $u ) {
		return '<a href="' . esc_url( $u ) . '" target="_blank" rel="noopener">KVKK aydınlatma metnini</a> okudum ve kişisel verilerimin işlenmesini kabul ediyorum.';
	}
	return 'Kişisel verilerimin rezervasyon amacıyla işlenmesini kabul ediyorum (KVKK).';
}

function arenafit_booking_form( $args = array() ) {
	$a    = wp_parse_args(
		$args,
		array(
			'kind' => '',
			'item' => 0,
			'lock' => false,
		)
	);
	$kind = sanitize_key( $a['kind'] );
	$item = absint( $a['item'] );
	if ( ! $kind && isset( $_GET['kind'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$kind = sanitize_key( wp_unslash( $_GET['kind'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
	}
	if ( ! $item && isset( $_GET['item'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$item = absint( $_GET['item'] ); // phpcs:ignore WordPress.Security.NonceVerification
	}
	$preset = arenafit_item_ok( $kind, $item );
	if ( ! $preset ) {
		$kind = '';
		$item = 0;
	}
	$lock = $preset && $a['lock'];

	$trainers = get_posts(
		array(
			'post_type'   => 'af_trainer',
			'numberposts' => -1,
			'orderby'     => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		)
	);
	$halls    = get_posts(
		array(
			'post_type'   => 'af_hall',
			'numberposts' => -1,
			'orderby'     => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		)
	);

	$tz  = wp_timezone();
	$now = new DateTimeImmutable( 'today', $tz );
	$min = $now->format( 'Y-m-d' );
	$max = $now->modify( '+' . ARENAFIT_HORIZON . ' days' )->format( 'Y-m-d' );

	ob_start();
	?>
	<div class="af-booking" data-lock="<?php echo $lock ? '1' : '0'; ?>">
		<form class="af-form" novalidate>
			<input type="hidden" name="action" value="arenafit_book">
			<input type="text" name="af_website" class="af-hp" tabindex="-1" autocomplete="off" aria-hidden="true">

			<?php if ( ! $trainers && ! $halls ) : ?>
				<?php arenafit_empty( 'Rezervasyon için henüz hizmet tanımlanmadı.', 'Panelden Antrenörler veya Salonlar bölümüne ekleme yapın.' ); ?>
			<?php else : ?>

			<div class="af-field">
				<label for="af-target-<?php echo esc_attr( $item ? $item : 'x' ); ?>">Hizmet</label>
				<?php if ( $lock ) : ?>
					<input type="hidden" name="target" value="<?php echo esc_attr( $kind . ':' . $item ); ?>"<?php echo arenafit_target_data( $kind, $item ); // phpcs:ignore ?>>
					<div class="af-chip-fixed"><?php echo arenafit_icon( 'check' ); // phpcs:ignore ?> <?php echo esc_html( get_the_title( $item ) ); ?></div>
				<?php else : ?>
					<select name="target" id="af-target-<?php echo esc_attr( $item ? $item : 'x' ); ?>" required>
						<option value="">Seçiniz…</option>
						<?php if ( $trainers ) : ?>
							<optgroup label="Personal Trainer / Antrenör">
								<?php foreach ( $trainers as $p ) : ?>
									<option value="trainer:<?php echo (int) $p->ID; ?>"<?php echo arenafit_target_data( 'trainer', $p->ID ); // phpcs:ignore ?><?php selected( 'trainer' === $kind && $item === $p->ID ); ?>><?php echo esc_html( get_the_title( $p ) ); ?></option>
								<?php endforeach; ?>
							</optgroup>
						<?php endif; ?>
						<?php if ( $halls ) : ?>
							<optgroup label="Salon Kiralama">
								<?php foreach ( $halls as $p ) : ?>
									<option value="hall:<?php echo (int) $p->ID; ?>"<?php echo arenafit_target_data( 'hall', $p->ID ); // phpcs:ignore ?><?php selected( 'hall' === $kind && $item === $p->ID ); ?>><?php echo esc_html( get_the_title( $p ) ); ?></option>
								<?php endforeach; ?>
							</optgroup>
						<?php endif; ?>
					</select>
				<?php endif; ?>
			</div>

			<div class="af-row">
				<div class="af-field">
					<label for="af-date-<?php echo esc_attr( $item ? $item : 'x' ); ?>">Tarih</label>
					<input type="date" name="date" id="af-date-<?php echo esc_attr( $item ? $item : 'x' ); ?>" min="<?php echo esc_attr( $min ); ?>" max="<?php echo esc_attr( $max ); ?>" required>
				</div>
				<div class="af-field af-duration" hidden>
					<label for="af-dur-<?php echo esc_attr( $item ? $item : 'x' ); ?>">Süre</label>
					<select name="duration" id="af-dur-<?php echo esc_attr( $item ? $item : 'x' ); ?>">
						<option value="60">1 saat</option>
						<option value="120">2 saat</option>
						<option value="180">3 saat</option>
					</select>
				</div>
			</div>

			<div class="af-field">
				<span class="af-label">Uygun saatler</span>
				<input type="hidden" name="start" value="">
				<div class="af-slots" role="group" aria-label="Uygun saatler" aria-live="polite">
					<p class="af-hint">Önce hizmet ve tarih seçin.</p>
				</div>
			</div>

			<div class="af-field af-referee" hidden>
				<label class="af-check"><input type="checkbox" name="referee" value="1"> <span>Hakem ayarlama hizmeti istiyorum <em class="af-referee__price"></em></span></label>
			</div>

			<div class="af-summary" hidden></div>

			<div class="af-row">
				<div class="af-field">
					<label for="af-name-<?php echo esc_attr( $item ? $item : 'x' ); ?>">Ad soyad</label>
					<input type="text" name="name" id="af-name-<?php echo esc_attr( $item ? $item : 'x' ); ?>" autocomplete="name" value="<?php echo esc_attr( arenafit_prefill( 'name' ) ); ?>" required>
				</div>
				<div class="af-field">
					<label for="af-phone-<?php echo esc_attr( $item ? $item : 'x' ); ?>">Telefon</label>
					<input type="tel" name="phone" id="af-phone-<?php echo esc_attr( $item ? $item : 'x' ); ?>" autocomplete="tel" inputmode="tel" placeholder="05xx xxx xx xx" value="<?php echo esc_attr( arenafit_prefill( 'phone' ) ); ?>" required>
				</div>
			</div>
			<div class="af-field">
				<label for="af-email-<?php echo esc_attr( $item ? $item : 'x' ); ?>">E-posta <small>(isteğe bağlı, onay mesajı için)</small></label>
				<input type="email" name="email" id="af-email-<?php echo esc_attr( $item ? $item : 'x' ); ?>" autocomplete="email" value="<?php echo esc_attr( arenafit_prefill( 'email' ) ); ?>">
			</div>
			<div class="af-field">
				<label for="af-note-<?php echo esc_attr( $item ? $item : 'x' ); ?>">Notunuz <small>(isteğe bağlı)</small></label>
				<textarea name="note" id="af-note-<?php echo esc_attr( $item ? $item : 'x' ); ?>" rows="3"></textarea>
			</div>
			<label class="af-check"><input type="checkbox" name="kvkk" value="1" required> <span><?php echo wp_kses( arenafit_kvkk_label(), array( 'a' => array( 'href' => array(), 'target' => array(), 'rel' => array() ) ) ); ?></span></label>

			<div class="af-msg" role="alert" aria-live="polite"></div>
			<button type="submit" class="af-btn af-btn--accent af-btn--block"><?php echo arenafit_icon( 'calendar' ); // phpcs:ignore ?> Rezervasyon Talebi Gönder</button>
			<p class="af-fine">Talebiniz ekibimize iletilir; onay telefon veya e-posta ile bildirilir.</p>

			<?php endif; ?>
		</form>
	</div>
	<?php
	return ob_get_clean();
}

function arenafit_booking_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'kind' => '',
			'item' => 0,
			'lock' => '',
		),
		$atts,
		'arenafit_booking'
	);
	return arenafit_booking_form(
		array(
			'kind' => $atts['kind'],
			'item' => $atts['item'],
			'lock' => ! empty( $atts['lock'] ),
		)
	);
}
add_shortcode( 'arenafit_booking', 'arenafit_booking_shortcode' );
