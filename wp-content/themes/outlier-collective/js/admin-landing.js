/**
 * Admin: attachment picker for Outlier Landing image fields.
 * Uses the WordPress media modal (loaded via wp_enqueue_media).
 */
(function ($) {
	'use strict';

	$(function () {
		$('.oc-image-field').each(function () {
			var $wrap = $(this);
			var targetId = $wrap.data('target');
			var $input = $('#' + targetId);

			$wrap.find('.oc-upload').on('click', function (e) {
				e.preventDefault();
				var frame = wp.media({
					title: 'Select image',
					button: { text: 'Use this image' },
					multiple: false,
				});
				frame.on('select', function () {
					var att = frame.state().get('selection').first().toJSON();
					$input.val(att.id);
					var thumb =
						att.sizes && att.sizes.thumbnail
							? att.sizes.thumbnail.url
							: att.url;
					$wrap.find('.oc-image-preview').html(
						'<img src="' +
							thumb +
							'" alt="" style="max-width:120px;height:auto;" />'
					);
				});
				frame.open();
			});

			$wrap.find('.oc-remove').on('click', function (e) {
				e.preventDefault();
				$input.val('');
				$wrap.find('.oc-image-preview').empty();
			});
		});
	});
})(jQuery);
