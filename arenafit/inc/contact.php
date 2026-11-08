<?php
/**
 * İletişim / şikayet / öneri formu.
 *
 * Gelen mesajlar panelde "Mesajlar" altında kayıt numarasıyla saklanır,
 * site sahibine e-posta ile iletilir (Yanıtla düğmesi gönderene yanıt verir).
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function arenafit_message_types() {
	return array(
		'contact'    => 'Genel iletişim',
		'complaint'  => 'Şikayet',
		'suggestion' => 'Öneri',
		'info'       => 'Bilgi talebi (üyelik / paket / salon)',
	);
}

function arenafit_message_statuses() {
	return array(
		'new'      => 'Yeni',
		'progress' => 'İnceleniyor',
		'resolved' => 'Çözüldü',
	);
}

function arenafit_register_message_type() {
	register_post_type(
		'af_message',
		array(
			'labels'          => array(
				'name'          => 'Mesajlar',
				'singular_name' => 'Mesaj',
				'menu_name'     => 'Mesajlar / Şikayetler',
				'all_items'     => 'Tüm Mesajlar',
				'add_new'       => 'Yeni Kayıt',
				'add_new_item'  => 'Yeni Kayıt Ekle',
				'edit_item'     => 'Mesajı Görüntüle',
				'search_items'  => 'Mesaj Ara',
				'not_found'     => 'Mesaj bulunamadı',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 26,
			'capability_type' => 'post',
			'supports'        => array( 'title' ),
		)
	);
}
add_action( 'init', 'arenafit_register_message_type' );

function arenafit_message_fields( $fields ) {
	$fields['af_message'] = array(
		'_af_type'     => array( 'label' => 'Konu türü', 'type' => 'select', 'options' => arenafit_message_types() ),
		'_af_name'     => array( 'label' => 'Ad soyad', 'type' => 'text' ),
		'_af_phone'    => array( 'label' => 'Telefon', 'type' => 'text' ),
		'_af_email'    => array( 'label' => 'E-posta', 'type' => 'text' ),
		'_af_subject'  => array( 'label' => 'Konu', 'type' => 'text' ),
		'_af_body'     => array( 'label' => 'Mesaj', 'type' => 'textarea' ),
		'_af_status'   => array( 'label' => 'Durum', 'type' => 'select', 'options' => arenafit_message_statuses() ),
		'_af_internal' => array( 'label' => 'Yönetici notu', 'type' => 'textarea', 'help' => 'Yalnızca siz görürsünüz. Örn: "Aradım, sorun giderildi."' ),
	);
	return $fields;
}
add_filter( 'arenafit_fields', 'arenafit_message_fields' );

function arenafit_message_titles( $titles ) {
	$titles['af_message'] = 'Mesaj detayı';
	return $titles;
}
add_filter( 'arenafit_meta_box_titles', 'arenafit_message_titles' );

function arenafit_message_columns( $cols ) {
	return array(
		'cb'      => $cols['cb'],
		'title'   => 'Kayıt',
		'af_type' => 'Tür',
		'af_who'  => 'Gönderen',
		'af_st'   => 'Durum',
		'date'    => 'Tarih',
	);
}
add_filter( 'manage_af_message_posts_columns', 'arenafit_message_columns' );

function arenafit_message_column_data( $col, $post_id ) {
	switch ( $col ) {
		case 'af_type':
			$t = arenafit_message_types();
			$k = get_post_meta( $post_id, '_af_type', true );
			echo esc_html( isset( $t[ $k ] ) ? $t[ $k ] : '—' );
			break;
		case 'af_who':
			echo esc_html( get_post_meta( $post_id, '_af_name', true ) ) . '<br><small>' . esc_html( get_post_meta( $post_id, '_af_phone', true ) ) . '</small>';
			break;
		case 'af_st':
			$s = arenafit_message_statuses();
			$k = get_post_meta( $post_id, '_af_status', true );
			echo esc_html( isset( $s[ $k ] ) ? $s[ $k ] : 'Yeni' );
			break;
	}
}
add_action( 'manage_af_message_posts_custom_column', 'arenafit_message_column_data', 10, 2 );

/**
 * Menüde yeni (çözülmemiş) mesaj sayısını göster.
 */
