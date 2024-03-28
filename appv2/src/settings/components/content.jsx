import {
	Card,
	CardHeader,
	CardBody,
	CardFooter,
	Button,
	Animate,
	__experimentalText as Text,
	__experimentalHeading as Heading,
	ExternalLink,
} from '@wordpress/components';
import Logo from '../../images/default_logo.png';
import { __ } from '@wordpress/i18n';
import { useUser } from '../hooks/useUser';
import useSettings from '../hooks/useSettings';
import { useMemo } from '@wordpress/element';

export const Content = ({ heading, children, saveButton }) => {
	const { options } = useSettings();
	const { data, isLoading } = useUser(options.apiKey);

	const limit = useMemo(() => {
		if (isLoading || !data) {
			return 0;
		}

		if (data?.message === 'Invalid API Key') {
			return 0;
		}

		const { user } = data;

		const currentLimit =
			user?.plan?.limitImages +
			user?.bonusStockImages -
			user?.currentRequestImages;

		return isNaN(currentLimit) ? 0 : currentLimit;
	}, [data, isLoading]);

	return (
		<Animate type={isLoading ? 'loading' : ''}>
			{({ className }) => (
				<Card>
					<CardHeader>
						<div className="header-container">
							<div className="header">
								<img className="logo" src={Logo} alt="logo" />
								<Heading align="center">{heading}</Heading>
								<ExternalLink
									className="visit-website"
									href="https://www.imageseo.com"
									target="_blank"
								>
									{__('Visit website', 'imageseo')}
								</ExternalLink>
							</div>
							<div className="cta">
								<Text>
									{__('Remaining credits:', 'imageseo')}{' '}
									{limit}
								</Text>
								<span>
									<Button variant="primary">
										{__('Buy more!', 'imageseo')}
									</Button>
								</span>
							</div>
						</div>
					</CardHeader>
					<CardBody className={className}>{children}</CardBody>

					<CardFooter>
						<Text>{''}</Text>
						{saveButton && (
							<Button type="submit" variant="secondary">
								{__('Save', 'imageseo')}
							</Button>
						)}
					</CardFooter>
				</Card>
			)}
		</Animate>
	);
};
