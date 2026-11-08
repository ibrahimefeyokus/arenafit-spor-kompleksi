<?php
/**
 * E-posta ayarları: gönderici adı ve "E-posta Testi" sayfası.
 *
 * Rezervasyon bildirimlerinin gelmesi için sunucunun e-posta gönderebilmesi gerekir.
 * Bu sayfa bir deneme e-postası gönderir ve hata varsa nedenini gösterir.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Varsayılan "WordPress" gönderici adı yerine site adını kullan.
 */
function arenafit_mail_from_name( $name ) {
	return ( 'WordPress' === $name ) ? wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) : $name;
}
add_filter( 'wp_mail_from_name', 'arenafit_mail_from_name' );

/**
 * Özelleştirici'de SMTP bilgileri girilmişse e-postalar o hesap üzerinden gönderilir.
 * (Eklenti gerekmez. Gmail, Outlook veya hosting e-postası ile çalışır.)
 */
function arenafit_smtp_ready() {
	return '' !== trim( (string) arenafit_opt( 'smtp_host' ) );
}

function arenafit_smtp_init( $mailer ) {
	if ( ! arenafit_smtp_ready() ) {
		return;
	}
	$sec  = strtolower( trim( (string) arenafit_opt( 'smtp_secure', 'tls' ) ) );
	$sec  = in_array( $sec, array( 'tls', 'ssl' ), true ) ? $sec : '';
	$user = trim( (string) arenafit_opt( 'smtp_user' ) );

	$mailer->isSMTP();
	$mailer->Host        = trim( (string) arenafit_opt( 'smtp_host' ) );
	$mailer->Port        = (int) arenafit_opt( 'smtp_port', 587 );
	$mailer->SMTPSecure  = $sec;
	$mailer->SMTPAutoTLS = ( '' !== $sec );
	if ( '' !== $user ) {
		$mailer->SMTPAuth = true;
		$mailer->Username = $user;
		$mailer->Password = (string) arenafit_opt( 'smtp_pass' );
	}
}
add_action( 'phpmailer_init', 'arenafit_smtp_init' );

function arenafit_smtp_from( $from ) {
	if ( ! arenafit_smtp_ready() ) {
		return $from;
	}
	$f = trim( (string) arenafit_opt( 'smtp_from' ) );
	if ( ! is_email( $f ) ) {
		$f = trim( (string) arenafit_opt( 'smtp_user' ) );
	}
	return is_email( $f ) ? $f : $from;
}
add_filter( 'wp_mail_from', 'arenafit_smtp_from' );

/**
 * Yönetim menüsünde onay bekleyen rezervasyon sayısını göster.
 */
function arenafit_booking_bubble() {
	global $menu;
	if ( ! current_user_can( 'edit_posts' ) || ! is_array( $menu ) ) {
		return;
	}
	$q = new WP_Query(
		array(
			'post_type'      => 'af_booking',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_query'     => array( array( 'key' => '_af_status', 'value' => 'pending' ) ),
		)
	);
	$n = (int) $q->found_posts;
	if ( ! $n ) {
		return;
	}
	foreach ( $menu as $k => $item ) {
		if ( isset( $item[2] ) && 'edit.php?post_type=af_booking' === $item[2] ) {
			$menu[ $k ][0] .= ' <span class="awaiting-mod">' . (int) $n . '</span>'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		}
	}
}
add_action( 'admin_menu', 'arenafit_booking_bubble', 99 );

function arenafit_mail_test_menu() {
	add_submenu_page(
		'edit.php?post_type=af_booking',
		'E-posta Testi',
		'E-posta Testi',
		'manage_options',
		'arenafit-mail-test',
		'arenafit_mail_test_page'
	);
}
add_action( 'admin_menu', 'arenafit_mail_test_menu' );

