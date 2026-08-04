(function (window, document) {
	'use strict';

	var config = window.NELXSTD_PRT_CONFIG || {};
	var tourGroups = [];
	var activeTour = null;
	var launcherDismissedForSession = false;
	var editorPreview = null;
	var editorPreviewRaf = null;
	var navigationPositionRaf = null;

	var STYLE_VARS = [
		'--nelxstd-prt-card-background',
		'--nelxstd-prt-card-width',
		'--nelxstd-prt-card-padding',
		'--nelxstd-prt-card-radius',
		'--nelxstd-prt-card-border-style',
		'--nelxstd-prt-card-border-color',
		'--nelxstd-prt-card-border-width',
		'--nelxstd-prt-card-shadow',
		'--nelxstd-prt-title-color',
		'--nelxstd-prt-title-font-family',
		'--nelxstd-prt-title-font-size',
		'--nelxstd-prt-title-font-weight',
		'--nelxstd-prt-title-font-style',
		'--nelxstd-prt-title-text-transform',
		'--nelxstd-prt-title-text-decoration',
		'--nelxstd-prt-title-line-height',
		'--nelxstd-prt-title-letter-spacing',
		'--nelxstd-prt-title-word-spacing',
		'--nelxstd-prt-description-color',
		'--nelxstd-prt-description-font-family',
		'--nelxstd-prt-description-font-size',
		'--nelxstd-prt-description-font-weight',
		'--nelxstd-prt-description-font-style',
		'--nelxstd-prt-description-text-transform',
		'--nelxstd-prt-description-text-decoration',
		'--nelxstd-prt-description-line-height',
		'--nelxstd-prt-description-letter-spacing',
		'--nelxstd-prt-description-word-spacing',
		'--nelxstd-prt-indicator-background',
		'--nelxstd-prt-indicator-color',
		'--nelxstd-prt-indicator-font-family',
		'--nelxstd-prt-indicator-font-size',
		'--nelxstd-prt-indicator-font-weight',
		'--nelxstd-prt-indicator-font-style',
		'--nelxstd-prt-indicator-text-transform',
		'--nelxstd-prt-indicator-text-decoration',
		'--nelxstd-prt-indicator-line-height',
		'--nelxstd-prt-indicator-letter-spacing',
		'--nelxstd-prt-indicator-word-spacing',
		'--nelxstd-prt-indicator-padding',
		'--nelxstd-prt-indicator-radius',
		'--nelxstd-prt-skip-background',
		'--nelxstd-prt-skip-color',
		'--nelxstd-prt-skip-font-family',
		'--nelxstd-prt-skip-font-size',
		'--nelxstd-prt-skip-font-weight',
		'--nelxstd-prt-skip-font-style',
		'--nelxstd-prt-skip-text-transform',
		'--nelxstd-prt-skip-text-decoration',
		'--nelxstd-prt-skip-line-height',
		'--nelxstd-prt-skip-letter-spacing',
		'--nelxstd-prt-skip-word-spacing',
		'--nelxstd-prt-skip-border-style',
		'--nelxstd-prt-skip-border-color',
		'--nelxstd-prt-skip-border-width',
		'--nelxstd-prt-skip-shadow',
		'--nelxstd-prt-skip-padding',
		'--nelxstd-prt-skip-radius',
		'--nelxstd-prt-skip-hover-background',
		'--nelxstd-prt-skip-hover-color',
		'--nelxstd-prt-skip-hover-border-style',
		'--nelxstd-prt-skip-hover-border-color',
		'--nelxstd-prt-skip-hover-border-width',
		'--nelxstd-prt-skip-hover-shadow',
		'--nelxstd-prt-skip-hover-lift',
		'--nelxstd-prt-back-background',
		'--nelxstd-prt-back-color',
		'--nelxstd-prt-back-font-family',
		'--nelxstd-prt-back-font-size',
		'--nelxstd-prt-back-font-weight',
		'--nelxstd-prt-back-font-style',
		'--nelxstd-prt-back-text-transform',
		'--nelxstd-prt-back-text-decoration',
		'--nelxstd-prt-back-line-height',
		'--nelxstd-prt-back-letter-spacing',
		'--nelxstd-prt-back-word-spacing',
		'--nelxstd-prt-back-border-style',
		'--nelxstd-prt-back-border-color',
		'--nelxstd-prt-back-border-width',
		'--nelxstd-prt-back-shadow',
		'--nelxstd-prt-back-padding',
		'--nelxstd-prt-back-radius',
		'--nelxstd-prt-back-hover-background',
		'--nelxstd-prt-back-hover-color',
		'--nelxstd-prt-back-hover-border-style',
		'--nelxstd-prt-back-hover-border-color',
		'--nelxstd-prt-back-hover-border-width',
		'--nelxstd-prt-back-hover-shadow',
		'--nelxstd-prt-back-hover-lift',
		'--nelxstd-prt-next-background',
		'--nelxstd-prt-next-color',
		'--nelxstd-prt-next-font-family',
		'--nelxstd-prt-next-font-size',
		'--nelxstd-prt-next-font-weight',
		'--nelxstd-prt-next-font-style',
		'--nelxstd-prt-next-text-transform',
		'--nelxstd-prt-next-text-decoration',
		'--nelxstd-prt-next-line-height',
		'--nelxstd-prt-next-letter-spacing',
		'--nelxstd-prt-next-word-spacing',
		'--nelxstd-prt-next-border-style',
		'--nelxstd-prt-next-border-color',
		'--nelxstd-prt-next-border-width',
		'--nelxstd-prt-next-shadow',
		'--nelxstd-prt-next-padding',
		'--nelxstd-prt-next-radius',
		'--nelxstd-prt-next-hover-background',
		'--nelxstd-prt-next-hover-color',
		'--nelxstd-prt-next-hover-border-style',
		'--nelxstd-prt-next-hover-border-color',
		'--nelxstd-prt-next-hover-border-width',
		'--nelxstd-prt-next-hover-shadow',
		'--nelxstd-prt-next-hover-lift',
		'--nelxstd-prt-highlight-border-style',
		'--nelxstd-prt-highlight-border-color',
		'--nelxstd-prt-highlight-border-width',
		'--nelxstd-prt-highlight-shadow',
		'--nelxstd-prt-highlight-radius',
		'--nelxstd-prt-floating-background',
		'--nelxstd-prt-floating-color',
		'--nelxstd-prt-floating-font-family',
		'--nelxstd-prt-floating-font-size',
		'--nelxstd-prt-floating-font-weight',
		'--nelxstd-prt-floating-font-style',
		'--nelxstd-prt-floating-text-transform',
		'--nelxstd-prt-floating-text-decoration',
		'--nelxstd-prt-floating-line-height',
		'--nelxstd-prt-floating-letter-spacing',
		'--nelxstd-prt-floating-word-spacing',
		'--nelxstd-prt-floating-border-style',
		'--nelxstd-prt-floating-border-color',
		'--nelxstd-prt-floating-border-width',
		'--nelxstd-prt-floating-shadow',
		'--nelxstd-prt-floating-padding',
		'--nelxstd-prt-floating-radius',
		'--nelxstd-prt-floating-close-background',
		'--nelxstd-prt-floating-close-color',
		'--nelxstd-prt-floating-close-size',
		'--nelxstd-prt-floating-hover-background',
		'--nelxstd-prt-floating-hover-color',
		'--nelxstd-prt-floating-hover-border-style',
		'--nelxstd-prt-floating-hover-border-color',
		'--nelxstd-prt-floating-hover-border-width',
		'--nelxstd-prt-floating-hover-shadow',
		'--nelxstd-prt-floating-hover-lift',
		'--nelxstd-prt-floating-close-border-style',
		'--nelxstd-prt-floating-close-border-color',
		'--nelxstd-prt-floating-close-border-width',
		'--nelxstd-prt-floating-close-shadow',
		'--nelxstd-prt-floating-close-hover-lift',
	];

	function ready(callback) {
		if ('loading' === document.readyState) {
			document.addEventListener('DOMContentLoaded', callback);
			return;
		}

		callback();
	}

	function safeQuerySelector(selector) {
		try {
			return selector ? document.querySelector(selector) : null;
		} catch (error) {
			return null;
		}
	}

	function getElementPosition(element) {
		var rect = element.getBoundingClientRect();

		return {
			top: rect.top + window.scrollY,
			left: rect.left + window.scrollX,
		};
	}

	function normalizeTourId(tourId) {
		return String(tourId || 'default').toLowerCase().replace(/[^a-z0-9_-]/g, '-') || 'default';
	}

	function getCompletionKey(tourId) {
		return String(config.pageId || 0) + '::' + normalizeTourId(tourId);
	}

	function getLocalStorageKey(tourId) {
		return 'nelxstd_prt_completed_' + String(config.userId || 0) + '_' + getCompletionKey(tourId);
	}

	function getLauncherDismissedKey() {
		var path = window.location && window.location.pathname ? window.location.pathname : '';
		return 'nelxstd_prt_launcher_dismissed_' + String(config.userId || 0) + '_' + String(config.pageId || 0) + '_' + path;
	}

	function isCompleted(tourId) {
		var key = getCompletionKey(tourId);
		var completedKeys = config.completedKeys || [];

		if (completedKeys.indexOf(key) !== -1) {
			return true;
		}

		try {
			return window.localStorage.getItem(getLocalStorageKey(tourId)) === '1';
		} catch (error) {
			return false;
		}
	}

	function markComplete(tourId) {
		var body;
		var key = getCompletionKey(tourId);

		try {
			window.localStorage.setItem(getLocalStorageKey(tourId), '1');
		} catch (error) {}

		if ((config.completedKeys || []).indexOf(key) === -1) {
			config.completedKeys = (config.completedKeys || []).concat([key]);
		}

		if (!config.isLoggedIn || !config.ajaxUrl || !config.nonce) {
			return;
		}

		body = new window.FormData();
		body.append('action', 'nelxstd_prt_complete_tour');
		body.append('nonce', config.nonce);
		body.append('pageId', config.pageId || 0);
		body.append('tourId', normalizeTourId(tourId));

		window.fetch(config.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: body,
		}).catch(function () {});
	}

	function parseStyles(source) {
		var raw = source.getAttribute('data-nelxstd-prt-styles') || '{}';

		try {
			return JSON.parse(raw);
		} catch (error) {
			return {};
		}
	}

	function collectTours() {
		var sources = Array.prototype.slice.call(document.querySelectorAll('[data-nelxstd-prt-step="1"]'));
		var groups = {};

		sources.forEach(function (source) {
			var tourId = normalizeTourId(source.getAttribute('data-nelxstd-prt-tour-id'));
			var mode = source.getAttribute('data-nelxstd-prt-target-mode') || 'element';
			var selector = source.getAttribute('data-nelxstd-prt-target-selector') || '';
			var target = 'selector' === mode ? safeQuerySelector(selector) : source;
			var order = parseInt(source.getAttribute('data-nelxstd-prt-order') || '10', 10);

			if (!target) {
				return;
			}

			if (!groups[tourId]) {
				groups[tourId] = {
					id: tourId,
					steps: [],
				};
			}

			groups[tourId].steps.push({
				source: source,
				target: target,
				order: isNaN(order) ? 10 : order,
				title: source.getAttribute('data-nelxstd-prt-title') || '',
				description: source.getAttribute('data-nelxstd-prt-description') || '',
				placement: source.getAttribute('data-nelxstd-prt-tooltip-placement') || 'auto',
				styles: parseStyles(source),
			});
		});

		tourGroups = Object.keys(groups)
			.map(function (tourId) {
				groups[tourId].steps.sort(sortSteps);
				groups[tourId].position = getElementPosition(groups[tourId].steps[0].target);
				return groups[tourId];
			})
			.sort(function (a, b) {
				if (a.position.top !== b.position.top) {
					return a.position.top - b.position.top;
				}

				return a.position.left - b.position.left;
			});
	}

	function sortSteps(a, b) {
		var aPosition;
		var bPosition;

		if (a.order !== b.order) {
			return a.order - b.order;
		}

		aPosition = getElementPosition(a.target);
		bPosition = getElementPosition(b.target);

		if (aPosition.top !== bPosition.top) {
			return aPosition.top - bPosition.top;
		}

		return aPosition.left - bPosition.left;
	}

	function setStyleVar(name, value) {
		if (null === value || typeof value === 'undefined' || '' === value) {
			return;
		}

		document.documentElement.style.setProperty(name, String(value));
	}

	function clearStyleVars() {
		STYLE_VARS.forEach(function (name) {
			document.documentElement.style.removeProperty(name);
		});

	}

	function dimensionToCss(value, fallback) {
		var unit;
		var parts;

		if (null === value || typeof value === 'undefined' || '' === value) {
			return fallback || '';
		}

		if (typeof value === 'object') {
			unit = value.unit || 'px';

			if (Object.prototype.hasOwnProperty.call(value, 'size')) {
				if ('' === value.size || null === value.size || typeof value.size === 'undefined') {
					return fallback || '';
				}

				return value.size + unit;
			}

			if (Object.prototype.hasOwnProperty.call(value, 'top')) {
				parts = ['top', 'right', 'bottom', 'left'].map(function (side) {
					var sideValue = value[side];
					return (null === sideValue || typeof sideValue === 'undefined' || '' === sideValue ? 0 : sideValue) + unit;
				});

				return parts.join(' ');
			}
		}

		if ('number' === typeof value || /^-?\d+(\.\d+)?$/.test(String(value))) {
			return value + 'px';
		}

		return String(value);
	}

	function shadowToCss(value, fallback) {
		var horizontal;
		var vertical;
		var blur;
		var spread;
		var color;
		var inset;

		if (!value) {
			return fallback || 'none';
		}

		if ('string' === typeof value) {
			return value;
		}

		if ('object' === typeof value) {
			horizontal = null !== value.horizontal && typeof value.horizontal !== 'undefined' ? value.horizontal : 0;
			vertical = null !== value.vertical && typeof value.vertical !== 'undefined' ? value.vertical : 0;
			blur = null !== value.blur && typeof value.blur !== 'undefined' ? value.blur : 0;
			spread = null !== value.spread && typeof value.spread !== 'undefined' ? value.spread : 0;
			color = value.color || 'rgba(0, 0, 0, 0.18)';
			inset = value.inset ? 'inset ' : '';

			return inset + horizontal + 'px ' + vertical + 'px ' + blur + 'px ' + spread + 'px ' + color;
		}

		return fallback || 'none';
	}

	function applyTypographyVars(prefix, typography) {
		var map;

		if (!typography) {
			return;
		}

		map = {
			font_family: 'font-family',
			font_size: 'font-size',
			font_weight: 'font-weight',
			font_style: 'font-style',
			text_transform: 'text-transform',
			text_decoration: 'text-decoration',
			line_height: 'line-height',
			letter_spacing: 'letter-spacing',
			word_spacing: 'word-spacing',
		};

		if (typography.color) {
			setStyleVar('--nelxstd-prt-' + prefix + '-color', typography.color);
		}

		Object.keys(map).forEach(function (key) {
			if (null !== typography[key] && typeof typography[key] !== 'undefined' && '' !== typography[key]) {
				setStyleVar('--nelxstd-prt-' + prefix + '-' + map[key], dimensionToCss(typography[key], ''));
			}
		});
	}

	function applyBorderVars(prefix, border) {
		if (!border) {
			return;
		}

		setStyleVar('--nelxstd-prt-' + prefix + '-border-style', border.style || 'solid');
		setStyleVar('--nelxstd-prt-' + prefix + '-border-color', border.color || 'transparent');
		setStyleVar('--nelxstd-prt-' + prefix + '-border-width', dimensionToCss(border.width, '0px'));
	}

	function applyButtonVars(prefix, styles) {
		if (!styles) {
			return;
		}

		setStyleVar('--nelxstd-prt-' + prefix + '-background', styles.background);
		setStyleVar('--nelxstd-prt-' + prefix + '-color', styles.color);
		setStyleVar('--nelxstd-prt-' + prefix + '-padding', dimensionToCss(styles.padding, '11px 16px'));
		setStyleVar('--nelxstd-prt-' + prefix + '-radius', dimensionToCss(styles.radius, '999px'));
		setStyleVar('--nelxstd-prt-' + prefix + '-shadow', shadowToCss(styles.shadow, 'none'));
		setStyleVar('--nelxstd-prt-' + prefix + '-hover-background', styles.hover_background || styles.background);
		setStyleVar('--nelxstd-prt-' + prefix + '-hover-color', styles.hover_color || styles.color);
		setStyleVar('--nelxstd-prt-' + prefix + '-hover-shadow', shadowToCss(styles.hover_shadow, shadowToCss(styles.shadow, 'none')));
		setStyleVar('--nelxstd-prt-' + prefix + '-hover-lift', dimensionToCss(styles.hover_lift, '-1px'));
		applyTypographyVars(prefix, styles.typography);
		applyBorderVars(prefix, styles.border);
		applyBorderVars(prefix + '-hover', styles.hover_border || styles.border);
	}

	function applyTourStyles(styles) {
		var card;
		var indicator;
		var highlight;
		var floating;

		clearStyleVars();

		styles = styles || {};
		card = styles.card || {};
		indicator = styles.indicator || {};
		highlight = styles.highlight || {};
		floating = styles.floating || {};

		// Card.
		setStyleVar('--nelxstd-prt-card-background', card.background || '#ffffff');
		setStyleVar('--nelxstd-prt-card-width', dimensionToCss(card.width, '360px'));
		setStyleVar('--nelxstd-prt-card-padding', dimensionToCss(card.padding, '22px'));
		setStyleVar('--nelxstd-prt-card-radius', dimensionToCss(card.radius, '20px'));
		setStyleVar('--nelxstd-prt-card-shadow', shadowToCss(card.shadow, '0 24px 70px rgba(15, 23, 42, 0.22)'));
		applyBorderVars('card', card.border);


		// Text.
		applyTypographyVars('title', styles.title || {});
		applyTypographyVars('description', styles.description || {});

		// Indicator.
		setStyleVar('--nelxstd-prt-indicator-background', indicator.background || '#e0f7fa');
		setStyleVar('--nelxstd-prt-indicator-color', indicator.color || '#1e3a8a');
		setStyleVar('--nelxstd-prt-indicator-padding', dimensionToCss(indicator.padding, '5px 10px'));
		setStyleVar('--nelxstd-prt-indicator-radius', dimensionToCss(indicator.radius, '999px'));
		applyTypographyVars('indicator', indicator.typography || {});

		// Buttons.
		applyButtonVars('skip', styles.skip || {});
		applyButtonVars('back', styles.back || {});
		applyButtonVars('next', styles.next || {});

		// Target highlight.
		applyBorderVars('highlight', highlight.border);
		setStyleVar('--nelxstd-prt-highlight-shadow', shadowToCss(highlight.shadow, '0 18px 55px rgba(30, 58, 138, 0.28)'));
		setStyleVar('--nelxstd-prt-highlight-radius', dimensionToCss(highlight.radius, '14px'));

		// Floating replay button.
		setStyleVar('--nelxstd-prt-floating-background', floating.background || 'linear-gradient(135deg, #1e3a8a, #00b7c2)');
		setStyleVar('--nelxstd-prt-floating-color', floating.color || '#ffffff');
		setStyleVar('--nelxstd-prt-floating-padding', dimensionToCss(floating.padding, '14px 18px'));
		setStyleVar('--nelxstd-prt-floating-radius', dimensionToCss(floating.radius, '999px'));
		setStyleVar('--nelxstd-prt-floating-shadow', shadowToCss(floating.shadow, '0 16px 36px rgba(30, 58, 138, 0.28)'));
		setStyleVar('--nelxstd-prt-floating-hover-background', floating.hover_background || floating.background || 'linear-gradient(135deg, #1e3a8a, #00b7c2)');
		setStyleVar('--nelxstd-prt-floating-hover-color', floating.hover_color || floating.color || '#ffffff');
		setStyleVar('--nelxstd-prt-floating-hover-shadow', shadowToCss(floating.hover_shadow, shadowToCss(floating.shadow, '0 16px 36px rgba(30, 58, 138, 0.28)')));
		setStyleVar('--nelxstd-prt-floating-hover-lift', dimensionToCss(floating.hover_lift, '-1px'));
		setStyleVar('--nelxstd-prt-floating-close-background', floating.close_background || '#ffffff');
		setStyleVar('--nelxstd-prt-floating-close-color', floating.close_color || '#1e3a8a');
		setStyleVar('--nelxstd-prt-floating-close-size', dimensionToCss(floating.close_size, '22px'));
		setStyleVar('--nelxstd-prt-floating-close-hover-lift', dimensionToCss(floating.close_hover_lift, '0px'));
		setStyleVar('--nelxstd-prt-floating-close-shadow', shadowToCss(floating.close_shadow, '0 5px 16px rgba(15, 23, 42, 0.22)'));
		applyTypographyVars('floating', floating.typography || {});
		applyBorderVars('floating', floating.border);
		applyBorderVars('floating-hover', floating.hover_border || floating.border);
		applyBorderVars('floating-close', floating.close_border);
	}



	function getPopoverElement(popover) {
		if (!popover) {
			return null;
		}

		if (popover.wrapper && 1 === popover.wrapper.nodeType) {
			return popover.wrapper;
		}

		return 1 === popover.nodeType ? popover : null;
	}

	function getPopoverNavigation(popover, wrapper) {
		if (popover && popover.footerButtons && 1 === popover.footerButtons.nodeType) {
			return popover.footerButtons;
		}

		return wrapper ? wrapper.querySelector('.driver-popover-navigation-btns') : null;
	}

	function getPopoverFooter(popover, wrapper) {
		if (popover && popover.footer && 1 === popover.footer.nodeType) {
			return popover.footer;
		}

		return wrapper ? wrapper.querySelector('.driver-popover-footer') : null;
	}

	function getNavigationParts(popover) {
		var wrapper = getPopoverElement(popover);

		return {
			wrapper: wrapper,
			footer: getPopoverFooter(popover, wrapper),
			navigation: getPopoverNavigation(popover, wrapper),
		};
	}

	function keepNavigationInFooter(parts) {
		if (!parts || !parts.footer || !parts.navigation) {
			return;
		}

		// Driver.js creates the navigation inside the footer. Re-assert that structure
		// before applying either layout so desktop Inside Card uses the exact same DOM
		// arrangement as mobile.
		if (parts.navigation.parentNode !== parts.footer) {
			parts.footer.appendChild(parts.navigation);
		}
	}

	function setNavigationProperty(navigation, property, value) {
		if (navigation) {
			navigation.style.setProperty(property, value, 'important');
		}
	}

	function positionNavigationInside(popover) {
		var parts = getNavigationParts(popover);

		if (!parts.wrapper || !parts.navigation) {
			return;
		}

		keepNavigationInFooter(parts);

		// Match the working mobile implementation explicitly instead of relying only
		// on a CSS class. Inline !important values prevent theme or cached Elementor
		// CSS from pushing the real frontend buttons outside the card.
		setNavigationProperty(parts.navigation, 'position', 'static');
		setNavigationProperty(parts.navigation, 'top', 'auto');
		setNavigationProperty(parts.navigation, 'right', 'auto');
		setNavigationProperty(parts.navigation, 'bottom', 'auto');
		setNavigationProperty(parts.navigation, 'left', 'auto');
		setNavigationProperty(parts.navigation, 'max-width', '100%');
		setNavigationProperty(parts.navigation, 'width', 'auto');
		setNavigationProperty(parts.navigation, 'flex-wrap', 'nowrap');
		setNavigationProperty(parts.navigation, 'z-index', 'auto');
	}

	function prepareOutsideNavigation(popover) {
		var parts = getNavigationParts(popover);
		var viewportPadding = 12;

		if (!parts.wrapper || !parts.navigation) {
			return null;
		}

		keepNavigationInFooter(parts);

		if (window.innerWidth <= 600) {
			positionNavigationInside(popover);
			return null;
		}

		// Remove the group from the footer flow before Driver.js measures and places
		// the card. Its final viewport coordinates are applied on the next frame.
		setNavigationProperty(parts.navigation, 'position', 'fixed');
		setNavigationProperty(parts.navigation, 'top', '0px');
		setNavigationProperty(parts.navigation, 'right', 'auto');
		setNavigationProperty(parts.navigation, 'bottom', 'auto');
		setNavigationProperty(parts.navigation, 'left', '0px');
		setNavigationProperty(parts.navigation, 'max-width', 'calc(100vw - ' + (viewportPadding * 2) + 'px)');
		setNavigationProperty(parts.navigation, 'width', 'max-content');
		setNavigationProperty(parts.navigation, 'flex-wrap', 'wrap');
		setNavigationProperty(parts.navigation, 'z-index', '1000000001');

		return parts;
	}

	function positionOutsideNavigation(popover) {
		var parts = getNavigationParts(popover);
		var popoverRect;
		var navigationRect;
		var viewportPadding = 12;
		var gap = 8;
		var viewportWidth;
		var navigationWidth;
		var desiredLeft;
		var maximumLeft;
		var clampedLeft;

		if (!parts.wrapper || !parts.navigation || !parts.wrapper.classList.contains('nelxstd-prt-nav-outside')) {
			return;
		}

		if (window.innerWidth <= 600) {
			positionNavigationInside(popover);
			return;
		}

		prepareOutsideNavigation(popover);

		popoverRect = parts.wrapper.getBoundingClientRect();
		navigationRect = parts.navigation.getBoundingClientRect();
		viewportWidth = document.documentElement.clientWidth || window.innerWidth;
		navigationWidth = Math.min(navigationRect.width, Math.max(0, viewportWidth - (viewportPadding * 2)));

		// Always place the group below the card. Right-align it with the card when
		// there is room, then clamp the whole group inside the viewport at either edge.
		desiredLeft = popoverRect.right - navigationWidth;
		maximumLeft = Math.max(viewportPadding, viewportWidth - viewportPadding - navigationWidth);
		clampedLeft = Math.min(maximumLeft, Math.max(viewportPadding, desiredLeft));

		setNavigationProperty(parts.navigation, 'top', Math.round(popoverRect.bottom + gap) + 'px');
		setNavigationProperty(parts.navigation, 'left', Math.round(clampedLeft) + 'px');
		setNavigationProperty(parts.navigation, 'right', 'auto');
		setNavigationProperty(parts.navigation, 'bottom', 'auto');
	}

	function scheduleNavigationPosition(popover, position) {
		if (navigationPositionRaf) {
			window.cancelAnimationFrame(navigationPositionRaf);
		}

		// Driver.js performs its own card placement immediately after onPopoverRender.
		// Two frames ensure our final outside coordinates use the settled card rectangle.
		navigationPositionRaf = window.requestAnimationFrame(function () {
			navigationPositionRaf = window.requestAnimationFrame(function () {
				navigationPositionRaf = null;

				if ('outside' === position && window.innerWidth > 600) {
					positionOutsideNavigation(popover);
				} else {
					positionNavigationInside(popover);
				}
			});
		});
	}

	function applyNavigationPosition(popover, position) {
		var parts = getNavigationParts(popover);
		var isOutside = 'outside' === position;

		if (!parts.wrapper || !parts.navigation) {
			return;
		}

		parts.wrapper.classList.toggle('nelxstd-prt-nav-outside', isOutside);
		parts.wrapper.classList.toggle('nelxstd-prt-nav-inside', !isOutside);

		if (isOutside && window.innerWidth > 600) {
			prepareOutsideNavigation(popover);
		} else {
			positionNavigationInside(popover);
		}

		scheduleNavigationPosition(popover, isOutside ? 'outside' : 'inside');
	}

	function repositionActiveOutsideNavigation() {
		var popover = document.querySelector('.nelxstd-prt-driver-popover.nelxstd-prt-nav-outside');

		if (popover) {
			scheduleNavigationPosition(popover, 'outside');
		}
	}

	function editorSetting(settings, key, fallback) {
		return settings && Object.prototype.hasOwnProperty.call(settings, key) && '' !== settings[key] && null !== settings[key] && typeof settings[key] !== 'undefined' ? settings[key] : fallback;
	}

	function editorColor(settings, key, fallback) {
		return editorSetting(settings, key, fallback) || fallback;
	}

	function editorBorder(settings, prefix, fallback) {
		var style = editorSetting(settings, prefix + '_border', '');
		var color = editorSetting(settings, prefix + '_color', '');
		var width = editorSetting(settings, prefix + '_width', '');

		if (!style && !color && !width) {
			return fallback;
		}

		return {
			style: style || fallback.style || 'solid',
			color: color || fallback.color || 'transparent',
			width: width || fallback.width || 0,
		};
	}

	function editorShadow(settings, prefix, fallback) {
		var value = settings ? settings[prefix + '_box_shadow'] : null;

		if (!value || 'object' !== typeof value) {
			return fallback;
		}

		if ('' === value.horizontal && '' === value.vertical && '' === value.blur && '' === value.spread && !value.color) {
			return fallback;
		}

		return value;
	}

	function editorTypography(settings, prefix, color, size, fallbackColor) {
		return {
			color: editorColor(settings, color, fallbackColor || '#272626'),
			font_family: editorSetting(settings, prefix + '_font_family', ''),
			font_size: editorSetting(settings, prefix + '_font_size', { size: size, unit: 'px' }),
			font_weight: editorSetting(settings, prefix + '_font_weight', ''),
			font_style: editorSetting(settings, prefix + '_font_style', ''),
			text_transform: editorSetting(settings, prefix + '_text_transform', ''),
			text_decoration: editorSetting(settings, prefix + '_text_decoration', ''),
			line_height: editorSetting(settings, prefix + '_line_height', ''),
			letter_spacing: editorSetting(settings, prefix + '_letter_spacing', ''),
			word_spacing: editorSetting(settings, prefix + '_word_spacing', ''),
		};
	}

	function editorButtonStyle(settings, prefix, background, color) {
		var normalBorder = editorBorder(settings, 'nelxstd_prt_' + prefix + '_border', { style: 'solid', color: 'transparent', width: 0 });
		var normalShadow = editorShadow(settings, 'nelxstd_prt_' + prefix + '_shadow', 'none');

		return {
			background: editorColor(settings, 'nelxstd_prt_' + prefix + '_background', background),
			color: editorColor(settings, 'nelxstd_prt_' + prefix + '_color', color),
			typography: editorTypography(settings, 'nelxstd_prt_' + prefix + '_typography', '', 13, color),
			border: normalBorder,
			shadow: normalShadow,
			padding: editorSetting(settings, 'nelxstd_prt_' + prefix + '_padding_control', { top: 11, right: 16, bottom: 11, left: 16, unit: 'px' }),
			radius: editorSetting(settings, 'nelxstd_prt_' + prefix + '_radius_control', { top: 999, right: 999, bottom: 999, left: 999, unit: 'px' }),
			hover_background: editorColor(settings, 'nelxstd_prt_' + prefix + '_hover_background', background),
			hover_color: editorColor(settings, 'nelxstd_prt_' + prefix + '_hover_color', color),
			hover_border: editorBorder(settings, 'nelxstd_prt_' + prefix + '_hover_border', normalBorder),
			hover_shadow: editorShadow(settings, 'nelxstd_prt_' + prefix + '_hover_shadow', normalShadow),
			hover_lift: editorSetting(settings, 'nelxstd_prt_' + prefix + '_hover_lift_control', { size: -1, unit: 'px' }),
		};
	}

	function editorFloatingBackground(settings, hover, normalFallback) {
		var prefix = hover ? 'nelxstd_prt_floating_hover' : 'nelxstd_prt_floating';
		var type = editorSetting(settings, prefix + '_background_type', '');

		if (hover && !type) {
			return normalFallback || 'linear-gradient(135deg, #1e3a8a, #00b7c2)';
		}

		if (!hover && !type) {
			var legacy = editorSetting(settings, 'nelxstd_prt_floating_background', '');
			if (legacy && '#1e3a8a' !== String(legacy).toLowerCase()) {
				return legacy;
			}
		}

		if ('classic' === type) {
			return editorColor(settings, prefix + '_background', '#1e3a8a');
		}

		return 'linear-gradient(' + (editorSetting(settings, prefix + '_gradient_angle_control', { size: 135, unit: 'deg' }).size || 135) + 'deg, ' + editorColor(settings, prefix + '_gradient_color_a', '#1e3a8a') + ', ' + editorColor(settings, prefix + '_gradient_color_b', '#00b7c2') + ')';
	}

	function buildEditorPreviewStyles(settings) {
		var cardBorder = editorBorder(settings, 'nelxstd_prt_card_border', { style: 'solid', color: '#e5e7eb', width: 0 });
		var cardShadow = editorShadow(settings, 'nelxstd_prt_card_shadow', '0 24px 70px rgba(15, 23, 42, 0.22)');
		var indicatorBorder = { style: 'solid', color: 'transparent', width: 0 };
		var floating = {
			background: editorFloatingBackground(settings, false),
			color: editorColor(settings, 'nelxstd_prt_floating_color', '#ffffff'),
			typography: editorTypography(settings, 'nelxstd_prt_floating_typography', '', 14, '#ffffff'),
			border: editorBorder(settings, 'nelxstd_prt_floating_border', { style: 'solid', color: 'transparent', width: 0 }),
			shadow: editorShadow(settings, 'nelxstd_prt_floating_shadow', '0 16px 36px rgba(30, 58, 138, 0.28)'),
			padding: editorSetting(settings, 'nelxstd_prt_floating_padding_control', { top: 14, right: 18, bottom: 14, left: 18, unit: 'px' }),
			radius: editorSetting(settings, 'nelxstd_prt_floating_radius_control', { top: 999, right: 999, bottom: 999, left: 999, unit: 'px' }),
			hover_background: editorFloatingBackground(settings, true, editorFloatingBackground(settings, false)),
			hover_color: editorColor(settings, 'nelxstd_prt_floating_hover_color', '#ffffff'),
			hover_border: editorBorder(settings, 'nelxstd_prt_floating_hover_border', { style: 'solid', color: 'transparent', width: 0 }),
			hover_shadow: editorShadow(settings, 'nelxstd_prt_floating_hover_shadow', '0 16px 36px rgba(30, 58, 138, 0.28)'),
			hover_lift: editorSetting(settings, 'nelxstd_prt_floating_hover_lift_control', { size: -1, unit: 'px' }),
			close_background: editorColor(settings, 'nelxstd_prt_floating_close_background', '#ffffff'),
			close_color: editorColor(settings, 'nelxstd_prt_floating_close_color', '#1e3a8a'),
			close_border: editorBorder(settings, 'nelxstd_prt_floating_close_border', { style: 'solid', color: 'transparent', width: 0 }),
			close_shadow: editorShadow(settings, 'nelxstd_prt_floating_close_shadow', '0 5px 16px rgba(15, 23, 42, 0.22)'),
			close_size: editorSetting(settings, 'nelxstd_prt_floating_close_size_control', { size: 22, unit: 'px' }),
			close_hover_lift: editorSetting(settings, 'nelxstd_prt_floating_close_hover_lift_control', { size: 0, unit: 'px' }),
		};

		return {
			card: {
				background: editorColor(settings, 'nelxstd_prt_card_background', '#ffffff'),
				width: editorSetting(settings, 'nelxstd_prt_card_width_control', { size: 360, unit: 'px' }),
				padding: editorSetting(settings, 'nelxstd_prt_card_padding_control', { top: 22, right: 22, bottom: 22, left: 22, unit: 'px' }),
				radius: editorSetting(settings, 'nelxstd_prt_card_radius_control', { top: 20, right: 20, bottom: 20, left: 20, unit: 'px' }),
				border: cardBorder,
				shadow: cardShadow,
			},
			navigation_position: editorSetting(settings, 'nelxstd_prt_navigation_position', 'inside'),
			title: editorTypography(settings, 'nelxstd_prt_title_typography', 'nelxstd_prt_title_color', 20, '#272626'),
			description: editorTypography(settings, 'nelxstd_prt_description_typography', 'nelxstd_prt_description_color', 14, '#626262'),
			indicator: {
				background: editorColor(settings, 'nelxstd_prt_indicator_background', '#e0f7fa'),
				color: editorColor(settings, 'nelxstd_prt_indicator_color', '#1e3a8a'),
				typography: editorTypography(settings, 'nelxstd_prt_indicator_typography', '', 12, editorColor(settings, 'nelxstd_prt_indicator_color', '#1e3a8a')),
				padding: editorSetting(settings, 'nelxstd_prt_indicator_padding_control', { top: 5, right: 10, bottom: 5, left: 10, unit: 'px' }),
				radius: editorSetting(settings, 'nelxstd_prt_indicator_radius_control', { top: 999, right: 999, bottom: 999, left: 999, unit: 'px' }),
				border: indicatorBorder,
			},
			skip: editorButtonStyle(settings, 'skip', '#fff0eb', '#f4511e'),
			back: editorButtonStyle(settings, 'back', '#f4f7fb', '#272626'),
			next: editorButtonStyle(settings, 'next', '#1e3a8a', '#ffffff'),
			highlight: {
				border: editorBorder(settings, 'nelxstd_prt_highlight_border', { style: 'solid', color: '#00b7c2', width: 4 }),
				shadow: editorShadow(settings, 'nelxstd_prt_highlight_shadow', '0 18px 55px rgba(30, 58, 138, 0.28)'),
				radius: editorSetting(settings, 'nelxstd_prt_highlight_radius_control', { top: 14, right: 14, bottom: 14, left: 14, unit: 'px' }),
			},
			floating: floating,
		};
	}

	function getEditorElementId(candidate) {
		if (!candidate) {
			return '';
		}

		if (candidate.id) {
			return String(candidate.id);
		}

		if (candidate.model && candidate.model.id) {
			return String(candidate.model.id);
		}

		if (candidate.model && candidate.model.get) {
			return String(candidate.model.get('id') || '');
		}

		if (candidate.get) {
			return String(candidate.get('id') || '');
		}

		return '';
	}

	function getEditorSettings(candidate) {
		var settings;

		if (!candidate) {
			return null;
		}

		if (candidate.settings) {
			settings = candidate.settings;
			if (settings.toJSON) {
				return settings.toJSON();
			}
			if (settings.attributes) {
				return settings.attributes;
			}
		}

		if (candidate.model && candidate.model.get) {
			settings = candidate.model.get('settings');
			if (settings && settings.toJSON) {
				return settings.toJSON();
			}
			if (settings && settings.attributes) {
				return settings.attributes;
			}
		}

		return null;
	}

	function getEditorCurrentContainer(view) {
		var editor = window.parent && window.parent.elementor ? window.parent.elementor : null;

		if (view && view.container) {
			return view.container;
		}

		if (editor && editor.getCurrentElement) {
			try {
				return editor.getCurrentElement();
			} catch (error) {}
		}

		if (editor && editor.selection && editor.selection.getElements) {
			try {
				var selected = editor.selection.getElements();
				if (selected && selected.length) {
					return selected[0];
				}
			} catch (error) {}
		}

		return null;
	}

	function getPreviewSourceForElement(elementId) {
		var sources = Array.prototype.slice.call(document.querySelectorAll('[data-nelxstd-prt-editor-preview="1"]'));

		if (elementId) {
			var matching = sources.find(function (source) {
				return source.getAttribute('data-nelxstd-prt-element-id') === elementId;
			});
			if (matching) {
				return matching;
			}
		}

		return sources[0] || null;
	}

	function getPreviewProgress(source) {
		var total = 1;
		var current = 1;
		var tourId = source ? normalizeTourId(source.getAttribute('data-nelxstd-prt-tour-id')) : 'default';
		var group = tourGroups.find(function (item) {
			return item.id === tourId;
		});

		if (group && group.steps && group.steps.length) {
			total = group.steps.length;
			current = group.steps.findIndex(function (step) {
				return source && step.source === source;
			}) + 1;
			if (current < 1) {
				current = 1;
			}
		}

		return { current: current, total: total };
	}

	function ensureEditorPreview() {
		var title;
		var description;
		var footer;
		var progress;
		var navigation;
		var back;
		var next;
		var skip;

		if (editorPreview || !config.isEditor) {
			return editorPreview;
		}

		editorPreview = document.createElement('div');
		editorPreview.className = 'nelxstd-prt-editor-preview nelxstd-prt-driver-popover';
		editorPreview.setAttribute('aria-hidden', 'true');

		title = document.createElement('div');
		title.className = 'driver-popover-title';

		description = document.createElement('div');
		description.className = 'driver-popover-description';

		skip = document.createElement('button');
		skip.type = 'button';
		skip.className = 'driver-popover-close-btn';
		skip.textContent = text('skip');

		footer = document.createElement('div');
		footer.className = 'driver-popover-footer';

		progress = document.createElement('span');
		progress.className = 'driver-popover-progress-text';

		navigation = document.createElement('div');
		navigation.className = 'driver-popover-navigation-btns';

		back = document.createElement('button');
		back.type = 'button';
		back.className = 'driver-popover-prev-btn driver-popover-footer-btn';
		back.textContent = text('back');

		next = document.createElement('button');
		next.type = 'button';
		next.className = 'driver-popover-next-btn driver-popover-footer-btn';
		next.textContent = text('next');

		navigation.appendChild(back);
		navigation.appendChild(next);
		footer.appendChild(progress);
		footer.appendChild(navigation);
		editorPreview.appendChild(title);
		editorPreview.appendChild(description);
		editorPreview.appendChild(footer);
		editorPreview.appendChild(skip);
		document.body.appendChild(editorPreview);

		return editorPreview;
	}

	function destroyEditorPreview() {
		if (editorPreview && editorPreview.parentNode) {
			editorPreview.parentNode.removeChild(editorPreview);
		}

		editorPreview = null;
		applyGroupStyles(tourGroups[0]);
	}

	function renderEditorPreview(source, settings) {
		var preview;
		var styles;
		var title;
		var description;
		var progress;

		if (!config.isEditor || (!source && !settings)) {
			destroyEditorPreview();
			return;
		}

		preview = ensureEditorPreview();
		styles = settings ? buildEditorPreviewStyles(settings) : parseStyles(source);
		title = settings ? editorSetting(settings, 'nelxstd_prt_step_title', text('Tour step')) : source.getAttribute('data-nelxstd-prt-title') || text('Tour step');
		description = settings ? editorSetting(settings, 'nelxstd_prt_step_description', '') : source.getAttribute('data-nelxstd-prt-description') || '';
		progress = source ? getPreviewProgress(source) : { current: 1, total: 1 };

		preview.querySelector('.driver-popover-title').textContent = title || text('Tour step');
		preview.querySelector('.driver-popover-description').textContent = description || '';
		preview.querySelector('.driver-popover-progress-text').textContent = text('stepProgress').replace('%1$s', progress.current).replace('%2$s', progress.total);
		applyTourStyles(styles);
		applyNavigationPosition(preview, styles.navigation_position);
	}

	function syncEditorPreview(view) {
		if (!config.isEditor) {
			return;
		}

		var container = getEditorCurrentContainer(view);
		var settings = getEditorSettings(container);
		var elementId = getEditorElementId(container);
		var source = getPreviewSourceForElement(elementId);

		if (settings) {
			if ('yes' === settings.nelxstd_prt_enable_step && 'yes' === settings.nelxstd_prt_show_card_preview) {
				renderEditorPreview(source, settings);
			} else {
				destroyEditorPreview();
			}
			return;
		}

		if (source) {
			renderEditorPreview(source);
			return;
		}

		destroyEditorPreview();
	}

	function scheduleEditorPreviewSync(view) {
		if (editorPreviewRaf) {
			window.cancelAnimationFrame(editorPreviewRaf);
		}

		editorPreviewRaf = window.requestAnimationFrame(function () {
			editorPreviewRaf = null;
			syncEditorPreview(view);
		});
	}

	function bindEditorPreview() {
		var editor = window.parent && window.parent.elementor ? window.parent.elementor : null;
		var channel = editor && editor.channels ? editor.channels.editor : null;

		if (!config.isEditor || !channel || channel.__nelxstdPrtBound) {
			return;
		}

		channel.__nelxstdPrtBound = true;
		channel.on('change', function (view) {
			scheduleEditorPreviewSync(view);
		});
	}

	function applyGroupStyles(group) {
		if (group && group.steps && group.steps.length) {
			applyTourStyles(group.steps[0].styles || {});
		}
	}

	function isLauncherDismissed() {
		if (launcherDismissedForSession) {
			return true;
		}

		if (config.isEditor) {
			return false;
		}

		try {
			return window.localStorage.getItem(getLauncherDismissedKey()) === '1';
		} catch (error) {
			return false;
		}
	}

	function dismissLauncher() {
		var shell = document.querySelector('.nelxstd-prt-launcher-shell');

		launcherDismissedForSession = true;

		if (!config.isEditor) {
			try {
				window.localStorage.setItem(getLauncherDismissedKey(), '1');
			} catch (error) {}
		}

		if (shell) {
			shell.remove();
		}
	}

	function createLauncher() {
		var shell;
		var button;
		var closeButton;

		if (!tourGroups.length || document.querySelector('.nelxstd-prt-launcher-shell') || isLauncherDismissed()) {
			return;
		}

		shell = document.createElement('div');
		shell.className = 'nelxstd-prt-launcher-shell';

		button = document.createElement('button');
		button.type = 'button';
		button.className = 'nelxstd-prt-launcher';
		button.setAttribute('aria-label', config.isEditor ? text('previewTour') : text('replayTour'));
		button.textContent = config.isEditor ? text('previewTour') : allToursCompleted() ? text('replayTour') : text('takeTour');
		button.addEventListener('click', function () {
			startTour(getFirstTour() || tourGroups[0]);
		});

		closeButton = document.createElement('button');
		closeButton.type = 'button';
		closeButton.className = 'nelxstd-prt-launcher-close';
		closeButton.setAttribute('aria-label', text('close'));
		closeButton.title = text('close');
		closeButton.textContent = '×';
		closeButton.addEventListener('click', function (event) {
			event.preventDefault();
			event.stopPropagation();
			dismissLauncher();
		});

		shell.appendChild(button);
		shell.appendChild(closeButton);
		document.body.appendChild(shell);

		applyGroupStyles(tourGroups[0]);
	}

	function allToursCompleted() {
		return tourGroups.every(function (group) {
			return isCompleted(group.id);
		});
	}

	function getFirstTour() {
		if (config.isEditor) {
			return tourGroups[0];
		}

		return (
			tourGroups.find(function (group) {
				return !isCompleted(group.id);
			}) || tourGroups[0]
		);
	}

	function startTour(group) {
		var driverFactory = window.driver && window.driver.js && window.driver.js.driver;

		if (!group || !driverFactory) {
			return;
		}

		if (activeTour && activeTour.isActive && activeTour.isActive()) {
			activeTour.destroy();
		}

		applyGroupStyles(group);

		activeTour = driverFactory({
			animate: true,
			allowClose: true,
			allowKeyboardControl: true,
			disableActiveInteraction: false,
			doneBtnText: text('done'),
			nextBtnText: text('next'),
			prevBtnText: text('back'),
			overlayColor: (config.colors && config.colors.dark) || '#272626',
			overlayOpacity: 0.55,
			popoverClass: 'nelxstd-prt-driver-popover',
			popoverOffset: 14,
			progressText: getProgressText(),
			showButtons: ['next', 'previous', 'close'],
			showProgress: true,
			smoothScroll: true,
			stagePadding: 8,
			stageRadius: 14,
			steps: group.steps.map(function (step) {
				var popover = {
					title: step.title,
					description: step.description,
					onPopoverRender: function (renderedPopover) {
						applyTourStyles(step.styles || {});
						applyNavigationPosition(renderedPopover, (step.styles || {}).navigation_position);
						decoratePopover(renderedPopover);
					},
				};

				if (step.placement && step.placement !== 'auto') {
					popover.side = step.placement;
				}

				return {
					element: step.target,
					popover: popover,
				};
			}),
			onDestroyed: function () {
				applyGroupStyles(group);

				if (!config.isEditor) {
					markComplete(group.id);
				}
				updateLauncher();
			},
		});

		activeTour.drive();
	}

	function decoratePopover(popover) {
		if (popover.closeButton) {
			popover.closeButton.textContent = text('skip');
			popover.closeButton.setAttribute('aria-label', text('skip'));
		}
	}

	function getProgressText() {
		return text('stepProgress').replace('%1$s', '{{current}}').replace('%2$s', '{{total}}');
	}

	function updateLauncher() {
		var button = document.querySelector('.nelxstd-prt-launcher');
		var label;

		if (button) {
			label = config.isEditor ? text('previewTour') : allToursCompleted() ? text('replayTour') : text('takeTour');
			button.textContent = label;
			button.setAttribute('aria-label', label);
		}
	}

	function bindLaunchers() {
		document.querySelectorAll('[data-nelxstd-prt-launch-tour]').forEach(function (launcher) {
			if (launcher.getAttribute('data-nelxstd-prt-bound') === '1') {
				return;
			}

			launcher.setAttribute('data-nelxstd-prt-bound', '1');
			launcher.addEventListener('click', function () {
				var tourId = normalizeTourId(launcher.getAttribute('data-nelxstd-prt-launch-tour'));
				var group =
					tourGroups.find(function (item) {
						return item.id === tourId;
					}) || getFirstTour();

				startTour(group);
			});
		});
	}

	function text(key) {
		return (config.i18n && config.i18n[key]) || key;
	}

	window.addEventListener('resize', repositionActiveOutsideNavigation, { passive: true });
	window.addEventListener('scroll', repositionActiveOutsideNavigation, { passive: true, capture: true });

	function init() {
		if (!config.isLoggedIn && !config.isEditor) {
			return;
		}

		collectTours();
		createLauncher();
		bindLaunchers();
		if (config.isEditor) {
			bindEditorPreview();
			syncEditorPreview();
		}

		if (!config.isEditor && tourGroups.length && getFirstTour() && !isCompleted(getFirstTour().id)) {
			window.setTimeout(function () {
				startTour(getFirstTour());
			}, 800);
		}
	}

	ready(init);

	if (window.elementorFrontend && window.elementorFrontend.hooks) {
		window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function () {
			window.setTimeout(function () {
				collectTours();
				createLauncher();
				bindLaunchers();
				if (config.isEditor) {
					bindEditorPreview();
					syncEditorPreview();
				}
				updateLauncher();
			}, 150);
		});
	}
})(window, document);
