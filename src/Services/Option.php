<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
	exit;
}

use ImageSeoWP\Helpers\SocialMedia;

class Option
{
	/**
	 * @var array
	 */
	protected $optionsDefault = [
		'api_key'                    => '',
		'allowed'                    => false,
		'active_alt_write_upload'    => 1,
		'active_rename_write_upload' => 1,
		'default_language_ia'        => IMAGESEO_LOCALE,
		'alt_template_default'       => '[keyword_1] - [keyword_2]',
		'social_media_post_types'    => [
			'post',
		],
		'social_media_type'          => [
			SocialMedia::OPEN_GRAPH['name'],
		],
		'social_media_settings'      => [
			'layout'                 => 'CARD_LEFT',
			'textColor'              => '#000000',
			'contentBackgroundColor' => '#ffffff',
			'starColor'              => '#F8CA00',
			'visibilitySubTitle'     => true,
			'visibilitySubTitleTwo'  => true,
			'visibilityRating'       => false,
			'visibilityAvatar'       => true,
			'logoUrl'                => IMAGESEO_URL_DIST . '/images/favicon.png',
			'defaultBgImg'           => IMAGESEO_URL_DIST . '/images/default_logo.png',
			'textAlignment'          => 'top'
		],
		'altFilter'                  => 'ALL',
		'altFill'                    => 'FILL_ALL',
		'optimizeAlt'                => 0,
		'language'                   => IMAGESEO_LOCALE,
	];

	/**
	 * Get options default.
	 *
	 * @return array
	 */
	public function getOptionsDefault()
	{
		return $this->convert_to_camel_case($this->optionsDefault);
	}

	/**
	 * @return array
	 */
	public function getOptions()
	{
		return apply_filters(
			'imageseo_get_options',
			wp_parse_args(get_option(IMAGESEO_SLUG), $this->getOptionsDefault())
		);
	}

	/**
	 * @param string $name
	 *
	 * @return array
	 */
	public function getOption($name)
	{
		$options = $this->getOptions();
		if (!array_key_exists($name, $options)) {
			return null;
		}

		return apply_filters('imageseo_' . $name . '_option', $options[$name]);
	}

	/**
	 * @param array $options
	 *
	 * @return $this
	 */
	public function setOptions($options)
	{
		update_option(IMAGESEO_SLUG, $options);

		return $this;
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return $this
	 */
	public function setOptionByKey($key, $value)
	{
		$options = $this->getOptions();
		$options[$key] = $value;
		$this->setOptions($options);

		return $this;
	}

	private function convert_to_camel_case($options)
	{
		$camelCased = [];

		foreach ($options as $key => $value) {
			if (strpos($key, '_') === -1) {
				$camelCased[$key] = $value;
				continue;
			};

			$camelCaseKey = lcfirst(str_replace('_', '', ucwords($key, '_')));
			$camelCased[$camelCaseKey] = $value;
		}

		return $camelCased;
	}
}
