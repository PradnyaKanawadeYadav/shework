(function ($) {
	'use strict';

	// Variable globale renseignée si des modifications dans la page de paramétrages ont été faites
	let saveBeforeExit = false;

	/**
	 * Événement sur la checkbox global des composants avancés
	 * Change la valeur de tous les checkbox
	 */
	$('#all-advanced').on('click', function () {
		if ($(this).prop('checked') === true) {
			$('.eac-elements__common-item.widgets.advanced input').prop('checked', 1);
		} else if ($(this).prop('checked') === false) {
			$('.eac-elements__common-item.widgets.advanced input').prop('checked', 0);
		}
		$('#eac-elements-saved').css('display', 'none');
		$('#eac-elements-notsaved').css('display', 'none');
	});

	/**
	 * Événement sur la checkbox global des composants communs
	 * Change la valeur de tous les checkbox
	 */
	$('#all-components').on('click', function () {
		if ($(this).prop('checked') === true) {
			$('.eac-elements__common-item.widgets.common input').prop('checked', 1);
		} else if ($(this).prop('checked') === false) {
			$('.eac-elements__common-item.widgets.common input').prop('checked', 0);
		}
		$('#eac-elements-saved').css('display', 'none');
		$('#eac-elements-notsaved').css('display', 'none');
	});

	/**
	 * Événement sur la checkbox global des composants Header & Footer
	 * Change la valeur de tous les checkbox
	 */
	$('#all-ehf').on('click', function () {
		if ($(this).prop('checked') === true) {
			$('.eac-elements__common-item.widgets.ehf input').prop('checked', 1);
		} else if ($(this).prop('checked') === false) {
			$('.eac-elements__common-item.widgets.ehf input').prop('checked', 0);
		}
		$('#eac-elements-saved').css('display', 'none');
		$('#eac-elements-notsaved').css('display', 'none');
	});

	/**
	 * Événement sur la checkbox global des fonctionnalités avancés
	 * Change la valeur de tous les checkbox
	 */
	$('#all-features-advanced').on('click', function () {
		if ($(this).prop('checked') === true) {
			$('.eac-elements__common-item.features.advanced input').prop('checked', 1);
		} else if ($(this).prop('checked') === false) {
			$('.eac-elements__common-item.features.advanced input').prop('checked', 0);
		}
		$('#eac-elements-saved').css('display', 'none');
		$('#eac-elements-notsaved').css('display', 'none');
	});

	/**
	 * Événement sur la checkbox global des fonctionnalités communes
	 * Change la valeur de tous les checkbox
	 */
	$('#all-features-common').on('click', function () {
		if ($(this).prop('checked') === true) {
			$('.eac-elements__common-item.features.common input').prop('checked', 1);
		} else if ($(this).prop('checked') === false) {
			$('.eac-elements__common-item.features.common input').prop('checked', 0);
		}
		$('#eac-elements-saved').css('display', 'none');
		$('#eac-elements-notsaved').css('display', 'none');
	});

	/** Affiche un message sous le bouton 'submit' du formulaire lorsqu'un élément a été modifié */
	const $formElements = $('form#eac-form-elements');
	const formElementsSerialize = $formElements.serialize();
	$('form#eac-form-elements :input').on('change', (evt) => {
		//const $headerInput = jQuery(evt.currentTarget).closest('.eac-elements__common-item').prevAll('.eac-elements__table-common.header').find('input');
		//$headerInput.prop('checked', 1);
		if ($formElements.serialize() !== formElementsSerialize) {
			$('#eac-form-elements #eac-sumit').css('background-color', 'red');
			saveBeforeExit = true;
		} else {
			$('#eac-form-elements #eac-sumit').css('background-color', 'rgb(55,177,55)');
			saveBeforeExit = false;
		}
	});

	/** Affiche un message sous le bouton 'submit' du formulaire WC intégration lorsqu'un élément a été modifié */
	const $formWcIntegration = $('form#eac-form-wc-integration');
	const formWcIntegrationSerialize = $formWcIntegration.serialize();
	$('form#eac-form-wc-integration :input').on('change', () => {
		if ($formWcIntegration.serialize() !== formWcIntegrationSerialize) {
			$('#eac-form-wc-integration #eac-sumit').css('background-color', 'red');
			saveBeforeExit = true;
		} else {
			$('#eac-form-wc-integration #eac-sumit').css('background-color', 'rgb(55,177,55)');
			saveBeforeExit = false;
		}
	});

	/**
	 * Le formulaire des options générales est soumis
	 * serialize et retourne les données
	 */
	$('form#eac-form-elements').on('submit', (evt) => {
		evt.preventDefault();
		$.ajax({
			url: settingsElements.ajax_url,
			type: 'post',
			data: {
				action: settingsElements.ajax_action,
				nonce: settingsElements.ajax_nonce,
				fields: $('form#eac-form-elements input').serialize(),
			},
		}).done(function (response) {
			// Sytématiquement qu'il y est erreur ou pas
			saveBeforeExit = false;
			if (response.success === false) {
				$('#eac-elements-notsaved').html(response.data);
				$('#eac-elements-notsaved').css('display', 'block');
			} else {
				$('#eac-elements-saved').html(response.data);
				$('#eac-elements-saved').css('display', 'block');
				setTimeout(() => { window.location.reload(true); }, 250);
			}
		});
	});

	/**
	 * Le formulaire des options de l'intégration WC est soumis
	 * serialize et retourne les données
	 * @since 2.0.1
	 */
	$('form#eac-form-wc-integration').on('submit', (evt) => {
		evt.preventDefault();
		$.ajax({
			url: wcintegration.ajax_url,
			type: 'post',
			data: {
				action: wcintegration.ajax_action,
				nonce: wcintegration.ajax_nonce,
				fields: $("#eac-form-wc-integration").serialize(),
			},
		}).done(function (response) {
			// Sytématiquement qu'il y est erreur ou pas
			saveBeforeExit = false;
			if (response.success === false) {
				$('#eac-wc-integration-notsaved').html(response.data);
				$('#eac-wc-integration-notsaved').css('display', 'block');
			} else {
				$('#eac-wc-integration-saved').html(response.data);
				$('#eac-wc-integration-saved').css('display', 'block');
				setTimeout(() => { window.location.reload(true); }, 250);
			}
		});
	});

	// Teste si des modifications ont été faites avant de sortir de la page de paramétrages
	$(window).on('beforeunload', (evt) => {
		const queryString = window.location.search;
		const urlParams = new URLSearchParams(queryString);
		const page = urlParams.has('page') ? decodeURIComponent(urlParams.get('page')) : false;
		if (page && page === 'eac-components' && saveBeforeExit) {
			const confirmationMessage = '\\o/';
			(evt || window.event).returnValue = confirmationMessage; // Gecko + IE
			return confirmationMessage;
		}
	});

	/**
	 * Événement sur le la liste des pages de l'onglet WC intégration
	 * Cache les DIVs qui ont besoin de l'URL de le page produits
	 * @since 2.0.1
	 */
	$('form#eac-form-wc-integration #wc_product_select_page').on('change', function () {
		if (this.value === '') {
			$('.eac-elements__common-item.redirect').addClass('hide');
			$('.eac-elements__common-item.breadcrumb').addClass('hide');
			$('.eac-elements__common-item.metas').addClass('hide');
			$('.eac-elements__common-item.pages').addClass('hide');
		} else {
			$('.eac-elements__common-item.redirect').removeClass('hide');
			$('.eac-elements__common-item.breadcrumb').removeClass('hide');
			$('.eac-elements__common-item.metas').removeClass('hide');
			if ($('#wc_product_catalog').prop('checked') === true) {
				$('.eac-elements__common-item.pages').removeClass('hide');
			}
		}
	});

	/**
	 * Événement sur la checkbox 'catalog' de l'onglet WC intégration
	 * Cache les DIVs qui ont besoin que la checkbox soit active
	 * @since 2.0.1
	 */
	$('form#eac-form-wc-integration #wc_product_catalog').on('click', function () {
		if ($(this).prop('checked') === true) {
			$('.eac-elements__common-item.request').removeClass('hide');
			if ($('#wc_product_select_page').val() !== '') {
				$('.eac-elements__common-item.pages').removeClass('hide');
			}
		} else {
			$('.eac-elements__common-item.request').addClass('hide');
			$('.eac-elements__common-item.pages').addClass('hide');
		}
	});

	/**
	 * Gestion des events des onglets
	 *
	 *	Header
	 *	tabs-nav
	 *		li href=tab-1 tab-active
	 *		li href=tab-2
	 *		li href=tab-3
	 *		li href=tab-4
	 *		li href=tab-5
	 *		li href=tab-6
	 *	tabs-stage
	 *		form
	 *			div id=tab-1
	 *			div id=tab-2
	 *			div id=tab-3
	 *			div id=tab-4
	 *			div id=tab-5
	 *			div class=eac-saving-box
	 *		form
	 *			div id=tab-6
	 *			div class=eac-saving-box
	 *      div id=tab-7 // Info générale pas dans un formulaire
	 *
	 * Pas de function flêche pour les onglets on perd le this de l'onglet
	 * Avec une fonction flêche le this est global
	 */
	$('.tabs-nav a').on('click', function (evt) {
		evt.preventDefault();

		$('.tab-active').removeClass('tab-active');
		$(this).parent().addClass('tab-active');

		// Cache toutes les div dans les formulaires et l'onglet Info système
		$('.tabs-stage > form > div').css('display', 'none');
		$('.tabs-stage #tab-7').css('display', 'none');

		// La valeur de l'attribut href de l'onglet de navigation correspond à l'ID de la div à afficher
		$($(this).attr('href')).css('display', 'block');

		// Affiche le bouton de sauvegarde des réglages par le formulaire, parent de la div affichée si pas l'onglet Info système
		if ('#tab-7' !== $(this).attr('href')) {
			$($(this).attr('href')).parent().find('.eac-saving-box').css('display', 'block');
		}

		// Cache les infos sauvé, non sauvé
		$('#eac-elements-saved').css('display', 'none');
		$('#eac-elements-notsaved').css('display', 'none');
	});

	$('.tabs-nav a:first').trigger('click'); // Default premier onglet

	/**
	 * Initalise la boîte de dialogue slug: acf-json
	 * @since 1.8.7
	 */
	$('#eac-dialog_acf-json').dialog({
		title: 'ACF JSON',
		dialogClass: 'wp-dialog',
		autoOpen: false,
		draggable: false,
		width: '640px',
		modal: true,
		resizable: false,
		closeOnEscape: true,
		position: {
			my: "center",
			at: "center",
			of: window
		},
		open: function () {
			// close dialog by clicking the overlay behind it
			$('.ui-widget-overlay').bind('click', function () {
				$('#eac-dialog_acf-json').dialog('close');
			});
		},
		create: function () {
			// style fix for WordPress admin
			$('.ui-dialog-titlebar-close').addClass('ui-button');
		},
	});

	// bind de l'icone '?' pour ouvrir la boîte de dialogue acf-json
	$('a span.acf-json').on('click', (evt) => {
		evt.preventDefault();
		$('#eac-dialog_acf-json').dialog('open');
	});

	/**
	 * Initalise la boîte de dialogue slug: grant-option-page
	 * @since 1.9.6
	 */
	$('#eac-dialog_grant-option').dialog({
		title: 'Grant Access Options Page',
		dialogClass: 'wp-dialog',
		autoOpen: false,
		draggable: false,
		width: '640px',
		modal: true,
		resizable: false,
		closeOnEscape: true,
		position: {
			my: "center",
			at: "center",
			of: window
		},
		open: function () {
			// close dialog by clicking the overlay behind it
			$('.ui-widget-overlay').bind('click', function () {
				$('#eac-dialog_grant-option').dialog('close');
			});
		},
		create: function () {
			// style fix for WordPress admin
			$('.ui-dialog-titlebar-close').addClass('ui-button');
		},
	});

	// bind de l'icone '?' pour ouvrir la boîte de dialogue grant-option-page
	$('a span.grant-option-page').on('click', (evt) => {
		evt.preventDefault();
		$('#eac-dialog_grant-option').dialog('open');
	});

	/**
	 * Initalise la boîte de dialogue slug: unfiltered-medias
	 * @since 2.1.1
	 */
	$('#eac-dialog_grant-medias').dialog({
		title: 'Grant Upload JSON',
		dialogClass: 'wp-dialog',
		autoOpen: false,
		draggable: false,
		width: '640px',
		modal: true,
		resizable: false,
		closeOnEscape: true,
		position: {
			my: "center",
			at: "center",
			of: window
		},
		open: function () {
			// close dialog by clicking the overlay behind it
			$('.ui-widget-overlay').bind('click', function () {
				$('#eac-dialog_grant-medias').dialog('close');
			});
		},
		create: function () {
			// style fix for WordPress admin
			$('.ui-dialog-titlebar-close').addClass('ui-button');
		},
	});

	/** bind de l'icone '?' pour ouvrir la boîte de dialogue unfiltered-medias */
	$('a span.grant-medias-upload').on('click', (evt) => {
		evt.preventDefault();
		$('#eac-dialog_grant-medias').dialog('open');
	});

	/**
	 * Événement click sur le nombre d'occurrences des composants dans le page de paramétrage du plugin
	 * @since 2.2.6
	 */
	$('span.eac-elements__item-count').on('click', (evt) => {
		evt.preventDefault();
		const countTitle = $(evt.currentTarget).prev().html();
		const permas = $(evt.currentTarget).attr('data-perma').split(',');
		const nombre = parseInt($(evt.currentTarget).text());
		let contentUrl = [];
		if ((permas && permas.length === 0) || nombre === 0) {
			return;
		}

		$.each(permas, (index, perma) => {
			contentUrl.push('<div><a href="' + perma + '" target="_autre" rel="noopener noreferrer">' + perma + '</a></div>');
		});

		$.fancybox.open([{
			type: 'ajax',
			src: elementsCount.ajax_content,
			opts: {
				smallBtn: true,
				buttons: [''],
				toolbar: false,
				width: 680,
				height: 500,
				afterLoad: function (instance, current) {
					const $divTitle = current.$content.find('.eac-elements-count-title');
					const $divContent = current.$content;
					$divTitle.append(countTitle + '</br></br>');
					$divContent.append(contentUrl);
				},
			}
		}]);
	});

	/**
	 * Initalise la boîte de dialogue slug: widget-count
	 * @since 2.2.6
	 */
	$('#eac-dialog_elements-help').dialog({
		title: 'Element usage',
		dialogClass: 'wp-dialog',
		autoOpen: false,
		draggable: false,
		width: '640px',
		modal: true,
		resizable: false,
		closeOnEscape: true,
		position: {
			my: "center",
			at: "center",
			of: window
		},
		open: function () {
			// close dialog by clicking the overlay behind it
			$('.ui-widget-overlay').bind('click', function () {
				$('#eac-dialog_elements-help').dialog('close');
			});
		},
		create: function () {
			// style fix for WordPress admin
			$('.ui-dialog-titlebar-close').addClass('ui-button');
		},
	});

	/** bind de l'icone '?' pour ouvrir la boîte de dialogue elements usage */
	$('a span.grant-elements-usage').on('click', (evt) => {
		evt.preventDefault();
		$('#eac-dialog_elements-help').dialog('open');
	});

	/**
	 * Initalise la boîte de dialogue slug: html-widget
	 * @since 2.2.9
	 */
	$('#eac-dialog_html-widget').dialog({
		title: 'Elementor HTML',
		dialogClass: 'wp-dialog',
		autoOpen: false,
		draggable: false,
		width: '640px',
		modal: true,
		resizable: false,
		closeOnEscape: true,
		position: {
			my: "center",
			at: "center",
			of: window
		},
		open: function () {
			// close dialog by clicking the overlay behind it
			$('.ui-widget-overlay').bind('click', function () {
				$('#eac-dialog_html-widget').dialog('close');
			});
		},
		create: function () {
			// style fix for WordPress admin
			$('.ui-dialog-titlebar-close').addClass('ui-button');
		},
	});

	// bind de l'icone '?' pour ouvrir la boîte de dialogue html-widget
	$('a span.html-widget').on('click', (evt) => {
		evt.preventDefault();
		$('#eac-dialog_html-widget').dialog('open');
	});

})(jQuery);
