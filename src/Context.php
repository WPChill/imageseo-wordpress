<?php

namespace ImageSeoWP;

if (!defined('ABSPATH')) {
	exit;
}

use ImageSeoWP\Bootstrap;

/**
 * Only use for get one context
 */
abstract class Context
{

	/**
	 * @static
	 * @since 1.0
	 * @var Bootstrap|null
	 */
	protected static $context;

	/**
	 * Create context if not exist
	 *
	 * @static
	 * @since 1.0
	 */
	public static function getContext()
	{
		if (null !== self::$context) {
			return self::$context;
		}

		self::$context = new Bootstrap();

		self::getClasses(__DIR__ . '/Services', 'services', 'Services\\');
		self::getClasses(__DIR__ . '/Actions', 'actions', 'Actions\\');



		return self::$context;
	}

	/**
	 * @static
	 * @param string $path
	 * @param string $type
	 * @param string $namespace
	 * @return void
	 */
	public static function getClasses($path, $type, $namespace = '')
	{
		$files      = array_diff(scandir($path), ['..', '.']);
		foreach ($files as $filename) {
			$pathCheck = $path . '/' . $filename;
			if (is_dir($pathCheck)) {
				self::getClasses($pathCheck, $type, $namespace . $filename . '\\');
				continue;
			}

			$data = '\\ImageSeoWP\\' . $namespace . str_replace('.php', '', $filename);

			switch ($type) {
				case 'services':
					self::$context->setService($data);
					break;
				case 'actions':
					self::$context->setAction($data);
					break;
			}
		}
	}
}
