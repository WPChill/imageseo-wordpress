<?php

namespace ImageSeoWP\Actions\Admin\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

class Deactivate
{
    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        add_action('admin_notices', [$this, 'modalDeactivate']);

        add_action('wp_ajax_imageseo_deactivate_plugin', [$this, 'feedback']);
    }

    public function modalDeactivate()
    {
        $screen = get_current_screen();
        if (!$screen) {
            return;
        }

        try {
            if ('plugins' !== $screen->id) {
                return;
            }

            echo "<div id='deactivate-intent-imageseo'></div>";
        } catch (\Exception $e) {
        }
    }

    public function feedback()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        if (!wp_verify_nonce($_GET['_wpnonce'], 'imageseo_deactivate_plugin')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        $html = '<p>Désactivation du plugin</p>
        <p>
            Message : <br/>
            %s
        </p>
        
        <p>Raisons : </p>
        <ul>
            <li>Désactivation temporaire : %s</li>
            <li>Mauvais support : %s</li>
            <li>Plugin trop compliqué : %s</li>
            <li>Manque de fonctionnalités : %s</li>
        </ul>
        ';

        if (!isset($_POST['values'])) {
            wp_send_json_success();
            exit;
        }

        $values = $_POST['values'];

        $message = isset($values['message']) ? $values['message'] : '';
        $deactivate_temporary = isset($values['deactivate_temporary']) && 'true' == $values['deactivate_temporary'] ? true : false;
        $bad_support = isset($values['bad_support']) && 'true' == $values['bad_support'] ? true : false;
        $plugin_complicated = isset($values['plugin_complicated']) && 'true' == $values['plugin_complicated'] ? true : false;
        $lack_feature = isset($values['lack_feature']) && 'true' == $values['lack_feature'] ? true : false;

        $html = sprintf($html, $message, $deactivate_temporary ? 'Oui' : 'Non', $bad_support ? 'Oui' : 'Non', $plugin_complicated ? 'Oui' : 'Non', $lack_feature ? 'Oui' : 'Non');

        try {
            wp_mail('support@imageseo.io', 'Désactivation du plugin', $html, ['Content-Type: text/html; charset=UTF-8']);
        } catch (\Execption $e) {
        }

        wp_send_json_success();
    }
}
