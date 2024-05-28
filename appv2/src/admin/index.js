import { RenameAlt } from './RenameAlt';
import { RenameFile } from './RenameFile';
import { createRoot } from '@wordpress/element';
import './index.css';
import { Snackbar } from './Snackbar';

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

	const contentBody = document.getElementById('wpbody-content');
	const div = document.createElement('div');
	const wrapper = document.createElement('div');
	wrapper.setAttribute('id', 'imageseo-snackbar-wrapper');
	div.setAttribute('id', 'imageseo-snackbar-root');
	wrapper.style.position = 'fixed';
	wrapper.style.left = '10px';
	wrapper.style.right = '10px';
	wrapper.style.bottom = '10px';
	wrapper.style.zIndex = '1000';
	wrapper.style.textAlign = 'center';
	wrapper.style.width = '100%';
	wrapper.style.height = '50px';
	wrapper.style.pointerEvents = 'none';

	contentBody.prepend(wrapper);
	wrapper.appendChild(div);
	const root = createRoot(document.getElementById('imageseo-snackbar-root'));

	root.render(<Snackbar />);
});
