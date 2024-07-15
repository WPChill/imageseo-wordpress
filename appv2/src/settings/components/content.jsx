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
	Spinner,
} from '@wordpress/components';
import Logo from '../../images/default_logo.png';
import { __ } from '@wordpress/i18n';
import { useUser } from '../hooks/useUser';
import useSettings from '../hooks/useSettings';
import { useMemo, useCallback } from '@wordpress/element';

export const Content = ({ heading, children, saveButton }) => {
	const { options } = useSettings();
	const { data, isLoading } = useUser(options.apiKey);

	const loggedIn = useMemo(() => {
		if (isLoading || !data) {
			return false;
		}

		if (data?.message === 'Invalid API Key') {
			return false;
		}

		return true;
	}, []);

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

	const headToStore = useCallback(() => {
		window.open('https://app.imageseo.com/plan/', '_blank');
	}, []);

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
							<div>
								{loggedIn && (
									<span className="remaining-credits-info">
										{isLoading ? (
											<Spinner />
										) : (
											sprintf(
												__(
													'Remaining credits %d',
													'imageseo'
												),
												limit
											)
										)}
									</span>
								)}
							</div>
							<div className="cta">
								<span>
									<Button
										variant="primary"
										onClick={headToStore}
									>
										{__('Buy more credits', 'imageseo')}
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
