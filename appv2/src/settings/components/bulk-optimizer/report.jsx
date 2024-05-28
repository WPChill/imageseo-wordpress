import {
	Animate,
	__experimentalDivider as Divider,
	__experimentalText as Text,
	__experimentalHeading as Heading,
} from '@wordpress/components';
import useSettings from '../../hooks/useSettings';
import { useOptimizerStatus } from '../../hooks/useOptimizerStatus';
import { __ } from '@wordpress/i18n';

export const Report = () => {
	const { loading } = useSettings();
	const { data } = useOptimizerStatus();

	return (
		<Animate type={loading ? 'loading' : ''}>
			{({ className }) => (
				<div className={`${className ? className : ''}`}>
					<Divider style={{ marginTop: 15, marginBottom: 15 }} />
					<Heading level={4}>
						{data?.status === 'running'
							? __('Current report', 'imageseo')
							: __('Last report', 'imageseo')}
					</Heading>
					<Text>
						{__('Optimized: ', 'imageseo')} {data?.report.optimized}
					</Text>
					<br />
					<Text>
						{__('Skipped: ', 'imageseo')} {data?.report.skipped}
					</Text>
					<br />
					<Text>
						{__('Failed: ', 'imageseo')} {data?.report.failed}
					</Text>
				</div>
			)}
		</Animate>
	);
};
