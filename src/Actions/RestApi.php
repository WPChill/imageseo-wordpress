<?php

namespace ImageSeoWP\Actions;

use WP_REST_Response;

if (!defined('ABSPATH')) {
	exit;
}

class RestApi
{
	public $namespace = 'imageseo/v1';

	public function hooks()
	{
		add_action('rest_api_init', [$this, 'register_routes']);
	}

	public function register_routes()
	{
		register_rest_route($this->namespace, '/settings/', [
			'methods' => 'GET',
			'callback' => [$this, 'get_settings'],
			'permission_callback' => [$this, 'settings_permissions_check']
		]);

		register_rest_route($this->namespace, '/settings/', [
			'methods' => 'POST',
			'callback' => [$this, 'update_settings'],
			'permission_callback' => [$this, 'settings_permissions_check']
		]);

		register_rest_route($this->namespace, '/register/', [
			'methods' => 'POST',
			'callback' => [$this, 'register'],
			'permission_callback' => [$this, 'settings_permissions_check']
		]);

		register_rest_route($this->namespace, '/validate-api-key/', [
			'methods' => 'POST',
			'callback' => [$this, 'validate_api_key'],
			'permission_callback' => [$this, 'settings_permissions_check']
		]);

		register_rest_route($this->namespace, '/start-bulk-optimizer/', [
			'methods' => 'POST',
			'callback' => [$this, 'start_bulk_optimizer'],
			'permission_callback' => [$this, 'settings_permissions_check']
		]);

		register_rest_route($this->namespace, '/get-bulk-optimizer-status/', [
			'methods' => 'GET',
			'callback' => [$this, 'get_bulk_optimizer_status'],
			'permission_callback' => [$this, 'settings_permissions_check']
		]);

		register_rest_route($this->namespace, '/stop-bulk-optimizer/', [
			'methods' => 'POST',
			'callback' => [$this, 'stop_bulk_optimizer'],
			'permission_callback' => [$this, 'settings_permissions_check']
		]);

		register_rest_route($this->namespace, '/optimize-image/', [
			'methods' => 'POST',
			'callback' => [$this, 'optimize_image'],
			'permission_callback' => [$this, 'settings_permissions_check']
		]);

		register_rest_route($this->namespace, '/save-property/', [
			'methods' => 'POST',
			'callback' => [$this, 'save_property'],
			'permission_callback' => [$this, 'settings_permissions_check']
		]);

		register_rest_route($this->namespace, '/optimizer-errors/', [
			'methods' => 'GET',
			'callback' => [$this, 'get_optimizer_errors'],
			'permission_callback' => [$this, 'settings_permissions_check']
		]);

		register_rest_route($this->namespace, '/debug-info/', [
			'methods' => 'GET',
			'callback' => [$this, 'get_debug_info'],
			'permission_callback' => [$this, 'settings_permissions_check']
		]);

		register_rest_route($this->namespace, '/image-query/', [
			'methods' => 'GET',
			'callback' => [$this, 'image_query'],
			'permission_callback' => [$this, 'settings_permissions_check']
		]);
	}

	public function settings_permissions_check(): bool
	{
		return current_user_can('manage_options');
	}

	public function get_settings(): WP_REST_Response
	{
		$options = $this->convert_to_camel_case(imageseo_get_options());
		return new WP_REST_Response($options, 200);
	}

	public function update_settings($request): WP_REST_Response
	{
		$settings = $request->get_json_params();
		update_option(IMAGESEO_SLUG, $this->sanitize_settings($settings));
		return new WP_REST_Response($settings, 200);
	}

