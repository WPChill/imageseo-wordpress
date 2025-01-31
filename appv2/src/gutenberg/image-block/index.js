import { withImageAltButton } from './with-image-alt-button';
import { __ } from '@wordpress/i18n';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { registerPlugin } from '@wordpress/plugins';
import { UpdateAltTextForAllImages } from './update-alt-text-for-all-images';
import './index.css';
import './store';

// Wait for WordPress to be fully loaded
window.addEventListener('DOMContentLoaded', () => {
	// Register the filter
	wp.hooks.addFilter(
		'editor.BlockEdit',
		'imageseo/with-image-alt-button',
		withImageAltButton
	);

	// Register the sidebar panel plugin
	registerPlugin('imageseo-settings', {
		render: () => (
			<PluginDocumentSettingPanel
				name="imageseo-panel"
				title={__('Image SEO', 'imageseo')}
				className="imageseo-panel"
			>
				<UpdateAltTextForAllImages />
			</PluginDocumentSettingPanel>
		),
		icon: 'format-image',
	});
});
