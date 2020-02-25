<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class PluginLinks
{
    public function hooks()
    {
        add_filter('plugin_action_links', [$this, 'pluginLinks'], 10, 2);
    }

    public function pluginLinks($links, $file)
    {
        if (IMAGESEO_BNAME !== $file) {
            return $links;
        }

        $settings = sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=imageseo-options'), __('Settings', 'imageseo'));
        $documentation = sprintf('<a href="%s" target="_blank">%s</a>', 'https://imageseo.io/getting-started-wordpress/', __('Docs', 'imageseo'));
        $premium = sprintf('<a href="%s" style="font-weight:bold" target="_blank">%s</a>', 'https://app.imageseo.io/plan', __('Become PREMIUM !', 'imageseo'));

        array_unshift($links, $settings, $documentation, $premium);

        return $links;
    }
}
