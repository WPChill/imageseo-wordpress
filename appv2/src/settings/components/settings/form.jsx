import {
	__experimentalText as Text,
	__experimentalHeading as Heading,
	__experimentalDivider as Divider,
	ToggleControl,
	SelectControl,
} from '@wordpress/components';
import useSettings from '../../hooks/useSettings';
import { __ } from '@wordpress/i18n';

export const Form = () => {
	const { options, global, setOptions } = useSettings();

	const toggleControlCb = (key, val) => {
		if (val && !(options?.socialMediaPostTypes || []).includes(key)) {
			setOptions({
				socialMediaPostTypes: [
					...(options?.socialMediaPostTypes || []),
					key,
				],
			});
		}

		if (!val && (options?.socialMediaPostTypes || []).includes(key)) {
			setOptions({
				socialMediaPostTypes: (
					options?.socialMediaPostTypes || []
				).filter((e) => e !== key),
			});
		}
	};
	return (
		<div>
			<Heading level={4} lineHeight={2}>
				{__('On-upload optimization', 'imageseo')}
			</Heading>
			<ToggleControl
				label={__('Fill alt', 'imageseo')}
				help={__(
					'The plugin will automatically write an alternative to the images you will upload.',
					'imageseo'
				)}
				checked={options.activeAltWriteUpload}
				onChange={(value) =>
					setOptions({ activeAltWriteUpload: value })
				}
			/>
			<ToggleControl
				label={__('Rename files', 'imageseo')}
				help={__(
					'The plugin will automatically rewrite with SEO friendly content the name of the images you will upload.',
					'imageseo'
				)}
				checked={options.activeRenameWriteUpload}
				onChange={(value) =>
					setOptions({ activeRenameWriteUpload: value })
				}
			/>
			<SelectControl
				label={__('Language', 'imageseo')}
				options={global.languages || []}
				onChange={(value) => {
					setOptions({ defaultLanguageIa: value });
				}}
				value={
					options?.defaultLanguageIa ||
					global?.currentLanguage ||
					'en'
				}
			/>
			<Divider style={{ marginTop: 15, marginBottom: 15 }} />

			<Heading level={4} lineHeight={2}>
				{__('Social media card generator', 'imageseo')}
			</Heading>
			<Text style={{ marginBottom: 15, display: 'inline-block' }}>
				{__(
					'Automatic generation for the following post types:',
					'imageseo'
				)}
			</Text>
			{global?.allowedPostTypes?.map((postType) => (
				<div key={postType.value} style={{ maxWidth: 300 }}>
					<ToggleControl
						__nextHasNoMarginBottom={true}
						checked={(options?.socialMediaPostTypes || []).includes(
							postType.value
						)}
						label={postType.label}
						onChange={(val) => toggleControlCb(postType.value, val)}
					/>
				</div>
			))}
		</div>
	);
};
