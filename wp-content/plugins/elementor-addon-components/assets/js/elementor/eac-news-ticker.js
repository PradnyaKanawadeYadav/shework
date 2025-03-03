/**
 * Description: Cette méthode est déclenchée lorsque le composant 'eac-addon-news-ticker' est chargé dans la page
 *
 * @param Le contenu du conteneur
 * @since 1.9.2
 */
import { EacReadTheFeed } from '../modules/eac-modules.js';

class widgetNewsTicker extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                targetInstance: '.eac-news-ticker',
                targetWrapper: '.news-ticker_wrapper',
                targetWrapperTitle: '.news-ticker_wrapper-title',
                targetWrapperContent: '.news-ticker_wrapper-content div',
                targetWrapperControl: '.news-ticker_wrapper-control',
                targetWrapperControlPlay: '.play',
                targetWrapperControlPause: '.pause',
                targetWrapperControlLeft: '.left',
                targetWrapperControlRight: '.right',
                targetItems: '.news-ticker_item',
				targetNonce: '#news_nonce',
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        return {
            $targetInstance: this.$element.find(selectors.targetInstance),
            $targetWrapper: this.$element.find(selectors.targetWrapper),
            $targetWrapperTitle: this.$element.find(selectors.targetWrapperTitle),
            $targetWrapperContent: this.$element.find(selectors.targetWrapperContent),
            $targetWrapperControl: this.$element.find(selectors.targetWrapperControl),
            $targetWrapperControlPlay: this.$element.find(selectors.targetWrapperControlPlay),
            $targetWrapperControlPause: this.$element.find(selectors.targetWrapperControlPause),
            $targetWrapperControlLeft: this.$element.find(selectors.targetWrapperControlLeft),
            $targetWrapperControlRight: this.$element.find(selectors.targetWrapperControlRight),
            $targetItems: this.$element.find(selectors.targetItems),
			nonce: this.$element.find(selectors.targetNonce).val(),
            settings: this.$element.find(selectors.targetWrapper).data('settings') || {},
            itemsArray: [],
            currentFeedIndex: 0,
            iterationScrollCount: 0,
            isRTL: jQuery('html').attr('dir') && jQuery('html').attr('dir') === 'rtl' ? true : false,
        };
    }

    onInit() {
		const that = this;
        super.onInit();

        // Erreur settings
        if (Object.keys(this.elements.settings).length === 0) {
            return;
        }

        // Lecture automatique du flux
        if (this.elements.settings.data_auto) {
            this.elements.$targetWrapperContent.removeClass('animationPause').addClass('animationPlay');
            this.elements.$targetWrapperControlPlay.toggle(1);
            this.elements.$targetWrapperControlPause.toggle(1);
        }

        // Boucle et charge les flux dans un tableau
        jQuery.each(this.elements.$targetItems, (index, item) => {
            that.elements.itemsArray.push(item.innerHTML);
        });

        // Appel du service
        this.setNewsTickerElements(this.elements.itemsArray[this.elements.currentFeedIndex], this.elements.settings, this.elements.nonce, this.elements.$targetWrapperTitle, this.elements.$targetWrapperContent);
    }

    bindEvents() {
		const that = this;
        this.elements.$targetWrapperControlPlay.on('keydown click', this.onControlEventPlay.bind(this));
        this.elements.$targetWrapperControlPause.on('keydown click', this.onControlEventPause.bind(this));
        this.elements.$targetWrapperControlPlay.on('animationend', () => { // Change l'état de l'animation du bouton 'play' en fin d'animation
            that.elements.$targetWrapperControlPlay.css('animation-play-state', 'paused');
        });
        this.elements.$targetWrapperControlLeft.on('keydown click', this.onControlEventLeft.bind(this));
        this.elements.$targetWrapperControlRight.on('keydown click', this.onControlEventRight.bind(this));
        this.elements.$targetWrapperContent.on('animationiteration', this.onAnimationIteration.bind(this));
    }

    /** Toggle de l'animation Play */
    onControlEventPlay(evt) {
        const id = evt.code || evt.key || 0;
        if ('keydown' === evt.type && ('Enter' !== id && 'Space' !== id)) {
            return;
        }
        evt.preventDefault();

        this.elements.$targetWrapperContent.removeClass('animationPause').addClass('animationPlay');
        this.elements.$targetWrapperControlPlay.css({ 'display': 'none', 'animation-play-state': 'paused' });
        this.elements.$targetWrapperControlPause.css('display', 'inline-block');
        this.elements.$targetWrapperControlPause.focus();

        // Recharge l'animation reinitialisée par les boutons Gauche/Droite
        if (!this.elements.isRTL) {
            this.elements.$targetWrapperContent.css({
                '-webkit-animation': 'newsTickerHrz ' + this.elements.settings.data_speed + 's linear infinite',
                'animation': 'newsTickerHrz ' + this.elements.settings.data_speed + 's linear infinite',
            });
        } else {
            this.elements.$targetWrapperContent.css({
                '-webkit-animation': 'newsTickerHrzRtl ' + this.elements.settings.data_speed + 's linear infinite',
                'animation': 'newsTickerHrzRtl ' + this.elements.settings.data_speed + 's linear infinite',
            });
        }
    }

    /** Toggle de l'animation Pause */
    onControlEventPause(evt) {
        const id = evt.code || evt.key || 0;
        if ('keydown' === evt.type && ('Enter' !== id && 'Space' !== id)) {
            return;
        }
        evt.preventDefault();

        this.elements.$targetWrapperContent.removeClass('animationPlay').addClass('animationPause');
        this.elements.$targetWrapperControlPlay.css({ 'display': 'inline-block', 'animation-play-state': 'running' });
        this.elements.$targetWrapperControlPlay.focus();
        this.elements.$targetWrapperControlPause.css('display', 'none');
    }

    /** Click bouton de gauche. Flux précédent. Reinitialise les différents css */
    onControlEventLeft(evt) {
        const id = evt.code || evt.key || 0;
        if ('keydown' === evt.type && ('Enter' !== id && 'Space' !== id)) {
            return;
        }
        evt.preventDefault();

        this.elements.$targetWrapperContent.removeClass('animationPlay');
        this.elements.$targetWrapperControlPlay.css({ 'display': 'none', 'display': 'inline-block', 'animation-play-state': 'running' });
        this.elements.$targetWrapperControlPause.css('display', 'none');

        // Restart de l'animation
        this.elements.$targetWrapperContent.css({ '-webkit-animation': 'restartNewsTickerHrz', 'animation': 'restartNewsTickerHrz' });

        // Calcul de l'index du tableau des flux
        this.elements.currentFeedIndex = (this.elements.currentFeedIndex - 1) <= 0 ? 0 : this.elements.currentFeedIndex - 1;
        this.elements.iterationScrollCount = 0;

        // Vide les champs
        this.elements.$targetWrapperTitle.html('');
        this.elements.$targetWrapperContent.empty();

        // Appel du service
        this.setNewsTickerElements(this.elements.itemsArray[this.elements.currentFeedIndex], this.elements.settings, this.elements.nonce, this.elements.$targetWrapperTitle, this.elements.$targetWrapperContent);
    }

    /** Click bouton de gauche. Flux précédent. Reinitialise les différents css */
    onControlEventRight(evt) {
        const id = evt.code || evt.key || 0;
        if ('keydown' === evt.type && ('Enter' !== id && 'Space' !== id)) {
            return;
        }
        evt.preventDefault();

        this.elements.$targetWrapperContent.removeClass('animationPlay');
        this.elements.$targetWrapperControlPlay.css({ 'display': 'inline-block', 'animation-play-state': 'running' });
        this.elements.$targetWrapperControlPause.css('display', 'none');

        // Restart de l'animation
        this.elements.$targetWrapperContent.css({ '-webkit-animation': 'restartNewsTickerHrz', 'animation': 'restartNewsTickerHrz' });

        // Calcul de l'index du tableau des flux
        this.elements.currentFeedIndex = (this.elements.currentFeedIndex + 1) >= this.elements.itemsArray.length ? 0 : this.elements.currentFeedIndex + 1;
        this.elements.iterationScrollCount = 0;

        // Vide les champs
        this.elements.$targetWrapperTitle.html('');
        this.elements.$targetWrapperContent.empty();

        // Appel du service
        this.setNewsTickerElements(this.elements.itemsArray[this.elements.currentFeedIndex], this.elements.settings, this.elements.nonce, this.elements.$targetWrapperTitle, this.elements.$targetWrapperContent);
    }

    /** Click bouton de gauche. Flux précédent. Reinitialise les différents css */
    onAnimationIteration() {
        this.elements.iterationScrollCount++;

        // Le nombre d'itération est atteint
        if (this.elements.iterationScrollCount > this.elements.settings.data_loop - 1) {
            this.elements.iterationScrollCount = 0;
            this.elements.currentFeedIndex++;

            // Tous les items du flux sont lus, réinitialisation de l'index des flux
            if (this.elements.currentFeedIndex >= this.elements.itemsArray.length) {
                this.elements.currentFeedIndex = 0;
            }

            // Vide les champs
            this.elements.$targetWrapperTitle.html('');
            this.elements.$targetWrapperContent.empty();

            // Appel du service
            this.setNewsTickerElements(this.elements.itemsArray[this.elements.currentFeedIndex], this.elements.settings, this.elements.nonce, this.elements.$targetWrapperTitle, this.elements.$targetWrapperContent);
        }
    }

    /**
     * setNewsTickerElement
     *
     * Prepare et lance la requête vers le flux distant
     * Collecte et affiche les données dans les champs correspondants
     * 
     * @param itemFeed			Le titre et l'url corespondante
     * @param settings			Les paramètres d'affichage
     * @param $targetTitle		Le champ titre
     * @param $targetContent	Le champ contenu
     */
    setNewsTickerElements(itemFeed, settings, nonce, $targetTitle, $targetContent) {
        // Titre et URL du flux
        const currentItemTitle = itemFeed.split('::')[0];
        const currentItemUrl = itemFeed.split('::')[1];
        // Construction de l'objet de la requête Ajax
        const instanceAjax = new EacReadTheFeed(currentItemUrl.replace(/\s+/g, ''), nonce, settings.data_id);

        // L'appel Ajax est asynchrone, ajaxComplete est déclenché
        jQuery(document).on('ajaxComplete', (evt, xhr, ajaxSettings) => {
            // Le même random number généré lors de la création de l'objet Ajax
            if (instanceAjax !== null && ajaxSettings.ajaxOptions && ajaxSettings.ajaxOptions === instanceAjax.getOptions()) {
                evt.stopImmediatePropagation();

                // Les items à afficher
                const allItems = instanceAjax.getItems();

                // Une erreur Ajax ??
                if (allItems.headError) {
                    $targetTitle.text(currentItemTitle);
                    $targetContent.append('<p class="news-ticker_content-item"><span>' + allItems.headError + '</span></p>');
                    $targetContent.css("animation-duration", "10s !important");
                    return false;
                }

                // Pas d'item
                if (!allItems.rss) {
                    $targetTitle.text(currentItemTitle);
                    $targetContent.append('<p class="news-ticker_content-item"><span>Nothing to display</span></p>');
                    $targetContent.css("animation-duration", "10s !important");
                    return false;
                }

                const profileTitle = allItems.profile.headTitle;
                const profileLink = allItems.profile.headLink;
                const Items = allItems.rss;

                // Vitesse de l'animation
                $targetContent.css("animation-duration", settings.data_speed + 's');

                // Ajoute le titre du flux
                $targetTitle.html("<a href='" + profileLink + "'>" + currentItemTitle + "</a>");

                jQuery.each(Items, (index, item) => {
                    // Nombre d'items à afficher
                    if (index >= settings.data_nombre) {
                        return false;
                    }

                    const title = item.title;
                    const url = item.lien;
                    const date = settings.data_date ? new Date(item.update).toLocaleDateString() : '';

                    // Formatte le contenu de chaque item
                    const news = settings.data_rtl === 'left' ?
                        "<p class='news-ticker_content-item'>" +
                        "<span class='date'>" + date + "</span>" +
                        "<a href='" + url + "' tabindex='-1'>" +
                        "<span class='news'>" + title + "</span>" +
                        "</a>" +
                        "<span class='separator'>|</span>" +
                        "</p>" :
                        "<p class='news-ticker_content-item'>" +
                        "<a href='" + url + "' tabindex='-1'>" +
                        "<span class='news'>" + title + "</span>" +
                        "</a>" +
                        "<span class='date'>" + date + "</span>" +
                        "<span class='separator'>|</i></span>" +
                        "</p>";

                    if (settings.data_rtl === 'left') {
                        // Ajoute le contenu du flux à la fin
                        $targetContent.append(news);
                    } else {
                        // Insere le contenu du flux au début
                        $targetContent.prepend(news);
                    }
                });
            }
        });
    }
}

/**
 * Description: La class est créer lorsque le composant 'eac-addon-news-ticker' est chargé dans la page
 *
 * @param elements (Ex: $scope)
 * @since 2.1.2
 */
jQuery(window).on('elementor/frontend/init', () => {
    elementorFrontend.elementsHandler.attachHandler('eac-addon-news-ticker', widgetNewsTicker);
});
