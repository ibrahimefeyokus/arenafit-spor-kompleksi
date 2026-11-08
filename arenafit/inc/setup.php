<?php
/**
 * Tema kurulumu, varlıklar, özelleştirici, şema verisi.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function arenafit_setup() {
	load_theme_textdomain( 'arenafit', ARENAFIT_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 260,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_image_size( 'arenafit-card', 800, 560, true );
	add_image_size( 'arenafit-portrait', 640, 800, true );
	add_image_size( 'arenafit-wide', 1400, 800, true );

	register_nav_menus(
		array(
			'primary' => 'Ana menü',
			'footer'  => 'Alt menü',
		)
	);
}
add_action( 'after_setup_theme', 'arenafit_setup' );

function arenafit_content_width() {
	$GLOBALS['content_width'] = 1200;
}
add_action( 'after_setup_theme', 'arenafit_content_width', 0 );

function arenafit_assets() {
	wp_enqueue_style( 'arenafit-main', ARENAFIT_URI . '/assets/css/main.css', array(), ARENAFIT_VERSION );
	wp_enqueue_style( 'arenafit-style', get_stylesheet_uri(), array( 'arenafit-main' ), ARENAFIT_VERSION );
	wp_enqueue_script(
		'arenafit-main',
		ARENAFIT_URI . '/assets/js/main.js',
		array(),
		ARENAFIT_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
	wp_enqueue_script(
		'arenafit-voice',
		ARENAFIT_URI . '/assets/js/voice.js',
		array( 'arenafit-main' ),
		ARENAFIT_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
	wp_localize_script(
		'arenafit-main',
		'ArenaFit',
		array(
			'ajax' => admin_url( 'admin-ajax.php' ),
			'nav'  => array(
				'home'     => home_url( '/' ),
				'trainers' => get_post_type_archive_link( 'af_trainer' ),
				'packages' => get_post_type_archive_link( 'af_package' ),
				'halls'    => get_post_type_archive_link( 'af_hall' ),
				'booking'  => arenafit_booking_url(),
				'member'   => arenafit_member_url(),
				'contact'  => arenafit_contact_url(),
				'account'  => arenafit_account_url(),
				'facility' => arenafit_facility_url(),
				'a11y'     => arenafit_a11y_url(),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'arenafit_assets' );

function arenafit_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'arenafit_excerpt_more' );

/**
 * Menü atanmamışsa gösterilecek varsayılan menü.
 */
function arenafit_fallback_menu() {
	$items = array(
		'PT Eğitimleri'  => get_post_type_archive_link( 'af_package' ),
		'Antrenörler'    => get_post_type_archive_link( 'af_trainer' ),
		'Salon Kiralama' => get_post_type_archive_link( 'af_hall' ),
		'Tesis'          => arenafit_facility_url(),
		'Üyelik'         => arenafit_member_url(),
	);
	echo '<ul class="af-menu">';
	foreach ( $items as $label => $url ) {
		if ( $url ) {
			echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
		}
	}
	echo '</ul>';
}

/* ------------------------------------------------------------------
 * Özelleştirici (Görünüm > Özelleştir > ArenaFit Ayarları)
 * ----------------------------------------------------------------*/

function arenafit_sanitize_time( $v ) {
	if ( preg_match( '/^([01]?\d|2[0-3]):([0-5]\d)$/', trim( (string) $v ), $m ) ) {
		return sprintf( '%02d:%s', (int) $m[1], $m[2] );
	}
	return '';
}

