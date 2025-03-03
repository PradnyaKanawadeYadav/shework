/**
 * Description: Cette méthode est déclenchée lorsque qu'un élément container/section/column est chargé dans la page
 *
 * @since 2.0.0
 */

class widgetBackgroundImages extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                targetInstance: '.eac-background__images-wrapper',
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        return {
            $targetInstance: this.$element.find(selectors.targetInstance),
            javascriptId: 'script[bgi-js-id="' + this.$element.data('id') + '"]',
        };
    }

    onInit() {
        super.onInit();

        if (jQuery(this.elements.javascriptId).length > 0) {
            jQuery(this.elements.javascriptId).remove();
        }
    }
}

jQuery(window).on('elementor/frontend/init', () => {
    const EacAddonsBackgroundImages = ($element) => {
        elementorFrontend.elementsHandler.addHandler(widgetBackgroundImages, {
            $element,
        });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/section', EacAddonsBackgroundImages);
    elementorFrontend.hooks.addAction('frontend/element_ready/container', EacAddonsBackgroundImages);
    elementorFrontend.hooks.addAction('frontend/element_ready/column', EacAddonsBackgroundImages);
});
