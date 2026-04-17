/**
 * Outlier Coaching — flat editorial landing (GSAP + ScrollTrigger + ScrollSmoother + SplitText).
 * SplitText falls back to SplitType when needed. Landing boot: window load → Smoother → hero → scroll STs.
 */
(function () {
	'use strict';

	var doc = document;
	var body = doc.body;
	var reduceMotion = false;

	try {
		reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	} catch (e) {
		reduceMotion = false;
	}

	if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
		if (body.classList.contains('outlier-landing')) {
			body.style.opacity = '1';
			body.style.visibility = 'visible';
			body.classList.remove('oc-landing-boot');
		}
		return;
	}

	if (typeof ScrollSmoother !== 'undefined' && typeof SplitText !== 'undefined') {
		gsap.registerPlugin(ScrollTrigger, ScrollSmoother, SplitText);
	} else {
		gsap.registerPlugin(ScrollTrigger);
		if (typeof ScrollSmoother !== 'undefined') {
			gsap.registerPlugin(ScrollSmoother);
		}
		if (typeof SplitText !== 'undefined') {
			gsap.registerPlugin(SplitText);
		}
	}

	/* Set by initHeroAccentPast when ScrollSmoother eases content after native scroll stops. */
	var ocHeroPastSync = null;

	function ready(fn) {
		if (doc.readyState === 'loading') {
			doc.addEventListener('DOMContentLoaded', fn);
		} else {
			fn();
		}
	}

	function initSmoothScroll() {
		if (typeof ScrollTrigger !== 'undefined') {
			ScrollTrigger.config({
				limitCallbacks: true,
				ignoreMobileResize: true,
			});
		}
		if (reduceMotion) {
			return null;
		}
		var wrap = doc.querySelector('#smooth-wrapper');
		var content = doc.querySelector('#smooth-content');

		if (typeof ScrollSmoother !== 'undefined' && wrap && content) {
			/*
			 * Pins must use transform inside #smooth-content; default pinType "fixed" breaks
			 * against the smoothed wrapper and scrubs sit at the wrong progress.
			 */
			ScrollTrigger.defaults({ pinType: 'transform' });
			var smoother = ScrollSmoother.create({
				wrapper: wrap,
				content: content,
				smooth: 1.8,
				smoothTouch: 0.1,
				effects: true,
				normalizeScroll: true,
				ignoreMobileResize: true,
				onUpdate: function () {
					if (ocHeroPastSync) {
						ocHeroPastSync();
					}
				},
			});
			doc.documentElement.classList.add('oc-scroll-smoother');
			return smoother;
		}

		if (typeof Lenis === 'undefined') {
			return null;
		}

		/*
		 * Static preview (body.preview-standalone): skip Lenis — it desyncs ScrollTrigger unless
		 * ScrollSmoother or a scrollerProxy is wired. Native scroll keeps reveals + contact copy visible.
		 * WordPress landing uses Lenis only when ScrollSmoother isn’t available (see functions.php).
		 */
		if (body.classList.contains('preview-standalone')) {
			return null;
		}

		var lenis = new Lenis({
			duration: 1.4,
			smoothWheel: true,
			touchMultiplier: 1.2,
		});

		lenis.on('scroll', function () {
			ScrollTrigger.update();
			if (ocHeroPastSync) {
				ocHeroPastSync();
			}
		});
		gsap.ticker.add(function (time) {
			lenis.raf(time * 1000);
		});
		gsap.ticker.lagSmoothing(0);
		return lenis;
	}

	function splitLines(el) {
		if (!el || el.getAttribute('data-oc-split') === '1') {
			return null;
		}
		if (typeof SplitText !== 'undefined') {
			el.setAttribute('data-oc-split', '1');
			return new SplitText(el, { type: 'lines', linesClass: 'split-line' });
		}
		if (typeof SplitType !== 'undefined') {
			el.setAttribute('data-oc-split', '1');
			return new SplitType(el, { types: 'lines', lineClass: 'split-line' });
		}
		return null;
	}

	function splitWords(el) {
		if (!el || el.getAttribute('data-oc-split') === '1') {
			return null;
		}
		if (typeof SplitText !== 'undefined') {
			el.setAttribute('data-oc-split', '1');
			return new SplitText(el, { type: 'words', wordsClass: 'oc-split-word' });
		}
		if (typeof SplitType !== 'undefined') {
			el.setAttribute('data-oc-split', '1');
			return new SplitType(el, { types: 'words' });
		}
		return null;
	}

	/**
	 * Reduced motion: show body + hero immediately (no scroll boot sequence).
	 */
	function initLandingIntro() {
		if (!reduceMotion) {
			return;
		}
		body.classList.remove('oc-landing-boot');
		gsap.set(body, { visibility: 'visible', opacity: 1, clearProps: 'visibility,opacity' });
		var heroRm = doc.querySelector('.chapter--hero');
		var logoRm = heroRm && heroRm.querySelector('.hero__logo');
		var linesRm = gsap.utils.toArray('.hero__headline .hero-line');
		if (logoRm) {
			gsap.set(logoRm, { opacity: 1, y: 0, clearProps: 'all' });
		}
		gsap.set(linesRm, { opacity: 1, y: 0, clearProps: 'all' });
		gsap.set('.hero__sub', { opacity: 1, y: 0 });
		gsap.set('.hero__cta', { opacity: 1, scale: 1 });
	}

	/**
	 * Full-load hero entrance after ScrollSmoother + hero background image are ready.
	 */
	function revealLandingBodyThen(fn) {
		body.classList.remove('oc-landing-boot');
		gsap.set(body, { visibility: 'visible', opacity: 1 });
		if (typeof fn === 'function') {
			fn();
		}
	}

	function runHeroEntranceAnimation(onComplete) {
		var hero = doc.querySelector('.chapter--hero');
		if (!hero) {
			revealLandingBodyThen(null);
			if (onComplete) {
				onComplete();
			}
			return;
		}

		var logo = hero.querySelector('.hero__logo');
		var lines = gsap.utils.toArray('.hero__headline .hero-line');
		var sub = hero.querySelector('.hero__sub');
		var cta = hero.querySelector('.hero__cta');

		if (sub) {
			gsap.set(sub, { opacity: 0, y: 14 });
		}
		if (cta) {
			gsap.set(cta, { opacity: 0, scale: 0.94 });
		}

		if (!lines.length) {
			revealLandingBodyThen(null);
			if (logo) {
				gsap.set(logo, { opacity: 1, y: 0 });
			}
			if (sub) {
				gsap.set(sub, { opacity: 1, y: 0 });
			}
			if (cta) {
				gsap.set(cta, { opacity: 1, scale: 1 });
			}
			if (onComplete) {
				onComplete();
			}
			return;
		}

		/* Same motion recipe as the logo: opacity + short Y travel, power2.out, 0.55s. */
		var heroInDur = 0.55;
		var heroInEase = 'power2.out';
		var heroTravel = 14;

		if (logo) {
			gsap.set(logo, { opacity: 0, y: -heroTravel });
		}
		/* Lines mirror the logo (ease up from below by the same distance). */
		gsap.set(lines, { opacity: 0, y: heroTravel, force3D: true });

		var tl = gsap.timeline({
			onComplete: onComplete,
		});

		/*
		 * Logo → each headline line (same tween as logo, staggered by the same gap as logo→line1).
		 * Sub/CTA after the last line finishes.
		 */
		var lineStart = 0;
		if (logo) {
			tl.to(logo, { opacity: 1, y: 0, duration: heroInDur, ease: heroInEase }, 0);
			lineStart = 0.36;
		}
		var lineStagger = 0.36;
		tl.to(
			lines,
			{
				opacity: 1,
				y: 0,
				duration: heroInDur,
				stagger: lineStagger,
				ease: heroInEase,
				force3D: true,
			},
			lineStart
		);
		var headlineEnd = lineStart + heroInDur + (lines.length - 1) * lineStagger;
		if (sub) {
			tl.to(sub, { opacity: 1, y: 0, duration: 0.68, ease: 'power2.out' }, headlineEnd + 0.12);
		}
		if (cta) {
			tl.to(cta, { opacity: 1, scale: 1, duration: 0.55, ease: 'power2.out' }, headlineEnd + 0.38);
		}

		tl.pause(0);

		function playTl() {
			requestAnimationFrame(function () {
				revealLandingBodyThen(function () {
					requestAnimationFrame(function () {
						tl.play(0);
					});
				});
			});
		}

		var img = doc.querySelector('.hero__bg-img');
		if (img && img.getAttribute('src')) {
			var started = false;
			function kick() {
				if (started) {
					return;
				}
				started = true;
				playTl();
			}
			if (img.complete && img.naturalWidth > 0) {
				if (typeof img.decode === 'function') {
					img.decode().then(kick).catch(kick);
				} else {
					kick();
				}
			} else {
				var pre = new Image();
				pre.onload = kick;
				pre.onerror = kick;
				try {
					pre.src = img.currentSrc || img.src;
				} catch (err) {
					kick();
				}
			}
		} else {
			playTl();
		}
	}

	function initLandingScrollAnimations() {
		initHeroAccentPast();
		initTalksAccentOnScroll();

		initProblemChapter();
		initPathChapter();
		initPathJourney();
		initOfferingsLabel();
		initOfferingsHorizontalScroll();
		initOfferingsArrowNav();
		/*
		 * Offerings card STs first; Philosophy uses the same pin + scrub + SplitText/SplitType line
		 * reveal as Recognition — run it in the same rAF tail + refresh so triggers below offerings
		 * measure like the green section (avoids scrub never firing / stuck opacity 0).
		 */
		function scheduleInitOfferingsAndPhilosophy() {
			requestAnimationFrame(function () {
				requestAnimationFrame(function () {
					requestAnimationFrame(function () {
						initOfferings();
						initPhilosophyChapter();
						if (typeof ScrollTrigger !== 'undefined') {
							ScrollTrigger.refresh();
						}
					});
				});
			});
		}
		scheduleInitOfferingsAndPhilosophy();

		initTestimonials();
		initUpcomingTalks();
		initTalks();
		initContact();
		initFooter();
		initCursor();
	}

	function runLandingAfterLoad() {
		initSmoothScroll();
		ScrollTrigger.refresh();

		runHeroEntranceAnimation(function () {
			requestAnimationFrame(function () {
				initLandingScrollAnimations();
				ScrollTrigger.refresh();
			});
		});
	}

	function initHeroAccentPast() {
		var hero = doc.querySelector('.chapter--hero');
		if (!hero || !doc.querySelector('.hero-accent')) {
			return;
		}
		var problem = doc.querySelector('.chapter--problem');
		var root = doc.documentElement;
		var rafScroll = null;
		var lastPast = null;
		function syncHeroPast() {
			var past;
			if (problem) {
				var pr = problem.getBoundingClientRect();
				var vh = window.innerHeight || 0;
				/*
				 * Orange “different.” when the green Recognition band is clearly in view while
				 * hero copy can still show — not only after the hero has fully left (bottom <= 0).
				 */
				past = pr.top < vh * 0.66;
			} else {
				var rect = hero.getBoundingClientRect();
				past = rect.bottom <= 0;
			}
			if (lastPast === past) {
				return;
			}
			lastPast = past;
			root.classList.toggle('oc-hero-past', past);
		}
		function onScrollRaf() {
			if (rafScroll != null) {
				return;
			}
			rafScroll = requestAnimationFrame(function () {
				rafScroll = null;
				syncHeroPast();
			});
		}
		syncHeroPast();
		/*
		 * ScrollSmoother keeps native scroll on the window; #smooth-wrapper is fixed and never
		 * scrolls. A body ScrollTrigger onUpdate can stay inactive or mis-scoped — sync from
		 * window scroll (and ST refresh) instead.
		 */
		window.addEventListener('scroll', onScrollRaf, { passive: true });
		if (typeof ScrollTrigger !== 'undefined') {
			ScrollTrigger.addEventListener('refresh', syncHeroPast);
		}
		window.addEventListener('load', syncHeroPast, { once: true });
		ocHeroPastSync = syncHeroPast;
	}

	/** Orange “We do talks.” when the army band is in view — same threshold idea as hero / Recognition. */
	function initTalksAccentOnScroll() {
		var talks = doc.querySelector('.chapter--talks');
		if (!talks || !doc.querySelector('.talks-accent')) {
			return;
		}
		var root = doc.documentElement;
		var rafScroll = null;
		var lastOn = null;
		function syncTalksAccent() {
			var tr = talks.getBoundingClientRect();
			var vh = window.innerHeight || 0;
			var on = tr.top < vh * 0.66;
			if (lastOn === on) {
				return;
			}
			lastOn = on;
			root.classList.toggle('oc-talks-accent-on', on);
		}
		function onScrollRaf() {
			if (rafScroll != null) {
				return;
			}
			rafScroll = requestAnimationFrame(function () {
				rafScroll = null;
				syncTalksAccent();
			});
		}
		syncTalksAccent();
		window.addEventListener('scroll', onScrollRaf, { passive: true });
		if (typeof ScrollTrigger !== 'undefined') {
			ScrollTrigger.addEventListener('refresh', syncTalksAccent);
		}
		window.addEventListener('load', syncTalksAccent, { once: true });
	}

	/**
	 * Orange dot + scrubbed reveals for Recognition + Philosophy.
	 * `splitLines`: SplitText/SplitType per wrapped line. Philosophy uses false — one `<p>` per block (fixed 5-step copy order).
	 */
	function initPinnedAccentParagraphs(section, opts) {
		opts = opts || {};
		var pinEndPx = opts.pinEndPx != null ? opts.pinEndPx : 96;
		var scrubEndPx = opts.scrubEndPx != null ? opts.scrubEndPx : 400;
		var lineStartBase = opts.lineStartBase != null ? opts.lineStartBase : 0.06;
		var lineGap = opts.lineGap != null ? opts.lineGap : 0.19;
		var lineDur = opts.lineDur != null ? opts.lineDur : 0.16;
		var accentAt = opts.accentAt != null ? opts.accentAt : 0.88;
		var splitLinesMode = opts.splitLines === true;

		if (!section) {
			return;
		}
		var dot = section.querySelector('.problem__accent-dot');
		var paras = gsap.utils.toArray(section.querySelectorAll('.problem__text p'));
		var accent = section.querySelector('.problem__accent');

		if (!paras.length) {
			return;
		}

		if (reduceMotion) {
			gsap.set([dot].concat(paras), { clearProps: 'all' });
			if (accent) {
				gsap.set(accent, { clearProps: 'all' });
			}
			return;
		}

		var revealTargets = [];
		if (splitLinesMode) {
			paras.forEach(function (p) {
				var sl = splitLines(p);
				if (sl && sl.lines && sl.lines.length) {
					sl.lines.forEach(function (ln) {
						revealTargets.push(ln);
					});
				} else {
					revealTargets.push(p);
				}
			});
		} else {
			revealTargets = paras.slice();
		}

		if (dot) {
			gsap.set(dot, { scale: 0, transformOrigin: 'center center' });
		}
		gsap.set(revealTargets, { opacity: 0, y: 22 });
		if (!splitLinesMode && accent) {
			gsap.set(accent, { opacity: 0 });
		}

		ScrollTrigger.create({
			trigger: section,
			start: 'top top',
			end: '+=' + String(pinEndPx),
			pin: true,
			pinSpacing: true,
			invalidateOnRefresh: true,
		});

		var tl = gsap.timeline({
			scrollTrigger: {
				trigger: section,
				start: 'top 55%',
				end: '+=' + String(scrubEndPx),
				scrub: 0.45,
				invalidateOnRefresh: true,
			},
		});

		if (dot) {
			tl.to(dot, { scale: 1, duration: 0.1, ease: 'power2.out' }, 0);
		}
		revealTargets.forEach(function (el, i) {
			var t = lineStartBase + i * lineGap;
			tl.to(el, { opacity: 1, y: 0, duration: lineDur, ease: 'power2.out' }, t);
		});
		if (!splitLinesMode && accent) {
			tl.to(accent, { opacity: 1, duration: 0.15, ease: 'power2.out' }, accentAt);
		}
	}

	/** Recognition (army): wrapped lines via SplitText/SplitType. */
	var OC_PINNED_ACCENT_LINE_OPTS = {
		splitLines: true,
		pinEndPx: 96,
		scrubEndPx: 420,
		lineStartBase: 0.05,
		lineGap: 0.175,
		lineDur: 0.15,
	};

	/** Philosophy (brown): five `<p>` blocks in order — no line-splitting inside paragraphs. */
	var OC_PHILOSOPHY_PINNED_OPTS = {
		splitLines: false,
		pinEndPx: 96,
		scrubEndPx: 400,
		lineStartBase: 0.05,
		lineGap: 0.2,
		lineDur: 0.16,
	};

	function initProblemChapter() {
		initPinnedAccentParagraphs(doc.querySelector('.chapter--problem'), OC_PINNED_ACCENT_LINE_OPTS);
	}

	function initPhilosophyChapter() {
		var ph = doc.getElementById('philosophy');
		if (!ph) {
			return;
		}
		initPinnedAccentParagraphs(ph, OC_PHILOSOPHY_PINNED_OPTS);
	}

	function initPathChapter() {
		var ch = doc.querySelector('.chapter--path');
		if (!ch || reduceMotion) {
			return;
		}

		var label = ch.querySelector('.chapter__label');
		if (label) {
			var sl = splitLines(label);
			if (sl && sl.lines) {
				gsap.from(sl.lines, {
					y: 60,
					opacity: 0,
					duration: 1,
					stagger: 0.12,
					ease: 'power3.out',
					scrollTrigger: { trigger: label, start: 'top 80%' },
				});
			}
		}

		var intro = ch.querySelector('[data-oc-path-intro]');
		if (intro) {
			var sw = splitWords(intro);
			var w = sw && (sw.words || sw.elements);
			if (w && w.length) {
				gsap.from(w, {
					y: 20,
					opacity: 0,
					duration: 0.55,
					stagger: 0.05,
					ease: 'power3.out',
					scrollTrigger: { trigger: intro, start: 'top 82%' },
				});
			}
		}
	}

	/** Scroll-scrubbed SVG trail, walker, step opacity, markers — desktop (min-width 1025px) only; CSS hides SVG below. */
	function initPathJourney() {
		var scene = doc.querySelector('[data-oc-path-scroll-scene]');
		if (!scene) {
			return;
		}

		var fg = doc.querySelector('.path-journey__trail-fg');
		var markersWrap = doc.querySelector('.path-journey__markers');
		var walker = doc.querySelector('.path-journey__walker');
		var steps = doc.querySelectorAll('[data-oc-path-step]');

		if (!fg || !markersWrap || !walker || steps.length !== 4) {
			return;
		}

		var STOP_FR = [0.25, 0.5, 0.75, 1];
		/* Midpoints between stops — which column is “current” while the walker travels each leg. */
		var PATH_STEP_ACTIVE_EDGES = [0.125, 0.375, 0.625, 0.875];
		var pathLen = 0;
		var markerEls = [];
		var lastP = -1;
		var stPath = null;
		var onResizePath = null;

		function buildMarkers() {
			markersWrap.innerHTML = '';
			markerEls = [];
			STOP_FR.forEach(function () {
				var g = doc.createElementNS('http://www.w3.org/2000/svg', 'g');
				g.setAttribute('class', 'path-journey__marker');
				var c = doc.createElementNS('http://www.w3.org/2000/svg', 'circle');
				c.setAttribute('r', '5');
				c.setAttribute('cx', '0');
				c.setAttribute('cy', '0');
				g.appendChild(c);
				markersWrap.appendChild(g);
				markerEls.push(g);
			});
		}

		function layoutMarkers() {
			pathLen = fg.getTotalLength();
			if (!pathLen) {
				return;
			}
			fg.style.strokeDasharray = String(pathLen);
			STOP_FR.forEach(function (fr, i) {
				var pt = fg.getPointAtLength(pathLen * fr);
				if (markerEls[i]) {
					markerEls[i].setAttribute('transform', 'translate(' + pt.x + ',' + pt.y + ')');
				}
			});
		}

		function pathActiveStepIndex(progress) {
			var p = Math.min(1, Math.max(0, progress));
			for (var i = 0; i < PATH_STEP_ACTIVE_EDGES.length; i++) {
				if (p < PATH_STEP_ACTIVE_EDGES[i]) {
					return i;
				}
			}
			return STOP_FR.length - 1;
		}

		function syncPathStepStates(progress) {
			var active = pathActiveStepIndex(progress);
			steps.forEach(function (step, i) {
				step.classList.remove('is-path-revealed', 'is-path-active', 'is-path-passed', 'is-path-ahead');
				if (i < active) {
					step.classList.add('is-path-passed', 'is-path-revealed');
				} else if (i === active) {
					step.classList.add('is-path-active', 'is-path-revealed');
				} else {
					step.classList.add('is-path-ahead');
				}
			});
		}

		function applyProgress(p) {
			if (!pathLen) {
				return;
			}
			fg.style.strokeDashoffset = String(pathLen * (1 - p));

			var pt = fg.getPointAtLength(pathLen * p);
			var delta = Math.min(6, pathLen * 0.006);
			var pt2 = fg.getPointAtLength(Math.min(pathLen * p + delta, pathLen));
			var ang = (Math.atan2(pt2.y - pt.y, pt2.x - pt.x) * 180) / Math.PI;
			walker.setAttribute(
				'transform',
				'translate(' + pt.x + ',' + pt.y + ') rotate(' + ang + ') translate(0,-14)'
			);

			syncPathStepStates(p);

			if (lastP < 0) {
				for (var j = 0; j < STOP_FR.length; j++) {
					if (p >= STOP_FR[j] && markerEls[j]) {
						markerEls[j].classList.add('path-journey__marker--done');
					}
				}
			} else {
				for (var i = 0; i < STOP_FR.length; i++) {
					if (lastP < STOP_FR[i] && p >= STOP_FR[i]) {
						var g = markerEls[i];
						if (g && !g.classList.contains('path-journey__marker--done')) {
							g.classList.add('path-journey__marker--done');
							g.classList.add('is-path-pulse');
							(function (group) {
								var dot = group.querySelector('circle');
								function onEnd() {
									group.classList.remove('is-path-pulse');
									if (dot) {
										dot.removeEventListener('animationend', onEnd);
									}
								}
								if (dot) {
									dot.addEventListener('animationend', onEnd);
								}
							})(g);
						}
					}
				}
			}
			lastP = p;
		}

		function clearPathDomState() {
			markersWrap.innerHTML = '';
			markerEls = [];
			pathLen = 0;
			lastP = -1;
			fg.style.strokeDasharray = '';
			fg.style.strokeDashoffset = '';
			walker.removeAttribute('transform');
			steps.forEach(function (s) {
				s.classList.remove('is-path-revealed', 'is-path-active', 'is-path-passed', 'is-path-ahead');
			});
		}

		function killPathScroll() {
			if (stPath) {
				stPath.kill();
				stPath = null;
			}
			if (onResizePath) {
				window.removeEventListener('resize', onResizePath);
				onResizePath = null;
			}
			clearPathDomState();
		}

		function setupDesktopReducedMotion() {
			buildMarkers();
			layoutMarkers();
			if (pathLen) {
				fg.style.strokeDasharray = String(pathLen);
				fg.style.strokeDashoffset = '0';
			}
			steps.forEach(function (s) {
				s.classList.remove('is-path-active', 'is-path-ahead');
				s.classList.add('is-path-passed', 'is-path-revealed');
			});
			if (pathLen) {
				var endPt = fg.getPointAtLength(pathLen);
				var prevPt = fg.getPointAtLength(Math.max(0, pathLen - 5));
				var a = (Math.atan2(endPt.y - prevPt.y, endPt.x - prevPt.x) * 180) / Math.PI;
				walker.setAttribute(
					'transform',
					'translate(' + endPt.x + ',' + endPt.y + ') rotate(' + a + ') translate(0,-14)'
				);
			}
			markerEls.forEach(function (g) {
				g.classList.add('path-journey__marker--done');
			});
		}

		function setupDesktopScrub() {
			buildMarkers();
			layoutMarkers();
			if (pathLen) {
				fg.style.strokeDashoffset = String(pathLen);
			}

			lastP = -1;
			stPath = ScrollTrigger.create({
				trigger: scene,
				start: 'top top',
				end: 'bottom bottom',
				scrub: true,
				onUpdate: function (self) {
					applyProgress(self.progress);
				},
			});

			onResizePath = function () {
				layoutMarkers();
				ScrollTrigger.refresh();
			};
			window.addEventListener('resize', onResizePath);

			requestAnimationFrame(function () {
				requestAnimationFrame(function () {
					layoutMarkers();
					ScrollTrigger.refresh();
					if (stPath) {
						applyProgress(stPath.progress);
					}
				});
			});
		}

		if (reduceMotion) {
			ScrollTrigger.matchMedia({
				'(min-width: 1025px)': function () {
					setupDesktopReducedMotion();
					return killPathScroll;
				},
			});
			return;
		}

		ScrollTrigger.matchMedia({
			'(min-width: 1025px)': function () {
				setupDesktopScrub();
				return killPathScroll;
			},
		});
	}

	/**
	 * Tablet/phone: prev/next control horizontal scroll (vertical pin + scrub only from 1025px up).
	 */
	function initOfferingsArrowNav() {
		var viewport = doc.querySelector('.offerings__viewport');
		var scroller = doc.getElementById('oc-offerings-scroller');
		var prev = doc.getElementById('oc-offerings-prev');
		var next = doc.getElementById('oc-offerings-next');
		if (!viewport || !scroller || !prev || !next) {
			return;
		}

		function touchRange() {
			return window.matchMedia('(max-width: 1024px)').matches;
		}

		function maxScroll() {
			return scroller.scrollWidth - scroller.clientWidth;
		}

		function stepPx() {
			/* Phone/tablet: one full-width slide per step (matches CSS gap: 0 + min-width: 100%). */
			if (touchRange()) {
				return Math.max(1, Math.round(scroller.clientWidth));
			}
			var card = scroller.querySelector('.offering-card');
			var gap = 0;
			try {
				gap = parseFloat(window.getComputedStyle(scroller).gap) || 0;
			} catch (e) {
				gap = 0;
			}
			var w = 0;
			if (card) {
				w = card.getBoundingClientRect().width;
			}
			if (w < 40) {
				w = Math.round(scroller.clientWidth * 0.82);
			}
			return Math.round(w + gap);
		}

		function syncArrowsOn() {
			var m = maxScroll();
			viewport.classList.toggle('offerings__viewport--arrows-on', touchRange() && m > 8);
		}

		function syncDisabled() {
			if (!viewport.classList.contains('offerings__viewport--arrows-on')) {
				prev.disabled = true;
				next.disabled = true;
				return;
			}
			var mx = maxScroll();
			var sl = scroller.scrollLeft;
			prev.disabled = sl <= 2;
			next.disabled = sl >= mx - 2;
		}

		function onPrev() {
			if (!touchRange()) {
				return;
			}
			scroller.scrollBy({ left: -stepPx(), behavior: 'smooth' });
		}

		function onNext() {
			if (!touchRange()) {
				return;
			}
			scroller.scrollBy({ left: stepPx(), behavior: 'smooth' });
		}

		prev.addEventListener('click', onPrev);
		next.addEventListener('click', onNext);
		scroller.addEventListener('scroll', syncDisabled, { passive: true });

		function onResizeOrMq() {
			syncArrowsOn();
			syncDisabled();
			if (typeof ScrollTrigger !== 'undefined') {
				ScrollTrigger.refresh();
			}
		}

		var mql = window.matchMedia('(max-width: 1024px)');
		if (mql.addEventListener) {
			mql.addEventListener('change', onResizeOrMq);
		} else if (mql.addListener) {
			mql.addListener(onResizeOrMq);
		}
		window.addEventListener('resize', onResizeOrMq, { passive: true });
		window.addEventListener('load', onResizeOrMq, { once: true });

		onResizeOrMq();
	}

	/**
	 * Pin “What this is” and map vertical scroll to horizontal card travel (large viewports only).
	 */
	function initOfferingsHorizontalScroll() {
		if (reduceMotion) {
			return;
		}
		var section = doc.querySelector('.chapter--offerings');
		var scroller = section && section.querySelector('.offerings__scroller');
		if (!section || !scroller) {
			return;
		}

		ScrollTrigger.matchMedia({
			'(min-width: 1025px)': function () {
				var tween;
				var proxy = { p: 0 };

				function range() {
					return Math.max(0, scroller.scrollWidth - scroller.clientWidth);
				}

				function apply() {
					var r = range();
					scroller.scrollLeft = r > 0 ? proxy.p * r : 0;
				}

				function go() {
					if (tween) {
						return;
					}
					if (range() < 48) {
						doc.documentElement.classList.remove('oc-offerings-scroll-drive');
						return;
					}
					doc.documentElement.classList.add('oc-offerings-scroll-drive');
					proxy.p = 0;
					apply();
					tween = gsap.to(proxy, {
						p: 1,
						ease: 'none',
						onUpdate: apply,
						scrollTrigger: {
							trigger: section,
							start: 'top top',
							end: function () {
								var r = range();
								return '+=' + String(Math.max(520, Math.round(r * 1.45) + 140));
							},
							pin: true,
							scrub: 0.85,
							invalidateOnRefresh: true,
							onToggle: function (self) {
								section.classList.toggle('oc-offerings-is-pinned', self.isActive);
							},
						},
					});
					/*
					 * This tween is scheduled after initLandingScrollAnimations (incl. philosophy pin).
					 * Without refresh, triggers below offerings still use pre-pin-spacer coordinates —
					 * philosophy copy stays at opacity 0 because its scrub never runs in view.
					 */
					requestAnimationFrame(function () {
						requestAnimationFrame(function () {
							if (typeof ScrollTrigger !== 'undefined') {
								ScrollTrigger.refresh();
							}
						});
					});
				}

				function scheduleGo() {
					requestAnimationFrame(function () {
						requestAnimationFrame(go);
					});
				}

				window.addEventListener('load', scheduleGo, { once: true });
				if (doc.readyState === 'complete') {
					scheduleGo();
				}

				return function () {
					window.removeEventListener('load', scheduleGo);
					if (tween) {
						if (tween.scrollTrigger) {
							tween.scrollTrigger.kill();
						}
						tween.kill();
						tween = null;
					}
					proxy.p = 0;
					scroller.scrollLeft = 0;
					section.classList.remove('oc-offerings-is-pinned');
					doc.documentElement.classList.remove('oc-offerings-scroll-drive');
				};
			},
			'(max-width: 1024px)': function () {
				doc.documentElement.classList.remove('oc-offerings-scroll-drive');
				section.classList.remove('oc-offerings-is-pinned');
			},
		});
	}

	function initOfferings() {
		if (reduceMotion) {
			return;
		}
		if (doc.documentElement.classList.contains('oc-offerings-scroll-drive')) {
			return;
		}
		var offeringsCh = doc.querySelector('.chapter--offerings');
		if (offeringsCh && offeringsCh.getAttribute('data-oc-offerings-animated') === '1') {
			return;
		}
		if (offeringsCh) {
			offeringsCh.setAttribute('data-oc-offerings-animated', '1');
		}
		doc.querySelectorAll('[data-oc-offering]').forEach(function (card, i) {
			var title = card.querySelector('.offering-card__title');
			var desc = card.querySelector('.offering-card__desc');

			gsap.from(card, {
				y: 50,
				opacity: 0,
				duration: 0.85,
				ease: 'power3.out',
				delay: i * 0.15,
				scrollTrigger: { trigger: card, start: 'top 88%' },
			});

			if (title) {
				gsap.fromTo(
					title,
					{ clipPath: 'inset(0 100% 0 0)' },
					{
						clipPath: 'inset(0 0% 0 0)',
						duration: 0.85,
						ease: 'power3.out',
						scrollTrigger: { trigger: title, start: 'top 90%' },
					}
				);
			}
			if (desc) {
				var spl = splitLines(desc);
				if (spl && spl.lines) {
					gsap.from(spl.lines, {
						y: 30,
						opacity: 0,
						duration: 0.8,
						stagger: 0.08,
						ease: 'power3.out',
						scrollTrigger: { trigger: desc, start: 'top 92%' },
					});
				}
			}
		});
	}

	function initTestimonials() {
		var ch = doc.querySelector('.chapter--testimonials');
		if (!ch) {
			return;
		}
		var label = ch.querySelector('.chapter__label');
		if (!reduceMotion && label) {
			var labSpl = splitLines(label);
			var stLabel = {
				trigger: label,
				start: 'top 86%',
				toggleActions: 'play none none none',
				once: true,
				fastScrollEnd: true,
				invalidateOnRefresh: true,
			};
			if (labSpl && labSpl.lines) {
				gsap.from(labSpl.lines, {
					y: 40,
					opacity: 0,
					duration: 0.75,
					stagger: 0.1,
					ease: 'power2.out',
					immediateRender: false,
					scrollTrigger: stLabel,
				});
			} else {
				gsap.from(label, {
					y: 36,
					opacity: 0,
					duration: 0.75,
					ease: 'power2.out',
					immediateRender: false,
					scrollTrigger: stLabel,
				});
			}
		}
		if (reduceMotion) {
			return;
		}
		var items = gsap.utils.toArray(ch.querySelectorAll('[data-oc-testimonial]'));
		if (!items.length) {
			return;
		}

		/*
		 * Per-card triggers (not one scrub timeline) — reliable with ScrollSmoother / transform
		 * scroll; each card plays when it enters the viewport so they appear one-by-one as you scroll.
		 */
		items.forEach(function (card) {
			gsap.from(card, {
				y: 44,
				opacity: 0,
				duration: 0.72,
				ease: 'power2.out',
				immediateRender: false,
				scrollTrigger: {
					trigger: card,
					start: 'top 92%',
					toggleActions: 'play none none none',
					once: true,
					fastScrollEnd: true,
					invalidateOnRefresh: true,
				},
			});
		});
	}

	function initUpcomingTalks() {
		var ch = doc.querySelector('.chapter--upcoming-talks');
		if (!ch) {
			return;
		}
		if (reduceMotion) {
			return;
		}
		var label = ch.querySelector('.chapter__label');
		var cards = gsap.utils.toArray(ch.querySelectorAll('[data-oc-upcoming-talk]'));

		var stLabel = {
			trigger: label || ch,
			start: 'top 86%',
			toggleActions: 'play none none none',
			once: true,
			fastScrollEnd: true,
			invalidateOnRefresh: true,
		};

		if (label) {
			var ls = splitLines(label);
			if (ls && ls.lines && ls.lines.length) {
				gsap.from(ls.lines, {
					y: 32,
					opacity: 0,
					duration: 0.65,
					stagger: 0.08,
					ease: 'power2.out',
					immediateRender: false,
					scrollTrigger: stLabel,
				});
			} else {
				gsap.from(label, {
					y: 32,
					opacity: 0,
					duration: 0.68,
					ease: 'power2.out',
					immediateRender: false,
					scrollTrigger: stLabel,
				});
			}
		}

		if (!cards.length) {
			return;
		}

		cards.forEach(function (card) {
			gsap.from(card, {
				y: 40,
				opacity: 0,
				duration: 0.72,
				ease: 'power2.out',
				immediateRender: false,
				scrollTrigger: {
					trigger: card,
					start: 'top 92%',
					toggleActions: 'play none none none',
					once: true,
					fastScrollEnd: true,
					invalidateOnRefresh: true,
				},
			});
		});
	}

	function initTalks() {
		var ch = doc.querySelector('.chapter--talks');
		if (!ch || reduceMotion) {
			return;
		}
		var inner = ch.querySelector('[data-oc-talks-inner]');
		var headline = ch.querySelector('[data-oc-talks-headline]');
		var cta = ch.querySelector('.talks__cta');
		if (!inner) {
			return;
		}

		gsap.set(inner, { clipPath: 'inset(0 100% 0 0)' });
		if (headline) {
			gsap.set(headline, { opacity: 0, y: 36 });
		}
		if (cta) {
			gsap.set(cta, { opacity: 0, y: 24 });
		}

		var tl = gsap.timeline({
			scrollTrigger: {
				trigger: ch,
				/* Was top 72% — fired late; start as soon as the band enters view. */
				start: 'top 92%',
				toggleActions: 'play none none reverse',
				fastScrollEnd: true,
			},
		});

		tl.to(inner, {
			clipPath: 'inset(0 0% 0 0)',
			duration: 0.5,
			ease: 'power2.out',
		});
		if (headline) {
			tl.to(headline, { opacity: 1, y: 0, duration: 0.4, ease: 'power2.out' }, '-=0.32');
		}
		if (cta) {
			tl.to(cta, { opacity: 1, y: 0, duration: 0.34, ease: 'power2.out' }, '-=0.26');
		}
	}

	function initContact() {
		if (reduceMotion) {
			return;
		}
		var ch = doc.querySelector('.chapter--contact');
		if (!ch) {
			return;
		}
		var head = ch.querySelector('[data-oc-contact-head]');
		var lede = ch.querySelector('[data-oc-contact-lede]');
		var actions = ch.querySelector('.contact__actions');
		var btns = actions ? gsap.utils.toArray(actions.children) : [];

		if (!head && !lede && !btns.length) {
			return;
		}

		/* Same motion language as hero logo / lines: short travel, power2.out, ~0.55s. */
		var inDur = 0.55;
		var inEase = 'power2.out';
		var travel = 14;

		var headLines = null;
		if (head) {
			var hs = splitLines(head);
			if (hs && hs.lines && hs.lines.length) {
				headLines = hs.lines;
			}
		}

		var lineStagger = 0.36;
		/*
		 * Timeline + from() defaults to immediateRender — children snap to opacity 0 before the
		 * trigger plays, so the whole contact block can look “empty” if ScrollTrigger is late.
		 */
		var tl = gsap.timeline({
			defaults: { immediateRender: false },
			scrollTrigger: {
				trigger: ch,
				start: 'top 88%',
				toggleActions: 'play none none none',
				once: true,
				fastScrollEnd: true,
				invalidateOnRefresh: true,
			},
		});

		var pos = 0;
		if (headLines) {
			tl.from(
				headLines,
				{
					opacity: 0,
					y: travel,
					duration: inDur,
					stagger: lineStagger,
					ease: inEase,
					force3D: true,
				},
				pos
			);
			pos += inDur + (headLines.length - 1) * lineStagger + 0.12;
		} else if (head) {
			tl.from(
				head,
				{
					opacity: 0,
					y: travel,
					duration: inDur,
					ease: inEase,
					force3D: true,
				},
				pos
			);
			pos += inDur + 0.12;
		}

		/* Lede: one block — no line-split (avoids wrap orphans on resize). */
		if (lede) {
			tl.from(
				lede,
				{
					opacity: 0,
					y: travel,
					duration: inDur,
					ease: inEase,
				},
				pos
			);
			pos += inDur - 0.08;
		}

		if (btns.length) {
			tl.from(
				btns,
				{
					opacity: 0,
					y: travel,
					duration: 0.5,
					stagger: 0.14,
					ease: inEase,
				},
				pos
			);
		}
	}

	function initFooter() {
		var footer = doc.querySelector('.site-footer');
		if (!footer || reduceMotion) {
			return;
		}

		var logoWrap = footer.querySelector('.site-footer__logo');
		var logoImg = logoWrap && logoWrap.querySelector('img.site-footer__logo-img, img');
		var copyEl = footer.querySelector('.site-footer__copy');

		var tl = gsap.timeline({
			scrollTrigger: {
				trigger: footer,
				/* Fire as soon as the footer enters the viewport (was top 88% — often never ran with Smoother / short pages). */
				start: 'top bottom',
				toggleActions: 'play none none none',
			},
		});

		/*
		 * Do not tween opacity on the logo wrapper — if the ScrollTrigger never advances, the mark
		 * stayed at opacity:0 and looked “missing”. Subtle Y-only motion on the image keeps it visible.
		 */
		if (logoImg) {
			tl.from(logoImg, { y: 14, duration: 0.6, ease: 'power3.out' }, 0);
		} else if (logoWrap) {
			tl.from(logoWrap, { y: 14, duration: 0.6, ease: 'power3.out' }, 0);
		}
		if (copyEl) {
			tl.from(copyEl, { y: 16, opacity: 0, duration: 0.55, ease: 'power3.out' }, 0.08);
		}

		var confidential = footer.querySelector('[data-oc-footer-confidential]');
		if (confidential) {
			var cs = splitLines(confidential);
			if (cs && cs.lines) {
				tl.from(
					cs.lines,
					{
						y: 24,
						opacity: 0,
						duration: 0.72,
						stagger: 0.06,
						ease: 'power3.out',
					},
					0.16
				);
			} else {
				tl.from(confidential, { y: 20, opacity: 0, duration: 0.65, ease: 'power3.out' }, 0.16);
			}
		}

		requestAnimationFrame(function () {
			requestAnimationFrame(function () {
				if (typeof ScrollTrigger !== 'undefined') {
					ScrollTrigger.refresh();
					try {
						var rect = footer.getBoundingClientRect();
						if (rect.top < window.innerHeight && rect.bottom > 0) {
							tl.progress(1, false);
						}
					} catch (e) {
						/* ignore */
					}
				}
			});
		});
	}

	function initOfferingsLabel() {
		if (reduceMotion) {
			return;
		}
		var el = doc.querySelector('.chapter--offerings .chapter__label');
		if (!el) {
			return;
		}
		var spl = splitLines(el);
		if (spl && spl.lines) {
			gsap.from(spl.lines, {
				y: 60,
				opacity: 0,
				duration: 1,
				stagger: 0.12,
				ease: 'power3.out',
				scrollTrigger: { trigger: el, start: 'top 80%' },
			});
		}
	}

	function initCursor() {
		var el = doc.getElementById('oc-cursor');
		if (!el) {
			return;
		}

		var coarse = false;
		try {
			coarse = window.matchMedia('(pointer: coarse)').matches || window.matchMedia('(hover: none)').matches;
		} catch (e) {
			coarse = false;
		}

		if (reduceMotion || coarse) {
			return;
		}

		/* Only now hide the system cursor — CSS used to hide it whenever oc-gsap-landing loaded,
		   which left no cursor if this init failed or media didn’t match. */
		body.classList.add('oc-custom-cursor-on');

		el.classList.add('is-active');

		var xTo = gsap.quickTo(el, 'x', { duration: 0.3, ease: 'power3' });
		var yTo = gsap.quickTo(el, 'y', { duration: 0.3, ease: 'power3' });
		gsap.set(el, { x: 0, y: 0 });

		window.addEventListener(
			'mousemove',
			function (e) {
				xTo(e.clientX);
				yTo(e.clientY);
			},
			{ passive: true }
		);

		var hoverables = 'a, button, .btn, input, textarea, select, [role="button"]';
		doc.querySelectorAll(hoverables).forEach(function (node) {
			node.addEventListener('mouseenter', function () {
				el.classList.add('cursor--hover');
			});
			node.addEventListener('mouseleave', function () {
				el.classList.remove('cursor--hover');
			});
		});

		var innerDot = el.querySelector('.custom-cursor__inner');
		if (innerDot) {
			doc.querySelectorAll('article.offering-card, article.testimonial-card').forEach(function (node) {
				node.addEventListener('mouseenter', function () {
					gsap.to(innerDot, {
						scale: 3.2,
						opacity: 0.5,
						duration: 0.3,
						ease: 'power2.out',
						overwrite: 'auto',
					});
				});
				node.addEventListener('mouseleave', function () {
					gsap.to(innerDot, {
						scale: 1,
						opacity: 1,
						duration: 0.3,
						ease: 'power2.out',
						overwrite: 'auto',
					});
				});
			});
		}
	}

	var landingLoadStarted = false;

	ready(function () {
		if (reduceMotion) {
			initLandingIntro();
			initSmoothScroll();
			initLandingScrollAnimations();
			ScrollTrigger.refresh();
			window.addEventListener(
				'load',
				function () {
					ScrollTrigger.refresh();
				},
				{ once: true }
			);
			return;
		}

		function startLandingLoad() {
			if (landingLoadStarted) {
				return;
			}
			landingLoadStarted = true;
			runLandingAfterLoad();
		}

		window.addEventListener('load', startLandingLoad, { once: true });
		if (doc.readyState === 'complete') {
			startLandingLoad();
		}
	});
})();