function arenafit_customizer_fields() {
	// anahtar => [etiket, tür, varsayılan, açıklama]
	return array(
		'hero_title'   => array( 'Ana sayfa başlığı', 'text', 'Hedefine giden yol, doğru antrenörle başlar.', '' ),
		'hero_text'    => array( 'Ana sayfa alt metni', 'textarea', 'Sertifikalı antrenörler, bireysel ve grup programları, profesyonel basketbol ve voleybol salonları. Tüm randevu ve rezervasyonlarınızı online yapın.', '' ),
		'hero_image'   => array( 'Ana sayfa arka plan görseli', 'image', '', 'Geniş ve yüksek kaliteli bir tesis fotoğrafı önerilir.' ),
		'phone'        => array( 'Telefon', 'text', '', 'Örn: 0 (224) 000 00 00' ),
		'whatsapp'     => array( 'WhatsApp numarası', 'text', '', 'Ülke koduyla, boşluksuz. Örn: 905320000000' ),
		'email'        => array( 'E-posta', 'email', '', '' ),
		'notify_email' => array( 'Rezervasyon bildirim e-postası', 'email', '', 'Yeni rezervasyon ve başvurular bu adrese gelir. Boşsa site yöneticisinin e-postası kullanılır.' ),
		'smtp_host'    => array( 'E-posta sunucusu (SMTP)', 'text', '', 'Bildirimlerin güvenle gelmesi için önerilir. Gmail: smtp.gmail.com · Outlook: smtp.office365.com · Hosting e-postası: hosting firmasının verdiği adres (örn. mail.siteadiniz.com). Boşsa sunucunun varsayılan gönderimi kullanılır.' ),
		'smtp_port'    => array( 'SMTP port', 'text', '587', 'Genelde 587 (tls) veya 465 (ssl).' ),
		'smtp_secure'  => array( 'SMTP şifreleme', 'text', 'tls', '"tls" (587 için) veya "ssl" (465 için) yazın.' ),
		'smtp_user'    => array( 'SMTP kullanıcı adı', 'text', '', 'Genelde e-posta adresinizin kendisi.' ),
		'smtp_pass'    => array( 'SMTP şifresi', 'password', '', 'Gmail için normal şifre değil, Google Hesabı > Güvenlik bölümünden oluşturulan "Uygulama şifresi" gerekir.' ),
		'smtp_from'    => array( 'Gönderen e-posta adresi', 'email', '', 'Boşsa SMTP kullanıcı adı kullanılır. Gmail/Outlook için hesabınızın adresi olmalıdır.' ),
		'address'      => array( 'Adres', 'textarea', '', '' ),
		'hours'        => array( 'Çalışma saatleri (görünen metin)', 'text', 'Her gün 07:00 – 23:00', '' ),
		'open'         => array( 'Rezervasyon başlangıç saati', 'time', '07:00', 'SS:DD biçiminde. Takvimde ilk saat dilimi.' ),
		'close'        => array( 'Rezervasyon bitiş saati', 'time', '23:00', 'SS:DD biçiminde. Son randevu bu saatte biter.' ),
		'map'          => array( 'Google Haritalar gömme bağlantısı', 'url', '', 'Google Haritalar > Paylaş > Harita yerleştir > src="..." içindeki adres.' ),
		'instagram'    => array( 'Instagram adresi', 'url', '', '' ),
		'facebook'     => array( 'Facebook adresi', 'url', '', '' ),
		'youtube'      => array( 'YouTube adresi', 'url', '', '' ),
		'currency'     => array( 'Para birimi simgesi', 'text', '₺', '' ),
		'plans'        => array( 'Üyelik planları', 'textarea', '', 'Her satıra bir plan. Biçim: Plan adı | Fiyat | Süre | Özellik 1, Özellik 2. Örn: Aylık | 1500 | 1 ay | Salon erişimi, Soyunma odası' ),
		'kvkk'         => array( 'KVKK / Aydınlatma metni adresi', 'url', '', 'Boşsa WordPress Gizlilik Politikası sayfası kullanılır.' ),
	);
}

function arenafit_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'arenafit',
		array(
			'title'    => 'ArenaFit Ayarları',
			'priority' => 30,
		)
	);

	foreach ( arenafit_customizer_fields() as $key => $f ) {
		list( $label, $type, $default, $desc ) = $f;
		switch ( $type ) {
			case 'textarea':
				$san = 'sanitize_textarea_field';
				break;
			case 'email':
				$san = 'sanitize_email';
				break;
			case 'url':
			case 'image':
				$san = 'esc_url_raw';
				break;
			case 'time':
				$san = 'arenafit_sanitize_time';
				break;
			default:
				$san = 'sanitize_text_field';
		}
		$wp_customize->add_setting(
			'arenafit_' . $key,
			array(
				'default'           => $default,
				'sanitize_callback' => $san,
			)
		);
		if ( 'image' === $type ) {
			$wp_customize->add_control(
				new WP_Customize_Image_Control(
					$wp_customize,
					'arenafit_' . $key,
					array(
						'label'       => $label,
						'description' => $desc,
						'section'     => 'arenafit',
					)
				)
			);
		} else {
			$wp_customize->add_control(
				'arenafit_' . $key,
				array(
					'label'       => $label,
					'description' => $desc,
					'section'     => 'arenafit',
					'type'        => ( 'time' === $type ) ? 'text' : $type,
				)
			);
		}
	}
}
add_action( 'customize_register', 'arenafit_customize_register' );

