/* eslint-disable import/namespace */
import { App } from './App';
import { createRoot } from '@wordpress/element';
import './index.css';
import { SettingsProvider } from './Context';

document.addEventListener('DOMContentLoaded', () => {
	const dom = document.getElementById('imageseo-settings-v2');
	const root = createRoot(dom);
	root.render(
		<SettingsProvider>
			<App />
		</SettingsProvider>
	);
});
