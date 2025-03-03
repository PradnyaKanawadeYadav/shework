
/** https://developers.elementor.com/a-new-method-for-attaching-a-js-handler-to-an-element/ */

class widgetClassCountDown extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                targetInstance: '.eac-count-down',
                targetContainer: '.count-down_container',
                targetTitle: '.count-down_container-title',
                targetExpireMessage: '.count-down_expire-message',
                targetExpireRedirect: '.count-down_expire-redirect',
                targetItems: '.count-down_container-items',
                targetItem: '.count-down_container-item',
                digitDays: '.count-down_digit-days',
                digitHours: '.count-down_digit-hours',
                digitMinutes: '.count-down_digit-minutes',
                digitSeconds: '.count-down_digit-seconds',
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        let components = {
            $targetInstance: this.$element.find(selectors.targetInstance),
            $targetContainer: this.$element.find(selectors.targetContainer),
            $targetTitle: this.$element.find(selectors.targetTitle),
            $targetExpireMessage: this.$element.find(selectors.targetExpireMessage),
            $targetExpireRedirect: this.$element.find(selectors.targetExpireRedirect),
            $targetItems: this.$element.find(selectors.targetItems),
            $targetItem: this.$element.find(selectors.targetItem),
            $digitDays: this.$element.find(selectors.digitDays),
            $digitHours: this.$element.find(selectors.digitHours),
            $digitMinutes: this.$element.find(selectors.digitMinutes),
            $digitSeconds: this.$element.find(selectors.digitSeconds),
            settings: this.$element.find(selectors.targetContainer).data('settings') || {},
            timer: null,
            globalTime: {
                day: 0,
                hour: 0,
                minute: 0,
                second: 0,
            },
            localStorageName: 'eac_evgtimer-' + this.$element.data('id'),
            localStorageInit: 'eac_evgtimer_init-' + this.$element.data('id'),
        };
        if (Object.keys(components.settings).length !== 0) {
            components.globalTime.day = components.settings.data_days_val;
            components.globalTime.hour = components.settings.data_hours_val;
            components.globalTime.minute = components.settings.data_minutes_val;
            components.globalTime.second = components.settings.data_seconds_val;
        }
        return components;
    }

    onInit() {
        super.onInit();

        if (Object.keys(this.elements.settings).length === 0) {
            return;
        }

        if (this.elements.settings.data_type === 'due_date') {
            localStorage.removeItem(this.elements.localStorageName);
            localStorage.removeItem(this.elements.localStorageInit);
        } else {
            const evgInit = localStorage.getItem(this.elements.localStorageInit);
            const evgCurrent = this.elements.globalTime.day + ':' + this.elements.globalTime.hour + ':' + this.elements.globalTime.minute;
            //console.log(evgInit+'::'+evgCurrent+"=>"+JSON.stringify(this.elements.globalTime));
            if (evgInit) {
                if (evgInit !== evgCurrent) {
                    localStorage.setItem(this.elements.localStorageInit, evgCurrent);
                    localStorage.setItem(this.elements.localStorageName, evgCurrent);
                }
            } else {
                localStorage.setItem(this.elements.localStorageInit, evgCurrent);
                localStorage.setItem(this.elements.localStorageName, evgCurrent);
            }
            this.applyGlobalTime();
        }

        if (parseInt(this.elements.globalTime.day) === 0 && parseInt(this.elements.globalTime.hour) === 0 && parseInt(this.elements.globalTime.minute) === 0 && parseInt(this.elements.globalTime.second) === 0) {
            this.runActions();
        } else {
            this.elements.timer = window.setInterval(this.countDownDueDate.bind(this), 1000);
        }

        /** Affiche la div après initialisation */
        this.elements.$targetInstance.css('visibility', 'visible');
    }

    onDestroy() {
        super.onDestroy();
        window.clearInterval(this.elements.timer);	// Stop timer
        this.elements.timer = null;
    }

    countDownDueDate() {
        const localTime = this.getGlobalTime();

        if (this.elements.settings.data_minutes && this.elements.$digitMinutes.hasClass('anim-digits')) {
            this.elements.$digitMinutes.removeClass('anim-digits');
        }

        if (localTime.second === 0) {
            if (localTime.minute === 0) {
                if (localTime.hour === 0) {
                    if (localTime.day === 0) {
                        this.runActions();
                        return;
                    } else {
                        const day = localTime.day - 1;
                        this.elements.globalTime.day = day;
                        if (this.elements.settings.data_days) this.elements.$digitDays.get(0).textContent = String(day).padStart(2, '0');
                    }
                    // Reset heure
                    this.elements.globalTime.hour = 23;
                    if (this.elements.settings.data_hours) this.elements.$digitHours.get(0).textContent = 23;
                } else {
                    const hour = localTime.hour - 1;
                    this.elements.globalTime.hour = hour;
                    if (this.elements.settings.data_hours) this.elements.$digitHours.get(0).textContent = String(hour).padStart(2, '0');
                }
                // Reset minute
                this.elements.globalTime.minute = 59;
                if (this.elements.settings.data_minutes) {
                    this.elements.$digitMinutes.get(0).textContent = 59;
                    //this.elements.$digitMinutes.addClass('anim-digits');
                }
            } else {
                const minute = localTime.minute - 1;
                this.elements.globalTime.minute = minute;
                if (this.elements.settings.data_minutes) {
                    this.elements.$digitMinutes.get(0).textContent = String(minute).padStart(2, '0');
                    //this.elements.$digitMinutes.addClass('anim-digits');
                }
            }
            // Reset seconde
            this.elements.globalTime.second = 59;
            if (this.elements.settings.data_seconds) this.elements.$digitSeconds.get(0).textContent = 59;
        } else {
            const second = localTime.second - 1;
            this.elements.globalTime.second = second;
            if (this.elements.settings.data_seconds) this.elements.$digitSeconds.get(0).textContent = (String(second).padStart(2, '0'));
        }

        /** C'est ici qu'il faut enregistrer le localstorage toutes les minutes */
        if (this.elements.settings.data_type === 'evergreen' && this.elements.globalTime.second === 59) {
            localStorage.setItem(this.elements.localStorageName, this.elements.globalTime.day + ':' + this.elements.globalTime.hour + ':' + this.elements.globalTime.minute);
        }
    }

    applyGlobalTime() {
        const local = localStorage.getItem(this.elements.localStorageName);
        if (local) {
            this.elements.globalTime.day = parseInt(local.split(':')[0]);
            this.elements.globalTime.hour = parseInt(local.split(':')[1]);
            this.elements.globalTime.minute = parseInt(local.split(':')[2]);
            this.elements.globalTime.second = 0;
            if (this.elements.settings.data_days) this.elements.$digitDays.get(0).textContent = (String(this.elements.globalTime.day).padStart(2, '0'));
            if (this.elements.settings.data_hours) this.elements.$digitHours.get(0).textContent = (String(this.elements.globalTime.hour).padStart(2, '0'));
            if (this.elements.settings.data_minutes) this.elements.$digitMinutes.get(0).textContent = (String(this.elements.globalTime.minute).padStart(2, '0'));
            if (this.elements.settings.data_seconds) this.elements.$digitSeconds.get(0).textContent = (String(this.elements.globalTime.second).padStart(2, '0'));
        }
    }

    getGlobalTime() {
        return {
            day: parseInt(this.elements.globalTime.day),
            hour: parseInt(this.elements.globalTime.hour),
            minute: parseInt(this.elements.globalTime.minute),
            second: parseInt(this.elements.globalTime.second),
        };
    }

    runActions() {
        window.clearInterval(this.elements.timer);
        this.elements.timer = null;

        if (!elementorFrontend.isEditMode() && jQuery.inArray('hide', this.elements.settings.data_actions) !== -1) {
            if (this.elements.settings.data_actions.length === 1) { // Uniquement hide dans le tableau
                this.elements.$targetInstance.remove();
            } else {
                this.elements.$targetTitle.remove();
                this.elements.$targetItems.remove();
            }
        }

        if (jQuery.inArray('message', this.elements.settings.data_actions) !== -1) {
            this.elements.$targetExpireMessage.css('display', 'block');
        }

        if (jQuery.inArray('redirect', this.elements.settings.data_actions) !== -1 && this.elements.$targetExpireRedirect.length > 0) {
            const redirectUrl = this.elements.$targetExpireRedirect.get(0).textContent;
            if ('' !== redirectUrl) {
                window.setTimeout(() => { window.location.href = redirectUrl; }, this.elements.settings.data_tempo * 1000);
            }
        }
    }
}

/**
 * Description: La class est créer lorsque le composant 'eac-addon-count-down' est chargé dans la page
 *
 * @param elements
 * @since 2.1.2
 */
jQuery(window).on('elementor/frontend/init', () => {
    elementorFrontend.elementsHandler.attachHandler('eac-addon-count-down', widgetClassCountDown);
});
