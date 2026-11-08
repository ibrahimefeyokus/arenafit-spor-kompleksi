/* ArenaFit – ön yüz betikleri */
(function () {
	'use strict';

	var $ = function (s, c) { return (c || document).querySelector(s); };
	var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

	/* Başlık gölgesi ------------------------------------------------------ */
	var header = $('.af-header');
	function onScroll() {
		if (header) { header.classList.toggle('is-stuck', window.scrollY > 8); }
	}
	onScroll();
	window.addEventListener('scroll', onScroll, { passive: true });

	/* Mobil menü ----------------------------------------------------------- */
	var toggle = $('.af-nav-toggle');
	var nav = $('#af-nav');
	function closeNav() {
		if (!nav || !toggle) { return; }
		nav.classList.remove('is-open');
		toggle.setAttribute('aria-expanded', 'false');
		document.body.classList.remove('nav-open');
	}
	if (toggle && nav) {
		toggle.addEventListener('click', function () {
			var open = nav.classList.toggle('is-open');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			document.body.classList.toggle('nav-open', open);
		});
		nav.addEventListener('click', function (e) {
			if (e.target.closest('a')) { closeNav(); }
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') { closeNav(); }
		});
	}

	/* Görünme animasyonu --------------------------------------------------- */
	var reveals = $$('.reveal');
	var noMotion = document.documentElement.classList.contains('a11y-motion') ||
		(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
	if (!noMotion && 'IntersectionObserver' in window) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (en.isIntersecting) {
					en.target.classList.add('is-in');
					io.unobserve(en.target);
				}
			});
		}, { threshold: 0.1 });
		reveals.forEach(function (el) { io.observe(el); });
	} else {
		reveals.forEach(function (el) { el.classList.add('is-in'); });
	}

	/* Paket filtresi ------------------------------------------------------- */
	var filterBox = $('.af-filterable');
	if (filterBox) {
		var buttons = $$('[data-af-filter]');
		var apply = function (type) {
			$$('.af-pkg', filterBox).forEach(function (card) {
				card.hidden = !(type === 'all' || card.getAttribute('data-type') === type);
			});
			buttons.forEach(function (b) {
				var on = b.getAttribute('data-af-filter') === type;
				b.classList.toggle('is-active', on);
				b.setAttribute('aria-pressed', on ? 'true' : 'false');
			});
		};
		buttons.forEach(function (b) {
			b.addEventListener('click', function () { apply(b.getAttribute('data-af-filter')); });
		});
		apply(filterBox.getAttribute('data-active') || 'all');
	}

	/* Salon galerisi ------------------------------------------------------- */
	$$('[data-af-gallery]').forEach(function (g) {
		var main = $('[data-af-main]', g);
		var thumbs = $$('.af-gallery__thumb', g);
		thumbs.forEach(function (t) {
			t.addEventListener('click', function () {
				if (!main) { return; }
				main.removeAttribute('srcset');
				main.removeAttribute('sizes');
				main.src = t.getAttribute('data-src');
				thumbs.forEach(function (x) { x.classList.remove('is-active'); x.setAttribute('aria-pressed', 'false'); });
				t.classList.add('is-active');
				t.setAttribute('aria-pressed', 'true');
			});
		});
	});

	/* Rezervasyon ve üyelik formları --------------------------------------- */
	var cfg = window.ArenaFit || {};
	var ajax = cfg.ajax || '/wp-admin/admin-ajax.php';

	function esc(s) {
		return String(s).replace(/[&<>"']/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	}

	function successPanel(box, message, summary) {
		box.innerHTML =
			'<div class="af-success" role="status">' +
			'<div class="af-success__ic"><svg class="af-i" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg></div>' +
			'<h3>Teşekkürler!</h3>' +
			'<p>' + esc(message) + '</p>' +
			(summary ? '<p class="af-success__sum">' + esc(summary) + '</p>' : '') +
			'</div>';
		box.scrollIntoView({ behavior: 'smooth', block: 'center' });
	}

	function submitForm(form, box, onFail) {
		var msg = $('.af-msg', form);
		var btn = $('button[type="submit"]', form);
		msg.textContent = '';
		$$('.is-invalid', form).forEach(function (el) { el.classList.remove('is-invalid'); el.removeAttribute('aria-invalid'); });

		/* Basit istemci doğrulaması (asıl doğrulama sunucudadır) */
		var bad = null;
		$$('[required]', form).forEach(function (el) {
			var empty = el.type === 'checkbox' ? !el.checked : !String(el.value).trim();
			if (empty) { el.classList.add('is-invalid'); el.setAttribute('aria-invalid', 'true'); bad = bad || el; }
		});
		if (bad) {
			msg.textContent = 'Lütfen işaretli zorunlu alanları doldurun.';
			bad.focus();
			return;
		}
		var start = form.elements.start;
		if (start && !start.value) {
			msg.textContent = 'Lütfen listeden bir saat seçin.';
			return;
		}

		form.classList.add('is-loading');
		if (btn) { btn.disabled = true; }

		fetch(ajax, { method: 'POST', credentials: 'same-origin', body: new FormData(form) })
			.then(function (r) { return r.json(); })
			.then(function (res) {
				if (res && res.success) {
					successPanel(box, res.data.message, res.data.summary);
				} else {
					msg.textContent = (res && res.data && res.data.message) || 'Bir sorun oluştu, lütfen tekrar deneyin.';
					if (onFail) { onFail(); }
				}
			})
			.catch(function () {
				msg.textContent = 'Bağlantı hatası. Lütfen internetinizi kontrol edip tekrar deneyin.';
			})
			.then(function () {
				form.classList.remove('is-loading');
				if (btn) { btn.disabled = false; }
			});
	}

	/* Üyelik */
	$$('.af-member, .af-ajax').forEach(function (box) {
		var form = $('form', box);
		if (!form) { return; }
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			submitForm(form, box);
		});
	});

	/* Rezervasyon */
	$$('.af-booking:not(.af-member)').forEach(function (box) {
		var form = $('form', box);
		if (!form || !form.elements.target) { return; }

		var target = form.elements.target;
		var date = form.elements.date;
		var dur = form.elements.duration;
		var startInput = form.elements.start;
		var slotsEl = $('.af-slots', box);
		var durRow = $('.af-duration', box);
		var refRow = $('.af-referee', box);
		var refBox = form.elements.referee;
		var summary = $('.af-summary', box);
		var chosenText = '';

		function selected() {
			if (target.tagName === 'SELECT') { return target.options[target.selectedIndex]; }
			return target;
		}
		function parse() {
			var v = (target.value || '').split(':');
			return { kind: v[0] || '', item: v[1] || '' };
		}
		function fmtMoney(n) {
			return Math.round(n).toLocaleString('tr-TR') + ' ₺';
		}
		function addMinutes(t, m) {
			var p = t.split(':');
			var total = parseInt(p[0], 10) * 60 + parseInt(p[1], 10) + m;
			return ('0' + Math.floor(total / 60)).slice(-2) + ':' + ('0' + (total % 60)).slice(-2);
		}

		function updateSummary() {
			var t = parse();
			var o = selected();
			if (!t.item || !date.value || !startInput.value) {
				summary.hidden = true;
				return;
			}
			var minutes = t.kind === 'hall' && dur ? parseInt(dur.value, 10) : 0;
			var d = new Date(date.value + 'T12:00:00');
			var label = d.toLocaleDateString('tr-TR', { day: 'numeric', month: 'long', year: 'numeric', weekday: 'long' });
			var name = o && o.textContent ? o.textContent.trim() : '';
			if (!name || target.tagName !== 'SELECT') {
				var chip = $('.af-chip-fixed', box);
				name = chip ? chip.textContent.trim() : '';
			}
			var html = '<strong>' + esc(name) + '</strong><br>' + esc(label) + ' · ' + esc(startInput.value) +
				(minutes ? ' – ' + esc(addMinutes(startInput.value, minutes)) : '');
			if (t.kind === 'hall' && o && o.dataset.price) {
				var price = parseFloat(o.dataset.price) || 0;
				var total = price * (minutes / 60);
				var refPrice = parseFloat(o.dataset.refereePrice) || 0;
				if (refBox && refBox.checked) { total += refPrice; }
				if (total > 0) { html += '<br>Tahmini ücret: <strong>' + fmtMoney(total) + '</strong>'; }
			}
			summary.innerHTML = html;
			summary.hidden = false;
		}

		function syncTarget() {
			var t = parse();
			var o = selected();
			var isHall = t.kind === 'hall';
			if (durRow) { durRow.hidden = !isHall; }
			var hasRef = isHall && o && o.dataset.referee === '1';
			if (refRow) {
				refRow.hidden = !hasRef;
				if (!hasRef && refBox) { refBox.checked = false; }
				var rp = $('.af-referee__price', refRow);
				if (rp) {
					var p = o ? parseFloat(o.dataset.refereePrice) || 0 : 0;
					rp.textContent = hasRef && p > 0 ? '(+' + fmtMoney(p) + ')' : '';
				}
			}
			loadSlots();
		}

		function loadSlots() {
			var t = parse();
			startInput.value = '';
			updateSummary();
			if (!t.item || !date.value) {
				slotsEl.innerHTML = '<p class="af-hint">Önce hizmet ve tarih seçin.</p>';
				return;
			}
			slotsEl.innerHTML = '<p class="af-hint">Uygun saatler yükleniyor…</p>';
			var url = ajax + '?action=arenafit_slots&kind=' + encodeURIComponent(t.kind) +
				'&item=' + encodeURIComponent(t.item) + '&date=' + encodeURIComponent(date.value) +
				'&duration=' + encodeURIComponent(t.kind === 'hall' && dur ? dur.value : 60);

			fetch(url, { credentials: 'same-origin' })
				.then(function (r) { return r.json(); })
				.then(function (res) {
					if (!res || !res.success) {
						slotsEl.innerHTML = '<p class="af-hint">' + esc((res && res.data && res.data.message) || 'Saatler alınamadı.') + '</p>';
						return;
					}
					var slots = res.data.slots || [];
					if (!slots.length) {
						slotsEl.innerHTML = '<p class="af-hint">Bu tarihte hizmet verilmiyor. Lütfen başka bir gün seçin.</p>';
						return;
					}
					var any = slots.some(function (s) { return s.ok; });
					slotsEl.innerHTML = '';
					slots.forEach(function (s) {
						var b = document.createElement('button');
						b.type = 'button';
						b.className = 'af-slot';
						b.textContent = s.t;
						b.setAttribute('aria-pressed', 'false');
						if (!s.ok) {
							b.disabled = true;
							b.title = 'Dolu';
							b.setAttribute('aria-label', s.t + ' dolu');
						} else {
							b.setAttribute('aria-label', s.t + ' boş, seçmek için basın');
						}
						b.addEventListener('click', function () {
							$$('.af-slot', slotsEl).forEach(function (x) { x.setAttribute('aria-pressed', 'false'); });
							b.setAttribute('aria-pressed', 'true');
							startInput.value = s.t;
							chosenText = s.t;
							updateSummary();
						});
						slotsEl.appendChild(b);
					});
					if (!any) {
						var p = document.createElement('p');
						p.className = 'af-hint';
						p.textContent = 'Bu tarihte tüm saatler dolu. Lütfen başka bir gün deneyin.';
						slotsEl.appendChild(p);
					}
				})
				.catch(function () {
					slotsEl.innerHTML = '<p class="af-hint">Saatler yüklenemedi. Lütfen tekrar deneyin.</p>';
				});
		}

		target.addEventListener('change', syncTarget);
		date.addEventListener('change', loadSlots);
		if (dur) { dur.addEventListener('change', loadSlots); }
		if (refBox) { refBox.addEventListener('change', updateSummary); }

		form.addEventListener('submit', function (e) {
			e.preventDefault();
			submitForm(form, box, loadSlots);
		});

		syncTarget();
	});
})();

