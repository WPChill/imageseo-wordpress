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

export function RenameAlt({ attachmentId, alt }) {
	const [currentAlt, setCurrentAlt] = useState(alt);
	const [isOptimizing, setIsOptimizing] = useState(false);
	const saveAlt = async () => {
		if (isOptimizing) return;
		setIsOptimizing(true);
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
			})
			.catch((e) => {
				console.error(e);
				setIsOptimizing(false);
			});
	};
	const requestOptimize = async () => {
		if (isOptimizing) return;
		setIsOptimizing(true);
		apiCall({ id: attachmentId, action: 'optimizeAlt' })
			.then((r) => {
				setIsOptimizing(false);
				if (r?.error) {
					return;
				}
				setCurrentAlt(r.altText || '');
			})
			.catch((e) => {
				console.error(e);
				setIsOptimizing(false);
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
						isPrimary
						isBusy={isOptimizing}
					>
						{__('Save', 'imageseo')}
					</Button>
					<Button
						variant="tertiary"
						onClick={requestOptimize}
						isBusy={isOptimizing}
					>
						{__('Auto-optimize', 'imageseo')}
					</Button>
				</ButtonGroup>
			</FlexItem>
		</Flex>
	);
}
