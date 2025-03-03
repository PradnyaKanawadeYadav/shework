
/**
 * Description: Cette méthode est déclenchée lorsque la section 'eac-addon-lecteur-rss' est chargée dans la page
 *
 * @param {selector} $element. Le contenu de la section
 * @since 1.0.0
 */
import { EacReadTheFeed, setGridItemsGlobalLink } from '../modules/eac-modules.js';

class widgetRssReader extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                targetInstance: '.eac-rss-galerie',
                targetSelect: '.select__options-items',
                targetButton: '#rss__read-button',
                targetHeader: '.rss-item__header',
                targetLoader: '#rss__loader-wheel',
                targetNonce: '#rss_nonce',
                target: '.rss-galerie',
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        return {
            $targetInstance: this.$element.find(selectors.targetInstance),
            $targetSelect: this.$element.find(selectors.targetSelect),
            $targetButton: this.$element.find(selectors.targetButton),
            $targetHeader: this.$element.find(selectors.targetHeader),
            $targetLoader: this.$element.find(selectors.targetLoader),
            targetNonce: this.$element.find(selectors.targetNonce).val(),
            $target: this.$element.find(selectors.target),
            settings: this.$element.find(selectors.target).data('settings') || {},
            instanceAjax: null,
            is_ios: /(Macintosh|iPhone|iPod|iPad).*AppleWebKit.*Safari/i.test(navigator.userAgent),
            widgetId: this.$element.data('id'),
        };
    }

    onInit() {
        super.onInit();

        // Première valeur de la liste par défaut
        this.elements.$targetSelect.find('option:first').attr('selected', 'selected');
    }

    bindEvents() {
        if (Object.keys(this.elements.settings).length > 0 && !elementorFrontend.isEditMode()) {
            this.elements.$targetSelect.on('change', this.onSelectChange.bind(this));
            this.elements.$targetButton.on('click touch', this.onTriggerButton.bind(this));
            this.elements.$targetInstance.on('keydown', this.onTriggerKeyboard.bind(this));
            jQuery(document).on('ajaxComplete', this.ajaxQueryComplete.bind(this));
        }
    }

    removeEmojis(myString) {
		if (!myString) { return ''; }
		return myString.replace(/([#0-9]\u20E3)|[\xA9\xAE\u203C\u2047-\u2049\u2122\u2139\u3030\u303D\u3297\u3299][\uFE00-\uFEFF]?|[\u2190-\u21FF][\uFE00-\uFEFF]?|[\u2300-\u23FF][\uFE00-\uFEFF]?|[\u2460-\u24FF][\uFE00-\uFEFF]?|[\u25A0-\u25FF][\uFE00-\uFEFF]?|[\u2600-\u27BF][\uFE00-\uFEFF]?|[\u2900-\u297F][\uFE00-\uFEFF]?|[\u2B00-\u2BF0][\uFE00-\uFEFF]?|(?:\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDEFF])[\uFE00-\uFEFF]?/g, '');
    }

    onSelectChange(evt) {
        evt.preventDefault();
        this.elements.$targetLoader.hide();
        this.elements.$target.attr('aria-busy', 'false');
        this.elements.$targetButton.attr('aria-expanded', 'false');
        this.elements.$target.empty();
        this.elements.$targetHeader.html('');
    }

    onTriggerButton(evt) {
        evt.preventDefault();
        this.elements.$targetButton.attr('aria-expanded', 'true');
        this.elements.$target.empty();
        this.elements.$targetHeader.html('');

        /** Initialisation de l'objet Ajax avec l'url du flux, du nonce et de l'ID du composant */
        this.elements.instanceAjax = new EacReadTheFeed(
            jQuery('.select__options-items option:selected', this.elements.$targetInstance).val().replace(/\s+/g, ''),
            this.elements.targetNonce,
            this.elements.settings.data_id
        );
        this.elements.$targetLoader.show();
        this.elements.$target.attr('aria-busy', 'true');
    }

    onTriggerKeyboard(evt) {
        const id = evt.code || evt.key || 0;
        if (!this.elements.$target.is(':empty')) {
            if ('Escape' === id) {
                evt.preventDefault();
                this.elements.$targetButton.attr('aria-expanded', 'false');
                this.elements.$target.empty();
                this.elements.$targetHeader.html('');
                this.elements.$targetSelect.trigger('focus');
            } else if ('End' === id) {
                evt.preventDefault();
                const $endArticle = this.elements.$target.find('article').find('a').last();
                $endArticle.trigger('focus');
            } else if ('Home' === id) {
                evt.preventDefault();
                const $homeArticle = this.elements.$target.find('article').find('a').first();
                $homeArticle.trigger('focus');
            }
        }
    }

    ajaxQueryComplete(event, xhr, ajaxSettings) {
        if (this.elements.instanceAjax !== null && ajaxSettings.ajaxOptions && ajaxSettings.ajaxOptions === this.elements.instanceAjax.getOptions()) { // Le même random number généré lors de la création de l'objet Ajax
            event.stopImmediatePropagation();
            this.elements.$targetLoader.hide();
            this.elements.$target.attr('aria-busy', 'false');

            // Les items à afficher
            const allItems = this.elements.instanceAjax.getItems();

            // Une erreur Ajax ??
            if (allItems.headError) {
                this.elements.$targetHeader.html('<span style="text-align:center; word-break:break-word;"><p>' + allItems.headError + '</p></span>');
                return false;
            }

            // Pas d'item
            if (!allItems.rss) {
                this.elements.$targetHeader.html('<span style="text-align: center">Nothing to display</span>');
                return false;
            }

            const Items = allItems.rss;
            const Profile = allItems.profile;
            const $wrapperHeadContent = jQuery('<div/>', { class: 'rss-item__header-content' });

            if (Profile.headLogo) {
                this.elements.$targetHeader.append('<div class="rss-item__header-img"><a href="' + Profile.headLink + '" aria-label="View RSS feed provider ' + Profile.headTitle + '"><img src="' + Profile.headLogo + '" alt="' + Profile.headTitle + '"></a></div>');
            }
            $wrapperHeadContent.append('<span><a href="' + Profile.headLink + '" aria-label="View RSS feed provider ' + Profile.headTitle + '"><h2><span dir="ltr">' + Profile.headTitle.substring(0, 27) + '...</span></h2></a></span>');
            $wrapperHeadContent.append('<span>' + Profile.headDescription + '</span>');
            this.elements.$targetHeader.append($wrapperHeadContent);

            const picto = this.getElementSettings('button_more_picto');
            let renderIcon = '';
            if (picto) {
                if (elementorFrontend.config.experimentalFeatures.e_font_icon_svg && !elementorFrontend.isEditMode()) {
                    const renderedIcon = typeof picto.rendered_tag !== 'undefined' ? picto.rendered_tag : '';
                    if (renderedIcon) {
                        renderIcon = renderedIcon.replace('<svg', '<svg aria-hidden="true"');
                    }
                } else {
                    renderIcon = picto.value ? `<i class="${picto.value}" aria-hidden='true'></i>` : '';
                }
            }

            // Parcours de tous les items à afficher
            jQuery.each(Items, (index, item) => {
                if (index >= this.elements.settings.data_nombre) { // Nombre d'item à afficher
                    return false;
                }

                const $wrapperItem = jQuery('<article/>', { class: 'rss-galerie__item' });
                let $wrapperContent;
                if (this.elements.settings.data_global_link) {
                    $wrapperContent = jQuery('<div/>', { class: 'rss-galerie__content global__card-link' });
                } else {
                    $wrapperContent = jQuery('<div/>', { class: 'rss-galerie__content' });
                }
                const $wrapperContentInner = jQuery('<div/>', { class: 'rss-galerie__content-inner' });

                /** Ajout du support de l'audio, de la vidéo et du PDF */
                if (item.img && this.elements.settings.data_img) {
                    let img = '';
                    let videoattr = '';
                    let classGlobalLink = '';
                    if (this.elements.settings.data_global_link) {
                        classGlobalLink = 'eac-accessible-link card-link';
                    } else {
                        classGlobalLink = 'eac-accessible-link';
                    }
                    const titreImg = jQuery.trim(item.title.replace(/"/g, ''));

                    if (item.img.match(/\.mp3|\.m4a/)) { // Flux mp3
                        img = '<div class="rss-galerie__item-image">' +
                            '<audio aria-label="Listen feed ' + titreImg + '" controls preload="none" src="' + item.img + '" type="audio/mp3"></audio>' +
                            '</div>';
                    } else if (item.img.match(/\.mp4|\.m4v/)) { // Flux mp4
                        videoattr = this.is_ios ? '<video aria-label="View feed video ' + titreImg + '" controls preload="metadata" type="video/mp4">' : '<video controls preload="none" type="video/mp4">';
                        img = '<div class="rss-galerie__item-image">' +
                            videoattr +
                            '<source src="' + item.img + '">' +
                            "Your browser doesn't support embedded videos" +
                            '</video>' +
                            '</div>';
                    } else if (item.img.match(/\.pdf/)) { // Fichier PDF
                        img = '<div class="rss-galerie__item-image" aria-label="View PDF file ' + titreImg + '">' +
                            '<a href="' + item.imgLink + '" data-elementor-open-lightbox="no" data-fancybox="rss-gallery" data-caption="' + titreImg + '">' +
                            '<i class="far fa-file-pdf" aria-hidden="true"></i></a></div>';
                    } else if (this.elements.settings.data_lightbox) { // Fancybox activée
                        img = '<div class="rss-galerie__item-image">' +
                            '<a href="' + item.imgLink + '" class="eac-accessible-link" aria-label="View feed image ' + titreImg + '" data-elementor-open-lightbox="no" data-fancybox="rss-gallery" data-caption="' + titreImg + '">' +
                            '<img class="img-focusable" src="' + item.img + '"></a></div>';
                    } else if (this.elements.settings.data_image_link) { // Lien de l'article sur l'image
                        img = '<div class="rss-galerie__item-image">' +
                            '<a href="' + item.lien + '" class="' + classGlobalLink + '" aria-label="View feed ' + titreImg + '">' +
                            '<img class="img-focusable" src="' + item.img + '"></a></div>';
                    } else {
                        img = '<div class="rss-galerie__item-image"><img src="' + item.img + '"></div>';
                    }
                    $wrapperContent.append(img);
                }

                // Ajout du titre
                if (this.elements.settings.data_title) {
                    item.title = this.removeEmojis(item.title);
                    item.title = item.title.split(' ', 12).join().replace(/,/g, ' ').replace(/["”“]+/g, '') + '...'; // Afficher 12 mots dans le titre
                    let titre = '';
                    if (this.elements.settings.data_title_link) {
                        let classGlobalLink = '';
                        if (this.elements.settings.data_global_link) {
                            classGlobalLink = 'eac-accessible-link card-link';
                        } else {
                            classGlobalLink = 'eac-accessible-link';
                        }

                        titre = '<div class="rss-galerie__item-link-post">' +
                            '<a href="' + item.lien + '" class="' + classGlobalLink + '" aria-label="View feed ' + item.title + '">' +
                            '<h2 class="rss-galerie__item-titre"><span dir="ltr">' + item.title + '</span></h2></a></div>';
                    } else {
                        titre = '<div class="rss-galerie__item-link-post"><h2 class="rss-galerie__item-titre"><span dir="ltr">' + item.title + '</span></h2></div>';
                    }
                    $wrapperContentInner.append(titre);
                }

                // Ajout du nombre de mots de la description
                if (this.elements.settings.data_excerpt) {
                    item.description = this.removeEmojis(item.description);
                    item.description = item.description.split(' ', this.elements.settings.data_excerpt_lenght).join().replace(/,/g, ' ').replace(/["”“]+/g, '') + '...';
                    // Ajout de la description
                    const description = '<div class="rss-galerie__item-description"><p><span dir="ltr">' + item.description + '</span></p></div>';
                    $wrapperContentInner.append(description);
                }

                // Ajout du bouton readmore
                if (this.elements.settings.data_readmore) {
                    let buttonLabel = '<span class="label-icon">' + this.elements.settings.data_readmore_label + '</span>';
                    if (renderIcon.length > 0) {
                        const icon = '<span class="button-icon eac-icon-svg">' + renderIcon + '</span>';
                        if ('before' === this.elements.settings.data_icon_pos) {
                            buttonLabel = icon + buttonLabel;
                        } else {
                            buttonLabel = buttonLabel + icon;
                        }
                    }
                    let classGlobalLink = '';
                    if (this.elements.settings.data_global_link) {
                        classGlobalLink = 'button-readmore card-link';
                    } else {
                        classGlobalLink = 'button-readmore';
                    }

                    const buttonReadmore = '<div class="buttons-wrapper">' +
                        '<a href="' + item.lien + '" class="' + classGlobalLink + '" aria-label="View feed ' + item.title + '">' +
                        '<span class="button__readmore-wrapper">' + buttonLabel + '</span>' +
                        '</a></div>';
                    $wrapperContentInner.append(buttonReadmore);
                }

                // Ajout de la date de publication/Auteur article
                if (this.elements.settings.data_date) {
                    const icon_user = '<svg aria-hidden="true" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M313.6 304c-28.7 0-42.5 16-89.6 16-47.1 0-60.8-16-89.6-16C60.2 304 0 364.2 0 438.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-25.6c0-74.2-60.2-134.4-134.4-134.4zM400 464H48v-25.6c0-47.6 38.8-86.4 86.4-86.4 14.6 0 38.3 16 89.6 16 51.7 0 74.9-16 89.6-16 47.6 0 86.4 38.8 86.4 86.4V464zM224 288c79.5 0 144-64.5 144-144S303.5 0 224 0 80 64.5 80 144s64.5 144 144 144zm0-240c52.9 0 96 43.1 96 96s-43.1 96-96 96-96-43.1-96-96 43.1-96 96-96z"></path></svg>';
                    const icon_calendar = '<svg aria-hidden="true" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M400 64h-48V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H160V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zm-6 400H54c-3.3 0-6-2.7-6-6V160h352v298c0 3.3-2.7 6-6 6z"></path></svg>';
                    const $wrapperMetas = jQuery('<div/>', { class: 'rss-galerie__item-metas' });
                    const dateUpdate = '<span class="rss-galerie__item-date eac-icon-svg">' + icon_calendar + new Date(item.update).toLocaleDateString(this.elements.settings.data_locale, { year: "numeric", month: "2-digit", day: "2-digit" }) + '</span>';
                    const Auteur = '<span class="rss-galerie__item-auteur eac-icon-svg">' + icon_user + item.author + '</span>';
                    $wrapperMetas.append(dateUpdate);
                    if (item.author) {
                        $wrapperMetas.append(Auteur);
                    }
                    $wrapperContentInner.append($wrapperMetas);
                }

                // Ajout dans les wrappers
                $wrapperContent.append($wrapperContentInner);
                $wrapperItem.append($wrapperContent);
                this.elements.$target.append($wrapperItem);
            });
            setTimeout(() => {
                jQuery('.rss-galerie__item', this.elements.$target).css({ transition: 'all 500ms linear', transform: 'scale(1)' });
            }, 200);
            if (this.elements.settings.data_global_link) {
                const $targetCards = this.elements.$target.find('.rss-galerie__content');
                setGridItemsGlobalLink($targetCards);
            }
        }
    }
}

/**
 * Description: La class est créer lorsque le composant 'eac-addon-lecteur-rss' est chargé dans la page
 *
 * @param elements
 * @since 2.1.0
 */
jQuery(window).on('elementor/frontend/init', () => {
    elementorFrontend.elementsHandler.attachHandler('eac-addon-lecteur-rss', widgetRssReader);
});
