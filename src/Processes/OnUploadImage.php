<?php

namespace ImageSeoWP\Processes;

use ImageSeoWP\Async\WPBackgroundProcess;

if (!defined('ABSPATH')) {
	exit;
}

class OnUploadImage extends WPBackgroundProcess
{
	protected $prefix = 'imageseo';
	protected $action = 'on_upload_image';

	public static $instance;

	public static function getInstance()
	{
		if (!isset(self::$instance) && !(self::$instance instanceof OnUploadImage)) {
			self::$instance = new OnUploadImage();
		}

		return self::$instance;
	}


	protected function task($item)
	{
		return false;
	}

	protected function complete()
	{
		parent::complete();
		// Actions to perform once all tasks are completed.
	}
}
