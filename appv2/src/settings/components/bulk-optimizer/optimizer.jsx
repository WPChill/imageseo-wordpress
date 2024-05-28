/* eslint-disable @wordpress/i18n-translator-comments */
import { __, sprintf } from '@wordpress/i18n';

import {
	Button,
	__experimentalSpacer as Spacer,
	__experimentalText as Text,
	Placeholder,
} from '@wordpress/components';
import useSettings from '../../hooks/useSettings';
import apiFetch from '@wordpress/api-fetch';
import { useOptimizerStatus } from '../../hooks/useOptimizerStatus';
import { useImageQuery } from '../../hooks/useImageQuery';
import { useMemo } from '@wordpress/element';
export const Optimizer = () => {
	const { global, options, addNotice } = useSettings();
	const { data, isLoading, error, mutate } = useOptimizerStatus();
	const { data: imageQuery, mutate: mutateImageQuery } = useImageQuery();
	const optimizeCb = async () => {
		await apiFetch({
			path: '/imageseo/v1/start-bulk-optimizer',
			method: 'POST',
		});
		mutate();
		addNotice({
			status: 'success',
			content: __('Optimizer started', 'imageseo'),
		});
	};

	const cancelOptimizerCb = async () => {
		await apiFetch({
			path: '/imageseo/v1/stop-bulk-optimizer',
			method: 'POST',
		});
		mutate();
		mutateImageQuery();
		addNotice({
			status: 'success',
			content: __('Optimizer stopped', 'imageseo'),
		});
	};

	const limitReached = useMemo(() => {
		return (
			data?.report?.errors?.length > 0 &&
			data?.report?.errors?.find(
				(err) =>
					err.trim() ===
					'You have reached the limit of images to optimize'
			) !== undefined
		);
	}, [data?.report?.errors]);

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

	if (data?.status === 'idle' && limitReached) {
		return (
			<div className="optimizer">
				<Text>
					{__(
						'You have reached the limit of images to optimize.',
						'imageseo'
					)}
				</Text>
				<Spacer marginY="5" />
				<Button
					isPrimary
					disabled={data?.status === 'running'}
					onClick={optimizeCb}
				>
					{__('Try again', 'imageseo')}
				</Button>
			</div>
		);
	}

	return (
		<div className="optimizer">
			{data?.status === 'idle' && (
				<>
					{options?.altFilter === 'NEXTGEN_GALLERY' && (
						<Text>
							{sprintf(
								__(
									"There are %1$s images in your NEXTGEN library and %2$s don't have an alternative text.",
									'imageseo'
								),
								global.bulkQuery.ids.length || 0,
								global.bulkQuery.nonOptimized.length || 0
							)}
						</Text>
					)}
					{options?.altFilter !== 'NEXTGEN_GALLERY' && (
						<Text>
							{sprintf(
								__(
									"There are %1$s images in your media library and %2$s don't have an alternative text.",
									'imageseo'
								),
								imageQuery?.totalImages || 0,
								imageQuery?.totalNoAlt || 0
							)}
						</Text>
					)}

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

					<progress
						value={
							(data?.report?.optimized / data?.report?.total) *
							100
						}
						max={100}
					/>
					<Spacer marginY="3" />
					<Button isPrimary onClick={cancelOptimizerCb}>
						{__('Stop optimizer', 'imageseo')}
					</Button>
				</div>
			)}
		</div>
	);
};
