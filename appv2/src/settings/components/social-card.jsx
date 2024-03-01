import { Animate } from '@wordpress/components';
import useSettings from '../hooks/useSettings';
import { Form } from './social-card/form';
import { Previewer } from './social-card/previewer';

export const SocialCard = () => {
	const { loading } = useSettings();

	return (
		<Animate type={loading ? 'loading' : ''}>
			{({ className }) => (
				<div
					className={`${className ? className : ''} social-card-screen`}
				>
					<Form />
					<Previewer />
				</div>
			)}
		</Animate>
	);
};
