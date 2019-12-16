<?php

namespace ImageSeoWP\Actions\Admin\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

class QueryImages
{
    public function __construct()
    {
        $this->queryImages = imageseo_get_service('QueryImages');
    }

    public function hooks()
    {
        add_action('wp_ajax_imageseo_query_images', [$this, 'query']);
    }

    public function query()
    {
        $filters = (isset($_POST['filters'])) ? $_POST['filters'] : false;

        if (!$filters) {
            wp_send_json_error([
                'code' => 'no_filters',
            ]);

            return;
        }

        $argsDefault = $this->queryImages->getQueryArgsDefault();

        // $args = array_merge($argsDefault, [
        //     'meta_key'       => AttachmentMeta::REPORT,
        //     'meta_compare'   => 'EXISTS',
        //     'fields'         => 'ids',
        //     'orderby'        => 'id',
        //     'order'          => 'ASC',
        // ]);

        wp_send_json_success(new \WP_Query($args));
    }
}
