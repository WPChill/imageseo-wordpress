<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
	exit;
}

use ImageSeoWP\Helpers\SocialMedia;

class GenerateImageSocial
{
	public $clientApiService;

	public function __construct()
	{
		$this->clientApiService = imageseo_get_service('ClientApi');
	}

	protected function getMinutes($content)
	{
		$word = str_word_count(strip_tags($content));
		$minutes = floor($word / 200);
		if ($minutes < 1) {
			$minutes = 1;
		}

		return $minutes;
	}

	/**
	 * @param object $post
	 *
	 * @return string
	 */
	protected function getSubTitle($post)
	{
		switch ($post->post_type) {
			case 'product':
				if (!function_exists('wc_get_product')) {
					return '';
				}

				$product = wc_get_product($post->ID);

				$subTitle = html_entity_decode(sprintf('%s%s', $product->get_price(), get_woocommerce_currency_symbol()));
				break;
			default:
				$subTitle = get_the_author_meta('display_name', $post->post_author);
				break;
		}

		return apply_filters('imageseo_get_sub_title_social_media', $subTitle, $post);
	}

	/**
	 * @param object $post
	 *
	 * @return string
	 */
	protected function getSubTitleTwo($post)
	{
		switch ($post->post_type) {
			case 'product':
				if (!function_exists('wc_get_product')) {
					return '';
				}

				$product = wc_get_product($post->ID);

				$subTitle = $product->get_review_count();
				break;
			default:
				$subTitle = sprintf(__('%s min read', 'imageseo'), $this->getMinutes($post->post_content));
				break;
		}

		return apply_filters('imageseo_get_sub_title_two_social_media', $subTitle, $post);
	}

	protected function getVisibilityRating($post, $settings)
	{
		switch ($post->post_type) {
			case 'product':
				$visibility = $settings['visibilityRating'];
				break;
			default:
				$visibility = false;
				break;
		}

		return apply_filters('imageseo_get_visibility_rating', $visibility, $post);
	}

	protected function getAvatarUrl($post)
	{
		return apply_filters('imageseo_get_avatar_url', get_avatar_url($post->post_author));
	}

	public function prepare($postId)
	{
		if (!imageseo_get_api_key()) {
			return false;
		}

		$limitExcedeed = imageseo_get_service('UserInfo')->hasLimitExcedeed();
		if ($limitExcedeed) {
			return;
		}

		$post = get_post($postId);
		if (!$post) {
			return;
		}

		$medias = [
			SocialMedia::OPEN_GRAPH['name'],
		];
		$settings = imageseo_get_service('Option')->getOption('social_media_settings');

		$siteTitle = get_bloginfo('name');
		$subTitle = $this->getSubTitle($post);
		$subTitleTwo = $this->getSubTitleTwo($post);

		$visibilityRating = $this->getVisibilityRating($post, $settings);

		$nbGoodStars = 0;
		if ($visibilityRating && function_exists('wc_get_product')) {
			$product = wc_get_product($post->ID);
			$nbGoodStars = ceil($product->get_average_rating());
		}
		$avatarUrl = $this->getAvatarUrl($post);

		$featuredImgUrl = apply_filters('imageseo_get_post_thumbnail_social_media', get_the_post_thumbnail_url($postId, 'full'), $postId);

		if (!$featuredImgUrl) {
			$featuredImgUrl = $settings['defaultBgImg'];
		}

		$isAlreadyGenerate = get_post_meta($postId, '_imageseo_social_media_image_is_generate', true);
		$transientCurrentProcess = get_transient('_imageseo_filename_social_process');
		if (!$transientCurrentProcess) {
			$transientCurrentProcess = [];
		}

		foreach ($medias as $media) {
			$formatFilename = sanitize_title(sprintf('%s-%s-%s', $siteTitle, $post->post_name, $media));
			$filename = apply_filters('imageseo_filename_social_media_image', $formatFilename, $postId, $media);

			if ($isAlreadyGenerate) {
				$currentAttachmentId = get_post_meta($postId, sprintf('_imageseo_social_media_image_%s', $media), true);
				wp_delete_attachment($currentAttachmentId, true);
			}

			$result = $this->generate($filename, [
				'title'                            => $post->post_title,
				'subTitle'                         => $subTitle,
				'subTitleTwo'                      => $subTitleTwo,
				'layout'                           => $settings['layout'],
				'textColor'                        => str_replace('#', '', $settings['textColor']),
				'contentBackgroundColor'           => str_replace('#', '', $settings['contentBackgroundColor']),
				'starColor'                        => str_replace('#', '', $settings['starColor']),
				'visibilitySubTitle'               => $settings['visibilitySubTitle'],
				'visibilitySubTitleTwo'            => $settings['visibilitySubTitleTwo'],
				'visibilityAvatar'                 => $settings['visibilityAvatar'],
				'visibilityRating'                 => $visibilityRating,
				'textAlignment'                    => $settings['textAlignment'],
				'logoUrl'                          => $settings['logoUrl'],
				'avatarUrl'                        => $avatarUrl,
				'bgImgUrl'                         => $featuredImgUrl,
				'nbGoodStars'                      => (string)$nbGoodStars,
			]);

			if (isset($result['attachment_id'])) {
				update_post_meta($postId, sprintf('_imageseo_social_media_image_%s', $media), $result['attachment_id']);

				unset($transientCurrentProcess[$postId]);
				set_transient('_imageseo_filename_social_process', $transientCurrentProcess, 20);
			}

			if (!$isAlreadyGenerate) {
				update_post_meta($postId, '_imageseo_social_media_image_is_generate', true);

				$client = imageseo_get_service('ClientApi')->getClient();
				$url = $client->getOptions()['host'] . '/projects/credits';
				$client->getHttpClient()->request(
					'POST',
					$url
				);
			}
		}
	}

	/**
	 * @param string $filename
	 * @param array  $data
	 *
	 * @example [
	 *      "title" => "Post title",
	 *      "media" => "open-graph"
	 *      "layout" => "CARD_LEFT"
	 *      "bgImgUrl" => "https://example.com/image.jpg"
	 *      "bgColor" => "FFFFFF"
	 *      "txtColor" => "000000"
	 * ]
	 */
	public function generate($filename, $data)
	{
		if (empty($filename) || empty($data)) {
			return;
		}

		if (!array_key_exists('media', $data)) {
			$data['media'] = SocialMedia::OPEN_GRAPH['name'];
		}

		if (apply_filters('imageseo_generate_social_mute_report_on_upload', true)) {
			imageseo_get_action('\ImageSeoWP\Actions\Admin\MediaLibrary')->muteOnUpload();
		}

		$image = $this->clientApiService->generateSocialMediaImage($data);

		$filenameWithExtension = $filename . '.jpg';
		$result = wp_upload_bits($filenameWithExtension, null, $image);

		$attachment = [
			'post_mime_type' => 'image/jpeg',
			'post_title'     => $filename,
			'post_content'   => '',
			'post_status'    => 'inherit',
			'guid'           => $result['url'],
		];

		$attachmentId = wp_insert_attachment($attachment, $result['file']);

		require_once ABSPATH . 'wp-admin/includes/image.php';

		$attachmentData = wp_generate_attachment_metadata($attachmentId, $result['file']);

		$now = new \DateTime('now');
		wp_update_attachment_metadata($attachmentId, array_merge($attachmentData, ['is_social' => true, 'last_updated' => $now->getTimestamp()]));

		return [
			'file_infos'    => $result,
			'attachment_id' => $attachmentId,
		];
	}
}
