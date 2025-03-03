(function ($) {
	'use strict';

	// Pu.... de gestion des font-size dans le theme Hueman
	if (jQuery().fitText) {
		//console.log('Events Window =>', jQuery._data(jQuery(window)[0], "events"));
		jQuery(':header').each(function () {
			jQuery(this).removeAttr('style');
			jQuery(window).off('resize.fittext orientationchange.fittext');
			jQuery(window).unbind('resize.fittext orientationchange.fittext');
		});
	}

	// Implémente le proto startsWith pour IE11
	if (!String.prototype.startsWith) {
		String.prototype.startsWith = function (searchString, position) {
			position = position || 0;
			return this.substring(position, searchString.length) === searchString;
		};
	}

	// Transforme la chaîne en slug, équivalent ~ à sanitize_title
	if (!String.prototype.toSlug) {
		String.prototype.toSlug = function () {
			let str = this;
			// Supprime les accents - diacritiques
			str = str.trim().normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
			str = str.replace(/[^a-z0-9\s-]/g, '') // supprime les caractères invalides
				.replace(/\s+/g, '-') // collapse whitespace and replace by a dash
				.replace(/-+/g, '-') // collapse dashes
				.replace(/^-+|-+$/g, ''); // supprime dashes début et fin
			return str;
		}
	}

	// Initialisation de la Fancybox
	if (jQuery.fancybox) {
		const language = window.navigator.userLanguage || window.navigator.language;
		const lng = language.split("-");
		const langFr = {
			fr: {
				CLOSE: "Fermer",
				NEXT: "Suivant",
				PREV: "Précédent",
				ERROR: "Le contenu ne peut être chargé. <br/> Essayer plus tard.",
				PLAY_START: "Lancer le diaporama",
				PLAY_STOP: "Diaporama sur pause",
				FULL_SCREEN: "Plein écran",
				THUMBS: "Miniatures",
				DOWNLOAD: "Télécharger",
				SHARE: "Partager",
				ZOOM: "Zoom"
			}
		};
		//jQuery.extend(jQuery.fancybox.defaults.i18n, langFr);
		jQuery.fancybox.defaults.lang = lng[0];
		jQuery.fancybox.defaults.idleTime = false;
		/*jQuery.fancybox.defaults.buttons = [
			"zoom",
			"slideShow",
			"thumbs",
			"close"
		];*/
	}

	//Enable/Disable mouse focus
	jQuery(document.body).on('mousedown keydown', function (evt) {
		if (evt.type === 'mousedown') {
			jQuery(document.body).addClass('eac-using-mouse');
		} else {
			jQuery(document.body).removeClass('eac-using-mouse');
		}
	});

	function triggerKeyDownToClickEvent(evt) {
		const id = evt.code || evt.key || 0;
		if ('Space' === id) {
			evt.preventDefault();
			const activeElement = document.activeElement;
			if (jQuery(activeElement).attr('href') !== '#' && !jQuery(activeElement).attr('data-fancybox')) {
				activeElement.dispatchEvent(new MouseEvent('click', { cancelable: true }));
			} else {
				jQuery(activeElement).trigger('click');
			}
		}
	}

	/** Evénement sur les boutons et les liens avec la touche Space pour l'accessibilité */
	jQuery(document.body).on('keydown', '.buttons-wrapper a.button-readmore, .buttons-wrapper a.button-cart, a.eac-accessible-link', triggerKeyDownToClickEvent);
	jQuery(document.body).on('keydown', '.mega-menu_nav-wrapper .mega-menu_top-link, .mega-menu_nav-wrapper .mega-menu_sub-link', triggerKeyDownToClickEvent);
	jQuery(document.body).on('keydown', '.sitemap-posts-list a, .swiper-pagination-bullet, #toctoc-body__list a, .eac-breadcrumbs-item a', triggerKeyDownToClickEvent);
	jQuery(document.body).on('keydown', '.woocommerce-mini-cart-item.mini_cart_item a, a.hst-hotspots__tooltip-link, .al-post__navigation-digit .page-numbers', triggerKeyDownToClickEvent);

	/** Les adresses e-mail obfusquées */
	const $obfuscatedMail = jQuery(document.body).find('a.eac-accessible-link.obfuscated-link');
	if ($obfuscatedMail.length > 0) {
		jQuery.each($obfuscatedMail, (index, item) => {
			const dataHref = jQuery(item).attr('data-link');
			const dataMask = '#actus.';
			const mailTo = 'mailto:';
			const newHref = dataHref.replace(dataMask, '@');
			jQuery(item).attr('href', mailTo + newHref);
			jQuery(item).removeAttr('data-link');
		});
	}

	/** Les numéro de téléphone obfusqués */
	const $obfuscatedTel = jQuery(document.body).find('a.eac-accessible-link.obfuscated-tel');
	if ($obfuscatedTel.length > 0) {
		jQuery.each($obfuscatedTel, (index, item) => {
			const dataHref = jQuery(item).attr('data-link');
			const dataMask = '#actus.';
			const telTo = 'tel:';
			const newHref = dataHref.replace(dataMask, '');
			jQuery(item).attr('href', telTo + newHref);
			jQuery(item).removeAttr('data-link');
		});
	}

})(jQuery);
