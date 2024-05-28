<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class MediaLibraryPinterest
{
    public function hooks()
    {
        add_filter('attachment_fields_to_edit', [$this, 'fieldsEdit'], 999, 2);
        add_action('attachment_fields_to_save', [$this, 'saveDataPinterest'], 10, 2);
    }

    /**
     * @param array $formFields
     * @param \WP_Post $post
     *
     * @return array
     */
    public function fieldsEdit( array $formFields, \WP_Post $post): array {
        global $pagenow;

        $formFields['imageseo-data-pin-description'] = [
            'label'         => __('Pinterest description', 'imageseo'),
            'input'         => 'textarea',
            'value' 		      => get_post_meta($post->ID, '_imageseo_data_pin_description', true),
            'show_in_edit'  => true,
            'show_in_modal' => true,
            'helps'         => '&lt;img src="#" data-pin-description="My description" /&gt;',
        ];
        $formFields['imageseo-data-pin-url'] = [
            'label'         => __('Pinterest URL', 'imageseo'),
            'input'         => 'text',
            'value' 		      => get_post_meta($post->ID, '_imageseo_data_pin_url', true),
            'show_in_edit'  => true,
            'show_in_modal' => true,
            'helps'         => '&lt;img src="#" data-pin-url="https://imageseo.io" /&gt;',
        ];
        $formFields['imageseo-data-pin-id'] = [
            'label'         => __('Pinterest ID', 'imageseo'),
            'input'         => 'text',
            'value' 		      => get_post_meta($post->ID, '_imageseo_data_pin_id', true),
            'show_in_edit'  => true,
            'show_in_modal' => true,
            'helps'         => '&lt;img src="#" data-pin-id="id-pin" /&gt;',
        ];
        $formFields['imageseo-data-pin-media'] = [
            'label'         => __('Pinterest Media', 'imageseo'),
            'input'         => 'text',
            'value' 		      => get_post_meta($post->ID, '_imageseo_data_pin_media', true),
            'show_in_edit'  => true,
            'show_in_modal' => true,
            'helps'         => '&lt;img src="#"  data-pin-media="https://example.com/my-image.jpg" /&gt;',
        ];

        if ('post.php' !== $pagenow) {
            $formFields['imageseo-has-report'] = [
                'label'         => __('ImageSEO Report', 'imageseo'),
                'input'         => 'html',
                'html'          => '<a id="imageseo-' . $post->ID . '" href="' . esc_url(admin_url('post.php?post=' . $post->ID . '&action=edit')) . '" class="button">' . __('View report', 'imageseo') . '</a>',
                'show_in_edit'  => true,
                'show_in_modal' => true,
            ];
        }

        return $formFields;
    }

    public function saveDataPinterest($post, $attachment)
    {
        if (isset($attachment['imageseo-data-pin-description'])) {
            update_post_meta($post['ID'], '_imageseo_data_pin_description', $attachment['imageseo-data-pin-description']);
        }
        if (isset($attachment['imageseo-data-pin-url'])) {
            update_post_meta($post['ID'], '_imageseo_data_pin_url', $attachment['imageseo-data-pin-url']);
        }
        if (isset($attachment['imageseo-data-pin-id'])) {
            update_post_meta($post['ID'], '_imageseo_data_pin_id', $attachment['imageseo-data-pin-id']);
        }
        if (isset($attachment['imageseo-data-pin-media'])) {
            update_post_meta($post['ID'], '_imageseo_data_pin_media', $attachment['imageseo-data-pin-media']);
        }

        return $post;
    }
}
