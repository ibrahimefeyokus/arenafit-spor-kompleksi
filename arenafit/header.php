<?php
/**
 * Üst bölüm.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$af_tel   = arenafit_opt( 'phone' );
$af_mail  = arenafit_opt( 'email' );
$af_hours = arenafit_opt( 'hours' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#081833">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<button type="button" class="skip-link screen-reader-text af-tts-skip" data-af-tts-quick>Sesli okumayı aç (Alt+Shift+S)</button>
<a class="skip-link screen-reader-text" href="#main">İçeriğe geç</a>

<div class="af-topbar">
	<div class="af-container af-topbar__in">
		<ul class="af-topbar__info">
			<?php if ( $af_tel ) : ?>
				<li><a href="<?php echo esc_url( arenafit_tel_href() ); ?>"><?php echo arenafit_icon( 'phone' ); // phpcs:ignore ?> <?php echo esc_html( $af_tel ); ?></a></li>
			<?php endif; ?>
			<?php if ( $af_mail ) : ?>
				<li class="af-hide-sm"><a href="mailto:<?php echo esc_attr( $af_mail ); ?>"><?php echo arenafit_icon( 'mail' ); // phpcs:ignore ?> <?php echo esc_html( $af_mail ); ?></a></li>
			<?php endif; ?>
			<?php if ( $af_hours ) : ?>
				<li class="af-hide-sm"><span><?php echo arenafit_icon( 'clock' ); // phpcs:ignore ?> <?php echo esc_html( $af_hours ); ?></span></li>
			<?php endif; ?>
		</ul>
		<ul class="af-social">
			<?php foreach ( array( 'instagram' => 'Instagram', 'facebook' => 'Facebook', 'youtube' => 'YouTube' ) as $k => $label ) : ?>
				<?php if ( arenafit_opt( $k ) ) : ?>
					<li><a href="<?php echo esc_url( arenafit_opt( $k ) ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $label . ' (yeni sekmede açılır)' ); ?>"><?php echo arenafit_icon( $k ); // phpcs:ignore ?></a></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</div>
</div>

<header class="af-header">
	<div class="af-container af-header__in">
		<div class="af-brand">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="af-brand__link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<span class="af-brand__mark"><?php echo arenafit_logo_mark(); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span class="af-brand__name"><?php echo arenafit_wordmark(); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				</a>
			<?php endif; ?>
		</div>

		<nav id="af-nav" class="af-nav" aria-label="Ana menü">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'af-menu',
					'fallback_cb'    => 'arenafit_fallback_menu',
					'depth'          => 2,
				)
			);
			?>
			<a class="af-btn af-btn--accent af-nav__cta" href="<?php echo esc_url( arenafit_booking_url() ); ?>">Rezervasyon Yap</a>
			<a class="af-btn af-nav__cta af-nav__account" href="<?php echo esc_url( arenafit_account_url() ); ?>"><?php echo arenafit_icon( 'user' ); // phpcs:ignore ?> <?php echo is_user_logged_in() ? 'Hesabım' : 'Giriş Yap / Üye Ol'; ?></a>
		</nav>

		<a class="af-account af-header__account" href="<?php echo esc_url( arenafit_account_url() ); ?>"><?php echo arenafit_icon( 'user' ); // phpcs:ignore ?> <span><?php echo is_user_logged_in() ? 'Hesabım' : 'Giriş Yap'; ?></span></a>

		<a class="af-btn af-btn--accent af-btn--sm af-header__cta" href="<?php echo esc_url( arenafit_booking_url() ); ?>"><?php echo arenafit_icon( 'calendar' ); // phpcs:ignore ?> Rezervasyon Yap</a>

		<button class="af-nav-toggle" type="button" aria-controls="af-nav" aria-expanded="false" aria-label="Menüyü aç/kapat">
			<?php echo arenafit_icon( 'menu', 'af-i--open' ); // phpcs:ignore ?>
			<?php echo arenafit_icon( 'close', 'af-i--close' ); // phpcs:ignore ?>
		</button>
	</div>
</header>

<main id="main" class="af-main">
