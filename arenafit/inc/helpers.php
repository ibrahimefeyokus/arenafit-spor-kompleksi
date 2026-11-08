<?php
/**
 * Yardımcı fonksiyonlar.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Satır içi SVG ikon.
 */
function arenafit_icon( $name, $class = '' ) {
	static $i = null;
	if ( null === $i ) {
		$i = array(
			'phone'     => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/>',
			'mail'      => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/>',
			'pin'       => '<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>',
			'clock'     => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
			'calendar'  => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
			'users'     => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
			'user'      => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
			'shield'    => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>',
			'check'     => '<path d="M20 6 9 17l-5-5"/>',
			'award'     => '<circle cx="12" cy="8" r="6"/><path d="M15.48 12.89 17 22l-5-3-5 3 1.52-9.11"/>',
			'dumbbell'  => '<path d="M6.5 6.5v11M17.5 6.5v11M3 9v6M21 9v6M6.5 12h11"/>',
			'parking'   => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M9 17V7h4a3 3 0 0 1 0 6H9"/>',
			'coffee'    => '<path d="M17 8h1a4 4 0 0 1 0 8h-1M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V8zM6 1v3M10 1v3M14 1v3"/>',
			'shower'    => '<path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/>',
			'locker'    => '<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M12 2v20M9 11v2M15 11v2"/>',
			'ball'      => '<circle cx="12" cy="12" r="10"/><path d="M12 2v20M2 12h20M5 5c4 3 4 11 0 14M19 5c-4 3-4 11 0 14"/>',
			'flag'      => '<path d="M4 22V4M4 4h13l-2 4 2 4H4"/>',
			'arrow'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
			'star'      => '<path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/>',
			'menu'      => '<path d="M3 6h18M3 12h18M3 18h18"/>',
			'close'     => '<path d="M18 6 6 18M6 6l12 12"/>',
			'instagram' => '<rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/>',
			'facebook'  => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
			'youtube'   => '<path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.94 2C5.12 20 12 20 12 20s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><path d="m9.75 15.02 5.75-3.02-5.75-3.02z"/>',
			'whatsapp'  => '<path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8z"/>',
			'bolt'      => '<path d="M13 2 3 14h9l-1 8 10-12h-9z"/>',
			'file'      => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h8"/>',
		);
	}
	if ( ! isset( $i[ $name ] ) ) {
		return '';
	}
	return '<svg class="af-i ' . esc_attr( $class ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $i[ $name ] . '</svg>';
}

/**
 * ArenaFit logo işareti: "A" harfi + arena halkası + top.
 * A harfinin rengi CSS ile değişir (koyu zeminde beyaz).
 */
function arenafit_logo_mark() {
	static $n = 0;
	++$n;
	$id = 'afg' . $n;
	return '<svg class="af-logo" viewBox="0 0 64 64" aria-hidden="true" focusable="false">'
		. '<defs><linearGradient id="' . $id . '" x1="0" y1="1" x2="1" y2="0"><stop offset="0" stop-color="#2f6bff"/><stop offset="1" stop-color="#8fb0ff"/></linearGradient></defs>'
		. '<g transform="rotate(-18 32 36)"><ellipse cx="32" cy="36" rx="29" ry="15" fill="none" stroke="url(#' . $id . ')" stroke-width="4" opacity=".5"/></g>'
		. '<path class="af-logo__ink" d="M15 51 L32 13 L49 51" fill="none" stroke="#081833" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>'
		. '<path class="af-logo__ink" d="M23.5 40 H40.5" fill="none" stroke="#081833" stroke-width="7" stroke-linecap="round"/>'
		. '<g transform="rotate(-18 32 36)"><path d="M3 36 A29 15 0 0 0 61 36" fill="none" stroke="url(#' . $id . ')" stroke-width="4" stroke-linecap="round"/></g>'
		. '<circle cx="53" cy="23.5" r="6" fill="#ff6b2c"/>'
		. '</svg>';
}

/**
 * Site adını yazar; adı "…FIT" ile bitiyorsa son kısmı vurgu renginde gösterir.
 */
function arenafit_wordmark() {
	$name = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	if ( preg_match( '/^(.+?)(fit)$/iu', $name, $m ) ) {
		return esc_html( $m[1] ) . '<span class="af-brand__fit">' . esc_html( $m[2] ) . '</span>';
	}
	return esc_html( $name );
}

/**
 * Özelleştirici ayarı (boşsa varsayılan).
 */
function arenafit_opt( $key, $default = '' ) {
	if ( '' === $default && function_exists( 'arenafit_customizer_fields' ) ) {
		// Özelleştir'de hiç kaydedilmemiş alanlar için tanımlı varsayılan değeri kullan.
		static $fields = null;
		if ( null === $fields ) {
			$fields = arenafit_customizer_fields();
		}
		if ( isset( $fields[ $key ][2] ) ) {
			$default = $fields[ $key ][2];
		}
	}
	$v = get_theme_mod( 'arenafit_' . $key, $default );
	return ( '' === $v || null === $v ) ? $default : $v;
}

/**
 * Yazı meta değeri (boşsa varsayılan).
 */
function arenafit_meta( $id, $key, $default = '' ) {
	$v = get_post_meta( $id, $key, true );
	return ( '' === $v || null === $v || false === $v ) ? $default : $v;
}

/**
 * Çok satırlı metni diziye çevirir.
 */
function arenafit_lines( $text ) {
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	return array_values( array_filter( array_map( 'trim', $lines ), 'strlen' ) );
}

/**
 * Para formatı.
 */
function arenafit_money( $n ) {
	if ( '' === $n || null === $n || ! is_numeric( $n ) ) {
		return '';
	}
	$n   = (float) $n;
	$dec = ( floor( $n ) === $n ) ? 0 : 2;
	return number_format_i18n( $n, $dec ) . ' ' . arenafit_opt( 'currency', '₺' );
}

function arenafit_days_labels() {
	return array(
		1 => 'Pazartesi',
		2 => 'Salı',
		3 => 'Çarşamba',
		4 => 'Perşembe',
		5 => 'Cuma',
		6 => 'Cumartesi',
		7 => 'Pazar',
	);
}

function arenafit_days_short() {
	return array(
		1 => 'Pzt',
		2 => 'Sal',
		3 => 'Çar',
		4 => 'Per',
		5 => 'Cum',
		6 => 'Cmt',
		7 => 'Paz',
	);
}

function arenafit_sports() {
	return array(
		'basketbol' => 'Basketbol',
		'voleybol'  => 'Voleybol',
		'diger'     => 'Çok Amaçlı',
	);
}

/**
 * Belirli bir sayfa şablonunu kullanan sayfanın adresi.
 */
function arenafit_template_url( $template, $fallback_path ) {
	static $cache = array();
	if ( ! isset( $cache[ $template ] ) ) {
		$p                  = get_posts(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'numberposts' => 1,
				'meta_key'    => '_wp_page_template',
				'meta_value'  => $template,
				'fields'      => 'ids',
			)
		);
		$cache[ $template ] = $p ? get_permalink( $p[0] ) : home_url( $fallback_path );
	}
	return $cache[ $template ];
}

