import { Animate } from '@wordpress/components';
import useSettings from '../hooks/useSettings';
import { Form } from './settings/form';

export const Settings = () => {
	const { loading } = useSettings();

	return (
		<Animate type={loading ? 'loading' : ''}>
			{({ className }) => (
				<div
					className={`${className ? className : ''} social-card-screen`}
				>
					<Form />
				</div>
			)}
		</Animate>
	);
};
