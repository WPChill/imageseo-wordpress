import { SelectControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import useSettings from '../../hooks/useSettings';

export const Form = () => {
	const { options, setOptions } = useSettings();

	return (
		<div className="form">
			<SelectControl
				label={__('Images to optimize', 'imageseo')}
				value={options?.altFiller || 'ALL'}
				onChange={(value) => {
					setOptions({ altFiller: value });
				}}
				options={[
					{
						value: 'ALL',
						label: __('Only media library images', 'imageseo'),
					},
					{
						value: 'FEATURED_IMAGE',
						label: __('Only featured images', 'imageseo'),
					},
				]}
			/>
			<SelectControl
				label={__('Optimize alt text', 'imageseo')}
				value={options?.altFill || 'FILL_ALL'}
				onChange={(value) => {
					setOptions({ altFill: value });
				}}
				options={[
					{
						value: 'FILL_ALL',
						label: __('Optimize all ALT texts', 'imageseo'),
					},
					{
						value: 'FILL_ONLY_EMPTY',
						label: __(
							'Optimize only missing ALT texts',
							'imageseo'
						),
					},
				]}
			/>
			<ToggleControl
				checked={options?.optimizeFile}
				onChange={(value) => {
					setOptions({ optimizeFile: value });
				}}
				label={__('Rename files', 'imageseo')}
			/>
		</div>
	);
};
