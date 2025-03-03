<?php
/**
 * Class: Eac_Acf_Field_Gallery
 * Name: ACF Image Gallery
 * Type: eac_gallery
 *
 * Description: Création et gestion d'un nouveau type de champ ACF 'eac_gallery'
 *
 * @since 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\EAC_Plugin;

if ( ! class_exists( 'Eac_Acf_Field_Gallery' ) ) :

	class Eac_Acf_Field_Gallery extends \acf_field {
		/**
		 * Controls field type visibilty in REST requests.
		 *
		 * @var bool
		 */
		public $show_in_rest = false;

		/**
		 * Valeurs relatives au type de champ ACF.
		 *
		 * @var array $env Plugin context such as 'url' and 'version'.
		 */
		private $env;

		public function __construct() {
			/**
			 * Field type (name) la référence est utilisée dans le code PHP et JS
			 * No spaces. Underscores allowed.
			 */
			$this->name          = 'eac_gallery';
			$this->label         = esc_html__( "Galerie d'images", 'eac-components' );
			$this->description   = esc_html__( "Améliorez votre site WordPress avec la galerie d'Images ACF et affichée la avec Elementor", 'eac-components' );
			$this->category      = 'content';
			$this->doc_url       = 'https://elementor-addon-components.com/add-and-publish-an-image-gallery-with-the-free-version-of-acf/';
			$this->tutorial_url  = '';
			$this->preview_image = EAC_PLUGIN_URL . 'includes/acf/gallery/assets/images/field-preview-gallery.png';
			$this->env           = array(
				'url'            => EAC_PLUGIN_URL . 'includes/acf/gallery',
				'version'        => EAC_PLUGIN_VERSION,
				'supported_type' => array( 'image/bmp', 'image/x-icon', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/webp', 'image/avif' ),
				'supported_post' => array( 'post.php', 'post-new.php', 'profile.php', 'user-edit.php', 'user-new.php' ),
			);
			parent::__construct();
			add_action( 'acf/render_field/name=eac_gallery', array( $this, 'render_field' ), 9, 1 );
			add_filter( 'acf/update_value/name=eac_gallery', array( $this, 'update_value' ), 10, 3 );
			/**add_filter( 'acf/format_value/name=eac_gallery', array( $this, 'format_value' ), 10, 3 );*/
		}

		public function render_field_settings( $field ) {
			// To render field settings on other tabs in ACF 6.0+:
			// https://www.advancedcustomfields.com/resources/adding-custom-settings-fields/#moving-field-setting
		}

		/**
		 * render_field
		 *
		 * @param mixed $field Le type de champ et ses données
		 *
		 * @return void
		 */
		public function render_field( $field ): void {
			$attachments = array();
			if ( ! empty( $field ) && ! empty( $field['value'] ) ) {
				$attachment_ids = array_map( 'absint', $field['value'] );
				// Ajout des meta données de chaque attachment ID pour l'affichage
				$attachments    = $this->add_meta_for_attachments( $attachment_ids );
			}
			?>
			<input type='hidden' name="<?php echo esc_attr( $field['name'] ); ?>" value='' />
			<input type='hidden' name="_<?php echo esc_attr( $this->name ); ?>_nonce[<?php echo esc_attr( $field['key'] ); ?>]" value="<?php echo esc_attr( wp_create_nonce( esc_attr( $field['key'] ) ) ); ?>" />
			<div class="<?php echo esc_attr( $this->add_class( 'container' ) ); ?>">
				<div class="<?php echo esc_attr( $this->add_class( 'attachments' ) ); ?> <?php echo esc_attr( $this->add_class( 'attachments' ) ); ?>-<?php echo esc_attr( $field['key'] ); ?>">
					<?php if ( ! empty( $attachments ) ) :
						foreach ( $attachments as $attachment ) :
							$media_id    = $attachment['attachment']->ID;
							$media_title = $attachment['attachment']->post_title;
							$media_class = $this->add_class( 'attachment-medium' );
							$media_url   = $attachment['metadata']['medium']['url'] ?? $attachment['metadata']['thumbnail']['url'];
							if ( empty( $media_url ) ) :
								$media_url   = EAC_PLUGIN_URL . 'includes/acf/gallery/assets/images/image-testemonials.webp';
								$media_class = $this->add_class( 'attachment-failed' );
							endif;
							?>
							<div data-id="<?php echo esc_attr( $media_id ); ?>" class="<?php echo esc_attr( $this->add_class( 'attachment-container' ) ); ?> <?php echo esc_attr( $this->add_class( "attachment-container-{$media_id}" ) ); ?> <?php echo esc_attr( $media_class ); ?>">
								<button type='button' class="<?php echo esc_attr( $this->add_class( 'remove-media' ) ); ?>" title='Remove this image'>
									<span class='dashicons dashicons-trash' aria-hidden='true'></span>
								</button>
								<input id="<?php echo esc_attr( $field['type'] ); ?>[<?php echo esc_attr( $field['_name'] ); ?>][]" type='hidden' name="<?php echo esc_attr( $field['type'] ); ?>[<?php echo esc_attr( $field['_name'] ); ?>][]" value="<?php echo esc_attr( $media_id ); ?>" />
								<img src="<?php echo esc_url( $media_url ); ?>" alt="<?php echo esc_attr( $media_title ); ?>"/>
								<?php if ( $media_class === $this->add_class( 'attachment-failed' ) ) : ?>
									<div class="<?php echo esc_attr( $this->add_class( 'file-name' ) ); ?>"><?php echo esc_attr( $media_title ); ?></div>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
				<div class="<?php echo esc_attr( $this->add_class( 'button-bottom' ) ); ?>">
					<button type='button' class="button button-primary <?php echo esc_attr( $this->add_class( 'add-media' ) ); ?>">
						<span class='dashicons dashicons-plus-alt2' aria-hidden='true'></span> <?php esc_html_e( 'Ajouter des images', 'eac-components' ); ?>
					</button>
					<button type='button' class="button button-primary <?php echo esc_attr( $this->add_class( 'remove-all-media' ) ); ?>">
						<span class='dashicons dashicons-minus' aria-hidden='true'></span> <?php esc_html_e( 'Supprimer toutes les images', 'eac-components' ); ?>
					</button>
					<div class="<?php echo esc_attr( $this->add_class( 'message-media' ) ); ?>"><?php esc_html_e( "N'oubliez-pas de sauvegarder vos modifications", 'eac-components' ); ?></div>
				</div>
			</div>
			<?php
		}

		/**
		 * add_class
		 *
		 * @param mixed $class Le nom de lma class à prefixer
		 *
		 * @return string
		 */
		public function add_class( $class ): string {
			return "eac-gallery__{$class}";
		}

		/**
		 * input_admin_enqueue_scripts
		 *
		 * Ajoute les styles et scripts nécessaires
		 *
		 * @return void
		 */
		public function input_admin_enqueue_scripts(): void {
			global $pagenow;

			if ( ! wp_script_is( 'jquery', 'enqueued' ) ) {
				wp_enqueue_script( 'jquery' );
			}

			// register & include JS
			wp_register_script( 'eac-acf-gallery', EAC_Plugin::instance()->get_script_url( 'includes/acf/gallery/assets/js/field-gallery' ), array(), '2.3.0', true );
			wp_enqueue_script( 'eac-acf-gallery' );
			// Passe les mime_type au JS
			wp_add_inline_script(
				'eac-acf-gallery',
				'var eacGalleryImage = ' . wp_json_encode(
					array(
						'mimesType' => $this->env['supported_type'],
					)
				),
				'before'
			);

			// register & include CSS
			wp_register_style( 'eac-acf-gallery', EAC_Plugin::instance()->get_style_url( 'includes/acf/gallery/assets/css/field-gallery' ), array(), '2.3.0' );
			wp_enqueue_style( 'eac-acf-gallery' );

			// register & include JS
			if ( ! wp_script_is( 'jquery-ui-sortable', 'enqueued' ) ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
			}

			// Enqueue required media files
			if ( in_array( $pagenow, $this->env['supported_post'], true ) && ! did_action( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}
		}

		/**
		 * update_value
		 *
		 * @param mixed $value Les IDs des attachements à sauvegarder
		 * @param mixed $post_id L'ID du post
		 * @param mixed $field Le type du champ et ses données
		 *
		 * @return array
		 */
		public function update_value( $value, $post_id, $field ): array {
			/**
			 * $field_name
			 * Propriété "_name" = name réel du champ (eac_gallery_5)
			 * Propriété "name" = pour une galerie dans un groupe, le name réel et préfixé avec le name du groupe (groupe_gallery_eac_gallery_5)
			 */
			$field_name = $field['_name'];
			$field_type = $field['type'];         // eac_gallery
			$field_key  = $field['key'];          // field_6756ec20af162
			$nonce_key  = "_{$field_type}_nonce"; /** "_eac_gallery_nonce":{"field_6756ec20af162":"57b73787b1"} */

			if ( empty( $_POST[ $nonce_key ] ) || empty( $_POST[ $nonce_key ][ $field_key ] ) ) {
				return array(); // Vide le champ metadata
			}

			// Nettoyage classique de la super globale
			$nonce_key = sanitize_text_field( wp_unslash( $_POST[ $nonce_key ][ $field_key ] ) );
			if ( ! wp_verify_nonce( $nonce_key, $field_key ) ) {
				return array();
			}

			if ( ! isset( $_POST[ $field_type ] ) || empty( $_POST[ $field_type ][ $field_name ] ) ) {
				return array();
			}
			/**
			 * $value = "eac_gallery":{"eac_gallery_5":["65606","59178"]}
			 * $value = $field_type:{$field_name:array(Ids des images)}
			 */
			$value = array_map( 'sanitize_text_field', wp_unslash( $_POST[ $field_type ][ $field_name ] ) );

			return $value;
		}

		/**
		 * format_value
		 *
		 * @param mixed $value Les IDs des attachements à sauvegarder
		 * @param mixed $post_id L'ID du post
		 * @param mixed $field Le type du champ et ses données
		 *
		 * @return array Les données structurées des attachments
		 */
		public function format_value( $value, $post_id, $field ): array {
			$attachment_ids = array();

			if ( ! empty( $value ) ) {
				$attachment_ids = array_map( 'absint', $value );
			}

			if ( empty( $attachment_ids ) ) {
				return array();
			}

			return $this->add_meta_for_attachments( $attachment_ids );
		}

		/**
		 * add_meta_for_attachments
		 *
		 * Transforme les attachments en données structurées et les informations des métas données
		 *
		 * @param array $attachment_ids le tableau des IDs
		 *
		 * @return array
		 */
		public function add_meta_for_attachments( $attachment_ids ): array {
			$attachment_data = array();
			$attachments     = get_posts(
				array(
					'post_type'              => 'attachment',
					'post__in'               => $attachment_ids,
					'post_status'            => 'inherit',
					'orderby'                => 'post__in',
					'order'                  => 'ASC',
					'numberposts'            => -1,
					'update_post_meta_cache' => true,
					'update_post_term_cache' => false,
				)
			);

			if ( ! is_wp_error( $attachments ) && ! empty( $attachments ) ) {
				foreach ( $attachments as $attachment ) {
					if ( ! in_array( $attachment->post_mime_type, $this->env['supported_type'], true ) ) {
						continue;
					}
					$metadata = array(); // Array des sizes pour chaque attachment utilisé dans render_field

					// Suppression de ces deux champs
					foreach ( array( 'post_password', 'guid' ) as $field ) {
						unset( $attachment->$field );
					}

					$meta_attachment  = wp_prepare_attachment_for_js( $attachment->ID );
					// Affecte les différentes dimensions dans les metadata
					if ( ! empty( $meta_attachment['sizes'] ) ) {
						foreach ( $meta_attachment['sizes'] as $key => $value ) {
							$metadata[ $key ] = $value;
						}
					}

					$attachment_data[] = array(
						'attachment' => $attachment,
						'metadata'   => $metadata,
					);
				}
			}
			return $attachment_data;
		}
	}
	acf_register_field_type( 'Eac_Acf_Field_Gallery' );

endif; // class_exists check
