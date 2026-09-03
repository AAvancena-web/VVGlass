/**
 * Shared section behaviour.
 *
 * Only the intro's Read More toggle. Everything else in the section is static,
 * and the FAQ uses <details>, which needs no script.
 */
(function () {
	'use strict';

	document.addEventListener('click', function (e) {
		var btn = e.target.closest ? e.target.closest('.vvss-read-more') : null;
		if (!btn) {
			return;
		}

		var panel = document.getElementById(btn.getAttribute('aria-controls'));
		if (!panel) {
			return;
		}

		var open = btn.getAttribute('aria-expanded') === 'true';
		btn.setAttribute('aria-expanded', open ? 'false' : 'true');
		panel.hidden = open;

		var label = btn.querySelector('span');
		if (label) {
			label.textContent = open ? 'Read More' : 'Read Less';
		}
	});
})();
