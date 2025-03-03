
class EacAcfFieldGallery {

	/** Constructeur */
	constructor() {
		this.datasets = []; // key:la clé(field_xxx) du champ, name: le nom du champ, type: eac_gallery
		if (typeof acf !== 'undefined' && typeof acf.addAction !== 'undefined') {
			//acf.unload.active = false;
			this.addActions();
			this.bindEvents();
			this.setSortable();
		}
	}

	/**
	 * setSortable
	 *
	 * Applique la fonction de réorganisation des images de la Lib jQuery UI sortable
	 */
	setSortable() {
		jQuery('.eac-gallery__attachments').sortable({
			placeholder: 'ui-state-highlight',
			tolerance: 'pointer',
			//containment: 'parent',
			cursor: 'move',
			scrollSensitivity: 30,
		}).disableSelection();
	}

	/**
	 * addActions
	 *
	 * These two events are called when a field element is ready for initialisation.
	 * ready: on page load similar to jQuery(document).ready()
	 * append: on new DOM elements appended via repeater field or other AJAX calls
	 */
	addActions() {
		acf.addAction('ready_field/type=eac_gallery', this.initDatasets.bind(this));
		acf.addAction('append_field/type=eac_gallery', this.initDatasets.bind(this));
	}

	/**
	 * bindEvents
	 *
	 * Active les événements click sur les boutons
	 */
	bindEvents() {
		jQuery(document).on('click', '.eac-gallery__add-media', this.onClickAddMedia.bind(this));
		jQuery(document).on('click', '.eac-gallery__remove-media', this.onClickRemoveMedia);
		jQuery(document).on('click', '.eac-gallery__remove-all-media', this.onClickRemoveAllMedia);

		/**jQuery(window).on('beforeunload', (evt) => {
			var isSavingPost = wp.data.select('core/editor').isSavingPost();
			var isAutosavingPost = wp.data.select('core/editor').isAutosavingPost();
			var success = wp.data.select('core/editor').didPostSaveRequestSucceed();
			console.log('Saved=>' + isSavingPost + ':: Auto saved=>' + isAutosavingPost + ':: Success=>' + success);
		});*/
	}

	/**
	 * wpMediaLibrary
	 *
	 * Recharge et sélection des images dans la librairie des médias
	 *
	 * @param container Conteneur dans lequel les images seront affichées
	 * @param dataset Field: Key, Name et Type
	 * @return	n/a
	 */
	wpMediaLibrary(container, dataset) {
		const that = this;

		if (typeof wp.media !== 'undefined') {
			const frame = wp.media({
				title: 'Add Images',
				button: { text: 'Add Images' },
				multiple: 'add',
				library: {
					orderby: 'date',
					order: 'DESC',
					query: true,
					type: eacGalleryImage.mimesType,
				}
			});

			// Sélection des anciennes et nouvelles images
			frame.on('select', function () {
				const attachments = frame.state().get('selection').toJSON();
				if (!attachments) return;
				let attachmentIds = attachments.map(attachment => {
					return attachment.id;
				});

				let selectedImages = frame.state().get('selection').map(attachment => {
					attachment.toJSON();
					return attachment.attributes;
				});
				that.renderAttachments(selectedImages, container, dataset);

			});

			// Recharge les images existantes
			frame.on('open', function () {
				let selection = frame.state().get('selection');
				let IDs = [];
				jQuery(container).children().each(function () {
					let dataId = jQuery(this).data('id');
					IDs.push(Number(dataId));
				});

				if (IDs.length > 0) {
					IDs.forEach(function (id) {
						let attachment = wp.media.attachment(id);
						attachment.fetch();
						selection.add(attachment ? [attachment] : []);
					});
				}
			});

			frame.open();
		}
	}

	/**
	 * renderAttachments
	 *
	 * Affiche les images sélectionnées avec la boîte de dialogue des medias
	 *
	 * @return	n/a
	 */
	renderAttachments(attachments, container, dataset) {
		let $content = '';
		attachments.forEach(function (attachment) {
			let defaultIcon = attachment.icon;
			let thumbnail_class = 'eac-gallery__attachment-picto';
			if (attachment.sizes && attachment.sizes.medium) {
				defaultIcon = attachment.sizes.medium.url;
				thumbnail_class = 'eac-gallery__attachment-container';
			} else if (attachment.sizes && attachment.sizes.full) {
				defaultIcon = attachment.sizes.full.url;
				thumbnail_class = 'eac-gallery__attachment-container';
			}
			$content += `<div data-id="${attachment.id}" class="${thumbnail_class}">`;
			$content += `<img src="${defaultIcon}"/>`;
			$content += `<input id="${dataset.type}[${dataset.name}][]" type="hidden" value="${attachment.id}" name="${dataset.type}[${dataset.name}][]"/>`;
			$content += '</div>';
		});

		jQuery(container).html($content);
	}

	/**
	 * onClickAddMedia
	 *
	 * Event click sur le bouton 'Ajouter des images'
	 *
	 * @return	n/a
	 */
	onClickAddMedia(evt) {
		//const { currentTarget, target } = evt;
		evt.preventDefault();
		const container = jQuery(evt.currentTarget).closest('.eac-gallery__container').find('.eac-gallery__attachments');
		const key = container.attr('class').match(/eac-gallery__attachments-(\w+)/)[1];
		const dataset = this.datasets.find((x) => x.key === key);
		this.wpMediaLibrary(container, dataset);
	}

	/**
	 * onClickRemoveMedia
	 *
	 * Event click sur le bouton 'Poubelle'
	 *
	 * @return	n/a
	 */
	onClickRemoveMedia(evt) {
		//const { currentTarget, target } = evt;
		evt.preventDefault();
		const id = jQuery(evt.currentTarget).closest('.eac-gallery__attachment-container').data('id');
		if (id && confirm('Are you sure you want to remove this image ?')) {
			jQuery(`.eac-gallery__attachment-container-${id}`).remove();
		}
	}

	/**
	 * onClickRemoveAllMedia
	 *
	 * Event click sur le bouton de suppression de toutes les images
	 *
	 * @return	n/a
	 */
	onClickRemoveAllMedia(evt) {
		evt.preventDefault();
		const $attachs = jQuery('.eac-gallery__attachments');
		if ($attachs && $attachs.children().length > 0 && confirm('Are you sure you want to remove all images ?')) {
			jQuery('.eac-gallery__attachments').empty();
		}
	}

	/**
	 * initDatasets
	 *
	 * Initialisation de $field
	 *
	 * @return	n/a
	 */
	initDatasets($field) {
		const dataset = $field && $field.data;
		if (!dataset) return false;
		this.datasets.push(dataset);
	}
}
new EacAcfFieldGallery();