	public function register($request): WP_REST_Response
	{
		$data = $request->get_json_params();
		$required = ['email', 'password', 'firstName', 'lastName', 'terms'];

		$missing = [];
		foreach ($required as $field) {
			if (!isset($data[$field])) {
				$missing[] = $field;
			}
		}

		if (!empty($missing)) {
			return new WP_REST_Response([
				'message' => __('Missing required fields', 'imageseo'),
				'fields' => $missing
			], 400);
		}


		$register = imageseo_get_service('Register');
		$user = $register->register($data['email'], $data['password'], [
			'firstname' => $data['firstName'],
			'lastname' => $data['lastName'],
			'newsletters' => $data['newsletters'] ?? false
		]);

		if ($user === null) {
			return new WP_REST_Response([
				'message' => __('Error registering user', 'imageseo')
			], 500);
		}

		$this->_validate_api_key($user['projects'][0]['apiKey']);

		return new WP_REST_Response($user, 200);
	}

	public function validate_api_key($request): WP_REST_Response
	{
		$data = $request->get_json_params();
		$owner = $this->_validate_api_key($data['apiKey']);

		return new WP_REST_Response($owner, 200);
	}

	private function _validate_api_key($apiKey): WP_REST_Response
	{
		$owner = imageseo_get_service('ClientApi')->validateApiKey($apiKey);
		if ($owner === null) {
			return new WP_REST_Response([
				'message' => __('Invalid API Key', 'imageseo')
			], 400);
		}

		return new WP_REST_Response($owner, 200);
	}

	public function optimize_image($request): WP_REST_Response
	{
		$optimizer = imageseo_get_service('Optimizer');
		$data = $request->get_json_params();
		$action = $data['action'] ?? 'optimizeAll';

		switch ($action) {
			case 'optimizeAlt':
				$return = $optimizer->getAndSetAlt($data['id']);
				break;
			case 'optimizeFilename':
				$return = $optimizer->getAndUpdateFilename($data['id']);
				break;
			default:
				$return = $optimizer->getAndSetOptimizedImage($data['id']);
				break;
		}

		return new WP_REST_Response($return, 200);
	}

	public function save_property($request): WP_REST_Response
	{
		$optimizer = imageseo_get_service('Optimizer');
		$data = $request->get_json_params();
		$action = $data['action'] ?? '';

		switch ($action) {
			case 'saveAlt':
				$optimizer->_updateAlt([
					'internalId' => $data['id'],
					'altText' => $data['value']
				]);

				$return = ['altText' => $data['value']];
				$optimizer->getAndUpdateMeta($data['id'], 'altText', $data['value']);
				break;
			case 'saveFilename':
				$filename = $optimizer->_updateFilename(
					$data['id'],
					[
						'internalId' => $data['id'],
						'filename' => $data['value']
					]
				);
				$return = ['filename' => $filename];

				$optimizer->getAndUpdateMeta($data['id'], 'filename', $filename);
				break;
			default:
				$return = ['error' => 'Invalid action'];
				break;
		}

		return new WP_REST_Response($return, 200);
	}

	public function image_query(): WP_REST_Response
	{
		$totalImages = imageseo_get_service('QueryImages')->getTotalImages(
			[
				'withCache'  => false,
				'forceQuery' => true
			]
		);
		$totalNoAlt  = imageseo_get_service('QueryImages')->getNumberImageNonOptimizeAlt(
			[
				'withCache'  => false,
				'forceQuery' => true
			]
		);

		return new WP_REST_Response([
			'totalImages' => $totalImages,
			'totalNoAlt' => $totalNoAlt
		], 200);
	}

	public function start_bulk_optimizer(): WP_REST_Response
	{
		$bulkOptimizer = imageseo_get_service('BulkOptimizer');
		$data = $bulkOptimizer->start();

		return new WP_REST_Response($data, 200);
	}

	public function get_bulk_optimizer_status(): WP_REST_Response
	{
		$bulkOptimizer = imageseo_get_service('BulkOptimizer');
		$data = $bulkOptimizer->getStatus();

		return new WP_REST_Response($data, 200);
	}

	public function stop_bulk_optimizer(): WP_REST_Response
	{
		$bulkOptimizer = imageseo_get_service('BulkOptimizer');
		$data = $bulkOptimizer->stop();

		return new WP_REST_Response($data, 200);
	}

	public function get_optimizer_errors(): WP_REST_Response
	{
		$bulkOptimizer = imageseo_get_service('BulkOptimizer');
		$data = $bulkOptimizer->getErrors();

		return new WP_REST_Response($data, 200);
	}

