<?php

namespace ImageSeoWP\Actions\Front;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\TypeContent;

class RenameFileContent
{
    /**
     * @return bool
     */
    protected function no_translate_action_ajax()
    {
        $action_ajax_no_translate = apply_filters('imageseo_ajax_no_translate', [
            'add-menu-item', // WP Core
            'query-attachments', // WP Core
            'avia_ajax_switch_menu_walker', // Enfold theme
            'query-themes', // WP Core
            'wpestate_ajax_check_booking_valability_internal', // WP Estate theme
            'wpestate_ajax_add_booking', // WP Estate theme
            'wpestate_ajax_check_booking_valability', // WP Estate theme
            'mailster_get_template', // Mailster Pro,
            'mmp_map_settings', // MMP Map,
            'elementor_ajax', // Elementor since 2.5
            'ct_get_svg_icon_sets', // Oxygen
            'oxy_render_nav_menu', // Oxygen
            'hotel_booking_ajax_add_to_cart', // Hotel booking plugin
            'imagify_get_admin_bar_profile', // Imagify Admin Bar
        ]);

        if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['action']) && in_array($_POST['action'], $action_ajax_no_translate)) { //phpcs:ignore
            return true;
        }

        if ('GET' === $_SERVER['REQUEST_METHOD'] && isset($_GET['action']) && in_array($_GET['action'], $action_ajax_no_translate)) { //phpcs:ignore
            return true;
        }

        return false;
    }

    public function hooks()
    {
        // if (is_admin() &&
        // (!wp_doing_ajax() || (wp_doing_ajax() && isset($_SERVER['HTTP_REFERER']) && false !== strpos($_SERVER['HTTP_REFERER'], 'wp-admin')))) {
        //     return;
        // }
        // if (is_admin() && (!wp_doing_ajax() || $this->no_translate_action_ajax())) {
        //     return;
        // }

        // add_action('init', [$this, 'updateContentFile'], 12);
    }

    public function updateContentFile()
    {
        if (!apply_filters('imageseo_frontend_active_rename_file', true)) {
            return;
        }

        $file = apply_filters('imageseo_debug_file', IMAGESEO_DIR . '/content.html');

        if (false && file_exists($file)) {
            echo $this->update(file_get_contents($file));
            exit;
        } else {
            ob_start([$this, 'update']);
        }
    }

    public function getAttachmentIdByUrl($url)
    {
        $dir = wp_upload_dir();

        // baseurl never has a trailing slash
        if (false === strpos($url, $dir['baseurl'] . '/')) {
            // URL points to a place outside of upload directory
            return false;
        }

        $file = basename($url);

        $query = [
            'post_type'  => 'attachment',
            'fields'     => 'ids',
            'meta_query' => [
                [
                    'key'     => '_old_wp_attached_file',
                    'value'   => $file,
                    'compare' => 'LIKE',
                ],
            ],
        ];

        // query attachments
        $ids = get_posts($query);

        if (!empty($ids)) {
            foreach ($ids as $id) {
                $oldMetadata = get_post_meta($id, '_old_wp_attachment_metadata', true);

                // first entry of returned array is the URL
                if ($url === sprintf('%s/%s', $dir['baseurl'], $oldMetadata['file'])) {
                    return $id;
                }
            }
        }

        $query['meta_query'][0]['key'] = '_old_wp_attachment_metadata';
        // query attachments again
        $ids = get_posts($query);

        if (empty($ids)) {
            return false;
        }

        foreach ($ids as $id) {
            $oldMetadata = get_post_meta($id, '_old_wp_attachment_metadata', true);

            foreach ($oldMetadata['sizes'] as $size => $values) {
                if ($values['file'] === $file) {
                    return $id;
                }
            }

            if (isset($oldMetadata['original_image']) && $file === $oldMetadata['original_image']) {
                return $id;
            }
        }

        if (isset($oldMetadata)) {
            return false;
        }
    }

    public function update($content)
    {
        $type = TypeContent::isJson($content) ? 'json' : 'html';

        if ('json' === $type) {
            return $content;
        }

        $regex = '#<img[^\>]*src=(?:\"|\')(?<src>([^"]*))(?:\"|\')[^\>]+?>#mU';

        preg_match_all($regex, $content, $matches);

        $matchesSrc = array_unique($matches['src']);

        foreach ($matchesSrc as $src) {
            if (false === strpos($src, 'wp-content/uploads')) {
                continue;
            }

            $attachmentId = $this->getAttachmentIdByUrl($src);

            if (!$attachmentId) {
                continue;
            }

            $oldMetadata = get_post_meta($attachmentId, '_old_wp_attachment_metadata', true);

            $metadata = wp_get_attachment_metadata($attachmentId);
            $filenameNeedReplace = wp_basename($src);

            $oldFilename = wp_basename($oldMetadata['file']);

            //basic file
            if ($oldFilename === $filenameNeedReplace) {
                $srcBySize = wp_get_attachment_image_src($attachmentId, 'full');
                $content = str_replace($src, $srcBySize[0], $content);
            } else {
                // Multiple Sizes
                foreach ($oldMetadata['sizes'] as $key => $data) {
                    $oldFilename = wp_basename($data['file']);
                    if ($oldFilename === $filenameNeedReplace) {
                        $srcBySize = wp_get_attachment_image_src($attachmentId, $key);
                        $content = str_replace($src, $srcBySize[0], $content);
                    }
                }
            }

            // Original Image
            if (isset($metadata['original_image']) && isset($oldMetadata['original_image'])) {
                $oldOriginalFilename = wp_basename($oldMetadata['original_image']);

                if ($oldOriginalFilename === $filenameNeedReplace) {
                    $srcBySize = wp_get_original_image_url($attachmentId);
                    $content = str_replace($src, $srcBySize, $content);
                }
            }
        }

        return $content;
    }
}
