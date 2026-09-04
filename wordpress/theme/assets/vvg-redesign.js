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

	/* ---- Shorter CTA wording on a phone ----
	   The long label is a 350px button, which is most of a phone screen. Swap it
	   at runtime rather than in the markup, so nothing but this file has to
	   change. Add entries to swap other wording; keys are matched lowercased.

	   Note this runs after first paint, so on a phone the long label is briefly
	   visible before it shortens. */
	var CTA_SHORT = {
		'book your consultation here': 'Book Now'
	};
	var CTA_BREAKPOINT = 640;

	var swapCtaText = function () {
		var narrow = window.innerWidth <= CTA_BREAKPOINT;
		var links  = document.querySelectorAll('.vvg-btn, .vvss-btn');

		for (var i = 0; i < links.length; i++) {
			var link = links[i];

			/* Text-only buttons only: replacing textContent would destroy an icon. */
			if (link.children.length) { continue; }

			var full = link.getAttribute('data-vvg-cta-full');

			if (full === null) {
				var text  = link.textContent.trim();
				var short = CTA_SHORT[text.toLowerCase()];
				if (!short) { continue; }
				full = text;
				link.setAttribute('data-vvg-cta-full', full);
				link.setAttribute('data-vvg-cta-short', short);
			}

			var wanted = narrow ? link.getAttribute('data-vvg-cta-short') : full;
			if (link.textContent.trim() !== wanted) { link.textContent = wanted; }
		}
	};

	swapCtaText();

	var ctaTimer = null;
	window.addEventListener('resize', function () {
		clearTimeout(ctaTimer);
		ctaTimer = setTimeout(swapCtaText, 150);
	});

	/* ---- Keep the sticky header sticky ----
	   position:sticky is cancelled by ANY ancestor that is a scroll container,
	   and a theme or page builder can put overflow:hidden on a wrapper we cannot
	   predict or reach from a stylesheet. Walk the header's own ancestors and
	   relax only that one case: hidden becomes clip, which looks identical and
	   clips just the same, but does not create a scroll container. auto and
	   scroll are left alone, because those are deliberate. */
	if (header) {
		for (var node = header.parentElement; node && node !== document.documentElement; node = node.parentElement) {
			var cs = getComputedStyle(node);
			if (cs.overflowX === 'hidden') { node.style.overflowX = 'clip'; }
			if (cs.overflowY === 'hidden') { node.style.overflowY = 'visible'; }
		}
	}

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

		var navClose = document.getElementById('vvgNavClose');
		if (navClose) {
			navClose.addEventListener('click', function () { setNav(false); });
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

	/* ---- Project lightbox ----
	   The project images are WPBakery output, so the trigger is bound to the
	   existing <img> elements rather than requiring new markup in the page. */
	var shots = document.querySelectorAll('.vvg-project img, .project_row .grid-image img');
	if (shots.length) {
		var lb = document.createElement('div');
		lb.className = 'vvg-lightbox';
		lb.setAttribute('role', 'dialog');
		lb.setAttribute('aria-modal', 'true');
		lb.setAttribute('aria-label', 'Project image viewer');
		lb.innerHTML =
			'<button type="button" class="vvg-lb-btn vvg-lb-close" aria-label="Close image viewer">' +
			'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 6.4 17.6 5 12 10.6 6.4 5 5 6.4 10.6 12 5 17.6 6.4 19l5.6-5.6 5.6 5.6 1.4-1.4-5.6-5.6z"/></svg></button>' +
			'<button type="button" class="vvg-lb-btn vvg-lb-prev" aria-label="Previous image">' +
			'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 11H7.8l5.6-5.6L12 4l-8 8 8 8 1.4-1.4L7.8 13H20z"/></svg></button>' +
			'<img alt="">' +
			'<button type="button" class="vvg-lb-btn vvg-lb-next" aria-label="Next image">' +
			'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 11h12.2l-5.6-5.6L12 4l8 8-8 8-1.4-1.4 5.6-5.6H4z"/></svg></button>' +
			'<span class="vvg-lb-count"></span>';
		document.body.appendChild(lb);

		var lbImg   = lb.querySelector('img');
		var lbCount = lb.querySelector('.vvg-lb-count');
		var index   = 0;
		var opener  = null;

		var render = function () {
			var src = shots[index];
			lbImg.src = src.currentSrc || src.src;
			lbImg.alt = src.alt || '';
			lbCount.textContent = (index + 1) + ' / ' + shots.length;
		};
		var open = function (i) {
			index = i;
			opener = shots[i];
			render();
			lb.classList.add('open');
			document.body.classList.add('vvg-nav-open');
			lb.querySelector('.vvg-lb-close').focus();
		};
		var close = function () {
			lb.classList.remove('open');
			document.body.classList.remove('vvg-nav-open');
			if (opener) { opener.focus(); }
		};
		var step = function (d) {
			index = (index + d + shots.length) % shots.length;
			opener = shots[index];
			render();
		};

		Array.prototype.forEach.call(shots, function (img, i) {
			img.setAttribute('tabindex', '0');
			img.addEventListener('click', function () { open(i); });
			img.addEventListener('keydown', function (e) {
				if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(i); }
			});
		});

		lb.querySelector('.vvg-lb-close').addEventListener('click', close);
		lb.querySelector('.vvg-lb-prev').addEventListener('click', function () { step(-1); });
		lb.querySelector('.vvg-lb-next').addEventListener('click', function () { step(1); });
		lb.addEventListener('click', function (e) { if (e.target === lb) { close(); } });
		document.addEventListener('keydown', function (e) {
			if (!lb.classList.contains('open')) { return; }
			if (e.key === 'Escape') { close(); }
			if (e.key === 'ArrowLeft') { step(-1); }
			if (e.key === 'ArrowRight') { step(1); }
		});
	}

	/* ---- Homepage hero slider ---- */
	var slides = document.querySelectorAll('.vvg-hero-slide');
	if (slides.length > 1) {
		var dots = document.querySelectorAll('.vvg-hero-dots button');
		var at = 0, timer;

		var show = function (i) {
			at = (i + slides.length) % slides.length;
			Array.prototype.forEach.call(slides, function (s, n) { s.classList.toggle('is-active', n === at); });
			Array.prototype.forEach.call(dots, function (d, n) { d.classList.toggle('is-active', n === at); });
		};
		var start = function () {
			clearInterval(timer);
			timer = setInterval(function () { show(at + 1); }, 6000);
		};

		Array.prototype.forEach.call(dots, function (dot, n) {
			dot.addEventListener('click', function () { show(n); start(); });
		});
		start();
	}
})();
