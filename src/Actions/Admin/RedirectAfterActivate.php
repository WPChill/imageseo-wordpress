<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class RedirectAfterActivate
{
    public function activate()
    {
        add_action('activated_plugin', [$this, 'redirect']);
    }

    public function redirect($plugin)
    {
        if ($plugin == plugin_basename(IMAGESEO_BNAME) && !imageseo_get_api_key()) {
            wp_redirect(admin_url('admin.php?page=imageseo-settings&wizard=1'));
            exit;
        }
    }
}
