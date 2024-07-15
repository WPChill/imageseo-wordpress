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

export function RenameAlt({ attachmentId, alt }) {
	const [currentAlt, setCurrentAlt] = useState(alt);
	const [isOptimizing, setIsOptimizing] = useState(false);
	const [requested, setRequested] = useState(false);
	const [done, setDone] = useState(null);
	const [action, setAction] = useState('');

	const saveAlt = async () => {
		if (isOptimizing) return;
		setIsOptimizing(true);
		setDone(false);
		setAction('save');
		saveProperty({
			id: attachmentId,
			action: 'saveAlt',
			value: currentAlt,
		})
			.then((r) => {
				setIsOptimizing(false);
				if (r?.error) {
					return;
				}
				setCurrentAlt(r.altText || '');
				setRequested(true);
				setDone(true);
				eventBus.publish('snackbar', {
					content: __('Alt saved', 'imageseo'),
					status: 'info',
				});
			})
			.catch((e) => {
				console.error(e);
				setIsOptimizing(false);
				setRequested(true);
				setDone(false);
				eventBus.publish('snackbar', {
					content: __('Something went wrong!', 'imageseo'),
					status: 'error',
				});
			});
	};
	const requestOptimize = async () => {
		if (isOptimizing) return;
		setIsOptimizing(true);
		setDone(false);
		setAction('optimize');
		apiCall({ id: attachmentId, action: 'optimizeAlt' })
			.then((r) => {
				setIsOptimizing(false);
				if (r?.error) {
					return;
				}
				setCurrentAlt(r.altText || '');
				setRequested(true);
				setDone(true);
				eventBus.publish('snackbar', {
					content: __('Alt optimized', 'imageseo'),
					status: 'info',
				});
			})
			.catch((e) => {
				eventBus.publish('snackbar', {
					content: __('Something went wrong!', 'imageseo'),
					status: 'error',
				});
				console.error(e);
				setIsOptimizing(false);
				setRequested(true);
				setDone(false);
			});
	};
	return (
		<Flex direction="column">
			<FlexItem>
				<TextControl
					label={__('Alt', 'imageseo')}
					value={currentAlt || ''}
					onChange={setCurrentAlt}
				/>
			</FlexItem>
			<FlexItem>
				<ButtonGroup>
					<Button
						style={{
							margin: '0 6px',
						}}
						onClick={saveAlt}
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
						// icon={requested && done ? 'yes' : undefined}
					>
						{action === 'optimize' && requested && done
							? __('Optimized', 'imageseo')
							: __('Optimize', 'imageseo')}
					</Button>
				</ButtonGroup>
			</FlexItem>
		</Flex>
	);
}
