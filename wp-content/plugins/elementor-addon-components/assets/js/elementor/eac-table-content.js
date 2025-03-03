/**
 * Description: Cette méthode est déclenchée lorsque la section 'eac-addon-toc' est chargée dans la page
 * Dependency: https://github.com/ndabas/toc
 *
 * @param {selector} $element. Le contenu de la section
 * @since 1.8.0
 */

class widgetTableOfContent extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                targetInstance: '.eac-table-of-content',
                toc: '#toctoc',
                tocHead: '#toctoc-head',
                tocHeadToggler: '#toctoc-head__toggler',
                tocHeadTogglerSvg: '#toctoc-head__toggler svg',
                tocHeadTogglerAwe: '#toctoc-head__toggler i',
                tocBody: '#toctoc-body',
                tocBodyList: '#toctoc-body__list',
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        return {
            $targetInstance: this.$element.find(selectors.targetInstance),
            $toc: this.$element.find(selectors.toc),
            $tocHead: this.$element.find(selectors.tocHead),
            $tocBody: this.$element.find(selectors.tocBody),
            $tocBodyList: this.$element.find(selectors.tocBodyList),
            $tocHeadToggler: this.$element.find(selectors.tocHeadToggler),
            $tocHeadTogglerIcon: this.$element.find(selectors.tocHeadTogglerSvg),
            settings: {
                listType: this.$element.find(selectors.targetInstance).data('settings').data_type,
                target: this.$element.find(selectors.targetInstance).data('settings').data_target,
                opened: this.$element.find(selectors.targetInstance).data('settings').data_opened,
                headPicto: ['▼', '▲'],
                headings: this.$element.find(selectors.targetInstance).data('settings').data_headings,
                trailer: this.$element.find(selectors.targetInstance).data('settings').data_trailer,
                ariaLabel: this.$element.find(selectors.targetInstance).data('settings').data_label,
                exclude: this.$element.find(selectors.targetInstance).data('settings').data_exclude,
                windowWith: window.innerWidth,
                $linksList: [],
            },
        };
    }

    onInit() {
        super.onInit();

        if (window.location.hash) {
            setTimeout(() => {
                let hash = window.location.hash;
                window.location.hash = '';
                window.location.hash = hash;
            }, 500);
        }

        // Option d'ouverture
        if (this.elements.settings.opened) {
            this.elements.$tocHeadTogglerIcon.css('transform', 'rotate(180deg)');
        }

        if (this.elements.settings.listType === 'numbered') {
            this.elements.$tocBodyList.css('list-style', 'none');
        } else if (this.elements.settings.listType === 'picto') {
            this.elements.$tocBodyList.css('list-style', 'none');
            this.elements.$tocBodyList.addClass('not-numbered');
        } else {
            this.elements.$tocBodyList.addClass('not-numbered');
            this.elements.$tocBodyList.css({ 'list-style': this.elements.settings.listType, 'list-style-position': 'inside' });
        }
        // Construction de la liste des ancres
        this.buildAnchorLinks();
    }

    bindEvents() {
        this.elements.$tocHead.on('click', this.onHeadClickKeyboardEvent.bind(this));
        if (!elementorFrontend.isEditMode()) {
            this.elements.$tocHead.on('keydown', this.onHeadClickKeyboardEvent.bind(this));
            this.elements.$tocBody.on('keydown', this.onBodyListKeyboardEvent.bind(this));
        }
        //jQuery(window).on('resize', this.onResizeWindow.bind(this));
        //jQuery(window).on('orientationchange', this.onOrientationChangeWindow());
    }

    buildAnchorLinks() {
        const target = this.elements.settings.target + ' '; // class: .site-content ...
        const headings = this.elements.settings.headings.split(','); // H1, H2, H3 ...
        const headingsTarget = (target + headings.join(',' + target)).split(',').join(','); // .site-content h1, .site-content h2, .site-content h3, ...
        const icon = this.getElementSettings('toc_content_picto');
        let renderIcon;
        if (icon) {
            if (elementorFrontend.config.experimentalFeatures.e_font_icon_svg && !elementorFrontend.isEditMode()) {
                const renderedIcon = typeof icon.rendered_tag !== 'undefined' ? icon.rendered_tag : '';
                if (renderedIcon) {
                    renderIcon = renderedIcon.replace('<svg', '<svg aria-hidden="true"');
                }
            } else {
                renderIcon = icon.value ? `<i class="${icon.value}" aria-hidden='true'></i>` : '';
            }
        }
        /** Saute les items du breadcrumb */
        this.elements.settings.exclude.push('eac-breadcrumbs-item');

        if (this.elements.settings.listType !== 'numbered') {
            let toc = '';
            let rank = 1;

            jQuery(headingsTarget).each((index, entry) => {
                const $this = jQuery(entry);
                const $parentElement = $this.closest('.elementor-widget');
                let eachBreak = false; // Because there is two nested each

                if ($this.attr('class') && this.elements.settings.exclude.length > 0) {
                    jQuery($this.attr('class').split(' ')).each((idx, classe) => {
                        if (jQuery.inArray(classe, this.elements.settings.exclude) >= 0) {
                            eachBreak = true;
                        }
                    });
                }
                if (eachBreak) {
                    return true;
                }

                // Check si l'élément n'est pas caché et s'il y a du contenu
                if (!$parentElement.is(':hidden') && $this.text().length > 0) {
                    // Le contenu du titre débarrassé d'éventuelles balises et réduit les espaces
                    const content = $this.text().replace(/\s+/g, ' ');
                    // Ajout du trailer
                    const trailerAnchor = this.elements.settings.trailer ? '-' + rank : '';
                    const currentTag = $this.prop('tagName').toLowerCase(); // tagName = h1 ... h6
                    // Formate l'ancre slugify
                    const contentAnchor = content.toSlug();
                    const fullAnchor = $this.is('[id]') ? $this.attr('id') : contentAnchor + trailerAnchor;
                    const ariaLabel = this.elements.settings.ariaLabel + content;
                    let contentWithTag = '';

                    // Ajout de l'ID et de la class dans le titre cible
                    $this.attr('id', fullAnchor).addClass('toctoc-jump-link');

                    if (this.elements.settings.listType === 'picto') {
                        contentWithTag = '<span class="link eac-icon-svg link-' + currentTag + '">' + renderIcon + '</span><a href="#' + fullAnchor + '" aria-label="' + ariaLabel + '"><span>' + content + '</span></a>'
                    } else {
                        contentWithTag = '<a href="#' + fullAnchor + '" aria-label="' + ariaLabel + '"><span class="link link-' + currentTag + '">' + content + '</span></a>'
                    }

                    toc += '<li>' + contentWithTag + '</li>';
                    rank++;
                }
            });
            // Ajoute l'item à la liste
            this.elements.$tocBodyList.append(toc);
        } else {
            this.elements.$tocBodyList.toc({ content: target, headings: this.elements.settings.headings, suffixe: this.elements.settings.trailer, exclude: this.elements.settings.exclude });
        }
        // Option d'ouverture
        if (this.elements.settings.opened) {
            this.elements.$tocBody.css('display', 'block');
        }

        // La liste des liens
        this.elements.$linksList = this.elements.$tocBodyList.find('a');
    }

    onHeadClickKeyboardEvent(evt) {
        if ('keydown' === evt.type) {
            const id = evt.code || evt.key || 0;
            if ('Enter' !== id && 'Space' !== id) {
                return;
            }
        }
        evt.preventDefault();
        this.elements.settings.opened ? this.elements.settings.opened = false : this.elements.settings.opened = true;

        if (this.elements.settings.opened) {
            this.elements.$tocHeadTogglerIcon.css('transform', 'rotate(180deg)');
            this.elements.$tocHead.attr('aria-expanded', 'true');
        } else {
            this.elements.$tocHeadTogglerIcon.css('transform', 'rotate(0deg)');
            this.elements.$tocHead.attr('aria-expanded', 'false');
        }
        this.elements.$tocBody.slideToggle(300);
    }

    onBodyListKeyboardEvent(evt) {
        const lastElement = document.activeElement;
        const id = evt.code || evt.key || 0;

        if ('Escape' === id) {
            this.elements.$tocHeadTogglerIcon.css('transform', 'rotate(0deg)');
            this.elements.$tocHead.attr('aria-expanded', 'false');
            this.elements.$tocBody.slideToggle(300);
            this.elements.$tocHead.trigger('focus');
        } else if ('ArrowDown' === id) {
            let currentElement = lastElement;
            evt.preventDefault();
            if (jQuery(currentElement).hasClass('toctoc-head')) {
                currentElement = this.elements.$linksList.get(0);
            } else if (jQuery(currentElement).attr('href')) {
                const currentLinkIndex = this.elements.$linksList.index(jQuery(currentElement));
                if (currentLinkIndex === (this.elements.$linksList.length - 1)) {
                    currentElement = this.elements.$linksList.get(0);
                } else if (currentLinkIndex + 1 < this.elements.$linksList.length) {
                    currentElement = this.elements.$linksList.get(currentLinkIndex + 1);
                } else {
                    currentElement = this.elements.$linksList.get(currentLinkIndex);
                }
            }
            jQuery(currentElement).trigger('focus');
        } else if ('ArrowUp' === id) {
            let currentElement = lastElement;
            evt.preventDefault();
            if (jQuery(currentElement).attr('href')) {
                const currentLinkIndex = this.elements.$linksList.index(jQuery(currentElement));
                if (currentLinkIndex - 1 > 0) {
                    currentElement = this.elements.$linksList.get(currentLinkIndex - 1);
                } else {
                    currentElement = this.elements.$linksList.get(0);
                }
            }
            jQuery(currentElement).trigger('focus');
        }
    }

    onResizeWindow() {
        // Calcule uniquement la largeur pour contourner la barre du navigateur qui s'efface sur les tablettes
        if (this.elements.settings.windowWith !== window.innerWidth) {
            this.elements.settings.windowWith = window.innerWidth;
            this.elements.settings.opened = false;
            this.elements.$tocHeadTogglerIcon.css('transform', 'rotate(0deg)');
            this.elements.$tocBodyList.slideUp(300);
        }
    }

    onOrientationChangeWindow() {
        window.dispatchEvent(new Event('resize'));
    }
}

jQuery(window).on('elementor/frontend/init', () => {
    elementorFrontend.elementsHandler.attachHandler('eac-addon-toc', widgetTableOfContent);
});
