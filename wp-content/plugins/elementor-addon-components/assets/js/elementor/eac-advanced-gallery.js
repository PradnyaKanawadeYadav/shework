/**
 * Description: Cette méthode est déclenchée lorsque le composant 'eac-addon-advanced-gallery' est chargé dans la page
 * Ref: * Elementor 3.1.0 https://developers.elementor.com/a-new-method-for-attaching-a-js-handler-to-an-element/
 * Justify mode: https://github.com/nk-o/flickr-justified-gallery
 * 
 * @param $element. Le contenu du widget
 * @since 2.2.0
 * @since 2.3.2 Global link pour chaque tiem
 * @since 2.3.3 Force la hauteur des items
 */
import { EacSwiper, setGridItemsGlobalLink } from '../modules/eac-modules.js';

class widgetAdvancedGallery extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                targetInstance: '.eac-advanced-gallery',
                target: '.advanced-gallery',
                targetSkipGrid: '.eac-skip-grid',
                targetCards: '.advanced-gallery__content-wrapper',
                imagesInstance: '.advanced-gallery__image-instance',
                itemsInstance: '.advanced-gallery__item',
                targetSizer: '.advanced-gallery__item-sizer',
                targetJustify: '.fj-gallery',
                filterWrapperLink: '.ag-filters__wrapper a',
                filterWrapperSelect: '.ag-filters__select',
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        let components = {
            $targetInstance: this.$element.find(selectors.targetInstance),
            $target: this.$element.find(selectors.target),
            $targetSkipGrid: this.$element.find(selectors.targetSkipGrid),
            $targetCards: this.$element.find(selectors.targetCards),
            $imagesInstance: this.$element.find(selectors.imagesInstance),
            $itemsInstance: this.$element.find(selectors.itemsInstance),
            $targetSizer: this.$element.find(selectors.targetSizer),
            $targetJustify: this.$element.find(selectors.targetJustify),
            filterWrapper: '.ag-filters__wrapper',
            $filterWrapperLink: this.$element.find(selectors.filterWrapperLink),
            filterItem: '.ag-filters__item',
            filterActive: 'ag-active',
            $filterWrapperSelect: this.$element.find(selectors.filterWrapperSelect),
            settings: this.$element.find(selectors.target).data('settings') || {},
            $targetId: null,
            isotopeInitialized: null,
            isotopeOptions: {
                itemSelector: '.advanced-gallery__item',
                percentPosition: true,
                masonry: {
                    columnWidth: '.advanced-gallery__item-sizer',
                },
                layoutMode: 'fitRows',
                sortBy: 'original-order',
                visibleStyle: { transform: 'scale(1)', opacity: 1 }, // Transition

            },
            justifyOptions: {
                itemSelector: '.fj-gallery-item',
                rowHeight: 250,
                rowHeightTolerance: 0,
                edgeCaseMinRowHeight: 1,
                gutter: 10,
                calculateItemsHeight: true
            },
            has_swiper: false,
        };
        components.$targetId = jQuery('#' + components.settings.data_id);
        components.isotopeOptions.layoutMode = components.settings.data_layout;
        components.justifyOptions.gutter = components.settings.data_gutter;
        components.justifyOptions.rowHeight = components.settings.data_rowheight;
        components.has_swiper = components.settings.data_sw_swiper || false;

        return components;
    }

    onInit() {
        super.onInit();

        if (this.elements.has_swiper) {
            this.setSwiperGallery();
        } else if ('justify' === this.elements.settings.data_layout) {
            window.setTimeout(() => { this.setLayoutModeJustify(); }, 100);
        } else {
            window.setTimeout(() => { this.setLayoutModeGrid(); }, 100);
        }
    }

    bindEvents() {
        if (!elementorFrontend.isEditMode()) {
            if (this.elements.$itemsInstance.length > 0) {
                this.elements.$targetInstance.on('keydown', (evt) => { this.setKeyboardEvents(evt); });

                if (this.elements.settings.data_overlay === 'overlay-in') {
                    this.elements.$itemsInstance.on('touchstart touchmove touchend', (evt) => {
                        evt.stopPropagation();
                    });
                }
            }

            if (!this.elements.has_swiper && this.elements.settings.data_filtre) {
                this.elements.$filterWrapperLink.on('click', (evt) => { this.onFilterGridClick(evt); });
                this.elements.$filterWrapperSelect.on('change', (evt) => { this.onFilterGridChange(evt); });
            }

            /** @since 2.3.2 */
            setGridItemsGlobalLink(this.elements.$targetCards);
        }

        /** Elementor nested tab trigger event resize */
        jQuery('.e-n-tab-title').on('click', () => {
            setTimeout(() => {
                window.dispatchEvent(new Event('resize'));
            }, 50);
        });

		jQuery(window).on('resize', () => {
			if (this.elements.settings.data_layout === 'fitRows') {
				window.setTimeout(() => {
					this.setEqualHeightRows();
				}, 300);
			}
		});
    }

    setLayoutModeGrid() {
        /** Applique le mode Metro */
        if (this.elements.settings.data_metro) {
            this.elements.$itemsInstance.addClass('mode-metro');
        } else {
            this.elements.$itemsInstance.removeClass('mode-metro');
        }

        this.elements.isotopeInitialized = this.elements.$targetId.isotope(this.elements.isotopeOptions);

        /** Charge les images */
        this.elements.$targetId.imagesLoaded().done(() => {
            if (this.elements.settings.data_layout === 'fitRows') {
                this.setEqualHeightRows();
            }
        });
    }

    /** @since 2.3.3 */
    setEqualHeightRows() {
        let maxRowHeight = 0;
        const $article = jQuery('.advanced-gallery__item', this.elements.$targetId);
		$article.css('height', '');

        $article.each((index, elem) => {
            const currentHeight = jQuery(elem).height();
            if (currentHeight > maxRowHeight) { maxRowHeight = currentHeight; }
        });
        if (maxRowHeight !== 0) {
            $article.height(maxRowHeight);
        }
        if (this.elements.isotopeInitialized.length > 0) {
            this.elements.$targetId.isotope('layout');
        } else {
            this.elements.isotopeInitialized = this.elements.$targetId.isotope(this.elements.isotopeOptions);
        }
    }

    setLayoutModeJustify() {
        // Supprime la div sizer utilisée uniquement pour les modes Grid/Masonry
        this.elements.$targetSizer.remove();

        this.elements.$targetId.imagesLoaded().done(() => {
            this.elements.$targetJustify.fjGallery(this.elements.justifyOptions);
        });
    }

    setSwiperGallery() {
        new EacSwiper(this.elements.settings, this.elements.$targetInstance);
    }

    onFilterGridClick(evt) {
        const $this = jQuery(evt.currentTarget);
        const $optionSet = $this.parents(this.elements.filterWrapper);
		evt.preventDefault();

        // L'item du filtre est déjà sélectionné
        if ($this.parents(this.elements.filterItem).hasClass(this.elements.filterActive)) {
            return;
        }

        $optionSet.find('.' + this.elements.filterActive).removeClass(this.elements.filterActive);
        $this.parents(this.elements.filterItem).addClass(this.elements.filterActive);
        this.elements.$targetId.isotope({ filter: $this.attr('data-filter') });
    }

    onFilterGridChange(evt) {
		evt.preventDefault();
        // Applique le filtre
        this.elements.$targetId.isotope({ filter: evt.currentTarget.value });
    }

    onElementChange(propertyName) {
        if ('ag_image_height' === propertyName && 'justify' === this.elements.settings.data_layout) {
            let heightRow = this.elements.justifyOptions.rowHeight;

            if ('ag_image_height' === propertyName) {
                heightRow = this.getElementSettings('ag_image_height')['size'];
            } else if ('ag_image_height_tablet_extra' === propertyName) {
                heightRow = this.getElementSettings('ag_image_height_tablet_extra')['size'];
            } else if ('ag_image_height_tablet' === propertyName) {
                heightRow = this.getElementSettings('ag_image_height_tablet')['size'];
            } else if ('ag_image_height_mobile_extra' === propertyName) {
                heightRow = this.getElementSettings('ag_image_height_mobile_extra')['size'];
            } else if ('ag_image_height_mobile' === propertyName) {
                heightRow = this.getElementSettings('ag_image_height_mobile')['size'];
            } else if ('ag_image_height_laptop' === propertyName) {
                heightRow = this.getElementSettings('ag_image_height_laptop')['size'];
            }
            this.elements.justifyOptions.rowHeight = heightRow;
            this.setLayoutModeJustify();
        }
    }

    setKeyboardEvents(evt) {
        const selecteur = 'button, a, [tabindex]:not([tabindex="-1"])';
        const id = evt.code || evt.key || 0;
        let $targetArticleFirst = null;
        let $targetArticleLast = null;
        let $targetArticleContentFirst = null;
        let $targetArticleContentLast = null;

        if (this.elements.settings.data_filtre) {
            const elementsFiltered = this.elements.$targetId.isotope('getFilteredItemElements');
            const $targetArticlesWithLinkFiltered = jQuery(elementsFiltered).find(selecteur);
            $targetArticleFirst = $targetArticlesWithLinkFiltered.first();
            $targetArticleLast = $targetArticlesWithLinkFiltered.last();
        } else {
            const $targetArticlesWithLink = this.elements.$itemsInstance.find(selecteur);
            $targetArticleFirst = $targetArticlesWithLink.first();
            $targetArticleLast = $targetArticlesWithLink.last();
        }
        $targetArticleContentFirst = $targetArticleFirst.closest('.advanced-gallery__content');
        $targetArticleContentLast = $targetArticleLast.closest('.advanced-gallery__content');

        if (('Tab' === id && !evt.shiftKey) || ('Tab' === id && evt.shiftKey)) {
            return true;
        } else if ('Home' === id) {
            evt.preventDefault();
            if (this.elements.settings.data_overlay === 'overlay-out') {
                $targetArticleFirst.trigger('focus');
            } else {
                $targetArticleContentFirst.trigger('focus');
                window.setTimeout(() => { $targetArticleFirst.focus(); }, 500);
            }
        } else if ('End' === id) {
            evt.preventDefault();
            if (this.elements.settings.data_overlay === 'overlay-out') {
                $targetArticleLast.trigger('focus');
            } else {
                $targetArticleContentLast.trigger('focus');
                window.setTimeout(() => { $targetArticleLast.focus(); }, 500);
            }
        } else if ('Escape' === id) {
            this.elements.$targetSkipGrid.trigger('focus');
        }
    }
}

/**
 * Description: La class est créer lorsque le composant 'eac-addon-advanced-gallery' est chargé dans la page
 *
 * @param elements (Ex: $scope)
 */
jQuery(window).on('elementor/frontend/init', () => {
    elementorFrontend.elementsHandler.attachHandler('eac-addon-advanced-gallery', widgetAdvancedGallery);
});