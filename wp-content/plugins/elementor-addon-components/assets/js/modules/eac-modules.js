
/**
 * Description: ES6 class Swipper exportée
 *
 * @since 2.1.3
 */
export class EacSwiper {
	constructor(settings, $targetInstance) {
		this.$targetInstance = $targetInstance;
		this.$swiperBullets = this.$targetInstance.find('.swiper-pagination-clickable span.swiper-pagination-bullet');
		this.target = this.$targetInstance.find('.swiper-wrapper:first').get(0);
		this.swiperNext = this.$targetInstance.find('.swiper-button-next:first').get(0);
		this.swiperPrev = this.$targetInstance.find('.swiper-button-prev:first').get(0);
		this.swiper = null;
		this.swiperEnabled = false;
		this.settings = settings;
		this.swiperOptions = {
			touchEventsTarget: 'wrapper',
			watchOverflow: true,
			autoplay: {
				enabled: this.settings.data_sw_autoplay,
				delay: this.settings.data_sw_delay,
				disableOnInteraction: false,
				pauseOnMouseEnter: true,
				reverseDirection: this.settings.data_sw_rtl
			},
			direction: this.settings.data_sw_dir,
			loop: this.settings.data_sw_autoplay === true ? this.settings.data_sw_loop : false,
			speed: 1000,
			grabCursor: true,
			watchSlidesProgress: true,
			slidesPerView: this.settings.data_sw_imgs,
			centeredSlides: this.settings.data_sw_centered,
			loopedSlides: this.settings.data_sw_imgs === 'auto' ? 2 : null,
			mode: 'horizontal',
			freeMode: {
				enabled: this.settings.data_sw_free,
				momentumRatio: 1,
				//sticky: true,
			},
			spaceBetween: this.settings.data_sw_type ? parseInt(jQuery(':root').css('--eac-acf-relationship-grid-margin')) : 0,
			effect: this.settings.data_sw_effect,
			coverflowEffect: {
				rotate: 45,
				slideShadows: true,
			},
			creativeEffect: {
				prev: {
					translate: [0, 0, 0],
					opacity: 0,
				},
				next: {
					translate: ['100%', 0, 0],
				},
			},
			fadeEffect: {
				crossFade: true,
			},
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
			pagination: {
				el: '.swiper-pagination-bullet',
				type: 'bullets',
				clickable: this.settings.data_sw_pagination_click,
			},
			scrollbar: {
				enabled: this.settings.data_sw_scroll,
				el: '.swiper-scrollbar',
				draggable: true,
				dragSize: 100,
			},
			breakpoints: {
				// when window width is >= 0px
				0: {
					slidesPerView: 1,
				},
				// when window width is >= 460px
				460: {
					slidesPerView: this.settings.data_sw_imgs === 'auto' ? 'auto' : parseInt(this.settings.data_sw_imgs, 10) > 2 ? this.settings.data_sw_imgs - 2 : this.settings.data_sw_imgs,
				},
				// when window width is >= 767px
				767: {
					slidesPerView: this.settings.data_sw_imgs === 'auto' ? 'auto' : parseInt(this.settings.data_sw_imgs, 10) > 1 ? this.settings.data_sw_imgs - 1 : this.settings.data_sw_imgs,
				},
				// when window width is >= 1024px
				1024: {
					slidesPerView: this.settings.data_sw_imgs === 'auto' ? 'auto' : parseInt(this.settings.data_sw_imgs, 10) > 1 ? this.settings.data_sw_imgs - 1 : this.settings.data_sw_imgs,
				},
				// when window width is >= 1200px
				1200: {
					slidesPerView: this.settings.data_sw_imgs,
				}
			},
		};
		this.initSwiper();
	}

	initSwiper() {
		this.swiper = new Swiper(this.$targetInstance[0], this.swiperOptions);
		this.swiperEnabled = this.swiper.enabled;
		if (this.swiperEnabled) {
			this.setSwiperEvents();
		}
	}

	isEnabled() {
		return this.swiperEnabled;
	}

	setSwiperEvents() {
		if (this.swiperNext) {
			this.swiperNext.addEventListener('touchend', (evt) => {
				evt.preventDefault();
				this.swiper.slideNext();
			});
		}

		if (this.swiperPrev) {
			this.swiperPrev.addEventListener('touchend', (evt) => {
				evt.preventDefault();
				this.swiper.slidePrev();
			});
		}

		if (this.$swiperBullets) {
			jQuery.each(this.$swiperBullets, (index, bullet) => {
				bullet.addEventListener('touchend', { slidenum: index }, (evt) => {
					evt.preventDefault();

					if (this.swiper.params.loop === true) {
						this.swiper.slideToLoop(evt.data.slidenum);
					} else {
						this.swiper.slideTo(evt.data.slidenum);
					}

					if (this.swiper.params.autoplay.enabled === true && this.swiper.autoplay.paused === true) {
						this.swiper.autoplay.paused = false;
						this.swiper.autoplay.run();
					}
				});
			});
		}

		/** Accessibility */
		if (this.target) {
			this.target.addEventListener('focusin', () => {
				if (this.swiper.params.autoplay.enabled === true) {
					this.swiper.autoplay.stop();
				}
			});

			this.target.addEventListener('focusout', () => {
				if (this.swiper.params.autoplay.enabled === true) {
					this.swiper.autoplay.start();
				}
			});
		}
	}
}

