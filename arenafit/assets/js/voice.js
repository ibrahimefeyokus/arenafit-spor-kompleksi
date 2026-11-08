/* ArenaFit – Sesli okuma (metinden konuşma) ve sesli komut
 *
 * Sesli okuma: Tab ile ya da fareyle gelinen bağlantı, düğme ve form alanları
 * türü, adı ve durumuyla Türkçe okunur. Form mesajları, uygun saatler ve sonuçlar
 * kendiliğinden okunur. Kısayollar: Alt+Shift+S aç/kapat, Alt+Shift+R sayfayı oku,
 * Alt+Shift+H sonraki başlık, Alt+Shift+K kısayollar, Alt+Shift+V sesli komut, Esc sustur.
 *
 * Sesli komut: Web Speech API (Chrome / Edge). "rezervasyon", "antrenörler",
 * "tıkla <düğme adı>", "yaz <metin>" vb.
 */
(function () {
	'use strict';

	var $ = function (s, c) { return (c || document).querySelector(s); };
	var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
	var api = window.AFA11y;
	var cfg = window.ArenaFit || {};
	var nav = cfg.nav || {};
	if (!api) { return; }

	var synth = window.speechSynthesis || null;
	var SR = window.SpeechRecognition || window.webkitSpeechRecognition || null;
	var on = false;
	var voice = null;
	var rate = 1;
	var pending = [];
	var activated = false;
	var lastSpoken = '';
	var lastTime = 0;
	var welcomeUntil = 0;

	/* ---------------------------------------------------------------- Ses */
	function pickVoice() {
		if (!synth) { return; }
		var list = synth.getVoices() || [];
		var tr = list.filter(function (v) { return /^tr/i.test(v.lang); });
		var pref = tr.filter(function (v) { return /natural|online|google|microsoft/i.test(v.name); });
		voice = pref[0] || tr[0] || null;
		var warn = $('[data-af-tts-warn]');
		if (warn) {
			if (list.length && !tr.length) {
				warn.hidden = false;
				warn.textContent = 'Cihazınızda Türkçe ses bulunamadı; okuma varsayılan sesle yapılır. Windows\'ta Ayarlar > Saat ve dil > Konuşma bölümünden Türkçe ses ekleyebilirsiniz.';
			} else {
				warn.hidden = true;
			}
		}
	}
	if (synth) {
		pickVoice();
		if (typeof synth.addEventListener === 'function') { synth.addEventListener('voiceschanged', pickVoice); }
		else { synth.onvoiceschanged = pickVoice; }
	}

	/* Uzun metni ~180 karakterlik cümle parçalarına böler (tarayıcılar uzun metni yarıda kesebilir). */
	function chunks(text) {
		var parts = String(text).replace(/\s+/g, ' ').trim().match(/[^.!?…;]+[.!?…;]*/g) || [];
		var out = [], cur = '';
		parts.forEach(function (p) {
			if ((cur + ' ' + p).length > 180 && cur) { out.push(cur.trim()); cur = p; }
			else { cur += ' ' + p; }
		});
		if (cur.trim()) { out.push(cur.trim()); }
		return out;
	}

	function say(text, queue, force) {
		text = String(text || '').replace(/\s+/g, ' ').trim();
		if (!text || !synth || (!on && !force)) { return; }
		var now = Date.now();
		if (!queue && text === lastSpoken && now - lastTime < 700) { return; }
		lastSpoken = text;
		lastTime = now;
		if (!activated) { pending.push(text); return; }
		if (now < welcomeUntil) { queue = true; }
		if (!queue) { synth.cancel(); }
		chunks(text).forEach(function (c) {
			var u = new SpeechSynthesisUtterance(c);
			u.lang = 'tr-TR';
			u.rate = rate;
			if (voice) { u.voice = voice; }
			u.onerror = function (e) {
				if (e && e.error === 'not-allowed') { activated = false; pending.push(c); }
			};
			synth.speak(u);
		});
		hud(text);
	}
	function stop() { if (synth) { synth.cancel(); } pending = []; }

	/* Tarayıcılar ilk kullanıcı etkileşiminden önce konuşmaya izin vermeyebilir. */
	function activate() {
		if (activated) { return; }
		activated = true;
		if (pending.length) {
			var p = pending.slice();
			pending = [];
			p.forEach(function (t) { say(t, true, true); });
			welcomeUntil = Date.now() + 3500;
		}
		if (wantVoice && !listening) { startRec(); }
	}
	['keydown', 'pointerdown', 'touchstart'].forEach(function (ev) {
		document.addEventListener(ev, activate, { capture: true, passive: true });
	});

	/* Görsel geri bildirim kutusu (işiten ama az gören kullanıcılar için de). */
	var hudEl = null;
	function hud(text, listeningMode) {
		if (!hudEl) {
			hudEl = document.createElement('div');
			hudEl.className = 'af-voice-hud';
			hudEl.setAttribute('aria-hidden', 'true');
			document.body.appendChild(hudEl);
		}
		hudEl.textContent = (listeningMode ? '🎤 ' : '🔊 ') + text;
		hudEl.classList.add('is-on');
		clearTimeout(hud.t);
		hud.t = setTimeout(function () { if (!listening) { hudEl.classList.remove('is-on'); } }, 4000);
	}

	/* ------------------------------------------------- Öğeyi tarif etme */
	function clean(s) { return String(s || '').replace(/\s+/g, ' ').trim(); }

	function nameOf(el) {
		var n = el.getAttribute('aria-label');
		if (n) { return clean(n); }
		var lb = el.getAttribute('aria-labelledby');
		if (lb) {
			var t = lb.split(/\s+/).map(function (id) { var x = document.getElementById(id); return x ? x.textContent : ''; }).join(' ');
			if (clean(t)) { return clean(t); }
		}
		if (el.id) {
			var l = document.querySelector('label[for="' + (window.CSS && CSS.escape ? CSS.escape(el.id) : el.id) + '"]');
			if (l) { return clean(l.innerText || l.textContent); }
		}
		var wrap = el.closest('label');
		if (wrap) { return clean(wrap.innerText || wrap.textContent); }
		if (el.tagName === 'IMG') { return clean(el.alt); }
		var txt = clean(el.innerText || el.textContent);
		if (!txt) {
			var img = el.querySelector('img[alt]');
			if (img) { txt = clean(img.alt); }
		}
		return txt || clean(el.getAttribute('placeholder')) || clean(el.getAttribute('title'));
	}

	var INPUT_ROLES = {
		email: 'e-posta alanı', tel: 'telefon alanı', date: 'tarih alanı', password: 'şifre alanı',
		search: 'arama alanı', number: 'sayı alanı', url: 'adres alanı', time: 'saat alanı'
	};

	function describe(el) {
		if (!el || el === document.body) { return ''; }
		var tag = el.tagName;
		var role = el.getAttribute('role');
		var name = nameOf(el);
		var bits = [];
		var kind = '';

		if (/^H[1-6]$/.test(tag)) {
			kind = 'başlık, seviye ' + tag.charAt(1);
		} else if (tag === 'A') {
			kind = 'bağlantı';
		} else if (tag === 'SUMMARY') {
			kind = 'açılır bölüm, ' + (el.parentNode.open ? 'açık' : 'kapalı');
		} else if (tag === 'SELECT') {
			var o = el.options[el.selectedIndex];
			kind = 'açılır liste, seçili: ' + (o ? clean(o.text) : 'yok');
		} else if (tag === 'TEXTAREA') {
			kind = 'çok satırlı metin alanı';
		} else if (tag === 'INPUT') {
			var t = (el.type || 'text').toLowerCase();
			if (t === 'checkbox') { kind = 'onay kutusu, ' + (el.checked ? 'işaretli' : 'işaretli değil'); }
			else if (t === 'radio') { kind = 'seçenek, ' + (el.checked ? 'seçili' : 'seçili değil'); }
			else if (t === 'submit' || t === 'button') { kind = 'düğme'; name = name || el.value; }
			else { kind = INPUT_ROLES[t] || 'metin alanı'; }
		} else if (tag === 'BUTTON' || role === 'button') {
			kind = 'düğme';
		} else if (role === 'tab') {
			kind = 'sekme';
		} else if (tag === 'IMG') {
			kind = 'görsel';
		}

		bits.push(name);
		if (kind) { bits.push(kind); }

		if (tag === 'INPUT' || tag === 'TEXTAREA') {
			var ty = (el.type || '').toLowerCase();
			if (ty !== 'checkbox' && ty !== 'radio' && ty !== 'submit' && ty !== 'button') {
				if (ty === 'password') { bits.push(el.value ? 'dolu' : 'boş'); }
				else { bits.push(el.value ? 'yazılan: ' + el.value : 'boş'); }
			}
		}
		if (el.getAttribute('aria-pressed') === 'true') { bits.push('seçili'); }
		var exp = el.getAttribute('aria-expanded');
		if (exp) { bits.push(exp === 'true' ? 'açık' : 'kapalı'); }
		if (el.getAttribute('aria-current')) { bits.push('şu anki sayfa'); }
		if (el.disabled || el.getAttribute('aria-disabled') === 'true') { bits.push('kullanılamaz'); }
		if (el.required) { bits.push('zorunlu'); }
		if (el.getAttribute('aria-invalid') === 'true') { bits.push('hatalı, lütfen doldurun'); }
		if (tag === 'A' && el.target === '_blank' && !/yeni sekme/i.test(name)) { bits.push('yeni sekmede açılır'); }

		return bits.filter(Boolean).join(', ');
	}

	/* ------------------------------------------------- Sayfa ve başlıklar */
	function visible(el) { return !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length); }

	function readPage() {
		var main = $('#main') || document.body;
		var blocks = $$('h1, h2, h3, h4, p, li, dt, dd, th, td, label, .af-cal__day, summary', main).filter(function (b) {
			return visible(b) && !b.closest('[aria-hidden="true"], .af-a11y, nav, .screen-reader-text') && !b.parentNode.closest('p, li, td, dd, label, summary');
		});
		var text = blocks.map(function (b) {
			var t = clean(b.getAttribute('aria-label') || b.innerText);
			if (/^H[1-6]$/.test(b.tagName)) { t = 'Başlık: ' + t; }
			return t.replace(/[.!?:]?$/, '.');
		}).filter(function (t) { return t.length > 1; }).join(' ');
		stop();
		say(text || 'Bu sayfada okunacak metin bulunamadı.', true);
	}

	var hIndex = -1;
	function nextHeading(back) {
		var hs = $$('#main h1, #main h2, #main h3').filter(visible);
		if (!hs.length) { say('Bu sayfada başlık yok.'); return; }
		hIndex = back ? hIndex - 1 : hIndex + 1;
		if (hIndex >= hs.length) { hIndex = 0; }
		if (hIndex < 0) { hIndex = hs.length - 1; }
		var h = hs[hIndex];
		if (!h.hasAttribute('tabindex')) { h.setAttribute('tabindex', '-1'); }
		h.focus();
		h.scrollIntoView({ block: 'center' });
	}

	var HELP = 'Kısayollar: Tab ile sonraki öğeye, Shift Tab ile önceki öğeye geçin; bulunduğunuz öğe okunur. ' +
		'Enter ile bağlantıyı açın, boşluk tuşuyla düğmeye basın veya kutuyu işaretleyin. ' +
		'Alt Shift R: sayfayı baştan oku. Alt Shift H: sonraki başlığa geç. Alt Shift S: sesli okumayı kapat. ' +
		'Alt Shift V: sesli komutu aç veya kapat. Escape: okumayı durdur.';

	/* ------------------------------------------------- Olay dinleyicileri */
	document.addEventListener('focusin', function (e) {
		if (!on) { return; }
		say(describe(e.target));
	});

	var hoverT = null;
	document.addEventListener('mouseover', function (e) {
		if (!on) { return; }
		var el = e.target.closest('a, button, input, select, textarea, summary, [role="button"], h1, h2, h3, img[alt]');
		if (!el || el === hoverT) { return; }
		hoverT = el;
		var target = el;
		setTimeout(function () { if (hoverT === target) { say(describe(target)); } }, 350);
	});

	document.addEventListener('click', function (e) {
		if (!on) { return; }
		if (e.target.closest('a, button, input, select, textarea, label, summary, [role="button"]')) { return; }
		var b = e.target.closest('p, li, td, th, dd, dt, h4, .af-fact, .af-note');
		if (b) { say(clean(b.innerText)); }
	});

	document.addEventListener('change', function (e) {
		if (!on) { return; }
		var t = e.target;
		if (t.matches('input[type="checkbox"], input[type="radio"]')) {
			say(nameOf(t) + ', ' + (t.checked ? 'işaretlendi' : 'işaret kaldırıldı'));
		} else if (t.tagName === 'SELECT') {
			var o = t.options[t.selectedIndex];
			say('Seçildi: ' + (o ? clean(o.text) : ''));
		} else if (t.type === 'date' && t.value) {
			var d = new Date(t.value + 'T12:00:00');
			say('Tarih seçildi: ' + d.toLocaleDateString('tr-TR', { weekday: 'long', day: 'numeric', month: 'long' }));
		}
	});

	/* Saat düğmesi seçilince */
	document.addEventListener('click', function (e) {
		if (!on) { return; }
		var s = e.target.closest('.af-slot');
		if (s && !s.disabled) { say('Saat ' + s.textContent + ' seçildi. Şimdi ad, telefon ve onay kutusunu doldurup Rezervasyon Talebi Gönder düğmesine basın.'); }
	});

	/* Değişen bölgeleri (uygun saatler, form mesajları, başarı paneli) oku */
	function slotSummary(box) {
		var btns = $$('.af-slot', box);
		var hint = $('.af-hint', box);
		if (!btns.length) { return hint ? clean(hint.textContent) : ''; }
		var free = btns.filter(function (b) { return !b.disabled; }).map(function (b) { return b.textContent; });
		if (!free.length) { return 'Bu tarihte tüm saatler dolu.'; }
		return free.length + ' uygun saat var: ' + free.join(', ') + '. Saati seçmek için Tab ile ilerleyip Enter\'a basın.';
	}

	var liveT = {};
	function announce(node) {
		var key = node.className || node.tagName;
		clearTimeout(liveT[key]);
		liveT[key] = setTimeout(function () {
			var text = node.classList && node.classList.contains('af-slots') ? slotSummary(node) : clean(node.innerText || node.textContent);
			if (text) { say(text, node.getAttribute('role') !== 'alert' && !node.classList.contains('af-msg')); }
		}, 350);
	}

	if ('MutationObserver' in window) {
		new MutationObserver(function (muts) {
			if (!on) { return; }
			muts.forEach(function (m) {
				var t = m.target.nodeType === 1 ? m.target : m.target.parentNode;
				if (!t || !t.closest) { return; }
				var live = t.closest('[aria-live], [role="alert"], [role="status"]');
				if (live && !live.closest('.af-a11y')) { announce(live); }
				Array.prototype.forEach.call(m.addedNodes || [], function (n) {
					if (n.nodeType !== 1) { return; }
					var st = n.matches('[role="status"], [role="alert"]') ? n : n.querySelector('[role="status"], [role="alert"]');
					if (st) { announce(st); }
				});
			});
		}).observe(document.body, { childList: true, subtree: true, characterData: true });
	}

	/* ------------------------------------------------- Aç / kapat */
	var toolsBox = $('[data-af-tts-tools]');
	var rateSel = $('[data-af-tts-rate]');
	function syncTools(p) {
		if (toolsBox) { toolsBox.hidden = !p.tts; }
		rate = parseFloat(p.ttsRate) || 1;
		if (rateSel) { rateSel.value = String(rate); }
		$$('[data-af-tts-quick]').forEach(function (b) {
			b.textContent = p.tts ? 'Sesli okumayı kapat (Alt+Shift+S)' : 'Sesli okumayı aç (Alt+Shift+S)';
		});
	}

	function setTts(state, silent) {
		var was = on;
		if (!state && was && !silent) { say('Sesli okuma kapatıldı.', false, true); }
		on = !!state;
		if (on && !was && !silent) {
			say('Sesli okuma açıldı. ' + HELP, false);
		}
	}

	document.addEventListener('af:a11y', function (e) {
		var p = e.detail || {};
		syncTools(p);
		if (!!p.tts !== on) { setTts(p.tts); }
		if (!!p.voice !== wantVoice) { setVoice(p.voice); }
	});

	if (rateSel) {
		rateSel.addEventListener('change', function () {
			api.set('ttsRate', rateSel.value === '1' ? 0 : parseFloat(rateSel.value));
			say('Okuma hızı: ' + rateSel.options[rateSel.selectedIndex].text);
		});
	}
	$$('[data-af-read-page]').forEach(function (b) { b.addEventListener('click', readPage); });
	$$('[data-af-tts-help]').forEach(function (b) { b.addEventListener('click', function () { say(HELP); }); });
	$$('[data-af-tts-quick]').forEach(function (b) {
		b.addEventListener('click', function () { activate(); api.set('tts', !api.get().tts); });
	});

	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && synth && synth.speaking) { stop(); }
		if (!e.altKey || !e.shiftKey || e.ctrlKey || e.metaKey) { return; }
		var c = e.code;
		if (c === 'KeyS') { e.preventDefault(); activate(); api.set('tts', !api.get().tts); }
		else if (!on && c !== 'KeyV') { return; }
		else if (c === 'KeyR') { e.preventDefault(); readPage(); }
		else if (c === 'KeyH') { e.preventDefault(); nextHeading(false); }
		else if (c === 'KeyY') { e.preventDefault(); nextHeading(true); }
		else if (c === 'KeyK') { e.preventDefault(); say(HELP); }
		else if (c === 'KeyV') { e.preventDefault(); activate(); api.set('voice', !api.get().voice); }
	});

	/* ------------------------------------------------- Sesli komut */
	var rec = null;
	var wantVoice = false;
	var listening = false;
	var voiceBtn = $('[data-af-voice-btn]');
	if (!SR && voiceBtn) {
		voiceBtn.disabled = true;
		var sm = $('small', voiceBtn);
		if (sm) { sm.textContent = 'Bu tarayıcı desteklemiyor. Chrome veya Edge kullanın.'; }
	}
	if (!synth) {
		var tb = $('[data-toggle="tts"]');
		if (tb) { tb.disabled = true; var s2 = $('small', tb); if (s2) { s2.textContent = 'Bu tarayıcı sesli okumayı desteklemiyor.'; } }
	}

	function norm(s) { return String(s || '').toLocaleLowerCase('tr-TR').replace(/[.,!?]/g, '').replace(/\s+/g, ' ').trim(); }

	function go(url, label) {
		if (!url) { say(label + ' bulunamadı.'); return; }
		say(label + ' açılıyor.', false, true);
		setTimeout(function () { window.location.href = url; }, 900);
	}

	function clickByText(q) {
		var els = $$('a, button, [role="button"], summary, label').filter(visible);
		var hit = null, best = 0;
		els.forEach(function (el) {
			var n = norm(nameOf(el));
			if (!n) { return; }
			var score = n === q ? 3 : (n.indexOf(q) === 0 ? 2 : (n.indexOf(q) !== -1 ? 1 : 0));
			if (score > best) { best = score; hit = el; }
		});
		if (!hit) { say('"' + q + '" adında bir düğme veya bağlantı bulamadım.', false, true); return; }
		hit.focus();
		say(nameOf(hit) + ' seçildi.', false, true);
		setTimeout(function () { hit.click(); }, 500);
	}

	var COMMANDS = [
		[/^(ana ?sayfa|anasayfa|başa dön)$/, function () { go(nav.home, 'Ana sayfa'); }],
		[/antrenör/, function () { go(nav.trainers, 'Antrenörler sayfası'); }],
		[/(paket|pt eğitim|eğitimler|fiyat|ücret)/, function () { go(nav.packages, 'PT paketleri sayfası'); }],
		[/(salon|saha|kirala)/, function () { go(nav.halls, 'Salonlar sayfası'); }],
		[/(rezervasyon|randevu)/, function () { go(nav.booking, 'Rezervasyon sayfası'); }],
		[/(üyelik|üye ol|başvur)/, function () { go(nav.member, 'Üyelik sayfası'); }],
		[/(iletişim|şikayet|mesaj)/, function () { go(nav.contact, 'İletişim sayfası'); }],
		[/(hesabım|giriş yap|hesap)/, function () { go(nav.account, 'Hesabım sayfası'); }],
		[/(tesis|duş|otopark|kafe|soyunma)/, function () { go(nav.facility, 'Tesis olanakları sayfası'); }],
		[/(sayfayı oku|oku)$/, function () { readPage(); }],
		[/^(dur|sus|durdur|kes|yeter)$/, function () { stop(); }],
		[/aşağı/, function () { window.scrollBy({ top: window.innerHeight * 0.8, behavior: 'smooth' }); say('Aşağı kaydırıldı.', false, true); }],
		[/yukarı/, function () { window.scrollBy({ top: -window.innerHeight * 0.8, behavior: 'smooth' }); say('Yukarı kaydırıldı.', false, true); }],
		[/^(geri|geri dön)$/, function () { say('Önceki sayfaya dönülüyor.', false, true); setTimeout(function () { history.back(); }, 700); }],
		[/(sonraki başlık|başlık)$/, function () { nextHeading(false); }],
		[/(sesli okumayı aç)$/, function () { api.set('tts', true); }],
		[/(sesli okumayı kapat)$/, function () { api.set('tts', false); }],
		[/(yardım|komutlar|ne diyebilirim)/, function () {
			say('Kullanabileceğiniz komutlar: ana sayfa, antrenörler, paketler, salonlar, rezervasyon, üyelik, iletişim, hesabım, tesis, sayfayı oku, dur, aşağı, yukarı, geri, sonraki başlık. ' +
				'Bir düğmeye basmak için tıkla ve düğmenin adını söyleyin; örneğin tıkla randevu al. Seçili alana yazmak için yaz ve metni söyleyin; örneğin yaz Ahmet Yılmaz. ' +
				'Sesli komutu kapatmak için komutu kapat deyin.', false, true);
		}],
		[/(komutu kapat|dinlemeyi bırak|mikrofonu kapat)$/, function () { api.set('voice', false); }]
	];

	function handle(raw) {
		var q = norm(raw);
		if (!q) { return; }
		hud(raw, true);
		var m = q.match(/^(tıkla|bas|seç|aç)\s+(.+)$/);
		if (m) { clickByText(m[2]); return; }
		m = raw.trim().match(/^(yaz|yazın)\s+(.+)$/i);
		if (m) {
			var a = document.activeElement;
			if (a && (a.tagName === 'TEXTAREA' || (a.tagName === 'INPUT' && !/checkbox|radio|submit|button/.test(a.type)))) {
				var v = m[2];
				if (a.type === 'tel') { v = v.replace(/[^\d+ ]/g, ''); }
				if (a.type === 'email') { v = v.toLowerCase().replace(/\s+et\s+|\s*@\s*/g, '@').replace(/\s+nokta\s+/g, '.').replace(/\s+/g, ''); }
				a.value = v;
				a.dispatchEvent(new Event('input', { bubbles: true }));
				say('Yazıldı: ' + v, false, true);
			} else {
				say('Önce Tab ile bir metin alanına gelin, sonra yaz komutunu kullanın.', false, true);
			}
			return;
		}
		for (var i = 0; i < COMMANDS.length; i++) {
			if (COMMANDS[i][0].test(q)) { COMMANDS[i][1](); return; }
		}
		say('Anlamadım: ' + raw + '. Komutları duymak için yardım deyin.', false, true);
	}

	function startRec() {
		if (!SR || listening) { return; }
		try {
			rec = new SR();
			rec.lang = 'tr-TR';
			rec.continuous = true;
			rec.interimResults = false;
			rec.onresult = function (ev) {
				var r = ev.results[ev.results.length - 1];
				if (r && r.isFinal) { handle(r[0].transcript); }
			};
			rec.onerror = function (ev) {
				if (ev.error === 'not-allowed' || ev.error === 'service-not-allowed') {
					wantVoice = false;
					api.set('voice', false);
					say('Mikrofon izni verilmedi. Tarayıcının adres çubuğundaki mikrofon simgesinden izin verebilirsiniz.', false, true);
				}
			};
			rec.onend = function () {
				listening = false;
				if (wantVoice) { setTimeout(startRec, 300); }
				else if (hudEl) { hudEl.classList.remove('is-on', 'is-listening'); }
			};
			rec.start();
			listening = true;
			hud('Dinliyorum… Komutlar için "yardım" deyin.', true);
			if (hudEl) { hudEl.classList.add('is-listening'); }
		} catch (err) {
			listening = false;
		}
	}

	function setVoice(state) {
		wantVoice = !!state;
		if (wantVoice) {
			say('Sesli komut açıldı. Komutları duymak için yardım deyin.', false, true);
			if (activated) { startRec(); }
		} else {
			if (rec) { try { rec.stop(); } catch (e) {} }
			listening = false;
			if (hudEl) { hudEl.classList.remove('is-listening'); }
		}
	}

	/* ------------------------------------------------- Başlangıç */
	var p0 = api.get();
	syncTools(p0);
	if (p0.tts) {
		on = true;
		var h1 = $('#main h1');
		say('Sayfa: ' + (h1 ? clean(h1.innerText) : document.title) + '. Sayfayı dinlemek için Alt Shift R, kısayollar için Alt Shift K.', false);
	}
	if (p0.voice) { wantVoice = true; }
})();
