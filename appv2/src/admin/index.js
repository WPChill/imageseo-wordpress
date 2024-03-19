import { RenameAlt } from './RenameAlt';
import { RenameFile } from './RenameFile';
import { createRoot } from '@wordpress/element';
import './index.css';

document.addEventListener('DOMContentLoaded', () => {
	const renameAltApps = document.querySelectorAll(
		'.media-column-imageseo-alt'
	);
	const renameFilenameApps = document.querySelectorAll(
		'.media-column-imageseo-filename'
	);

	renameAltApps.forEach((rootApp) => {
		const root = createRoot(rootApp);
		const attachmentId = rootApp.getAttribute('data-id');
		const alt = rootApp.getAttribute('data-alt');
		root.render(<RenameAlt attachmentId={attachmentId} alt={alt} />);
	});

	renameFilenameApps.forEach((rootApp) => {
		const root = createRoot(rootApp);
		const attachmentId = rootApp.getAttribute('data-id');
		const filename = rootApp.getAttribute('data-filename');
		root.render(
			<RenameFile attachmentId={attachmentId} filename={filename} />
		);
	});
});
