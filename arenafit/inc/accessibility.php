<?php
/**
 * Erişilebilirlik: ziyaretçi araç çubuğu (yazı boyutu, kontrast, bağlantı vurgusu,
 * animasyon durdurma, okunaklı yazı) ve Erişilebilirlik Beyanı sayfası yardımcıları.
 *
 * Tercihler tarayıcıda (localStorage) saklanır; sunucuya veri gönderilmez.
 *
 * @package ArenaFit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function arenafit_a11y_url() {
	return arenafit_template_url( 'template-erisilebilirlik.php', '/erisilebilirlik/' );
}

/**
 * Sayfa çizilmeden önce kayıtlı tercihleri uygular (titreme olmaz).
 */
function arenafit_a11y_head() {
	?>
	<script>(function(){try{var p=JSON.parse(localStorage.getItem('afA11y')||'{}'),c=document.documentElement.classList;if(p.fs){c.add('a11y-fs-'+p.fs);}['contrast','links','motion','readable','cursor'].forEach(function(k){if(p[k]){c.add('a11y-'+k);}});}catch(e){}})();</script>
	<?php
}
add_action( 'wp_head', 'arenafit_a11y_head', 2 );

/**
 * Sağ alttaki erişilebilirlik düğmesi ve paneli.
 */
function arenafit_a11y_toolbar() {
	if ( ! apply_filters( 'arenafit_a11y_toolbar', true ) ) {
		return;
	}
	$toggles = array(
		'contrast' => array( 'Yüksek kontrast', 'Koyu zemin, açık yazı' ),
		'links'    => array( 'Bağlantıları vurgula', 'Altı çizili, belirgin bağlantılar' ),
		'readable' => array( 'Okunaklı yazı', 'Geniş harf ve satır aralığı' ),
		'motion'   => array( 'Animasyonları durdur', 'Kayan ve beliren efektler kapanır' ),
		'cursor'   => array( 'Büyük imleç', 'Fare imleci büyür' ),
	);
	?>
	<div class="af-a11y" data-af-a11y>
		<button type="button" class="af-a11y__btn" aria-expanded="false" aria-controls="af-a11y-panel">
			<svg class="af-i" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="4.5" r="2"/><path d="M4 8.5c2.6.9 5.3 1.3 8 1.3s5.4-.4 8-1.3M12 9.8V15m0 0-3.5 6.5M12 15l3.5 6.5"/></svg>
			<span class="screen-reader-text">Erişilebilirlik seçenekleri</span>
		</button>
		<div class="af-a11y__panel" id="af-a11y-panel" role="dialog" aria-modal="false" aria-labelledby="af-a11y-title" hidden>
			<div class="af-a11y__head">
				<h2 id="af-a11y-title">Erişilebilirlik</h2>
				<button type="button" class="af-a11y__close" aria-label="Paneli kapat"><?php echo arenafit_icon( 'close' ); // phpcs:ignore ?></button>
			</div>

			<div class="af-a11y__voice" role="group" aria-labelledby="af-a11y-voice">
				<span id="af-a11y-voice" class="af-a11y__label">Sesli yardım</span>
				<button type="button" class="af-a11y__toggle af-a11y__toggle--hl" data-toggle="tts" aria-pressed="false">
					<span><strong>Sesli okuma</strong><small>Gezindiğiniz her öğe sesli okunur (kısayol: Alt+Shift+S)</small></span>
					<i aria-hidden="true"></i>
				</button>
				<button type="button" class="af-a11y__toggle" data-toggle="voice" aria-pressed="false" data-af-voice-btn>
					<span><strong>Sesli komut</strong><small>"Rezervasyon", "antrenörler", "sayfayı oku" gibi komutlar</small></span>
					<i aria-hidden="true"></i>
				</button>
				<div class="af-a11y__tts" data-af-tts-tools hidden>
					<label for="af-tts-rate">Okuma hızı</label>
					<select id="af-tts-rate" data-af-tts-rate>
						<option value="0.8">Yavaş</option>
						<option value="1" selected>Normal</option>
						<option value="1.25">Hızlı</option>
						<option value="1.5">Çok hızlı</option>
					</select>
					<button type="button" class="af-btn af-btn--sm" data-af-read-page>Sayfayı oku</button>
					<button type="button" class="af-btn af-btn--sm" data-af-tts-help>Kısayollar</button>
				</div>
				<p class="af-a11y__warn" data-af-tts-warn hidden></p>
			</div>

			<div class="af-a11y__group" role="group" aria-labelledby="af-a11y-fs">
				<span id="af-a11y-fs" class="af-a11y__label">Yazı boyutu</span>
				<div class="af-a11y__fs">
					<button type="button" data-fs="-" aria-label="Yazıyı küçült">A−</button>
					<output aria-live="polite" data-fs-out>%100</output>
					<button type="button" data-fs="+" aria-label="Yazıyı büyüt">A+</button>
				</div>
			</div>

			<ul class="af-a11y__list">
				<?php foreach ( $toggles as $key => $t ) : ?>
					<li>
						<button type="button" class="af-a11y__toggle" data-toggle="<?php echo esc_attr( $key ); ?>" aria-pressed="false">
							<span><strong><?php echo esc_html( $t[0] ); ?></strong><small><?php echo esc_html( $t[1] ); ?></small></span>
							<i aria-hidden="true"></i>
						</button>
					</li>
				<?php endforeach; ?>
			</ul>

			<div class="af-a11y__foot">
				<button type="button" class="af-btn af-btn--sm" data-reset>Sıfırla</button>
				<a href="<?php echo esc_url( arenafit_a11y_url() ); ?>">Erişilebilirlik beyanı</a>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'arenafit_a11y_toolbar', 5 );

