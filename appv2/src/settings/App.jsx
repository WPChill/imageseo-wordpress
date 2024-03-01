import {
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { Content } from './components/content';
import { __ } from '@wordpress/i18n';
import { Welcome } from './components/welcome';
import { SocialCard } from './components/social-card';
import useSettings from './hooks/useSettings';
import { BulkOptimizer } from './components/bulk-optimizer';
import { Settings } from './components/settings';

const urlParams = new URLSearchParams(window.location.search);
const activeTab = urlParams.get('activeTab');

export function App() {
	const { options } = useSettings();
	const [tab, setTab] = useState(activeTab || 'welcome');

	const onChangeCb = (value) => {
		setTab(value);
		const url = new URL(window.location.href);
		url.searchParams.set('activeTab', value);
		window.history.replaceState(null, null, url);
	};

	return (
		<Content heading={__('ImageSEO', 'imageseo')}>
			<ToggleGroupControl onChange={onChangeCb} value={tab} isBlock>
				<ToggleGroupControlOption
					type="button"
					value="welcome"
					label={__('Welcome', 'imageseo')}
				/>
				<ToggleGroupControlOption
					disabled={!options?.allowed || false}
					type="button"
					value="socialcard"
					label={__('Social card', 'imageseo')}
				/>
				<ToggleGroupControlOption
					disabled={!options?.allowed || false}
					type="button"
					value="bulkoptimizer"
					label={__('Bulk optimization', 'imageseo')}
				/>
				<ToggleGroupControlOption
					disabled={!options?.allowed || false}
					type="button"
					value="settings"
					label={__('Settings', 'imageseo')}
				/>
			</ToggleGroupControl>
			{tab === 'welcome' && <Welcome />}
			{tab === 'socialcard' && <SocialCard />}
			{tab === 'bulkoptimizer' && <BulkOptimizer />}
			{tab === 'settings' && <Settings />}
		</Content>
	);
}
