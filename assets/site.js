/* Drift Create: mobile menu, focus the contact form's "sent" notice, click-to-play videos. */
(function ($) {
	'use strict';

	var $btn = $('.menu-toggle');
	var $menu = $('#menu');

	function bonsai_setMenu(open) {
		$btn.attr('aria-expanded', open ? 'true' : 'false');
		$menu.toggleClass('is-open', open);
	}

	if ($btn.length && $menu.length) {
		$btn.on('click.bonsai_menu', function () {
			bonsai_setMenu($btn.attr('aria-expanded') !== 'true');
		});
		// Close after following an in-page link (#apps, #contact…).
		$menu.on('click.bonsai_menu', 'a', function () {
			bonsai_setMenu(false);
		});
		$(document).on('keydown.bonsai_menu', function (e) {
			if (e.key === 'Escape') {
				bonsai_setMenu(false);
			}
		});
	}

	// After a successful send, move focus to the confirmation for screen readers.
	$('.notice--ok').trigger('focus');

	// Video module: swap the poster button for the player only when clicked,
	// so YouTube / Vimeo aren't contacted until the visitor asks for the video.
	$(document).on('click.bonsai_video', '.video__play', function () {
		var $play = $(this);
		var src = String($play.data('embed') || '');
		// Only ever load the two players the theme builds URLs for.
		if (!/^https:\/\/(www\.youtube-nocookie\.com\/embed\/|player\.vimeo\.com\/video\/)/.test(src)) {
			return;
		}
		var $frame = $('<iframe>', {
			src: src,
			title: String($play.data('title') || 'Video'),
			allow: 'autoplay; fullscreen; picture-in-picture; encrypted-media',
			allowfullscreen: true,
			referrerpolicy: 'strict-origin-when-cross-origin'
		});
		$play.replaceWith($frame);
		$frame.trigger('focus');
	});
})(jQuery);
