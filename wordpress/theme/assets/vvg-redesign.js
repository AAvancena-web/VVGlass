/**
 * VV Glass redesign — header behaviour.
 *
 * Vanilla JS, no jQuery dependency. Everything is guarded so this stays silent
 * on pages where the redesigned header is not present.
 */
(function () {
	'use strict';

	var header   = document.getElementById('masthead');
	var burger   = document.getElementById('vvgBurger');
	var drawer   = document.getElementById('vvgMobileNav');
	var overlay  = document.getElementById('vvgNavOverlay');
	var progress = document.getElementById('vvgScrollProgress');

	/* ---- Sticky state + reading progress ---- */
	if (header || progress) {
		var onScroll = function () {
			var y = window.pageYOffset;
			if (header) {
				header.classList.toggle('is-stuck', y > 20);
			}
			if (progress) {
				var max = document.documentElement.scrollHeight - window.innerHeight;
				progress.style.width = (max > 0 ? Math.min(100, (y / max) * 100) : 0) + '%';
			}
		};
		window.addEventListener('scroll', onScroll, { passive: true });
		window.addEventListener('resize', onScroll);
		onScroll();
	}

	/* ---- Mobile drawer ---- */
	if (burger && drawer) {
		var setNav = function (open) {
			burger.setAttribute('aria-expanded', open ? 'true' : 'false');
			burger.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
			drawer.classList.toggle('open', open);
			if (overlay) {
				overlay.classList.toggle('show', open);
			}
			document.body.classList.toggle('vvg-nav-open', open);
		};

		burger.addEventListener('click', function () {
			setNav(burger.getAttribute('aria-expanded') !== 'true');
		});

		if (overlay) {
			overlay.addEventListener('click', function () { setNav(false); });
		}

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') { setNav(false); }
		});

		/*
		 * Submenu toggles. wp_nav_menu has no hook for adding a button inside
		 * the <li> without a custom walker, so they are injected here instead.
		 */
		var parents = drawer.querySelectorAll('li.menu-item-has-children');
		Array.prototype.forEach.call(parents, function (li) {
			var link = li.querySelector(':scope > a');
			var sub  = li.querySelector(':scope > ul');
			if (!link || !sub) { return; }

			var row = document.createElement('div');
			row.className = 'vvg-m-row';
			li.insertBefore(row, link);
			row.appendChild(link);

			var btn = document.createElement('button');
			btn.type = 'button';
			btn.className = 'vvg-m-toggle';
			btn.setAttribute('aria-expanded', 'false');
			btn.setAttribute('aria-label', 'Toggle ' + link.textContent.trim() + ' submenu');
			btn.innerHTML = '<span>+</span>';
			row.appendChild(btn);

			btn.addEventListener('click', function () {
				var open = btn.getAttribute('aria-expanded') === 'true';
				btn.setAttribute('aria-expanded', open ? 'false' : 'true');
				sub.classList.toggle('open', !open);
			});
		});
	}
})();