	public function get_debug_info(): WP_REST_Response
	{
		$bulkOptimizer = imageseo_get_service('BulkOptimizer');
		$data = $bulkOptimizer->getDebug();

		return new WP_REST_Response($data, 200);
	}

	private function sanitize_settings($settings): array
	{
		$options = $this->convert_to_camel_case(imageseo_get_options());
		foreach ($settings as $key => $value) {
			switch ($key) {
				case 'apiKey':
				case 'api_key':
					$options['api_key'] = sanitize_text_field($value);
					$options['apiKey'] = sanitize_text_field($value);
					break;
				case 'allowed':
					$options['allowed'] = (bool) $value;
					break;
				case 'active_alt_write_upload':
				case 'activeAltWriteUpload':
					$options['activeAltWriteUpload'] = (bool) $value;
					$options['active_alt_write_upload'] = (bool) $value;
					break;
				case 'activeRenameWriteUpload':
				case 'active_rename_write_upload':
					$options['activeRenameWriteUpload'] = (bool) $value;
					$options['active_rename_write_upload'] = (bool) $value;
					break;
				case 'default_language_ia':
				case 'defaultLanguageIa':
					$options['default_language_ia'] = sanitize_text_field($value);
					$options['defaultLanguageIa'] = sanitize_text_field($value);
					break;
				case 'alt_template_default':
				case 'altTemplateDefault':
					$options['alt_template_default'] = sanitize_text_field($value);
					$options['altTemplateDefault'] = sanitize_text_field($value);
					break;
				case 'social_media_post_types':
				case 'socialMediaPostTypes':
					$options['social_media_post_types'] = array_map('sanitize_text_field', $value);
					$options['socialMediaPostTypes'] = array_map('sanitize_text_field', $value);
					break;
				case 'social_media_type':
				case 'socialMediaType':
					$options['social_media_type'] = array_map('sanitize_text_field', $value);
					$options['socialMediaType'] = array_map('sanitize_text_field', $value);
					break;
				case 'altFilter':
					$options['altFilter'] = sanitize_text_field($value);
					break;
				case 'altFill':
					$options['altFill'] = sanitize_text_field($value);
					break;
				case 'optimizeAlt':
					$options['optimizeAlt'] = (bool) $value;
					break;
				case 'optimizeFile':
					$options['optimizeFile'] = (bool) $value;
					break;
				case 'language':
					$options['language'] = sanitize_text_field($value);
					break;
				case 'social_media_settings':
				case 'socialMediaSettings':
					$values = [
						'layout'                 => sanitize_text_field($value["layout"]),
						'textColor'              => sanitize_hex_color($value["textColor"]),
						'contentBackgroundColor' => sanitize_hex_color($value["contentBackgroundColor"]),
						'starColor'              => sanitize_hex_color($value["starColor"]),
						'visibilitySubTitle'     => (bool) $value["visibilitySubTitle"],
						'visibilitySubTitleTwo'  => (bool) $value["visibilitySubTitleTwo"],
						'visibilityRating'       => (bool) $value["visibilityRating"],
						'visibilityAvatar'       => (bool) $value["visibilityAvatar"],
						'logoUrl'                => esc_url($value["logoUrl"]),
						'defaultBgImg'           => esc_url($value["defaultBgImg"]),
						'textAlignment'          => sanitize_text_field($value["textAlignment"]),
					];

					$options['social_media_settings'] = $values;
					$options['socialMediaSettings'] = $values;
					break;
			}
		}
		return $options;
	}

	private function convert_to_camel_case($options): array
	{
		$camelCased = [];
		foreach ($options as $key => $value) {
			if (strpos($key, '_') === -1) {
				$camelCased[$key] = $value;
				continue;
			}

			$camelCaseKey = lcfirst(str_replace('_', '', ucwords($key, '_')));
			$camelCased[$camelCaseKey] = $value;
		}

		return $camelCased;
	}
}
