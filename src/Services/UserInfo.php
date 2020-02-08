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

        $user = imageseo_get_service('ClientApi')->getOwnerByApiKey();
        $imageLeft = ($user['bonus_stock_images'] + $user['plan']['limit_images']) - $user['current_request_images'];

        $this->limitExcedeed = ($imageLeft <= 0) ? true : false;

        return $this->limitExcedeed;
    }
}