function arenafit_message_bubble() {
	global $menu;
	if ( ! current_user_can( 'edit_posts' ) || ! is_array( $menu ) ) {
		return;
	}
	$q = new WP_Query(
		array(
			'post_type'      => 'af_message',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_query'     => array( array( 'key' => '_af_status', 'value' => 'new' ) ),
		)
	);
	$n = (int) $q->found_posts;
	if ( ! $n ) {
		return;
	}
	foreach ( $menu as $k => $item ) {
		if ( isset( $item[2] ) && 'edit.php?post_type=af_message' === $item[2] ) {
			$menu[ $k ][0] .= ' <span class="awaiting-mod">' . (int) $n . '</span>'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		}
	}
}
add_action( 'admin_menu', 'arenafit_message_bubble', 99 );

function arenafit_contact_form( $args = array() ) {
	$type_get = isset( $_GET['tur'] ) ? sanitize_key( wp_unslash( $_GET['tur'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$types    = arenafit_message_types();
	$sel      = isset( $types[ $type_get ] ) ? $type_get : 'contact';

	ob_start();
	?>
	<div class="af-formbox af-ajax">
		<form class="af-form" novalidate data-kind="contact">
			<input type="hidden" name="action" value="arenafit_contact">
			<input type="text" name="af_website" class="af-hp" tabindex="-1" autocomplete="off" aria-hidden="true">

			<div class="af-field">
				<label for="afc-type">Konu türü</label>
				<select name="type" id="afc-type">
					<?php foreach ( $types as $k => $l ) : ?>
						<option value="<?php echo esc_attr( $k ); ?>"<?php selected( $sel, $k ); ?>><?php echo esc_html( $l ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="af-row">
				<div class="af-field">
					<label for="afc-name">Ad soyad</label>
					<input type="text" name="name" id="afc-name" autocomplete="name" value="<?php echo esc_attr( arenafit_prefill( 'name' ) ); ?>" required>
				</div>
				<div class="af-field">
					<label for="afc-phone">Telefon</label>
					<input type="tel" name="phone" id="afc-phone" autocomplete="tel" inputmode="tel" placeholder="05xx xxx xx xx" value="<?php echo esc_attr( arenafit_prefill( 'phone' ) ); ?>" required>
				</div>
			</div>
			<div class="af-field">
				<label for="afc-email">E-posta <small>(yanıt için önerilir)</small></label>
				<input type="email" name="email" id="afc-email" autocomplete="email" value="<?php echo esc_attr( arenafit_prefill( 'email' ) ); ?>">
			</div>
			<div class="af-field">
				<label for="afc-subject">Konu</label>
				<input type="text" name="subject" id="afc-subject" maxlength="120" required>
			</div>
			<div class="af-field">
				<label for="afc-body">Mesajınız</label>
				<textarea name="body" id="afc-body" rows="6" maxlength="3000" required></textarea>
			</div>
			<label class="af-check"><input type="checkbox" name="kvkk" value="1" required> <span><?php echo wp_kses( arenafit_kvkk_label(), array( 'a' => array( 'href' => array(), 'target' => array(), 'rel' => array() ) ) ); ?></span></label>

			<div class="af-msg" role="alert" aria-live="polite"></div>
			<button type="submit" class="af-btn af-btn--accent af-btn--block"><?php echo arenafit_icon( 'mail' ); // phpcs:ignore ?> Gönder</button>
			<p class="af-fine">Mesajınız ekibimize iletilir ve size kayıt numarası verilir.</p>
		</form>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'arenafit_contact', 'arenafit_contact_form' );

function arenafit_ajax_contact() {
	nocache_headers();

	if ( ! empty( $_POST['af_website'] ) ) {
		arenafit_fail( 'İşlem tamamlanamadı.' );
	}
	if ( arenafit_rate_limited( 'contact', 10 ) ) {
		arenafit_fail( 'Çok fazla mesaj gönderdiniz. Lütfen bir süre sonra tekrar deneyin veya bizi arayın.' );
	}

	$types   = arenafit_message_types();
	$type    = isset( $_POST['type'] ) ? sanitize_key( wp_unslash( $_POST['type'] ) ) : 'contact';
	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
	$body    = isset( $_POST['body'] ) ? sanitize_textarea_field( wp_unslash( $_POST['body'] ) ) : '';

	if ( ! isset( $types[ $type ] ) ) {
		$type = 'contact';
	}
	if ( mb_strlen( $name ) < 3 || mb_strlen( $name ) > 80 ) {
		arenafit_fail( 'Lütfen adınızı ve soyadınızı yazın.' );
	}
	if ( ! arenafit_valid_phone( $phone ) ) {
		arenafit_fail( 'Lütfen geçerli bir telefon numarası girin.' );
	}
	if ( ! empty( $_POST['email'] ) && ! is_email( $email ) ) {
		arenafit_fail( 'E-posta adresi geçersiz görünüyor.' );
	}
	if ( mb_strlen( $subject ) < 3 ) {
		arenafit_fail( 'Lütfen kısa bir konu başlığı yazın.' );
	}
	if ( mb_strlen( $body ) < 10 ) {
		arenafit_fail( 'Lütfen mesajınızı biraz daha ayrıntılı yazın.' );
	}
	if ( mb_strlen( $body ) > 3000 ) {
		arenafit_fail( 'Mesajınız çok uzun. Lütfen 3000 karakteri geçmeyin.' );
	}
	if ( empty( $_POST['kvkk'] ) ) {
		arenafit_fail( 'Devam etmek için KVKK aydınlatma metnini onaylamanız gerekir.' );
	}

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'af_message',
			'post_status' => 'publish',
			'post_title'  => $types[ $type ] . ': ' . $subject,
		),
		true
	);
	if ( is_wp_error( $post_id ) ) {
		arenafit_fail( 'Kayıt sırasında bir sorun oluştu. Lütfen tekrar deneyin.' );
	}
	$meta = array(
		'_af_type'    => $type,
		'_af_name'    => $name,
		'_af_phone'   => $phone,
		'_af_email'   => $email,
		'_af_subject' => $subject,
		'_af_body'    => $body,
		'_af_status'  => 'new',
	);
	if ( is_user_logged_in() ) {
		$meta['_af_user'] = get_current_user_id();
	}
	foreach ( $meta as $k => $v ) {
		update_post_meta( $post_id, $k, $v );
	}

	$ref  = 'AF-' . $post_id;
	$site = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

	$headers = array();
	if ( is_email( $email ) ) {
		$headers[] = 'Reply-To: ' . $email;
	}
	wp_mail(
		arenafit_notify_address(),
		'[' . $site . '] ' . $types[ $type ] . ' (' . $ref . '): ' . $subject,
		'Kayıt no: ' . $ref . "\nTür: " . $types[ $type ] . "\nAd soyad: " . $name . "\nTelefon: " . $phone . "\nE-posta: " . $email . "\nKonu: " . $subject . "\n\n" . $body . "\n\nPanelde aç: " . admin_url( 'post.php?post=' . $post_id . '&action=edit' ),
		$headers
	);
	if ( is_email( $email ) ) {
		wp_mail(
			$email,
			$site . ' – Mesajınız alındı (' . $ref . ')',
			'Merhaba ' . $name . ",\n\nMesajınızı aldık. Kayıt numaranız: " . $ref . "\nEkibimiz en kısa sürede sizinle iletişime geçecek.\n\nKonu: " . $subject . "\n\n" . $site
		);
	}

	wp_send_json_success(
		array(
			'message' => 'Mesajınız bize ulaştı. Ekibimiz en kısa sürede sizinle iletişime geçecek.',
			'summary' => 'Kayıt numaranız: ' . $ref,
		)
	);
}
add_action( 'wp_ajax_arenafit_contact', 'arenafit_ajax_contact' );
add_action( 'wp_ajax_nopriv_arenafit_contact', 'arenafit_ajax_contact' );