/**
 * Beyan sayfası içerik girilmemişse gösterilecek varsayılan metin.
 */
function arenafit_a11y_default_statement() {
	$name  = get_bloginfo( 'name' );
	$tel   = arenafit_opt( 'phone' );
	$mail  = arenafit_opt( 'email' );
	$items = array(
		'Klavye ile gezinme: Tüm menü, form ve düğmelere Tab tuşuyla ulaşılabilir; odaklanılan öğe belirgin bir çerçeveyle gösterilir.',
		'"İçeriğe geç" bağlantısı: Sayfanın başında, menüyü atlayıp doğrudan ana içeriğe geçmeyi sağlar.',
		'Ekran okuyucu uyumu: Sayfa yapısı başlık sırasına uygundur; ikonlar ekran okuyuculardan gizlenir, düğmelerin açıklayıcı adları vardır, menüde etkin sayfa bildirilir.',
		'Formlar: Her alanın görünür bir etiketi vardır; eksik alanlar hem renkle hem metinle belirtilir ve hata mesajları ekran okuyucuya anında okunur.',
		'Online rezervasyon takvimi: Boş ve dolu saatler yalnızca renkle değil, metinle de ("Dolu", "3 boş saat") belirtilir.',
		'Erişilebilirlik paneli: Sağ alttaki düğmeden yazı boyutu, yüksek kontrast, bağlantı vurgusu, okunaklı yazı, büyük imleç ve animasyon durdurma seçilebilir. Tercihler yalnızca sizin tarayıcınızda saklanır.',
		'Sesli okuma: Erişilebilirlik panelinden "Sesli okuma" açıldığında, Tab tuşuyla ya da fareyle üzerine gelinen her bağlantı, düğme ve form alanı türüyle birlikte Türkçe okunur. Form hataları, uygun saatler ve rezervasyon sonucu da kendiliğinden okunur. Kısayollar: Alt+Shift+S aç/kapat, Alt+Shift+R sayfayı oku, Alt+Shift+H başlıkları sırayla oku, Alt+Shift+K kısayolları dinle, Esc sustur.',
		'Sesli komut: Chrome ve Edge tarayıcılarında "Sesli komut" açılınca mikrofonla "ana sayfa", "antrenörler", "paketler", "salonlar", "rezervasyon", "üyelik", "iletişim", "hesabım", "sayfayı oku", "aşağı", "yukarı", "geri" ve "dur" komutları kullanılabilir.',
		'Hareket hassasiyeti: İşletim sisteminizde "hareketi azalt" seçiliyse animasyonlar otomatik kapanır.',
		'Mobil uyum: Site telefon ve tabletlerde yakınlaştırmaya izin verir; içerik yatay kaydırma gerektirmez.',
	);
	ob_start();
	?>
	<p><strong><?php echo esc_html( $name ); ?></strong> olarak web sitemizin engelli bireyler, yaşlılar ve geçici olarak zorluk yaşayan herkes tarafından kolayca kullanılabilmesini amaçlıyoruz. Sitemiz <strong>WCAG 2.1 AA</strong> yönergeleri esas alınarak tasarlanmıştır.</p>
	<h2>Sitede sunulan erişilebilirlik özellikleri</h2>
	<ul>
		<?php foreach ( $items as $it ) : ?>
			<li><?php echo esc_html( $it ); ?></li>
		<?php endforeach; ?>
	</ul>
	<h2>Tesisimizde fiziksel erişilebilirlik</h2>
	<p>Tesis girişimiz ve salonlarımız tekerlekli sandalye ile erişime uygundur. Özel bir ihtiyacınız varsa rezervasyon formundaki not alanına yazabilir veya bizi arayabilirsiniz; ekibimiz yardımcı olur.</p>
	<h2>Geri bildirim</h2>
	<p>Sitemizde erişmekte zorlandığınız bir bölüm olursa lütfen bize bildirin; en kısa sürede düzeltmeye çalışırız.</p>
	<p>
		<?php if ( $tel ) : ?>
			Telefon: <a href="<?php echo esc_url( arenafit_tel_href() ); ?>"><?php echo esc_html( $tel ); ?></a><br>
		<?php endif; ?>
		<?php if ( $mail ) : ?>
			E-posta: <a href="mailto:<?php echo esc_attr( $mail ); ?>"><?php echo esc_html( $mail ); ?></a><br>
		<?php endif; ?>
		İletişim formu: <a href="<?php echo esc_url( arenafit_contact_url() ); ?>">İletişim sayfası</a>
	</p>
	<?php
	return ob_get_clean();
}
