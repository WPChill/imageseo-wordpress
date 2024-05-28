<?php

namespace ImageSeoWP\Traits;

trait Debug
{
	public function resetDebug()
	{
		update_option($this->debugOption, []);
	}

	public function getDebug()
	{
		return get_option($this->debugOption, []);
	}

	public function writeDebug($value)
	{
		$currentDebug = get_option($this->debugOption, []);
		if ($this->debug) {
			if (is_array($value))
				update_option($this->debugOption, array_merge(
					$currentDebug,
					is_array($value) ? $value : [$value]
				));
		}
	}
}
