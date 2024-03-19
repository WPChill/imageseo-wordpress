import { SelectControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import useSettings from '../../hooks/useSettings';

export const Form = () => {
	const { options, setOptions, global } = useSettings();
	return (
		<div className="form">
			<SelectControl
				label={__('Images to optimize', 'imageseo')}
				value={options?.altFilter || 'ALL'}
				onChange={(value) => {
					console.log(value);
					setOptions({ altFilter: value });
				}}
				options={(global.altSpecification || []).map((alt) => ({
					value: alt.id,
					label: alt.label,
				}))}
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
