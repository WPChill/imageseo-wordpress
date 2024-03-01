<?php

namespace ImageSeoWP\Processes;

if (!defined('ABSPATH')) {
	exit;
}

class OnUploadImage extends \WP_Background_Process
{
	protected $prefix = 'imageseo';
	protected $action = 'on_upload_image';
	public $count = 0;
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
		// $item could include 'attachment_id' and other data you need.

		// Perform your task here. For example, processing an image.
		// Return false to remove the task from the queue.
		// Return $item to reattempt the task if it failed.
		error_log('OnUploadImage task: ' . print_r($item, true));
		$item['attachment_id'] = '2';
		return true;
	}

	protected function complete()
	{
		parent::complete();
		// Actions to perform once all tasks are completed.
	}
}
