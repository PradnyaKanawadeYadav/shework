(function ($) {
	'use strict';

	jQuery(function () {
		/**
		 * Événement sur le bouton d'ouverture de la popup modale dans un item du menu
		 * @since 1.9.6
		 */
		jQuery('.menu-item_button').on('click', (evt) => {
			evt.preventDefault();
			// 'data-id' du bouton = post_id de l'article passé au contenu de 'eac-admin_popup-content.php'
			const menu_item_id = jQuery(evt.currentTarget).attr('data-id');
			const post_title = jQuery(evt.currentTarget).attr('data-title') + ' (' + menu_item_id + ')';

			if (menu_item_id) {
				/** Ouverture de la popup modale */
				jQuery.fancybox.open([{
					type: 'ajax',
					src: menu.ajax_content + menu_item_id,
					opts: {
						smallBtn: true,
						buttons: [''],
						toolbar: false,
						width: 680,
						height: 610,
						afterLoad: function (instance, current) {
							const $form = current.$content.find('form#eac-form_menu-settings');

							/** Ajout du Color Picker pour les champs 'badge' des menus */
							jQuery('.menu-item_badge-color-picker').wpColorPicker();
							jQuery('.menu-item_badge-background-picker').wpColorPicker();

							/** Ajout de Icon Picker pour le champ 'icone' des menus */
							jQuery('.menu-item_icon-picker').fontIconPicker({
								source: EacIconLists,
								emptyIcon: true,
								hasSearch: true,
								theme: 'fip-grey',
							});

							/** Ajoute le titre de l'article */
							jQuery('.eac-form_menu-post-title').text(post_title);

							$form.on('submit', (evt) => {
								evt.preventDefault();
								jQuery.ajax({
									url: menu.ajax_url,
									type: 'post',
									data: {
										action: menu.ajax_action,
										nonce: menu.ajax_nonce,
										fields: $form.serialize(),
									},
								}).done(function (response) {
									if (response.success === false) {
										jQuery('#eac-menu-notsaved').text(response.data);
										jQuery('#eac-menu-notsaved').css('display', 'block');
									} else {
										jQuery('#eac-menu-saved').text(response.data);
										jQuery('#eac-menu-saved').css('display', 'block');
									}

									setTimeout(() => {
										jQuery('#eac-menu-notsaved').css('display', 'none');
										jQuery('#eac-menu-saved').css('display', 'none');
									}, 2000);
								});
							});

							/** Ouverture et sélection de l'image de la librairie des medias */
							jQuery('.menu-item_image-add-button').on('click', (evt) => {
								evt.preventDefault();
								const image = wp.media({
									title: 'Select or Upload Image',
									multiple: false,
									button: { text: 'Use this media' },
									library: {
										orderby: 'date',
										query: true,
										type: 'image'
									}
								}).open()
									.on('select', (evt) => {
										const uploaded_image = image.state().get('selection').first();
										const image_url = uploaded_image.toJSON().url;
										jQuery('.menu-item_image-picker').val(image_url);
									});
							});

							/** Suppression de l'image */
							jQuery('.menu-item_image-remove-button').on('click', (evt) => {
								evt.preventDefault();
								jQuery('.menu-item_image-picker').val('');
							});
						},
						afterClose: function () {
							jQuery('#eac-form_menu-settings').off('submit');

							jQuery('.menu-item_badge')
								.add('.menu-item_badge-color-picker')
								.add('.menu-item_icon-picker')
								.add('.menu-item_badge-background-picker')
								.add('.menu-item_image-add-button')
								.add('.menu-item_image-remove-button')
								.off('click');
						},
					}
				}]);
			}
		});
	});

})(jQuery);