function arenafit_booking_url( $kind = '', $item = 0 ) {
	$u = arenafit_template_url( 'template-rezervasyon.php', '/rezervasyon/' );
	if ( $kind && $item ) {
		$u = add_query_arg(
			array(
				'kind' => $kind,
				'item' => (int) $item,
			),
			$u
		);
	}
	return $u;
}

function arenafit_member_url( $package = 0 ) {
	$u = arenafit_template_url( 'template-uyelik.php', '/uyelik/' );
	if ( $package ) {
		$u = add_query_arg( 'package', (int) $package, $u );
	}
	return $u;
}

function arenafit_account_url() {
	return arenafit_template_url( 'template-hesabim.php', '/hesabim/' );
}

function arenafit_contact_url( $type = '' ) {
	$u = arenafit_template_url( 'template-iletisim.php', '/iletisim/' );
	return $type ? add_query_arg( 'tur', $type, $u ) : $u;
}

function arenafit_facility_url() {
	return arenafit_template_url( 'template-tesis.php', '/tesis-olanaklari/' );
}

function arenafit_tel_href() {
	$p = arenafit_opt( 'phone' );
	return $p ? 'tel:' . preg_replace( '/[^\d+]/', '', $p ) : '';
}

function arenafit_wa_link( $text = '' ) {
	$n = preg_replace( '/\D/', '', (string) arenafit_opt( 'whatsapp' ) );
	if ( ! $n ) {
		return '';
	}
	return 'https://wa.me/' . $n . ( $text ? '?text=' . rawurlencode( $text ) : '' );
}

/**
 * Sayfa başlık bandı.
 */
