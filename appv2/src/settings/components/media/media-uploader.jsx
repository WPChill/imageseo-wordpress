/* eslint-disable jsx-a11y/no-static-element-interactions */
/* eslint-disable jsx-a11y/click-events-have-key-events */
import { BaseControl, Button } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

export const MediaUploader = ({ id, label, help, value, onChange }) => {
	const [imageUrl, setImageUrl] = useState(value);

	const openMediaLibrary = () => {
		const frame = wp.media({
			title: __('Select or Upload Media', 'imageseo'),
			button: {
				text: __('Use this item', 'imageseo'),
			},
			multiple: false,
		});

		frame.on('select', () => {
			const attachment = frame.state().get('selection').first().toJSON();
			setImageUrl(attachment.url);
			onChange(attachment.url);
		});

		frame.open();
	};

	return (
		<div className="media-uploader-container" onClick={openMediaLibrary}>
			<BaseControl
				id={id}
				label={label}
				help={help}
				className="media-uploader"
			>
				{!imageUrl && (
					<Button isPrimary onClick={openMediaLibrary}>
						{__('Select Image', 'imageseo')}
					</Button>
				)}
				{imageUrl && <img src={imageUrl} alt="Selected" />}
			</BaseControl>
		</div>
	);
};