/**
 * Description: ES6 class exportée pour 'eac-rss-reader', 'eac-news-ticker', 'eac-pinterest-rss'
 *
 * @since 2.1.3
 */
export class EacReadTheFeed {
	constructor(feedUrl, nonce, id) {
		this.allItems = [];
		this.itemError = {};
		this.ajaxOption = Math.random().toString(36).substring(2, 10); // Génère un nombre aléatoire unique pour l'instance courante
		this.proxy = eacElementsPath.proxies + 'proxy-rss.php'; // eacElementsPath est initialisé dans 'eac-register-scripts.php'
		this.proxyUrl = encodeURIComponent(feedUrl);
		this.proxyNonce = nonce;
		this.proxyId = id;
		if (this.proxyUrl && this.proxyNonce && this.proxyId) {
			this.callRss();
		}
	}

	getItems() {
		return this.allItems[0];
	}

	getOptions() {
		return this.ajaxOption;
	}

	callRss() {
		const self = this;

		jQuery.ajax({
			url: this.proxy,
			type: 'GET',
			data: {
				url: this.proxyUrl,
				nonce: this.proxyNonce,
				id: this.proxyId
			},
			dataType: 'json',
			ajaxOptions: this.ajaxOption,
		}).done((data, textStatus, jqXHR) => { // les proxy echo des données 'encodées par json_encode', mais il peut être vide
			if (jqXHR.responseJSON === null) {
				self.itemError.headError = 'Nothing to display...';
				self.allItems.push(self.itemError);
				return false;
			}
			self.allItems.push(data);
		}).fail((jqXHR, textStatus) => { // Les proxy echo des données 'non encodées par json_encode'. textStatus == parseerror
			self.itemError.headError = jqXHR.responseText;
			self.allItems.push(self.itemError);
			return false;
		});
	}
}

/**
 * Description: ES6 class exportée pour les grilles/galeries qui implémentent les liens globaux dans une Fancybox
 * 
 * @since 2.3.2
 */
export class GridItemsGlobalLink {
	constructor(cards, fb, elemt_id) {
		this.$cards = cards;
		this.fancyBox = fb || false;
		this.id = elemt_id || 0;
		this.initGlobalLink();
	}

	initGlobalLink() {
		Array.from(this.$cards).forEach((card) => { // Object.entries(this.$cards)
			const cardLink = card.querySelector('a.card-link');
			const clickableElements = Array.from(card.querySelectorAll('a:not([data-fancybox]):not(.button-cart):not(.avatar-link)'));

			clickableElements.forEach((elem) => {
				elem.addEventListener('click', (evt) => { this.fancyBox ? evt.preventDefault() : evt.stopPropagation(); });
			});

			if (cardLink !== null) {
				card.addEventListener('click', () => {
					const noTextSelected = !window.getSelection().toString();
					if (noTextSelected) {
						this.fancyBox ? this.openFancyBox(cardLink) : cardLink.click();
					}
				});
			}
		});
	}

	openFancyBox(cardLink) {
		jQuery.fancybox.open([{
			type: 'iframe',
			src: cardLink.getAttribute('href'),
			opts: {
				slideClass: 'modalbox-visible-' + this.id,
				caption: cardLink.getAttribute('aria-label'),
				smallBtn: true,
				buttons: [''],
				toolbar: false,
				afterShow: function (instance, current) {
					//current.$content.css('max-block-size', '80vh');
					//current.$content.css('max-inline-size', '80vw');
					//current.$iframe.contents().find('a').css('pointer-events', 'none');
					//current.$iframe.contents().find('button, input[type="submit"]').attr('disabled', 'disabled').css('cursor', 'no-drop');
					current.$smallBtn.trigger('focus');

					current.$iframe.contents().find('body').on('keydown', (evt) => {
						current.$smallBtn.trigger('focus');
						evt.preventDefault();
						evt.stopPropagation();
					});
				},
			}
		}]);
	}
}

export function setGridItemsGlobalLink($cards) {
	Array.from($cards).forEach((card) => {
		const cardLink = card.querySelector('a.card-link');
		const clickableElements = Array.from(card.querySelectorAll('a:not([data-fancybox]):not(.button-cart):not(.avatar-link)'));

		clickableElements.forEach((elem) => {
			elem.addEventListener('click', (evt) => { evt.stopPropagation(); });
		});

		if (cardLink !== null) {
			card.addEventListener('click', () => {
				const noTextSelected = !window.getSelection().toString();
				if (noTextSelected) {
					cardLink.click(); //cardLink.dispatchEvent(new MouseEvent('click', { cancelable: true }));
				}
			});
		}
	});
}
