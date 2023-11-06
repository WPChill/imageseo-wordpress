<?php

namespace ImageSeoWP\Admin;

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

		add_submenu_page( 'admin.php??page=imageseo-settings', 'Settings', 'Settings', 'manage_options', 'imageseo-settings-page', array(
			$this,
			'imageseo_settings'
		),                16 );
	}

	/**
	 * Return active tab
	 *
	 * @return string
	 */
	private function get_active_tab() {
		return ( ! empty( $_GET['tab'] ) ? sanitize_title( wp_unslash( $_GET['tab'] ) ) : 'general' );
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
		return admin_url( 'admin.php?page=imageseo-settings-page' );
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

		?>
		<div class="wrap dlm-admin-settings <?php echo esc_attr( $tab ) . ' ' . esc_attr( $active_section ); ?>">
			<hr class="wp-header-end">
			<form method="post" action="options.php">

				<?php $this->generate_tabs( $settings ); ?>

				<?php

				if ( ! empty( $_GET['settings-updated'] ) ) {
					$this->need_rewrite_flush = true;
					echo '<div class="updated notice is-dismissible"><p>' . esc_html__( 'Settings successfully saved', 'download-monitor' ) . '</p></div>';

					$dlm_settings_tab_saved = get_option( 'dlm_settings_tab_saved', 'general' );

					echo '<script type="text/javascript">var dlm_settings_tab_saved = "' . esc_js( $dlm_settings_tab_saved ) . '";</script>';
				}

				// loop fields for this tab
				if ( isset( $settings[ $tab ] ) ) {

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

						echo '<table class="form-table">';

						foreach ( $settings[ $tab ]['sections'][ $active_section ]['fields'] as $option ) {

							$cs = 1;

							if ( ! isset( $option['type'] ) ) {
								$option['type'] = '';
							}

							$tr_class = 'dlm_settings dlm_' . $option['type'] . '_setting';
							echo '<tr valign="top" data-setting="' . ( isset( $option['name'] ) ? esc_attr( $option['name'] ) : '' ) . '" class="' . esc_attr( $tr_class ) . '">';
							if ( isset( $option['label'] ) && '' !== $option['label'] ) {
								echo '<th scope="row"><label for="setting-' . esc_attr( $option['name'] ) . '">' . esc_attr( $option['label'] ) . '</a></th>';
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

								if ( isset( $option['desc'] ) && '' !== $option['desc'] ) {
									echo ' <p class="imageseo-description description">' . wp_kses_post( $option['desc'] ) . '</p>';
								}
							}

							echo '</td></tr>';

						}

						echo '</table>';
					}
				}

				?>
				<div class="wp-clearfix"></div>
				<?php
				if ( isset( $settings[ $tab ] ) && ( isset( $settings[ $tab ]['sections'][ $active_section ]['fields'] ) && ! empty( $settings[ $tab ]['sections'][ $active_section ]['fields'] ) ) ) {
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
								'name'     => 'register_first_name',
								'std'      => '',
								'label'    => __( 'First Name', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Register your First Name on our App.', 'imageseo' ),
								'type'     => 'text',
								'priority' => 30,
							),
							array(
								'name'     => 'register_last_name',
								'std'      => '',
								'label'    => __( 'Last name', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Register your Last Name on our App.', 'imageseo' ),
								'type'     => 'text',
								'priority' => 30,
							),
							array(
								'name'     => 'register_email',
								'std'      => '',
								'label'    => __( 'Email', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Register your Email on our App.', 'imageseo' ),
								'type'     => 'email',
								'priority' => 30,
							),
							array(
								'name'     => 'register_password',
								'std'      => '',
								'label'    => __( 'Application password', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Register your password on our App', 'imageseo' ),
								'type'     => 'password',
								'priority' => 30,
							),
							array(
								'name'     => 'terms',
								'std'      => '',
								'label'    => __( 'Terms of Service', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'By checking this you agree to ImageSEO\'s <a href="https://imageseo.io/terms-conditions/">Terms of Service</a>', 'imageseo' ),
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
								'desc'     => __( 'Click the button to go to the Application Dashoard.', 'imageseo' ),
								'type'     => 'action_button',
								'link'     => '#',
								'priority' => 30,
							),
							array(
								'name'     => 'manage_account',
								'std'      => '',
								'label'    => __( 'Go to application', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Click the button to go to the Application Dashoard.', 'imageseo' ),
								'type'     => 'action_button',
								'link'     => 'https://app.imageseo.io/login',
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
								'label'    => __( 'Fill out ALT Texts', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'If you tick this box, the plugin will automatically write an alternative to the images you will upload.', 'imageseo' ),
								'type'     => 'checkbox',
								'priority' => 30,
							),
							array(
								'name'     => 'active_rename_write_upload',
								'std'      => '',
								'label'    => __( 'Rename your files', 'imageseo' ),
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
								'label'    => __( 'Author or Product price (WooCommerce only) - Subtitle', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Show the price product or author depending on the page', 'imageseo' ),
								'type'     => 'sub_checkbox',
								'parent'   => 'social_media_settings',
								'priority' => 30,
							),
							array(
								'name'     => 'visibilitySubTitleTwo',
								'std'      => '',
								'label'    => __( 'Reading time or Number of reviews (WooCommerce only) - Subtitle 2', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Show the reading time of an article or the number of reviews.', 'imageseo' ),
								'type'     => 'sub_checkbox',
								'parent'   => 'social_media_settings',
								'priority' => 30,
							),
							array(
								'name'     => 'visibilityRating',
								'std'      => '',
								'label'    => __( 'Stars rating', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Show the stars linked to a review of your product for example.', 'imageseo' ),
								'type'     => 'sub_checkbox',
								'parent'   => 'social_media_settings',
								'priority' => 30,
							),
							array(
								'name'     => 'visibilityAvatar',
								'std'      => '',
								'label'    => __( 'Display the author avatar', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Only use for post content', 'imageseo' ),
								'type'     => 'sub_checkbox',
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
								'priority' => 30,
							),
							array(
								'name'     => 'contentBackgroundColor',
								'std'      => '',
								'label'    => __( 'Background Color', 'imageseo' ),
								'cb_label' => '',
								'desc'     => '',
								'type'     => 'colorpicker',
								'priority' => 30,
							),
							array(
								'name'     => 'starColor',
								'std'      => '',
								'label'    => __( 'Star color', 'imageseo' ),
								'cb_label' => '',
								'desc'     => '',
								'type'     => 'colorpicker',
								'priority' => 30,
							),
							array(
								'name'     => 'logoUrl',
								'std'      => IMAGESEO_DIRURL . 'dist/images/default_logo.png',
								'label'    => __( 'Your logo', 'imageseo' ),
								'cb_label' => '',
								'desc'     => '',
								'type'     => 'text',
								'priority' => 30,
							),
							array(
								'name'     => 'defaultBgImg',
								'std'      => IMAGESEO_DIR . 'dist/images/default_image.png',
								'label'    => __( 'Default background image', 'imageseo' ),
								'cb_label' => '',
								'desc'     => '',
								'type'     => 'text',
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
								'label'    => __( 'File names and alt texts', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'In which language should we write your filenames and alternative texts?', 'imageseo' ),
								'type'     => 'select',
								'options'  => $languages,
								'priority' => 30,
							),
							array(
								'name'     => 'altFilter',
								'std'      => '',
								'label'    => __( 'Optimize images', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Which images do you want to optimize?', 'imageseo' ),
								'type'     => 'select',
								'options'  => array(
									'ALL'            => __( 'Only Media Library images', 'imageseo' ),
									'FEATURED_IMAGE' => __( 'Only featured images', 'imageseo' ),
								),
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
								'options'  => array(
									'FILL_ALL'        => __( 'Fill out all ALT Texts', 'imageseo' ),
									'FILL_ONLY_EMPTY' => __( 'Fill out only empty ALT Texts', 'imageseo' ),
								),
								'priority' => 30,
							),
							array(
								'name'     => 'formatAlt',
								'std'      => '',
								'label'    => __( 'Format', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Automatically write an alternative to the uploaded images.', 'imageseo' ),
								'type'     => 'radio',
								'options'  => array(
									'[keyword_1] - [keyword_2]',
									'[post_title] - [keyword_1]',
									'[site_title] - [keyword_1]',
									'CUSTOM_FORMAT'
								),
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
								'desc'     => '',
								'type'     => 'action_button',
								'priority' => 30,
							),
						)
					)
				),
			),
		);

		return apply_filters( 'imageseo_settings_tabs', $settings );
	}


	private function array_first_key( $a ) {
		reset( $a );

		return key( $a );
	}
}
