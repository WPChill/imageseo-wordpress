import { __ } from '@wordpress/i18n';
import { Button, CheckboxControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { fetchAltText } from '../utils/fetch-alt';

export const UpdateAltTextForAllImages = () => {
	const { updateBlockAttributes } = useDispatch('core/block-editor');
	const { updateSettings, setUpdating, setError } = useDispatch('imageseo');

	const { settings, isUpdating, error } = useSelect(
		(select) => ({
			settings: select('imageseo').getSettings(),
			isUpdating: select('imageseo').isUpdating(),
			error: select('imageseo').getError(),
		}),
		[]
	);

	// Get image blocks and calculate statistics
	const { totalImages, imagesWithAlt, percentage, imageBlocks } = useSelect(
		(select) => {
			const { getClientIdsWithDescendants, getBlock } =
				select('core/block-editor');

			const allBlockIds = getClientIdsWithDescendants();

			const imageBlocksFiltered = allBlockIds
				.map((id) => getBlock(id))
				.filter((block) => block.name === 'core/image');

			const total = imageBlocksFiltered.length;
			const withAlt = imageBlocksFiltered.filter(
				(block) =>
					block.attributes.alt && block.attributes.alt.trim() !== ''
			).length;

			return {
				totalImages: total,
				imagesWithAlt: withAlt,
				percentage: total > 0 ? Math.round((withAlt / total) * 100) : 0,
				imageBlocks: imageBlocksFiltered,
			};
		},
		[]
	);

	const handleUpdateAltText = async () => {
		setUpdating(true);

		try {
			// Process images in batches of 4
			const batchSize = 4;

			for (let i = 0; i < imageBlocks.length; i += batchSize) {
				const batch = imageBlocks.slice(i, i + batchSize);

				// Process batch in parallel
				await Promise.all(
					batch.map(async (block) => {
						const { id, url } = block.attributes;

						// Skip if has alt text and not overwriting
						if (
							!settings.overwriteExisting &&
							block.attributes.alt?.trim()
						) {
							return;
						}

						// Skip external images if not including non-library
						if (!settings.includeNonLibrary && !id) {
							return;
						}

						const response = await fetchAltText({ id, url });

						if (response.error) {
							setError(response.error);
							console.warn(
								`Error generating alt text for image ${id || url}:`,
								response.error
							);
							return;
						}

						updateBlockAttributes(block.clientId, {
							alt: response.altText,
						});
					})
				);
			}
			// Your alt text update logic here using the settings
		} catch (err) {
			setError(err);
			console.error('Error updating alt text:', err);
		} finally {
			setUpdating(false);
		}
	};

	return (
		<div className="imageseo-panel-content">
			<p className="description">
				{__(
					'Add alt text from your media library to images. For images without alt text, new descriptions will be automatically generated.',
					'imageseo'
				)}
			</p>

			<div className="alt-text-stats">
				{totalImages > 0 ? (
					<p>
						{__('Alt Text', 'imageseo')}{' '}
						<strong>
							{imagesWithAlt}/{totalImages}{' '}
							{__('images', 'imageseo')} ({percentage}%)
						</strong>
					</p>
				) : (
					<p>{__('No images found in the content', 'imageseo')}</p>
				)}
			</div>

			<CheckboxControl
				label={__('Overwrite existing alt text', 'imageseo')}
				checked={settings.overwriteExisting}
				onChange={(checked) =>
					updateSettings({
						overwriteExisting: checked,
					})
				}
			/>

			<CheckboxControl
				label={__('Include images not in library', 'imageseo')}
				checked={settings.includeNonLibrary}
				onChange={(checked) =>
					updateSettings({
						includeNonLibrary: checked,
					})
				}
			/>

			{error ? (
				<div className="error">ImageSEO Error: {error}</div>
			) : (
				<Button
					variant="secondary"
					className="refresh-alt-text-button"
					onClick={handleUpdateAltText}
					isBusy={isUpdating}
					disabled={isUpdating}
					icon="update"
				>
					{isUpdating
						? __('Updating all alts…', 'imageseo')
						: __('Update all alts', 'imageseo')}
				</Button>
			)}
		</div>
	);
};
