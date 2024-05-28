import { useContext } from '@wordpress/element';
import { SettingsContext } from './../Context';

const useSettings = () => {
	const context = useContext(SettingsContext);

	if (context === undefined) {
		throw new Error('useSettings must be used within a SettingsProvider');
	}

	return context;
};

export default useSettings;