function arenafit_mail_test_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$to      = arenafit_notify_address();
	$result  = null;
	$error   = '';
	$capture = function ( $err ) use ( &$error ) {
		$error = $err->get_error_message();
	};

	if ( isset( $_POST['arenafit_mail_test'] ) && check_admin_referer( 'arenafit_mail_test' ) ) {
		add_action( 'wp_mail_failed', $capture );
		$site   = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		$result = wp_mail( $to, '[' . $site . '] E-posta testi', "Bu bir deneme e-postasıdır.\n\nBunu okuyorsanız yeni rezervasyon, üyelik ve mesaj bildirimleri bu adrese ulaşabilir.\n\n" . $site );
		remove_action( 'wp_mail_failed', $capture );
	}
	?>
	<div class="wrap">
		<h1>E-posta Testi</h1>
		<p>Yeni rezervasyon, üyelik başvurusu, üye kaydı ve mesajlar şu adrese bildirim olarak gönderilir:</p>
		<p><strong><?php echo esc_html( $to ); ?></strong>
			&nbsp;<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=arenafit' ) ); ?>">Adresi değiştir (Özelleştir &gt; ArenaFit Ayarları)</a></p>

		<p>
			Gönderim yöntemi:
			<?php if ( arenafit_smtp_ready() ) : ?>
				<strong style="color:#0a7a46">SMTP (<?php echo esc_html( trim( (string) arenafit_opt( 'smtp_host' ) ) ); ?>)</strong>
			<?php else : ?>
				<strong>Sunucunun varsayılan gönderimi</strong> (SMTP bilgisi girilmemiş)
			<?php endif; ?>
		</p>

		<?php if ( true === $result ) : ?>
			<div class="notice notice-success"><p>Deneme e-postası gönderildi. Gelen kutunuzu ve <strong>Spam / Gereksiz</strong> klasörünü kontrol edin. Birkaç dakika içinde gelmezse aşağıdaki notlara bakın.</p></div>
		<?php elseif ( false === $result ) : ?>
			<div class="notice notice-error"><p><strong>E-posta gönderilemedi.</strong><?php echo $error ? ' Sunucunun yanıtı: ' . esc_html( $error ) : ''; ?></p></div>
		<?php endif; ?>

		<form method="post">
			<?php wp_nonce_field( 'arenafit_mail_test' ); ?>
			<p><button type="submit" name="arenafit_mail_test" value="1" class="button button-primary">Deneme E-postası Gönder</button></p>
		</form>

		<h2>E-posta gelmiyorsa</h2>
		<ul style="list-style:disc;margin-left:22px;max-width:760px">
			<li><strong>En güvenilir çözüm:</strong> <em>Görünüm &gt; Özelleştir &gt; ArenaFit Ayarları</em> içindeki <strong>E-posta sunucusu (SMTP)</strong> alanlarını doldurun. Gmail, Outlook veya hosting e-postanız çalışır; ek eklenti gerekmez. Sonra bu sayfadan deneme e-postası gönderin.</li>
			<li><strong>Kendi bilgisayarınızda (XAMPP / localhost):</strong> Bilgisayarlar varsayılan olarak e-posta gönderemez; SMTP bilgisi girmezseniz hata almanız normaldir. SMTP girerseniz yerelde de çalışır.</li>
			<li><strong>Gmail kullanıyorsanız:</strong> sunucu <code>smtp.gmail.com</code>, port <code>587</code>, şifreleme <code>tls</code>. Şifre olarak normal şifreniz değil, Google Hesabı &gt; Güvenlik &gt; 2 Adımlı Doğrulama altındaki <strong>Uygulama şifresi</strong> kullanılır.</li>
			<li><strong>Hosting üzerinde:</strong> Mesajlar spam'e düşebilir veya hosting e-postayı engelliyor olabilir. Kendi alan adınızdaki bir e-posta hesabının SMTP bilgilerini girmek spam riskini en aza indirir.</li>
			<li>Bildirim adresini <em>Özelleştir &gt; ArenaFit Ayarları &gt; Rezervasyon bildirim e-postası</em> alanından değiştirebilirsiniz.</li>
			<li>E-posta gelmese bile her rezervasyon <strong>Rezervasyonlar</strong> listesine kaydedilir. Hiçbir talep kaybolmaz.</li>
		</ul>
	</div>
	<?php
}
