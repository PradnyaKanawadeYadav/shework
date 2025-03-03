/**
 * Description: Cette méthode est déclenchée lorsque le composant 'eac-addon-acf-relationship' est chargé dans la page
 *
 * @since 1.9.7
 */
import { EacSwiper, setGridItemsGlobalLink } from '../modules/eac-modules.js';

class widgetRelationshipACF extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                targetInstance: '.eac-acf-relationship',
                targetArticles: 'article',
                targetCards: '.acf-relation_content-wrapper',
                targetSkipGrid: '.eac-skip-grid',
                target: '.acf-relation_container',
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        let components = {
            $targetInstance: this.$element.find(selectors.targetInstance),
            $targetArticles: this.$element.find(selectors.targetArticles),
            $targetCards: this.$element.find(selectors.targetCards),
            $targetSkipGrid: this.$element.find(selectors.targetSkipGrid),
            $target: this.$element.find(selectors.target),
            settings: this.$element.find(selectors.target).data('settings') || {},
            has_swiper: false,
        };
        components.has_swiper = components.settings.data_sw_swiper;

        return components;
    }

    onInit() {
        super.onInit();

        if (this.elements.has_swiper) {
            new EacSwiper(this.elements.settings, this.elements.$targetInstance);
        }
    }

    bindEvents() {
        if (this.elements.$targetArticles.length > 0) {
            this.elements.$targetInstance.on('keydown', this.setKeyboardEvents.bind(this));
        }

        if (!elementorFrontend.isEditMode()) {
            setGridItemsGlobalLink(this.elements.$targetCards);
        }
    }

    setKeyboardEvents(evt) {
        const id = evt.code || evt.key || 0;
        const selecteur = 'button, a, [tabindex]:not([tabindex="-1"])';

        if (('Tab' === id && !evt.shiftKey) || ('Tab' === id && evt.shiftKey)) {
            return true;
        } else if ('Home' === id) {
            evt.preventDefault();
            const $targetArticle = this.elements.$targetArticles.first();
            $targetArticle.find(selecteur).first().trigger('focus');
        } else if ('End' === id) {
            evt.preventDefault();
            const $targetArticle = this.elements.$targetArticles.last();
            $targetArticle.find(selecteur).last().trigger('focus');
        } else if ('Escape' === id) {
            this.elements.$targetSkipGrid.trigger('focus');
        }
    }
}

/**
 * Description: La class est créer lorsque le composant 'eac-addon-acf-relationship' est chargé dans la page
 *
 * @param elements (Ex: $scope)
 * @since 2.1.3
 */
jQuery(window).on('elementor/frontend/init', () => {
    elementorFrontend.elementsHandler.attachHandler('eac-addon-acf-relationship', widgetRelationshipACF);
});
