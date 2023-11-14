<?php

namespace ImageSeoWP\Admin;

use ImageSeoWP\Helpers\AltFormat;
use ImageSeoWP\Helpers\Bulk\AltSpecification;
use ImageSeoWP\Helpers\Pages;
use ImageSeoWP\Admin\Settings\Fields\Admin_Fields;
use ImageSeoWP\Admin\Settings\Fields\FieldFactory;
use ImageSeoWP\Admin\Settings\Fields\InstallPlugin;
use ImageSeoWP\Admin\Settings\Fields\Textarea;
use ImageSeoWP\Admin\Settings\Fields\Text;
use ImageSeoWP\Admin\Settings\Fields\Checkbox;
use ImageSeoWP\Admin\Settings\Fields\Password;
use ImageSeoWP\Admin\Settings\Fields\Radio;
use ImageSeoWP\Admin\Settings\Fields\Select;

class SettingsPage {

	public static $instance;

	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SettingsPage ) ) {
			self::$instance = new SettingsPage();
		}

		return self::$instance;
	}

	private function __construct() {
		$this->hooks();
		$this->load_fields();
	}

	private function hooks() {
		add_action( 'admin_menu', array( $this, 'pluginMenu' ) );
		add_action( 'admin_notices', array( $this, 'bulk_process' ) );
		add_action( 'imageseo_settings_page_social_card_start', array( $this, 'social_card_preview' ) );
	}

	private function load_fields() {
		require_once IMAGESEO_DIR . '/src/Admin/Settings/Fields/Admin_Fields.php';
		require_once IMAGESEO_DIR . '/src/Admin/Settings/Fields/Checkbox.php';
		require_once IMAGESEO_DIR . '/src/Admin/Settings/Fields/Password.php';
		require_once IMAGESEO_DIR . '/src/Admin/Settings/Fields/Radio.php';
		require_once IMAGESEO_DIR . '/src/Admin/Settings/Fields/Select.php';
		require_once IMAGESEO_DIR . '/src/Admin/Settings/Fields/Text.php';
		require_once IMAGESEO_DIR . '/src/Admin/Settings/Fields/Textarea.php';
		require_once IMAGESEO_DIR . '/src/Admin/Settings/Fields/FieldFactory.php';
		require_once IMAGESEO_DIR . '/src/Admin/Settings/Fields/InstallPlugin.php';
	}

	/**
	 * Add menu and sub pages.
	 *
	 * @see admin_menu
	 */
	public function pluginMenu() {
		add_menu_page(
			'Image SEO',
			'Image SEO',
			'manage_options',
			Pages::SETTINGS,
			array(
				$this,
				'imageseo_settings'
			),
			'dashicons-imageseo-logo'
		);
	}

	/**
	 * Return active tab
	 *
	 * @return string
	 */
	private function get_active_tab() {
		return ( ! empty( $_GET['tab'] ) ? sanitize_title( wp_unslash( $_GET['tab'] ) ) : 'welcome' );
	}

	/**
	 * Return active section
	 *
	 * @param $sections
	 *
	 * @return string
	 */
	private function get_active_section( $sections ) {
		return ( ! empty( $_GET['section'] ) ? sanitize_title( wp_unslash( $_GET['section'] ) ) : $this->array_first_key( $sections ) );
	}

	/**
	 * Get settings URL
	 *
	 * @return string
	 */
	public static function get_url() {
		return admin_url( 'admin.php?page=imageseo-settings' );
	}

	/**
	 * @param array $settings
	 */
	private function generate_tabs( $settings ) {
		?>
		<h2 class="nav-tab-wrapper">
			<?php
			foreach ( $settings as $key => $section ) {
				// backwards compatibility for when $section did not have 'title' index yet (it simply had the title set at 0)
				$title = ( isset( $section['title'] ) ? $section['title'] : $section[0] );

				echo '<a href="' . esc_url( add_query_arg( 'tab', $key, self::get_url() ) ) . '" class="nav-tab' . ( ( $this->get_active_tab() === $key ) ? ' nav-tab-active' : '' ) . '">' . esc_html( $title ) . ( ( isset( $section['badge'] ) && true === $section['badge'] ) ? ' <span class="dlm-upsell-badge">PAID</span>' : '' ) . '</a>';
			}
			$total      = imageseo_get_service( 'QueryImages' )->getTotalImages();
			$totalNoAlt = imageseo_get_service( 'QueryImages' )->getNumberImageNonOptimizeAlt();
			if ( 0 !== absint( $totalNoAlt ) ) {
				$info_text = sprintf( __( 'There are <strong>%s</strong> images in your media library and <strong>%s</strong> doesn\'t (don\'t) have an alternative text.', 'imageseo' ), absint( $total ), absint( $totalNoAlt ) );
			} else {
				$info_text = __( 'Congrats, all your images have alt text!', 'imageseo' );
			}
			echo '<div class="imageseo-info-text">' . wp_kses_post( $info_text ) . '</div>';
			?>
		</h2>
		<?php
	}

	/**
	 * The settings page
	 *
	 * @since 2.0.9
	 */
	public function imageseo_settings() {
		// initialize settings
		$settings       = $this->get_settings();
		$tab            = $this->get_active_tab();
		$active_section = $this->get_active_section( $settings[ $tab ]['sections'] );
		$options        = imageseo_get_options();
		if ( ! empty( $options['api_key'] ) && isset( $options['allowed'] ) && $options['allowed'] ) {
			?>
			<style>
				tr[data-setting="register_account"], tr[data-setting='register_first_name'], tr[data-setting='register_last_name'], tr[data-setting='register_email'], tr[data-setting='register_password'], tr[data-setting='terms'], tr[data-setting='newsletter'], tr[data-setting='register_account'] {
					display: none;
				}
			</style>
			<?php
		} else {
			?>
			<style>
				a.nav-tab[href$='imageseo-settings&tab=settings'],
				a.nav-tab[href$='imageseo-settings&tab=social_card'],
				a.nav-tab[href$='imageseo-settings&tab=bulk_optimizations'] {
					pointer-events: none;
					opacity: 0.3;
				}
			</style>
			<?php
		}
		$this->display_header();
		?>
		<div class="wrap imageseo-admin-settings <?php echo esc_attr( $tab ) . ' ' . esc_attr( $active_section ); ?>">
			<hr class="wp-header-end">
			<form method="post" action="options.php">

				<?php $this->generate_tabs( $settings ); ?>

				<?php
				// loop fields for this tab
				if ( isset( $settings[ $tab ] ) ) {
					do_action( 'imageseo_settings_page_' . $tab . '_start' );
					if ( count( $settings[ $tab ]['sections'] ) > 1 ) {

						?>
						<div class="wp-clearfix">
							<ul class="subsubsub imageseo-sub-nav">
								<?php foreach ( $settings[ $tab ]['sections'] as $section_key => $section ) : ?>
									<?php echo '<li' . ( ( $active_section == $section_key ) ? " class='active-section'" : '' ) . '>'; ?>
									<a href="<?php echo esc_url(
										add_query_arg(
											array(
												'tab'     => $tab,
												'section' => $section_key
											),
											self::get_url()
										)
									); ?>"><?php echo esc_html( $section['title'] ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						</div><!--.wp-clearfix-->
						<h2><?php echo esc_html( $settings[ $tab ]['sections'][ $active_section ]['title'] ); ?></h2>
						<?php
					}

					settings_fields( IMAGESEO_OPTION_GROUP );

					if ( ! empty( $settings[ $tab ]['sections'][ $active_section ]['fields'] ) ) {

						echo '<table class="form-table imageseo-' . esc_attr( $tab ) . '">';
						echo '<input type="hidden" name="imageseo-tab" value="' . esc_attr( $tab ) . '">';
						echo '<input type="hidden" name="imageseo-section" value="' . esc_attr( $active_section ) . '">';

						foreach ( $settings[ $tab ]['sections'][ $active_section ]['fields'] as $option ) {

							$cs = 1;

							if ( ! isset( $option['type'] ) ) {
								$option['type'] = '';
							}

							$tr_class = 'imageseo_settings imageseo_' . $option['type'] . '_setting';
							echo '<tr valign="top" data-setting="' . ( isset( $option['name'] ) ? esc_attr( $option['name'] ) : '' ) . '" class="' . esc_attr( $tr_class ) . '">';
							if ( isset( $option['label'] ) && '' !== $option['label'] ) {
								echo '<th scope="row"><label for="setting-' . esc_attr( $option['name'] ) . '">' . esc_attr( $option['label'] ) . '</label>';
								if ( isset( $option['desc'] ) && '' !== $option['desc'] ) {
									?>
									<div class='wpchill-tooltip'><i>[?]</i>
										<div class='wpchill-tooltip-content'>
											<?php echo wp_kses_post( $option['desc'] ); ?>
										</div>
									</div>
									<?php
								}
								echo '</th>';
							} else {
								$cs ++;
							}

							echo '<td colspan="' . esc_attr( $cs ) . '">';

							if ( ! isset( $option['type'] ) ) {
								$option['type'] = '';
							}

							// make new field object
							$field = FieldFactory::make( $option );

							// check if factory made a field
							if ( null !== $field ) {
								// render field
								$field->render();
							}

							echo '</td></tr>';

						}

						echo '</table>';
					}
					do_action( 'imageseo_settings_page_' . $tab . '_end' );
				}
				?>
				<div class="wp-clearfix"></div>
				<?php
				if ( 'welcome' !== $tab && isset( $settings[ $tab ] ) && ( isset( $settings[ $tab ]['sections'][ $active_section ]['fields'] ) && ! empty( $settings[ $tab ]['sections'][ $active_section ]['fields'] ) ) ) {
					?>
					<p class="submit">
						<input type="submit" class="button-primary"
						       value="<?php echo esc_html__( 'Save Changes', 'imageseo' ); ?>"/>
					</p>
				<?php } ?>
			</form>
		</div>
		<?php
	}

	public function get_settings() {
		if ( ! function_exists( 'wp_get_available_translations' ) ) {
			require_once ABSPATH . 'wp-admin/includes/translation-install.php';
		}
		$language_codes = \wp_get_available_translations();
		$languages      = array();
		foreach ( $language_codes as $key => $language ) {
			$languages[ $key ] = $language['english_name'];
		}
		// Create options arrays.
		$metas      = array();
		$fill_types = array();
		foreach ( AltSpecification::getMetas() as $meta ) {
			$metas[ $meta['id'] ] = $meta['label'];
		}
		foreach ( AltSpecification::getFillType() as $fill_type ) {
			$fill_types[ $fill_type['id'] ] = $fill_type['label'];
		}

		$settings = array(
			'welcome'            => array(
				'title'       => __( 'Welcome on board', 'imageseo' ),
				'description' => __( 'SEO Fact : More than 20% of Google traffic comes from image searches. We use AI to automatically optimize your images for SEO.', 'imageseo' ),
				'sections'    => array(
					'welcome' => array(
						'title'       => __( 'Welcome on board', 'imageseo' ),
						'description' => __( 'SEO Fact : More than 20% of Google traffic comes from image searches. We use AI to automatically optimize your images for SEO.', 'imageseo' ),
						'fields'      => array(
							array(
								'name'     => 'manage_account',
								'std'      => '',
								'title'    => __( 'Manage account', 'imageseo' ),
								'type'     => 'title',
								'priority' => 30,
							),
							array(
								'name'     => 'go_to_application',
								'std'      => '',
								'label'    => __( 'Go to application', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Click the button to go to the Application Dashoard.', 'imageseo' ),
								'type'     => 'action_button',
								'link'     => 'https://app.imageseo.io/login',
								'priority' => 30,
							),
							array(
								'name'     => 'already_api',
								'std'      => '',
								'title'    => __( 'You already have an API Key?', 'imageseo' ),
								'type'     => 'title',
								'priority' => 30,
							),
							array(
								'name'     => 'api_key',
								'std'      => '',
								'label'    => __( 'You API Key', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'If you tick this box, the plugin will automatically write an alternative to the images you will upload.', 'imageseo' ),
								'type'     => 'password',
								'priority' => 30,
							),
							array(
								'name'     => 'validate_api_key',
								'std'      => '',
								'label'    => __( 'Validate your API Key', 'imageseo' ),
								'cb_label' => '',
								'link'     => '#',
								'desc'     => __( 'If you tick this box, the plugin will automatically write an alternative to the images you will upload.', 'imageseo' ),
								'type'     => 'action_button',
								'priority' => 30,
							),
						)
					)
				),
			),
			'settings'           => array(
				'title'       => __( 'Settings', 'imageseo' ),
				'description' => __( 'You can change the default settings here.', 'imageseo' ),
				'sections'    => array(
					'optimization'       => array(
						'title'  => __( 'On-upload optimization', 'imageseo' ),
						'fields' => array(
							array(
								'name'     => 'active_alt_write_upload',
								'std'      => '',
								'label'    => __( 'Fill ALT', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'If you tick this box, the plugin will automatically write an alternative to the images you will upload.', 'imageseo' ),
								'type'     => 'checkbox',
								'priority' => 30,
							),
							array(
								'name'     => 'active_rename_write_upload',
								'std'      => '',
								'label'    => __( 'Rename files', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'If you tick this box, the plugin will automatically rewrite with SEO friendly content the name of the images you will upload. You will consume one credit for each image optimized.', 'imageseo' ),
								'type'     => 'checkbox',
								'priority' => 30,
							),
							array(
								'name'     => 'default_language_ia',
								'std'      => 'en_GB',
								'label'    => __( 'Language', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'In which language should we write your filenames and alternative texts.', 'imageseo' ),
								'type'     => 'select',
								'options'  => $languages,
								'priority' => 30,
							),
						)
					),
					'social_media_cards' => array(
						'title'  => __( 'Social Media Cards Generator', 'imageseo' ),
						'fields' => array(
							array(
								'name'     => 'social_media_post_types',
								'std'      => '',
								'label'    => __( 'Automatic generation', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Enable the automatic generation of Social Media Card for the selected post types. You will consume one credit by SociaL Media Cards created (1 page = 1 Social media card working on Twitter, Facebook and LinkedIn).', 'imageseo' ),
								'type'     => 'multi_checkbox',
								'options'  => array(
									'post' => __( 'Posts', 'imageseo' ),
									'page' => __( 'Pages', 'imageseo' ),
								),
								'priority' => 30,
							),
						)
					)
				),
			),
			'social_card'        => array(
				'title'       => __( 'Social Card', 'imageseo' ),
				'description' => __( 'Social cards are used by Twitter, LinkedIn & Facebook to display the preview of your pages and posts. Tuning Social Card will increase your engagement on Social Media.', 'imageseo' ),
				'sections'    => array(
					'card_template' => array(
						'title'  => __( 'Data displayed', 'imageseo' ),
						'fields' => array(
							array(
								'name'     => 'visibilitySubTitle',
								'std'      => '',
								'label'    => __( 'Subtitle', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Show the price product or author depending on the page ( Author or Product price (WooCommerce only) )', 'imageseo' ),
								'parent'   => 'social_media_settings',
								'type'     => 'checkbox',
								'priority' => 30,
							),
							array(
								'name'     => 'visibilitySubTitleTwo',
								'std'      => '',
								'label'    => __( 'Subtitle 2', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Show the reading time of an article or the number of reviews ( Reading time or Number of reviews (WooCommerce only) ).', 'imageseo' ),
								'type'     => 'checkbox',
								'parent'   => 'social_media_settings',
								'priority' => 30,
							),
							array(
								'name'     => 'visibilityRating',
								'std'      => '',
								'label'    => __( 'Stars rating', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Show the stars linked to a review of your product for example.', 'imageseo' ),
								'type'     => 'checkbox',
								'parent'   => 'social_media_settings',
								'priority' => 30,
							),
							array(
								'name'     => 'visibilityAvatar',
								'std'      => '',
								'label'    => __( 'Author avatar', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Only use for post content', 'imageseo' ),
								'type'     => 'checkbox',
								'parent'   => 'social_media_settings',
								'priority' => 30,
							),
							array(
								'name'     => 'layout',
								'std'      => '',
								'label'    => __( 'Layout', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Show the price product or author depending on the page', 'imageseo' ),
								'type'     => 'select',
								'parent'   => 'social_media_settings',
								'options'  => array(
									'CARD_LEFT'  => __( 'Card left', 'imageseo' ),
									'CARD_RIGHT' => __( 'Card right', 'imageseo' ),
								),
								'priority' => 30,
							),
							array(
								'name'     => 'textAlignment',
								'std'      => '',
								'label'    => __( 'Text alignment', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Show the price product or author depending on the page', 'imageseo' ),
								'type'     => 'select',
								'parent'   => 'social_media_settings',
								'options'  => array(
									'top'    => __( 'Top', 'imageseo' ),
									'center' => __( 'Center', 'imageseo' ),
									'bottom' => __( 'Bottom', 'imageseo' ),
								),
								'priority' => 30,
							),
							array(
								'name'     => 'textColor',
								'std'      => '',
								'label'    => __( 'Text color', 'imageseo' ),
								'cb_label' => '',
								'desc'     => '',
								'type'     => 'colorpicker',
								'parent'   => 'social_media_settings',
								'priority' => 30,
							),
							array(
								'name'     => 'contentBackgroundColor',
								'std'      => '',
								'label'    => __( 'Background Color', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Content background color', 'imageseo' ),
								'type'     => 'colorpicker',
								'parent'   => 'social_media_settings',
								'priority' => 30,
							),
							array(
								'name'     => 'starColor',
								'std'      => '',
								'label'    => __( 'Star color', 'imageseo' ),
								'cb_label' => '',
								'desc'     => '',
								'type'     => 'colorpicker',
								'parent'   => 'social_media_settings',
								'priority' => 30,
							),
							array(
								'name'     => 'logoUrl',
								'std'      => '',
								'label'    => __( 'Logo', 'imageseo' ),
								'cb_label' => '',
								'desc'     => '',
								'type'     => 'file_picker',
								'parent'   => 'social_media_settings',
								'priority' => 30,
							),
							array(
								'name'     => 'defaultBgImg',
								'std'      => '',
								'label'    => __( 'Background image', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Card\'s background image.', 'imageseo' ),
								'type'     => 'file_picker',
								'parent'   => 'social_media_settings',
								'priority' => 30,
							),
						)
					),
				),
			),
			'bulk_optimizations' => array(
				'title'       => __( 'Bulk Optimization', 'imageseo' ),
				'description' => __( 'SEO Fact : More than 20% of Google traffic comes from image searches. We use AI to automatically optimize your images for SEO.', 'imageseo' ),
				'sections'    => array(
					'welcome' => array(
						'title'       => __( 'Bulk optimization', 'imageseo' ),
						'description' => __( 'SEO Fact : More than 20% of Google traffic comes from image searches. We use AI to automatically optimize your images for SEO.', 'imageseo' ),
						'fields'      => array(
							array(
								'name'     => 'language',
								'std'      => '',
								'label'    => __( 'Language', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'In which language should we write your filenames and alternative texts?', 'imageseo' ),
								'type'     => 'select',
								'options'  => $languages,
								'priority' => 30,
							),
							array(
								'name'     => 'altFilter',
								'std'      => '',
								'label'    => __( 'Images', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Which images do you want to optimize?', 'imageseo' ),
								'type'     => 'select',
								'options'  => $metas,
								'priority' => 30,
							),
							array(
								'name'     => 'optimizeAlt',
								'std'      => '',
								'label'    => __( 'ALT text settings', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Fill out and optimize ALT Texts for SEO and Accessibility.', 'imageseo' ),
								'type'     => 'checkbox',
								'priority' => 30,
							),
							array(
								'name'     => 'altFill',
								'std'      => '',
								'label'    => __( 'Optimize Alt', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Which alt texts do you want to optimize?', 'imageseo' ),
								'type'     => 'select',
								'options'  => $fill_types,
								'priority' => 30,
							),
							array(
								'name'     => 'formatAlt',
								'std'      => '',
								'label'    => __( 'Format', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Automatically write an alternative to the uploaded images.', 'imageseo' ),
								'type'     => 'format',
								'options'  => AltFormat::getFormats(),
								'priority' => 30,
							),
							array(
								'name'     => 'formatAltCustom',
								'std'      => '',
								'label'    => __( 'Custom template', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'You can use multiple shortcode or what you want. Only for advanced user', 'imageseo' ),
								'type'     => 'text',
								'priority' => 30,
							),
							array(
								'name'     => 'optimizeFile',
								'std'      => '',
								'label'    => __( 'Rename files', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Renaming or moving file is always tricky and can on rare occasion brake your images. Before triggering, try to rename your file in the library , then check if the images you renamed are available on the related pages. If so, you can bulk optimize the filenames. Bulking alt texts is always safe.', 'imageseo' ),
								'type'     => 'checkbox',
								'priority' => 30,
							),
							array(
								'name'     => 'start_bulk_process',
								'std'      => '',
								'label'    => __( 'Start optimization', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Before you start the optimization process please make sure you have saved all your required settings.', 'imageseo' ),
								'type'     => 'action_button',
								'priority' => 30,
							),
						)
					)
				),
			),
		);
		$options  = imageseo_get_options();
		if ( empty( $options['api_key'] ) || ! isset( $options['allowed'] ) || ! $options['allowed'] ) {
			$settings['welcome']['sections']['welcome']['fields'] = array_merge(
				array(
					array(
						'name'     => 'register_account',
						'std'      => '',
						'title'    => __( 'Create an account - It\'s free', 'imageseo' ),
						'type'     => 'title',
						'priority' => 30,
					),
					array(
						'name'     => 'register_first_name',
						'std'      => '',
						'label'    => __( 'First Name', 'imageseo' ),
						'cb_label' => '',
						'type'     => 'text',
						'priority' => 30,
					),
					array(
						'name'     => 'register_last_name',
						'std'      => '',
						'label'    => __( 'Last name', 'imageseo' ),
						'cb_label' => '',
						'type'     => 'text',
						'priority' => 30,
					),
					array(
						'name'     => 'register_email',
						'std'      => '',
						'label'    => __( 'Email', 'imageseo' ),
						'cb_label' => '',
						'type'     => 'email',
						'priority' => 30,
					),
					array(
						'name'     => 'register_password',
						'std'      => '',
						'label'    => __( 'Password', 'imageseo' ),
						'cb_label' => '',
						'type'     => 'password',
						'priority' => 30,
					),
					array(
						'name'     => 'terms',
						'std'      => '',
						'label'    => __( 'Terms of Service', 'imageseo' ),
						'cb_label' => '',
						'desc'     => __( 'By checking this you agree to ImageSEO\'s <a href="https://imageseo.io/terms-conditions/" target="_blank">Terms of Service</a>', 'imageseo' ),
						'type'     => 'checkbox',
						'priority' => 30,
					),
					array(
						'name'     => 'newsletter',
						'std'      => '',
						'label'    => __( 'Newsletter', 'imageseo' ),
						'cb_label' => '',
						'desc'     => __( 'By checking this you subscribe to news and features updates, as well as occasional company announcements.', 'imageseo' ),
						'type'     => 'checkbox',
						'priority' => 30,
					),
					array(
						'name'     => 'register_account',
						'std'      => '',
						'label'    => __( 'Register', 'imageseo' ),
						'cb_label' => '',
						'type'     => 'action_button',
						'link'     => '#',
						'priority' => 30,
					)
				),
				$settings['welcome']['sections']['welcome']['fields']
			);
		}

		return apply_filters( 'imageseo_settings_tabs', $settings );
	}


	private function array_first_key( $a ) {
		reset( $a );

		return key( $a );
	}

	/**
	 * Display a notice with the bulk process status
	 *
	 * @since 2.0.9
	 */
	public function bulk_process() {
		$current_screen = get_current_screen();
		// Only show the notice on the settings page and if the user is admin
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( 'toplevel_page_imageseo-settings' !== $current_screen->base ) {
			return;
		}
		$bulk_settings = get_option( '_imageseo_bulk_process_settings', false );
		// If the bulk process settings are not set, return
		if ( false === $bulk_settings ) {
			return;
		}

		// Display a notice with the bulk process status
		?>
		<div class="notice notice-info">
			<p class="imageseo-optimization-status"><?php echo sprintf( esc_html__( 'Optimizing %s images!', 'imageseo' ), absint( $bulk_settings['total_images'] ) ); ?>
				<span class='imageseo-optimization__optimized_images'></span></p>
			<button id="get_bulk_process"
			        class="button button-primary"><?php esc_html_e( 'Update status', 'imageseo' ); ?></button>
			<button id='stop_bulk_process'
			        class="button button-primary"><?php esc_html_e( 'Stop process', 'imageseo' ); ?></button>
		</div>
		<?php
	}

	/**
	 * Display the Social Card preview
	 *
	 * @since 2.0.9
	 */
	public function social_card_preview() {
		$options        = imageseo_get_options();
		$options        = wp_parse_args( $options, imageseo_get_service( 'Option' )->getOptionsDefault() );
		$card_layout    = ( isset( $options['social_media_settings']['layout'] ) && 'CARD_LEFT' === $options['social_media_settings']['layout'] ) ? 'imageseo-media__layout--card-left' : 'imageseo-media__layout--card-right';
		$logo_url       = ( isset( $options['social_media_settings']['logoUrl'] ) && '' !== $options['social_media_settings']['logoUrl'] ) ? $options['social_media_settings']['logoUrl'] : IMAGESEO_DIRURL . '/dist/images/default_logo.png';
		$background_url = ( isset( $options['social_media_settings']['defaultBgImg'] ) && '' !== $options['social_media_settings']['defaultBgImg'] ) ? $options['social_media_settings']['defaultBgImg'] : IMAGESEO_DIRURL . '/dist/images/favicon.png';
		?>
		<div id='imageseo-preview-image'
		     class="<?php echo esc_attr( $card_layout ) ?> imageseo-media__container imageseo-media__container--preview"
		     style="border: 1px solid #999;        margin: 0 auto;    background-color: <?php echo esc_attr( $options['social_media_settings']['contentBackgroundColor'] ) ?>;">
			<div class='imageseo-media__container__image'
			     style="background-color: #ccc; background-image: url(<?php echo esc_url( $background_url ); ?>); background-position:center center; background-size:cover; background-repeat:no-repeat;">
			</div>
			<div class="imageseo-media__container__content imageseo-media__container__content--center">
				<img class='imageseo-media__content__logo' src="<?php echo esc_url( $logo_url ) ?>">
				<div class='imageseo-media__content__title'
				     style="color:<?php echo esc_attr( $options['social_media_settings']['textColor'] ) ?>;">
					Lorem ipsum (post_title)
				</div>
				<div class='imageseo-media__content__sub-title'
				     style="color:<?php echo esc_attr( $options['social_media_settings']['textColor'] ) ?>;<?php echo ( isset( $options['social_media_settings']['visibilitySubTitle'] ) && ( '1' === $options['social_media_settings']['visibilitySubTitle'] || true === $options['social_media_settings']['visibilitySubTitle'] ) ) ? '' : 'display:none;' ?>;">
					Sub title (like price or author)
				</div>
				<div class='imageseo-media__content__sub-title-two'
				     style="color:<?php echo esc_attr( $options['textColor'] ) ?>;<?php echo ( isset( $options['social_media_settings']['visibilitySubTitleTwo'] ) && ( '1' === $options['social_media_settings']['visibilitySubTitleTwo'] || true === $options['social_media_settings']['visibilitySubTitleTwo'] ) ) ? '' : 'display:none;' ?>;">
					Sub title 2 (like price or author)
				</div>
				<img class='imageseo-media__content__avatar'
				     src="<?php echo esc_url( IMAGESEO_DIRURL . '/dist/images/avatar-default.jpg' ); ?>"
				     style="<?php echo ( isset( $options['social_media_settings']['visibilityAvatar'] ) && ( '1' === $options['social_media_settings']['visibilityAvatar'] ) || true === $options['social_media_settings']['visibilityAvatar'] ) ? '' : 'display:none;' ?>">
				<div class='imageseo-media__content__stars flex'
				     style="<?php echo ( isset( $options['social_media_settings']['visibilityRating'] ) && '1' === $options['social_media_settings']['visibilityRating'] ) ? '' : 'display:none;' ?>">
					<?php
					echo $this->start_display( $options );
					echo $this->start_display( $options );
					echo $this->start_display( $options );
					echo $this->start_display( $options );
					?>
				</div>
			</div>
		</div>
		<?php
	}

	public function get_tab_settings( $tab, $section = false ) {
		$tab_settings = $this->get_settings();
		$tab_setting  = $tab_settings[ $tab ];
		$fields       = array();
		if ( ! empty( $tab_setting['sections'] ) ) {
			if ( $section ) {
				foreach ( $tab_setting['sections'][ $section ]['fields'] as $field ) {
					$fields[] = $field['name'];
				}
			} else {
				foreach ( $tab_setting['sections'] as $section ) {
					foreach ( $section['fields'] as $field ) {
						$fields[] = $field['name'];
					}
				}
			}
		}

		return $fields;
	}

	/**
	 * Display the header of the settings page
	 *
	 * @since 3.0.0
	 */
	public function display_header() {
		$user    = imageseo_get_service( 'ClientApi' )->getOwnerByApiKey();
		$credits = 0;
		if ( $user ) {
			$credits = absint( $user['plan']['limit_images'] ) + absint( $user['bonus_stock_images'] ) - absint( $user['current_request_images'] );
		}
		?>
		<div class="imageseo-info-header">
			<div class="imageseo-info-header__logo">
				<img src="<?php echo esc_url( IMAGESEO_URL_DIST . '/images/logo-blue.svg' ); ?>">
			</div>
			<div class='imageseo-info-header__optimization'>
			</div>
			<div class='imageseo-info-header__credits'>
				<?php echo '<p>' . esc_html__( 'Remaining credits:', 'imageseo' ) . '<span id="imageseo-remaining-credits"><strong>' . absint( $credits ) . '</strong></span></p><p><a href="https://app.imageseo.io/plan" target="_blank" class="button button-primary">' . esc_html__( 'Buy more!', 'imageseo' ) . '</a></p>'; ?>
			</div>
			<!--<div class="imageseo-info-header__support">
				<a href="https://imageseo.io/documentation/" target="_blank" class="button button-secondary"><span class='dashicons dashicons-external'></span><?php /*echo esc_html__( 'Documentation', 'imageseo' ); */ ?></a>
				<a href="https://imageseo.io/support/" target="_blank" class="button button-secondary"><span class='dashicons dashicons-email-alt'></span><?php /*echo esc_html__( 'Contact us for support!' , 'imageseo' ); */ ?></a>
			</div>-->
		</div>
		<?php
	}

	public function start_display( $options ) {
		?>
		<svg
			xmlns='http://www.w3.org/2000/svg'
			width='24'
			height='24'
			viewBox='0 0 24 24'
			fill="<?php echo esc_attr( $options['social_media_settings']['starColor'] ) ?>"
			stroke="<?php echo esc_attr( $options['social_media_settings']['starColor'] ) ?>"
			strokeWidth='2'
			strokeLinecap='round'
			strokeLinejoin='round'
		>
			<polygon
				points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'></polygon>
		</svg>
		<?php
	}
}
