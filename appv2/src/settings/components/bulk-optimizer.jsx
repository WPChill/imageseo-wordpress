import { Animate } from '@wordpress/components';
import useSettings from '../hooks/useSettings';
import { Form } from './bulk-optimizer/form';
import { Optimizer } from './bulk-optimizer/optimizer';
import { Report } from './bulk-optimizer/report';

export const BulkOptimizer = () => {
	const { loading } = useSettings();

	return (
		<Animate type={loading ? 'loading' : ''}>
			{({ className }) => (
				<>
					<div
						className={`${className ? className : ''} bulk-optimizer-screen`}
					>
						<Form />
						<Optimizer />
					</div>
					<Report />
				</>
			)}
		</Animate>
	);
};
