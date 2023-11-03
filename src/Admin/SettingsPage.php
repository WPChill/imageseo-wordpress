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

		add_menu_page(
			'Image SEO',
			'Image SEO',
			'manage_options',
			Pages::SETTINGS,
			[ $this, 'pluginSettingsPage' ],
			'dashicons-imageseo-logo'
		);

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
	 * @param array $array
	 *
	 * @return mixed
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

					if ( isset( $settings[ $tab ]['sections'][ $active_section ]['fields'] ) && ! empty( $settings[ $tab ]['sections'][ $active_section ]['fields'] ) ) {

						// output correct settings_fields
						// We change the output location so that it won't interfere with our upsells
						$option_name = 'imageseo_' . $tab . '_' . $active_section;
						settings_fields( $option_name );

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
								'type'     => 'text',
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
								'label'    => __( 'Automatically fill out ALT Texts when you upload an image', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'If you tick this box, the plugin will automatically write an alternative to the images you will upload.', 'imageseo' ),
								'type'     => 'checkbox',
								'priority' => 30,
							),
							array(
								'name'     => 'active_rename_write_upload',
								'std'      => '',
								'label'    => __( 'Automatically fill out ALT Texts when you upload an image', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'If you tick this box, the plugin will automatically write an alternative to the images you will upload.', 'imageseo' ),
								'type'     => 'checkbox',
								'priority' => 30,
							),
							array(
								'name'     => 'default_language_ia',
								'std'      => '',
								'label'    => __( 'Automatically rename your files when you upload a media', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'If you tick this box, the plugin will automatically rewrite with SEO friendly content the name of the images you will upload.', 'imageseo' ),
								'type'     => 'select',
								'options'  => array(),
								'priority' => 30,
							),
						)
					),
					'social_media_cards' => array(
						'title'  => __( 'Social Media Cards Generator', 'imageseo' ),
						'fields' => array(
							array(
								'name'     => 'active_alt_write_upload',
								'std'      => '',
								'label'    => __( 'Automatically fill out ALT Texts when you upload an image', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'If you tick this box, the plugin will automatically write an alternative to the images you will upload.', 'imageseo' ),
								'type'     => 'checkbox',
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
								'type'     => 'checkbox',
								'priority' => 30,
							),
							array(
								'name'     => 'visibilitySubTitleTwo',
								'std'      => '',
								'label'    => __( 'Reading time or Number of reviews (WooCommerce only) - Subtitle 2', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Show the reading time of an article or the number of reviews.', 'imageseo' ),
								'type'     => 'checkbox',
								'priority' => 30,
							),
							array(
								'name'     => 'visibilityRating',
								'std'      => '',
								'label'    => __( 'Stars rating', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Show the stars linked to a review of your product for example.', 'imageseo' ),
								'type'     => 'checkbox',
								'priority' => 30,
							),
							array(
								'name'     => 'visibilityAvatar',
								'std'      => '',
								'label'    => __( 'Display the author avatar', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Only use for post content', 'imageseo' ),
								'type'     => 'checkbox',
								'priority' => 30,
							),
						)
					),
					'card_layout'   => array(
						'title'  => __( 'Card layout', 'imageseo' ),
						'fields' => array(
							array(
								'name'     => 'layout',
								'std'      => '',
								'label'    => __( 'Author or Product price (WooCommerce only) - Subtitle', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Show the price product or author depending on the page', 'imageseo' ),
								'type'     => 'checkbox',
								'priority' => 30,
							),
							array(
								'name'     => 'textColor',
								'std'      => '',
								'label'    => __( 'Reading time or Number of reviews (WooCommerce only) - Subtitle 2', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Show the reading time of an article or the number of reviews.', 'imageseo' ),
								'type'     => 'checkbox',
								'priority' => 30,
							),
							array(
								'name'     => 'contentBackgroundColor',
								'std'      => '',
								'label'    => __( 'Stars rating', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Show the stars linked to a review of your product for example.', 'imageseo' ),
								'type'     => 'checkbox',
								'priority' => 30,
							),
							array(
								'name'     => 'starColor',
								'std'      => '',
								'label'    => __( 'Display the author avatar', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Only use for post content', 'imageseo' ),
								'type'     => 'checkbox',
								'priority' => 30,
							),
						)
					),
					'images'        => array(
						'title'  => __( 'Images', 'imageseo' ),
						'fields' => array(
							array(
								'name'     => 'logoUrl',
								'std'      => '',
								'label'    => __( 'Author or Product price (WooCommerce only) - Subtitle', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Show the price product or author depending on the page', 'imageseo' ),
								'type'     => 'checkbox',
								'priority' => 30,
							),
							array(
								'name'     => 'defaultBgImg',
								'std'      => '',
								'label'    => __( 'Reading time or Number of reviews (WooCommerce only) - Subtitle 2', 'imageseo' ),
								'cb_label' => '',
								'desc'     => __( 'Show the reading time of an article or the number of reviews.', 'imageseo' ),
								'type'     => 'checkbox',
								'priority' => 30,
							),
						)
					)
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
								'desc'     => __( 'I want to fill out and optimize my ALT Texts for SEO and Accessibility. It\'s really worth for SEO !', 'imageseo' ),
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
								'desc'     => __( 'If you tick this box, the plugin will automatically write an alternative to the images you will upload.', 'imageseo' ),
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
						)
					)
				),
			),
		);

		return apply_filters( 'imageseo_settings_tabs', $settings );
	}

	/**
	 * register_settings function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_settings() {
		$settings = $this->get_settings();

		// register our options and settings
		foreach ( $settings as $tab_key => $tab ) {

			foreach ( $tab['sections'] as $section_key => $section ) {

				$option_group = 'imageseo_' . $tab_key . '_' . $section_key;

				// Check to see if $section['fields'] is set, we could be using it for upsells
				if ( isset( $section['fields'] ) ) {
					foreach ( $section['fields'] as $field ) {

						if ( $field['type'] == 'group' ) {
							foreach ( $field['options'] as $group_field ) {

								if ( ! empty( $group_field['name'] ) ) {
									if ( isset( $group_field['std'] ) ) {
										add_option( $group_field['name'], $group_field['std'] );
									}
									register_setting( $option_group, $group_field['name'] );
								}

							}
							continue;
						}

						if ( ! empty( $field['name'] ) ) {
							if ( isset( $field['std'] ) ) {
								add_option( $field['name'], $field['std'] );
							}
							register_setting( $option_group, $field['name'] );
						}
					}
				}
			}
		}
	}

	private function array_first_key(
		$a
	) {
		reset( $a );

		return key( $a );
	}
}
