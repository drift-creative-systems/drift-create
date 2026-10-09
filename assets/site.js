/* Drift Create: mobile menu, light / dark toggle, focus the contact form's "sent" notice, click-to-play videos. */
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

	// Light / dark toggle. No saved choice = follow the OS (CSS media query).
	// A saved choice sets data-theme on <html>; inc/setup.php applies it in
	// <head> before first paint, so there's no flash of the wrong theme.
	var $html = $(document.documentElement);
	var $theme = $('.theme-toggle');
	var bonsai_darkQuery = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;

	function bonsai_isDark() {
		var saved = $html.attr('data-theme');
		if (saved === 'dark' || saved === 'light') {
			return saved === 'dark';
		}
		return !!(bonsai_darkQuery && bonsai_darkQuery.matches);
	}

	function bonsai_syncThemeToggle() {
		$theme.attr('aria-pressed', bonsai_isDark() ? 'true' : 'false');
	}

	if ($theme.length) {
		$theme.prop('hidden', false);
		bonsai_syncThemeToggle();
		$theme.on('click.bonsai_theme', function () {
			var next = bonsai_isDark() ? 'light' : 'dark';
			$html.attr('data-theme', next);
			try {
				window.localStorage.setItem('drift-theme', next);
			} catch (err) {
				// Storage blocked (private mode etc.) — the choice lasts this page only.
			}
			bonsai_syncThemeToggle();
		});
		// Keep the button state right if the OS theme changes while no choice is saved.
		if (bonsai_darkQuery) {
			if (bonsai_darkQuery.addEventListener) {
				bonsai_darkQuery.addEventListener('change', bonsai_syncThemeToggle);
			} else if (bonsai_darkQuery.addListener) {
				bonsai_darkQuery.addListener(bonsai_syncThemeToggle);
			}
		}
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
