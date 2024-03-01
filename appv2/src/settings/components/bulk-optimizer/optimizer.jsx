/* eslint-disable @wordpress/i18n-translator-comments */
import { __, sprintf } from '@wordpress/i18n';

import {
	Button,
	__experimentalSpacer as Spacer,
	__experimentalText as Text,
	__experimentalHeading as Heading,
	Placeholder,
} from '@wordpress/components';
import useSettings from '../../hooks/useSettings';
import apiFetch from '@wordpress/api-fetch';
import { useOptimizerStatus } from '../../hooks/useOptimizerStatus';

export const Optimizer = () => {
	const { global } = useSettings();
	const { data, isLoading, error, isFetching, mutate } = useOptimizerStatus();

	const optimizeCb = async () => {
		await apiFetch({
			path: '/imageseo/v1/start-bulk-optimizer',
			method: 'POST',
		});
		mutate();
	};

	const cancelOptimizerCb = async () => {
		await apiFetch({
			path: '/imageseo/v1/stop-bulk-optimizer',
			method: 'POST',
		});
		mutate();
	};

	if (isLoading) {
		return (
			<Placeholder
				className="optimizer"
				icon="update"
				label={__('Loading', 'imageseo')}
				instructions={__('Loading…', 'imageseo')}
			/>
		);
	}

	if (error) {
		return (
			<Placeholder
				className="optimizer"
				icon="warning"
				label={__('Error', 'imageseo')}
				instructions={__(
					'An error occurred while fetching the optimizer status.',
					'imageseo'
				)}
			/>
		);
	}

	return (
		<div className="optimizer">
			{data?.status === 'idle' && (
				<>
					<Text>
						{sprintf(
							__(
								"There are %1$s images in your media library and %2$s don't have an alternative text.",
								'imageseo'
							),
							global.totalImages,
							global.totalNoAlt
						)}
					</Text>
					<Spacer marginY="5" />
					<Button
						isPrimary
						disabled={data?.status === 'running'}
						onClick={optimizeCb}
					>
						{__('Start optimization', 'imageseo')}
					</Button>
				</>
			)}
			{data?.status === 'running' && (
				<div className="progress">
					<Text>
						{sprintf(
							__(
								'Optimizing %1$s images. ( %2$s/%3$s )',
								'imageseo'
							),
							data?.report?.total,
							data?.report?.optimized,
							data?.report?.total
						)}
					</Text>
					<progress value="0" max="100"></progress>
					<Spacer marginY="3" />
					<Button isPrimary onClick={cancelOptimizerCb}>
						{__('Stop optimizer', 'imageseo')}
					</Button>
				</div>
			)}
		</div>
	);
};
