<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
    exit;
}

class UserInfo
{
    protected $limitExcedeed = null;

    public function hasLimitExcedeed()
    {
        if (null !== $this->limitExcedeed) {
            return $this->limitExcedeed;
        }

        $data = imageseo_get_service('ClientApi')->getOwnerByApiKey();
		if(!isset($data['user'])){
			return true;
		}
		$user = $data['user'];
        if(!$user) {
			return true;
		}
        $imageLeft = ($user['bonusStockImages'] + $user['plan']['limitImages']) - $user['currentRequestImages'];

        $this->limitExcedeed = ($imageLeft <= 0) ? true : false;

        return $this->limitExcedeed;
    }
}