/**
 * Site simgesi (favicon) seçilmemişse ArenaFit işaretini kullan.
 * Özelleştir > Site Kimliği'nden kendi simgenizi yüklerseniz o kullanılır.
 */
function arenafit_default_favicon() {
	if ( has_site_icon() ) {
		return;
	}
	echo '<link rel="icon" type="image/svg+xml" href="' . esc_url( ARENAFIT_URI . '/assets/images/logo-mark.svg' ) . '">' . "\n";
}
add_action( 'wp_head', 'arenafit_default_favicon', 1 );
add_action( 'admin_head', 'arenafit_default_favicon', 1 );
add_action( 'login_head', 'arenafit_default_favicon', 1 );

/**
 * Yerel işletme yapılandırılmış verisi (arama motorları için).
 */
function arenafit_schema() {
	if ( ! is_front_page() ) {
		return;
	}
	$d = array(
		'@context' => 'https://schema.org',
		'@type'    => 'SportsActivityLocation',
		'name'     => get_bloginfo( 'name' ),
		'url'      => home_url( '/' ),
	);
	if ( arenafit_opt( 'phone' ) ) {
		$d['telephone'] = arenafit_opt( 'phone' );
	}
	if ( arenafit_opt( 'email' ) ) {
		$d['email'] = arenafit_opt( 'email' );
	}
	if ( arenafit_opt( 'address' ) ) {
		$d['address'] = array(
			'@type'         => 'PostalAddress',
			'streetAddress' => arenafit_opt( 'address' ),
			'addressCountry' => 'TR',
		);
	}
	$same = array_values( array_filter( array( arenafit_opt( 'instagram' ), arenafit_opt( 'facebook' ), arenafit_opt( 'youtube' ) ) ) );
	if ( $same ) {
		$d['sameAs'] = $same;
	}
	echo '<script type="application/ld+json">' . wp_json_encode( $d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'wp_head', 'arenafit_schema', 5 );

/**
 * Özelleştir > ArenaFit Tesis Olanakları: her olanak için fotoğraf, açıklama ve ayrıntılar.
 */
function arenafit_customize_facilities( $wp_customize ) {
	$wp_customize->add_section(
		'arenafit_fac',
		array(
			'title'       => 'ArenaFit Tesis Olanakları',
			'priority'    => 31,
			'description' => 'Soyunma odası, duş, otopark ve kafe kartlarının fotoğrafı ve metinleri. Fotoğraf yüklenmeyen kartta temanın hazır çizimi, metin girilmeyen kartta varsayılan metin gösterilir. En iyi görünüm için 4:3 oranında (örn. 1200x900) yatay fotoğraf kullanın.',
		)
	);
	$names = array(
		'locker'  => 'Soyunma odaları',
		'shower'  => 'Duş',
		'parking' => 'Otopark',
		'cafe'    => 'Kafe',
	);
	foreach ( $names as $k => $label ) {
		$wp_customize->add_setting( 'arenafit_fac_' . $k . '_img', array( 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'arenafit_fac_' . $k . '_img',
				array(
					'label'   => $label . ' – fotoğraf',
					'section' => 'arenafit_fac',
				)
			)
		);
		$wp_customize->add_setting( 'arenafit_fac_' . $k . '_text', array( 'sanitize_callback' => 'sanitize_textarea_field' ) );
		$wp_customize->add_control(
			'arenafit_fac_' . $k . '_text',
			array(
				'label'   => $label . ' – kısa açıklama',
				'section' => 'arenafit_fac',
				'type'    => 'textarea',
			)
		);
		$wp_customize->add_setting( 'arenafit_fac_' . $k . '_info', array( 'sanitize_callback' => 'sanitize_textarea_field' ) );
		$wp_customize->add_control(
			'arenafit_fac_' . $k . '_info',
			array(
				'label'       => $label . ' – ayrıntılar',
				'description' => 'Her satıra bir madde (Tesis sayfasında listelenir). Örn: 120 kilitli dolap',
				'section'     => 'arenafit_fac',
				'type'        => 'textarea',
			)
		);
	}
}
add_action( 'customize_register', 'arenafit_customize_facilities' );
