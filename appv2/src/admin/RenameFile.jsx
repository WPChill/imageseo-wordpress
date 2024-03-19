import {
	Button,
	TextControl,
	Flex,
	FlexItem,
	ButtonGroup,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { apiCall } from './utils';

export function RenameFile({ attachmentId, filename }) {
	const [currentFilename, setCurrentFilename] = useState(filename);
	const [isOptimizing, setIsOptimizing] = useState(false);

	const requestOptimize = async () => {
		if (isOptimizing) return;
		setIsOptimizing(true);
		apiCall({ id: attachmentId, action: 'optimizeFilename' })
			.then((r) => {
				setIsOptimizing(false);
				if (r?.error) {
					return;
				}
				setCurrentFilename(r.altText || '');
			})
			.catch((e) => {
				console.error(e);
				setIsOptimizing(false);
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
		</>
	);
}