function arenafit_page_hero( $eyebrow, $title, $desc = '' ) {
	?>
	<section class="af-pagehero">
		<div class="af-container">
			<?php if ( $eyebrow ) : ?>
				<span class="af-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<h1><?php echo esc_html( $title ); ?></h1>
			<?php if ( $desc ) : ?>
				<p><?php echo esc_html( $desc ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

/**
 * Bölüm başlığı.
 */
function arenafit_head( $eyebrow, $title, $text = '', $link = array() ) {
	?>
	<div class="af-head reveal">
		<div>
			<?php if ( $eyebrow ) : ?>
				<span class="af-eyebrow af-eyebrow--dark"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<h2><?php echo esc_html( $title ); ?></h2>
			<?php if ( $text ) : ?>
				<p><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>
		</div>
		<?php if ( ! empty( $link['url'] ) ) : ?>
			<a class="af-link" href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['label'] ); ?> <?php echo arenafit_icon( 'arrow' ); ?></a>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * İçerik yokken yöneticiye ipucu, ziyaretçiye sade mesaj.
 */
function arenafit_empty( $text, $admin_hint = '' ) {
	echo '<div class="af-empty"><p>' . esc_html( $text ) . '</p>';
	if ( $admin_hint && current_user_can( 'edit_posts' ) ) {
		echo '<p class="af-empty__hint">' . esc_html( $admin_hint ) . '</p>';
	}
	echo '</div>';
}

/**
 * Paketin seans başı ücreti.
 */
function arenafit_per_session( $id ) {
	$price    = (float) get_post_meta( $id, '_af_price', true );
	$sessions = (int) get_post_meta( $id, '_af_sessions', true );
	if ( $price > 0 && $sessions > 0 ) {
		return $price / $sessions;
	}
	return 0;
}

/**
 * Görsel yoksa yer tutucu.
 */
function arenafit_thumb( $size, $icon = 'dumbbell', $attr = array() ) {
	if ( has_post_thumbnail() ) {
		the_post_thumbnail( $size, array_merge( array( 'loading' => 'lazy' ), $attr ) );
	} else {
		echo '<span class="af-ph">' . arenafit_icon( $icon ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * Paket seviyesi etiketi.
 */
function arenafit_level_label( $key ) {
	$l = array(
		'all'          => 'Her seviyeye uygun',
		'beginner'     => 'Başlangıç',
		'intermediate' => 'Orta',
		'advanced'     => 'İleri',
	);
	return isset( $l[ $key ] ) ? $l[ $key ] : $l['all'];
}

/**
 * Virgülle ayrılmış görsel kimliklerini diziye çevirir.
 */
function arenafit_ids( $raw ) {
	return array_values( array_unique( array_filter( array_map( 'absint', explode( ',', (string) $raw ) ) ) ) );
}

/**
 * Tesis olanakları (Özelleştir > ArenaFit Tesis Olanakları'ndan düzenlenir).
 */
function arenafit_facilities() {
	$base = array(
		'locker'  => array( 'locker', 'Soyunma Odaları', 'Kilitli dolaplı, temiz ve ferah soyunma alanları. Kadın ve erkek için ayrı bölümler.' ),
		'shower'  => array( 'shower', 'Duş', 'Antrenman sonrası sıcak su ile rahatça yenilenebileceğiniz hijyenik duş kabinleri.' ),
		'parking' => array( 'parking', 'Otopark', 'Tesise gelen üye ve misafirlerimiz için ücretsiz otopark.' ),
		'cafe'    => array( 'coffee', 'Kafe', 'Antrenman öncesi ve sonrası protein shake, sağlıklı atıştırmalık ve içecekler.' ),
	);
	$out = array();
	foreach ( $base as $k => $f ) {
		$out[ $k ] = array(
			'icon'  => $f[0],
			'title' => $f[1],
			'text'  => arenafit_opt( 'fac_' . $k . '_text', $f[2] ),
			'image' => arenafit_opt( 'fac_' . $k . '_img', ARENAFIT_URI . '/assets/images/fac-' . $k . '.svg' ),
			'info'  => arenafit_lines( arenafit_opt( 'fac_' . $k . '_info' ) ),
		);
	}
	return $out;
}

/**
 * Tesis olanağı kartı.
 */
function arenafit_facility_card( $f, $detailed = false ) {
	echo '<article class="af-fac' . ( $f['image'] ? ' af-fac--img' : '' ) . ' reveal">';
	if ( $f['image'] ) {
		echo '<div class="af-fac__img"><img src="' . esc_url( $f['image'] ) . '" alt="' . esc_attr( $f['title'] ) . ' alanı" loading="lazy"></div>';
	}
	echo '<div class="af-fac__body">' . arenafit_icon( $f['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput
	echo '<h3>' . esc_html( $f['title'] ) . '</h3><p>' . esc_html( $f['text'] ) . '</p>';
	if ( $detailed && $f['info'] ) {
		echo '<ul class="af-fac__info">';
		foreach ( $f['info'] as $line ) {
			echo '<li>' . arenafit_icon( 'check' ) . ' ' . esc_html( $line ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '</ul>';
	}
	echo '</div></article>';
}

/**
 * Menüde etkin sayfayı ekran okuyuculara bildirir.
 */
function arenafit_menu_aria_current( $atts, $item ) {
	if ( ! empty( $item->current ) ) {
		$atts['aria-current'] = 'page';
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'arenafit_menu_aria_current', 10, 2 );

/**
 * Yeni sekmede açılan bağlantılar için ekran okuyucu notu.
 */
function arenafit_newtab() {
	return '<span class="screen-reader-text"> (yeni sekmede açılır)</span>';
}
