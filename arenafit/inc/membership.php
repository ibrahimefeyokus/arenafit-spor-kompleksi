<?php
/**
 * Üyelik başvurusu: plan listesi, form, AJAX işleyici.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Özelleştiricideki plan satırlarını diziye çevirir.
 * Biçim: Plan adı | Fiyat | Süre | Özellik 1, Özellik 2
 */
function arenafit_plans() {
	$out = array();
	foreach ( arenafit_lines( arenafit_opt( 'plans' ) ) as $line ) {
		$p = array_map( 'trim', explode( '|', $line ) );
		if ( '' === $p[0] ) {
			continue;
		}
		$out[] = array(
			'name'     => $p[0],
			'price'    => isset( $p[1] ) ? $p[1] : '',
			'period'   => isset( $p[2] ) ? $p[2] : '',
			'features' => isset( $p[3] ) ? array_values( array_filter( array_map( 'trim', explode( ',', $p[3] ) ) ) ) : array(),
		);
	}
	return $out;
}

function arenafit_interest_options() {
	return array(
		'Spor kompleksi üyeliği',
		'Bireysel PT',
		'Grup antrenmanı',
		'Salon kiralama',
	);
}

function arenafit_membership_form() {
	$plans = arenafit_plans();
	$pkg   = isset( $_GET['package'] ) ? absint( $_GET['package'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
	if ( $pkg && ( 'af_package' !== get_post_type( $pkg ) || 'publish' !== get_post_status( $pkg ) ) ) {
		$pkg = 0;
	}

	ob_start();
	?>
	<div class="af-booking af-member">
		<form class="af-form" novalidate data-kind="member">
			<input type="hidden" name="action" value="arenafit_member">
			<input type="text" name="af_website" class="af-hp" tabindex="-1" autocomplete="off" aria-hidden="true">
			<input type="hidden" name="package" value="<?php echo (int) $pkg; ?>">

			<?php if ( $pkg ) : ?>
				<div class="af-chip-fixed"><?php echo arenafit_icon( 'check' ); // phpcs:ignore ?> Seçilen paket: <?php echo esc_html( get_the_title( $pkg ) ); ?></div>
			<?php endif; ?>

			<div class="af-row">
				<div class="af-field">
					<label for="afm-name">Ad soyad</label>
					<input type="text" name="name" id="afm-name" autocomplete="name" value="<?php echo esc_attr( arenafit_prefill( 'name' ) ); ?>" required>
				</div>
				<div class="af-field">
					<label for="afm-phone">Telefon</label>
					<input type="tel" name="phone" id="afm-phone" autocomplete="tel" inputmode="tel" placeholder="05xx xxx xx xx" value="<?php echo esc_attr( arenafit_prefill( 'phone' ) ); ?>" required>
				</div>
			</div>
			<div class="af-field">
				<label for="afm-email">E-posta <small>(isteğe bağlı)</small></label>
				<input type="email" name="email" id="afm-email" autocomplete="email" value="<?php echo esc_attr( arenafit_prefill( 'email' ) ); ?>">
			</div>

			<?php if ( $plans ) : ?>
				<div class="af-field">
					<label for="afm-plan">Üyelik planı</label>
					<select name="plan" id="afm-plan">
						<option value="">Henüz karar vermedim</option>
						<?php foreach ( $plans as $pl ) : ?>
							<option value="<?php echo esc_attr( $pl['name'] ); ?>"><?php echo esc_html( $pl['name'] ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>

			<fieldset class="af-field af-fieldset">
				<legend class="af-label">İlgilendiğiniz hizmetler</legend>
				<div class="af-checks">
					<?php foreach ( arenafit_interest_options() as $opt ) : ?>
						<label class="af-check"><input type="checkbox" name="interest[]" value="<?php echo esc_attr( $opt ); ?>"> <span><?php echo esc_html( $opt ); ?></span></label>
					<?php endforeach; ?>
				</div>
			</fieldset>

			<div class="af-field">
				<label for="afm-note">Notunuz <small>(isteğe bağlı)</small></label>
				<textarea name="note" id="afm-note" rows="3"></textarea>
			</div>
			<label class="af-check"><input type="checkbox" name="kvkk" value="1" required> <span><?php echo wp_kses( arenafit_kvkk_label(), array( 'a' => array( 'href' => array(), 'target' => array(), 'rel' => array() ) ) ); ?></span></label>

			<div class="af-msg" role="alert" aria-live="polite"></div>
			<button type="submit" class="af-btn af-btn--accent af-btn--block"><?php echo arenafit_icon( 'users' ); // phpcs:ignore ?> Başvuruyu Gönder</button>
			<p class="af-fine">Başvurunuz alındıktan sonra sizi arayarak üyelik işlemlerini tamamlayacağız.</p>
		</form>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'arenafit_membership', 'arenafit_membership_form' );

function arenafit_ajax_member() {
	nocache_headers();

	if ( ! empty( $_POST['af_website'] ) ) {
		arenafit_fail( 'İşlem tamamlanamadı.' );
	}
	if ( arenafit_rate_limited( 'member', 10 ) ) {
		arenafit_fail( 'Çok fazla deneme yaptınız. Lütfen bir süre sonra tekrar deneyin veya bizi arayın.' );
	}

	$name  = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$note  = isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '';
	$plan  = isset( $_POST['plan'] ) ? sanitize_text_field( wp_unslash( $_POST['plan'] ) ) : '';
	$pkg   = isset( $_POST['package'] ) ? absint( $_POST['package'] ) : 0;

	$allowed  = arenafit_interest_options();
	$interest = array();
	if ( isset( $_POST['interest'] ) && is_array( $_POST['interest'] ) ) {
		foreach ( wp_unslash( $_POST['interest'] ) as $i ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$i = sanitize_text_field( $i );
			if ( in_array( $i, $allowed, true ) ) {
				$interest[] = $i;
			}
		}
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
	if ( empty( $_POST['kvkk'] ) ) {
		arenafit_fail( 'Devam etmek için KVKK aydınlatma metnini onaylamanız gerekir.' );
	}

	// Plan adı yalnızca tanımlı planlardan biri olabilir.
	$valid_plan = '';
	foreach ( arenafit_plans() as $pl ) {
		if ( $pl['name'] === $plan ) {
			$valid_plan = $plan;
		}
	}
	if ( $pkg && ( 'af_package' !== get_post_type( $pkg ) || 'publish' !== get_post_status( $pkg ) ) ) {
		$pkg = 0;
	}
	$plan_text = trim( $valid_plan . ( $pkg ? ( $valid_plan ? ' / ' : '' ) . 'Paket: ' . get_the_title( $pkg ) : '' ) );

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'af_member',
			'post_status' => 'publish',
			'post_title'  => $name . ' – ' . wp_date( 'd.m.Y H:i' ),
		),
		true
	);
	if ( is_wp_error( $post_id ) ) {
		arenafit_fail( 'Kayıt sırasında bir sorun oluştu. Lütfen tekrar deneyin.' );
	}
	update_post_meta( $post_id, '_af_name', $name );
	update_post_meta( $post_id, '_af_phone', $phone );
	update_post_meta( $post_id, '_af_email', $email );
	update_post_meta( $post_id, '_af_interest', implode( ', ', $interest ) );
	update_post_meta( $post_id, '_af_plan', $plan_text );
	update_post_meta( $post_id, '_af_note', $note );
	update_post_meta( $post_id, '_af_status', 'new' );

	$site = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$body = "Yeni bir üyelik başvurusu geldi.\n\n"
		. 'Ad soyad: ' . $name . "\n"
		. 'Telefon: ' . $phone . "\n"
		. 'E-posta: ' . $email . "\n"
		. 'İlgilendiği hizmetler: ' . implode( ', ', $interest ) . "\n"
		. 'Plan / paket: ' . $plan_text . "\n"
		. 'Not: ' . $note . "\n\n"
		. 'Yönetim paneli: ' . admin_url( 'edit.php?post_type=af_member' );
	wp_mail( arenafit_notify_address(), '[' . $site . '] Yeni üyelik başvurusu: ' . $name, $body );

	if ( is_email( $email ) ) {
		wp_mail( $email, $site . ' – Başvurunuz alındı', 'Merhaba ' . $name . ",\n\nÜyelik başvurunuzu aldık. Ekibimiz sizinle en kısa sürede iletişime geçecek.\n\n" . $site );
	}

	wp_send_json_success(
		array(
			'message' => 'Başvurunuz alındı. Ekibimiz sizinle en kısa sürede iletişime geçecek.',
			'summary' => '',
		)
	);
}
add_action( 'wp_ajax_arenafit_member', 'arenafit_ajax_member' );
add_action( 'wp_ajax_nopriv_arenafit_member', 'arenafit_ajax_member' );
