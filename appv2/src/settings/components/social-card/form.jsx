import {
	ToggleControl,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
	__experimentalGrid as Grid,
	__experimentalDivider as Divider,
	ColorPicker,
	BaseControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import useSettings from '../../hooks/useSettings';
import { MediaUploader } from '../media/media-uploader';

export const Form = () => {
	const { options, setOptions } = useSettings();
	return (
		<div>
			<ToggleControl
				label={__('Subtitle', 'imageseo')}
				checked={options.socialMediaSettings?.visibilitySubTitle}
				onChange={(value) => {
					setOptions({
						...options,
						socialMediaSettings: {
							...options.socialMediaSettings,
							visibilitySubTitle: value,
						},
					});
				}}
				help={__(
					'Show the price product or author depending on the page ( Product price (WooCommerce only) )',
					'imageseo'
				)}
			/>

			<ToggleControl
				label={__('Subtitle 2', 'imageseo')}
				checked={options.socialMediaSettings?.visibilitySubTitleTwo}
				onChange={(value) => {
					setOptions({
						...options,
						socialMediaSettings: {
							...options.socialMediaSettings,
							visibilitySubTitleTwo: value,
						},
					});
				}}
				help={__(
					'Show the reading time of an article or the number of reviews (WooCommerce only).',
					'imageseo'
				)}
			/>

			<ToggleControl
				label={__('Stars rating', 'imageseo')}
				checked={options.socialMediaSettings?.visibilityRating}
				onChange={(value) => {
					setOptions({
						...options,
						socialMediaSettings: {
							...options.socialMediaSettings,
							visibilityRating: value,
						},
					});
				}}
				help={__(
					'Show the stars linked to a review of your product for example.',
					'imageseo'
				)}
			/>

			<ToggleControl
				label={__('Author avatar', 'imageseo')}
				checked={options.socialMediaSettings?.visibilityAvatar}
				onChange={(value) => {
					setOptions({
						...options,
						socialMediaSettings: {
							...options.socialMediaSettings,
							visibilityAvatar: value,
						},
					});
				}}
				help={__('Only used for post content.', 'imageseo')}
			/>

			<ToggleGroupControl
				style={{ width: 300 }}
				value={options.socialMediaSettings?.layout || 'CARD_LEFT'}
				label={__('Layout', 'imageseo')}
				help={__('Choose the layout of the social card.', 'imageseo')}
				onChange={(value) => {
					setOptions({
						...options,
						socialMediaSettings: {
							...options.socialMediaSettings,
							layout: value,
						},
					});
				}}
			>
				<ToggleGroupControlOption
					type="button"
					value="CARD_LEFT"
					label={__('Card left', 'imageseo')}
				/>
				<ToggleGroupControlOption
					type="button"
					value="CARD_RIGHT"
					label={__('Card right', 'imageseo')}
				/>
			</ToggleGroupControl>

			<ToggleGroupControl
				value={options.socialMediaSettings?.textAlignment || 'top'}
				onChange={(value) => {
					setOptions({
						...options,
						socialMediaSettings: {
							...options.socialMediaSettings,
							textAlignment: value,
						},
					});
				}}
				label={__('Text alignment', 'imageseo')}
			>
				<ToggleGroupControlOption
					type="button"
					value="top"
					label={__('Top', 'imageseo')}
				/>
				<ToggleGroupControlOption
					type="button"
					value="center"
					label={__('Center', 'imageseo')}
				/>
				<ToggleGroupControlOption
					type="button"
					value="bottom"
					label={__('Bottom', 'imageseo')}
				/>
			</ToggleGroupControl>

			<Grid columns={3}>
				<BaseControl
					id="text-color"
					label={__('Text color', 'imageseo')}
				>
					<ColorPicker
						defaultValue={
							options.socialMediaSettings?.textColor || '#000000'
						}
						onChange={(value) => {
							setOptions({
								...options,
								socialMediaSettings: {
									...options.socialMediaSettings,
									textColor: value,
								},
							});
						}}
					/>
				</BaseControl>

				<BaseControl
					id="background-color"
					label={__('Background color', 'imageseo')}
				>
					<ColorPicker
						defaultValue={
							options.socialMediaSettings
								?.contentBackgroundColor || '#ffffff'
						}
						onChange={(value) => {
							setOptions({
								...options,
								socialMediaSettings: {
									...options.socialMediaSettings,
									contentBackgroundColor: value,
								},
							});
						}}
					/>
				</BaseControl>
				{options.socialMediaSettings?.visibilityRating && (
					<BaseControl
						id="star-color"
						label={__('Star color', 'imageseo')}
					>
						<ColorPicker
							defaultValue={
								options.socialMediaSettings?.starColor ||
								'#F8CA00'
							}
							onChange={(value) => {
								setOptions({
									...options,
									socialMediaSettings: {
										...options.socialMediaSettings,
										starColor: value,
									},
								});
							}}
						/>
					</BaseControl>
				)}
			</Grid>

			<MediaUploader
				label={__('Logo', 'imageseo')}
				value={options?.socialMediaSettings?.logoUrl}
				help={__('Click to upload a logo', 'imageseo')}
				onChange={(value) => {
					setOptions({
						...options,
						socialMediaSettings: {
							...options.socialMediaSettings,
							logoUrl: value,
						},
					});
				}}
			/>
			<Divider style={{ marginTop: 15, marginBottom: 15 }} />
			<MediaUploader
				label={__('Background image', 'imageseo')}
				value={options?.socialMediaSettings?.defaultBgImg}
				help={__('Click to upload a background Image', 'imageseo')}
				onChange={(value) => {
					setOptions({
						...options,
						socialMediaSettings: {
							...options.socialMediaSettings,
							defaultBgImg: value,
						},
					});
				}}
			/>
		</div>
	);
};