/* ArenaFit 1.2 – erişilebilirlik paneli, online takvim, antrenör arama, belge büyütme */
(function () {
	'use strict';

	var $ = function (s, c) { return (c || document).querySelector(s); };
	var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
	var root = document.documentElement;

	/* Erişilebilirlik paneli ---------------------------------------------- */
	var KEY = 'afA11y';
	var FS = [100, 112, 125, 140];
	var prefs = {};
	try { prefs = JSON.parse(localStorage.getItem(KEY) || '{}') || {}; } catch (e) { prefs = {}; }

	function save() {
		try { localStorage.setItem(KEY, JSON.stringify(prefs)); } catch (e) {}
	}
	function apply() {
		[1, 2, 3].forEach(function (n) { root.classList.toggle('a11y-fs-' + n, prefs.fs === n); });
		['contrast', 'links', 'motion', 'readable', 'cursor'].forEach(function (k) {
			root.classList.toggle('a11y-' + k, !!prefs[k]);
		});
		$$('[data-toggle]').forEach(function (b) {
			b.setAttribute('aria-pressed', prefs[b.getAttribute('data-toggle')] ? 'true' : 'false');
		});
		var out = $('[data-fs-out]');
		if (out) { out.textContent = '%' + FS[prefs.fs || 0]; }
		if (prefs.motion) { $$('.reveal').forEach(function (el) { el.classList.add('is-in'); }); }
		try { document.dispatchEvent(new CustomEvent('af:a11y', { detail: prefs })); } catch (e) {}
	}
	window.AFA11y = {
		get: function () { return prefs; },
		set: function (k, v) { prefs[k] = v || undefined; save(); apply(); }
	};

	var wrap = $('[data-af-a11y]');
	if (wrap) {
		var btn = $('.af-a11y__btn', wrap);
		var panel = $('.af-a11y__panel', wrap);
		var open = function (state) {
			panel.hidden = !state;
			btn.setAttribute('aria-expanded', state ? 'true' : 'false');
			if (state) {
				var first = $('button, a', panel);
				if (first) { first.focus(); }
			}
		};
		btn.addEventListener('click', function () { open(panel.hidden); });
		$('.af-a11y__close', panel).addEventListener('click', function () { open(false); btn.focus(); });
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && !panel.hidden) { open(false); btn.focus(); }
		});
		document.addEventListener('click', function (e) {
			if (!panel.hidden && !wrap.contains(e.target) && !e.target.closest('[data-af-a11y-open]')) { open(false); }
		});
		$$('[data-af-a11y-open]').forEach(function (b) {
			b.addEventListener('click', function () { open(true); });
		});
		$$('[data-fs]', panel).forEach(function (b) {
			b.addEventListener('click', function () {
				var n = prefs.fs || 0;
				n = b.getAttribute('data-fs') === '+' ? Math.min(3, n + 1) : Math.max(0, n - 1);
				prefs.fs = n || undefined;
				save(); apply();
			});
		});
		$$('[data-toggle]', panel).forEach(function (b) {
			b.addEventListener('click', function () {
				var k = b.getAttribute('data-toggle');
				prefs[k] = !prefs[k] || undefined;
				save(); apply();
			});
		});
		$('[data-reset]', panel).addEventListener('click', function () {
			prefs = {};
			save(); apply();
		});
		apply();
	}

	/* Online takvim şeridi → rezervasyon formuna tarih aktar --------------- */
	$$('[data-af-cal]').forEach(function (cal) {
		var scope = cal.closest('.af-detail') || document;
		var input = $('.af-booking input[type="date"]', scope);
		$$('.af-cal__day', cal).forEach(function (d) {
			d.setAttribute('aria-pressed', 'false');
			d.addEventListener('click', function () {
				$$('.af-cal__day', cal).forEach(function (x) { x.classList.remove('is-picked'); x.setAttribute('aria-pressed', 'false'); });
				d.classList.add('is-picked');
				d.setAttribute('aria-pressed', 'true');
				if (input) {
					input.value = d.getAttribute('data-date');
					input.dispatchEvent(new Event('change', { bubbles: true }));
					var slots = $('.af-slots', scope);
					if (slots && window.matchMedia('(max-width: 980px)').matches) {
						slots.scrollIntoView({ behavior: 'smooth', block: 'center' });
					}
				}
			});
		});
		if (input) {
			input.addEventListener('change', function () {
				$$('.af-cal__day', cal).forEach(function (x) {
					var on = x.getAttribute('data-date') === input.value;
					x.classList.toggle('is-picked', on);
					x.setAttribute('aria-pressed', on ? 'true' : 'false');
				});
			});
		}
	});

	/* Antrenör arama ------------------------------------------------------- */
	$$('[data-af-search]').forEach(function (inp) {
		var cards = $$(inp.getAttribute('data-af-search'));
		var count = $('[data-af-search-count]');
		var empty = $('[data-af-search-empty]');
		var norm = function (s) { return String(s || '').toLocaleLowerCase('tr-TR').trim(); };
		inp.addEventListener('input', function () {
			var q = norm(inp.value);
			var shown = 0;
			cards.forEach(function (c) {
				var ok = !q || norm(c.getAttribute('data-search')).indexOf(q) !== -1;
				c.hidden = !ok;
				if (ok) { shown++; c.classList.add('is-in'); }
			});
			if (count) { count.textContent = q ? shown + ' antrenör' : ''; }
			if (empty) { empty.hidden = shown !== 0; }
		});
	});

	/* Belge görselleri: basit, klavye uyumlu büyütme ----------------------- */
	$$('[data-af-lightbox]').forEach(function (list) {
		var links = $$('a', list);
		if (!links.length) { return; }
		var dlg = document.createElement('dialog');
		dlg.className = 'af-lightbox';
		dlg.setAttribute('aria-label', 'Belge görseli');
		dlg.innerHTML = '<button type="button" class="af-lightbox__close" aria-label="Kapat">&times;</button><img alt=""><p></p>';
		document.body.appendChild(dlg);
		var img = $('img', dlg), cap = $('p', dlg);
		$('.af-lightbox__close', dlg).addEventListener('click', function () { dlg.close(); });
		dlg.addEventListener('click', function (e) { if (e.target === dlg) { dlg.close(); } });
		links.forEach(function (a) {
			a.addEventListener('click', function (e) {
				if (typeof dlg.showModal !== 'function') { return; }
				e.preventDefault();
				img.src = a.href;
				img.alt = a.getAttribute('data-caption') || '';
				cap.textContent = a.getAttribute('data-caption') || '';
				dlg.showModal();
			});
		});
	});
})();
