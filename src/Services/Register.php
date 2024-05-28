<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
	exit;
}

class Register
{
	public $optionServices;
	public function __construct()
	{
		$this->optionServices = imageseo_get_service('Option');
	}

	public function register($email, $password, $options = [])
	{
		$response = wp_remote_post(IMAGESEO_API_URL . '/auth/register-from-plugin', [
			'headers' => [
				'Content-Type' => 'application/json',
			],
			'body' => json_encode([
				'firstname'       => isset($options['firstname']) ? $options['firstname'] : '',
				'lastname'        => isset($options['lastname']) ? $options['lastname'] : '',
				'newsletters'     => isset($options['newsletters']) ? $options['newsletters'] : false,
				'email'           => $email,
				'password'        => $password,
				'wp_url'          => site_url(),
				'withProject'     => true,
				'name'            => get_bloginfo('name'),
				'optins'          => 'terms',
			]),
			'timeout' => 50,
		]);

		if (is_wp_error($response)) {
			return null;
		}

		$body = json_decode(wp_remote_retrieve_body($response), true);
		$responseCode = wp_remote_retrieve_response_code($response);
		if ($responseCode !== 201) {
			if (isset($body['message'])) {
				return array('success' => false, 'data' => array('message' => $body['message'], 'code' => $responseCode));
			}
			return null;
		}

		$user = $body;

		$options = $this->optionServices->getOptions();

		$options['api_key'] = $user['projects'][0]['apiKey'];
		$options['apiKey'] = $user['projects'][0]['apiKey'];
		$options['allowed'] = true;
		$this->optionServices->setOptions($options);

		return $user;
	}
}
