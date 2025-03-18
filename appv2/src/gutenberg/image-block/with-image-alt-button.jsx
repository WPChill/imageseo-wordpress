import { Button } from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';
import { useDispatch, useSelect } from '@wordpress/data';
import { createHigherOrderComponent } from '@wordpress/compose';
import { fetchAltText } from '../utils/fetch-alt';
import { checkImageUrl } from '../utils/check-image-url';
import { __ } from '@wordpress/i18n';
import { useState, useEffect, useRef, useCallback } from '@wordpress/element';

const LOCK_ID = 'imageseo-url-check';

export const withImageAltButton = createHigherOrderComponent((BlockEdit) => {
	return (props) => {
		const [isLoading, setIsLoading] = useState(false);
		const previousUrlRef = useRef(props.attributes.url);
		const { setError } = useDispatch('imageseo');
		const { lockPostSaving, unlockPostSaving, savePost } =
			useDispatch('core/editor');
		const updatedUrlRef = useRef(null);

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
		const { isSavingPost, isAutosaving } = useSelect((select) => ({
			isSavingPost: select('core/editor').isSavingPost(),
			isAutosaving: select('core/editor').isAutosavingPost(),
		}));

		const settings = window?.imageSEO || {};
		const shouldOptimizeFilename =
			settings.rewriteFilename === 'true' ||
			settings.rewriteFilename === '1' ||
			settings.rewriteFilename === 1 ||
			settings.rewriteFilename === true;

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

		const checkImageUrlAndUpdate = useCallback(async () => {
			const { url, id } = props.attributes;

			if (!url || !shouldOptimizeFilename) {
				return false;
			}
			if (updatedUrlRef.current === url) {
				return false;
			}
			try {
				setIsLoading(true);

				const result = await checkImageUrl({ id, url });

				if (result.error) {
					return false;
				}

				if (result.url || result.altText) {
					const updates = {};

					if (result.url && result.url !== url) {
						updates.url = result.url;
					}

					if (
						result.altText &&
						result.altText !== props.attributes.alt
					) {
						updates.alt = result.altText;
					}

					if (Object.keys(updates).length > 0) {
						updateBlockAttributes(props.clientId, updates);
						updatedUrlRef.current = updates.url || url;
						return true;
					}
				}
				return false;
			} catch (err) {
				console.error('Error checking URL on save:', err);
				setError(err.message);
				return false;
			} finally {
				setIsLoading(false);
			}
		}, [
			props.attributes,
			props.clientId,
			setError,
			shouldOptimizeFilename,
			updateBlockAttributes,
		]);

		useEffect(() => {
			const handleSave = async () => {
				if (isSavingPost && !isAutosaving) {
					lockPostSaving(LOCK_ID);
					const wasUpdated = await checkImageUrlAndUpdate();
					unlockPostSaving(LOCK_ID);

					if (wasUpdated) {
						setTimeout(() => {
							savePost();
						}, 100);
					}
				}
			};

			handleSave();
		}, [
			isSavingPost,
			isAutosaving,
			checkImageUrlAndUpdate,
			lockPostSaving,
			unlockPostSaving,
			savePost,
		]);

		useEffect(() => {
			const { url } = props.attributes;
			if (url !== previousUrlRef.current) {
				checkImageUrlAndUpdate();
				previousUrlRef.current = url;
			}
		}, [checkImageUrlAndUpdate, props.attributes, props.attributes.url]);

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
