import { Button } from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';
import { useDispatch, useSelect } from '@wordpress/data';
import { createHigherOrderComponent } from '@wordpress/compose';
import { fetchAltText } from '../utils/fetch-alt';
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';

// Create a HOC to add our button to image blocks
export const withImageAltButton = createHigherOrderComponent((BlockEdit) => {
	return (props) => {
		const [isLoading, setIsLoading] = useState(false);
		const { setError } = useDispatch('imageseo');
		// Only proceed if this is an image block
		if (props.name !== 'core/image') {
			return <BlockEdit {...props} />;
		}

		const { error } = useSelect(
			(select) => ({
				error: select('imageseo').getError(),
			}),
			[]
		);

		const { updateBlockAttributes } = useDispatch('core/block-editor');

		const handleGenerateAltText = async () => {
			setIsLoading(true);
			const { id, url } = props.attributes;

			const response = await fetchAltText({
				id,
				url,
			});

			if (response.error) {
				setError(response.error);
			}

			setIsLoading(false);
			updateBlockAttributes(props.clientId, {
				alt: response.altText,
			});
		};

		return (
			<>
				<BlockEdit {...props} />
				<InspectorControls>
					<div className="components-panel__body is-opened">
						{error ? (
							<div className="error">ImageSEO Error: {error}</div>
						) : (
							<Button
								isPrimary
								onClick={handleGenerateAltText}
								className="components-button editor-post-featured-image__toggle"
								isBusy={isLoading}
							>
								{__('Generate alt with ImageSEO', 'imageseo')}
							</Button>
						)}
					</div>
				</InspectorControls>
			</>
		);
	};
}, 'withImageAltButton');
