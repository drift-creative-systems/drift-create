/* Drift Create: mobile menu, focus the contact form's "sent" notice. */
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
})(jQuery);
