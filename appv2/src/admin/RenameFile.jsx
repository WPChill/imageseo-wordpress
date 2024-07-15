import {
	Button,
	TextControl,
	Flex,
	FlexItem,
	ButtonGroup,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { apiCall, saveProperty } from './utils';
import { eventBus } from './EventBus';

export function RenameFile({ attachmentId, filename }) {
	const [currentFilename, setCurrentFilename] = useState(filename);
	const [isOptimizing, setIsOptimizing] = useState(false);
	const [requested, setRequested] = useState(false);
	const [done, setDone] = useState(null);
	const [action, setAction] = useState('');
	const saveFilename = async () => {
		if (isOptimizing) return;
		setIsOptimizing(true);
		setDone(false);
		setAction('save');
		saveProperty({
			id: attachmentId,
			action: 'saveFilename',
			value: currentFilename,
		})
			.then((r) => {
				setIsOptimizing(false);
				if (r?.error) {
					return;
				}
				setRequested(true);
				setDone(true);
				setCurrentFilename(r.filename || '');

				eventBus.publish('snackbar', {
					content: __('Filename saved', 'imageseo'),
					status: 'info',
				});
			})
			.catch((e) => {
				console.error(e);
				setRequested(true);
				setDone(false);
				setIsOptimizing(false);
				eventBus.publish('snackbar', {
					content: __('Something went wrong!', 'imageseo'),
					status: 'error',
				});
			});
	};
	const requestOptimize = async () => {
		if (isOptimizing) return;
		setAction('optimize');
		setIsOptimizing(true);
		setDone(false);
		apiCall({ id: attachmentId, action: 'optimizeFilename' })
			.then((r) => {
				setIsOptimizing(false);
				if (r?.error) {
					return;
				}
				setCurrentFilename(r.filename || '');
				setRequested(true);
				setDone(true);
				eventBus.publish('snackbar', {
					content: __('Filename optimized', 'imageseo'),
					status: 'info',
				});
			})
			.catch((e) => {
				console.error(e);
				setIsOptimizing(false);
				setDone(false);
				setRequested(true);
				eventBus.publish('snackbar', {
					content: __('Something went wrong!', 'imageseo'),
					status: 'error',
				});
			});
	};
	return (
		<>
			<Flex direction="column">
				<FlexItem>
					<TextControl
						label={__('Filename', 'imageseo')}
						value={currentFilename || ''}
						onChange={setCurrentFilename}
					/>
				</FlexItem>
				<FlexItem>
					<ButtonGroup>
						<Button
							style={{
								margin: '0 6px',
							}}
							onClick={saveFilename}
							variant="primary"
							isBusy={isOptimizing}
							// icon={requested && done ? 'yes' : undefined}
						>
							{action === 'save' && requested && done
								? __('Saved', 'imageseo')
								: __('Save', 'imageseo')}
						</Button>
						<Button
							variant="tertiary"
							onClick={requestOptimize}
							isBusy={isOptimizing}
							// icon={
							// 	action === 'optimize' && requested && done
							// 		? 'yes'
							// 		: undefined
							// }
						>
							{action === 'optimize' && requested && done
								? __('Optimized', 'imageseo')
								: __('Optimize', 'imageseo')}
						</Button>
					</ButtonGroup>
				</FlexItem>
			</Flex>
		</>
	);
}
