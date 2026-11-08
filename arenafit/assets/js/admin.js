/* ArenaFit - yönetim paneli galeri seçici */
jQuery(function ($) {
	'use strict';

	$(document).on('click', '.af-gallery-add', function (e) {
		e.preventDefault();
		var wrap = $(this).closest('.af-gallery');
		var input = wrap.find('input[type=hidden]');

		var frame = wp.media({
			title: 'Galeri görselleri',
			button: { text: 'Galeriye ekle' },
			multiple: 'add',
			library: { type: 'image' }
		});

		frame.on('open', function () {
			var ids = (input.val() || '').split(',').filter(Boolean);
			var sel = frame.state().get('selection');
			ids.forEach(function (id) {
				var att = wp.media.attachment(id);
				att.fetch();
				sel.add(att);
			});
		});

		frame.on('select', function () {
			var ids = [];
			var html = '';
			frame.state().get('selection').each(function (a) {
				var j = a.toJSON();
				ids.push(j.id);
				var src = j.sizes && j.sizes.thumbnail ? j.sizes.thumbnail.url : j.url;
				html += '<img src="' + src + '" alt="">';
			});
			input.val(ids.join(','));
			wrap.find('.af-gallery-prev').html(html);
		});

		frame.open();
	});

	$(document).on('click', '.af-gallery-clear', function (e) {
		e.preventDefault();
		var wrap = $(this).closest('.af-gallery');
		wrap.find('input[type=hidden]').val('');
		wrap.find('.af-gallery-prev').empty();
	});
});
