import {
	Button,
	Animate,
	__experimentalText as Text,
	__experimentalHeading as Heading,
	TextControl,
	CheckboxControl,
	Flex,
	FlexItem,
	ExternalLink,
	Icon,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import useSettings from '../hooks/useSettings';
import apiFetch from '@wordpress/api-fetch';

export const Welcome = () => {
	const { options, global, setOptions, addNotice } = useSettings();
	const [loadingRequest, setLoadingRequest] = useState(false);
	const [alreadyRegistered, setAlreadyRegistered] = useState(
		options?.allowed
	);
	const [errorMsg, setErrorMsg] = useState('');

	const [form, setForm] = useState({
		firstName: global?.user?.firstName || '',
		lastName: global?.user?.lastName || '',
		email: global?.user?.email || '',
		password: '',
		terms: false,
		news: false,
	});

	const validateApiKey = async () => {
		try {
			setErrorMsg('');
			setLoadingRequest(true);
			const data = await apiFetch({
				path: '/imageseo/v1/validate-api-key',
				method: 'POST',
				data: { apiKey: options.apiKey },
			});

			if (typeof data?.data?.message !== 'undefined') {
				setOptions({
					allowed: false,
				});
				setErrorMsg(data?.data?.message);
				setLoadingRequest(false);
				addNotice({
					status: 'error',
					content: data?.data?.message,
				});
				return;
			}

			setLoadingRequest(false);
			setOptions({
				allowed: true,
			});

			addNotice({
				status: 'success',
				content: __('API key validated', 'imageseo'),
			});
		} catch (error) {
			setLoadingRequest(false);
			addNotice({
				status: 'error',
				content: __('Error validating API key', 'imageseo'),
			});
			console.error('Error validating API key:', error);
		}
	};
	const registerAccount = async () => {
		try {
			setLoadingRequest(true);
			const response = await apiFetch({
				path: '/imageseo/v1/register',
				method: 'POST',
				data: form,
			});

			if (
				Object.prototype.hasOwnProperty.call(response, 'success') &&
				!response?.success
			) {
				throw new Error(
					Array.isArray(response?.data?.message)
						? response?.data?.message.join(',')
						: response?.data?.message
				);
			}

			if (response?.message) {
				addNotice({
					status: 'error',
					content: response?.message,
				});
				throw new Error('Something went wrong');
			}

			setOptions(
				{
					apiKey: response?.projects?.[0]?.apiKey,
					allowed: true,
				},
				false
			);
			addNotice({
				status: 'success',
				content: __('Account created', 'imageseo'),
			});

			setAlreadyRegistered(true);
			setLoadingRequest(false);
		} catch (error) {
			console.log(error);
			setLoadingRequest(false);
			addNotice({
				status: 'error',
				content: error.message,
			});
			console.error('Error registering account:', error);
		}
	};

	const handleFormChange = (key, value) => {
		setForm({ ...form, [key]: value });
	};

	return (
		<div className="welcome-screen">
			<Heading order={3} lineHeight={2} align="center">
				{__('Welcome to ImageSEO', 'imageseo')}
			</Heading>
			<Text align="center">
				{__(
					'We are happy to see you here! ImageSEO is a WordPress plugin that helps you to optimize your images for search engines and social media.',
					'imageseo'
				)}
			</Text>
			<Animate type={loadingRequest ? 'loading' : ''}>
				{({ className }) => (
					<>
						{alreadyRegistered ? (
							<div
								className={`form-container ${className ? className : ''}`}
							>
								<TextControl
									label={__('API Key', 'imageseo')}
									value={options?.apiKey || ''}
									onChange={(value) =>
										setOptions({ apiKey: value })
									}
								/>
								<Flex justifyContent="spaceBetween">
									<FlexItem>
										<Button
											disabled={loadingRequest}
											isPrimary
											onClick={validateApiKey}
										>
											{loadingRequest
												? __('Validating…', 'imageseo')
												: __(
														'Validate key',
														'imageseo'
													)}
										</Button>
									</FlexItem>
									{!options?.allowed && (
										<FlexItem>
											<Button
												isTertiary
												onClick={() =>
													setAlreadyRegistered(false)
												}
											>
												{__(
													'I want to register for a free account',
													'imageseo'
												)}
											</Button>
										</FlexItem>
									)}
									{options?.allowed && (
										<FlexItem>
											<Icon
												icon="yes"
												style={{ color: '#52c41a' }}
											/>
											<Text variant="muted">
												{__(
													'API key validated',
													'imageseo'
												)}
											</Text>
										</FlexItem>
									)}
								</Flex>
								{errorMsg && (
									<Text variant="error">{errorMsg}</Text>
								)}
							</div>
						) : (
							<div
								className={`form-container ${className ? className : ''}`}
							>
								<TextControl
									label={__('First name', 'imageseo')}
									value={form.firstName}
									onChange={(value) =>
										handleFormChange('firstName', value)
									}
								/>
								<TextControl
									label={__('Last name', 'imageseo')}
									value={form.lastName}
									onChange={(value) =>
										handleFormChange('lastName', value)
									}
								/>
								<TextControl
									label={__('Email', 'imageseo')}
									type="email"
									value={form.email}
									onChange={(value) =>
										handleFormChange('email', value)
									}
								/>
								<TextControl
									type="password"
									label={__('Password', 'imageseo')}
									value={form.password}
									onChange={(value) =>
										handleFormChange('password', value)
									}
								/>
								<CheckboxControl
									label={__(
										'I agree to the terms and conditions',
										'imageseo'
									)}
									help={
										<Text variant="muted">
											{__(
												'By creating an account, you agree to our terms and conditions.',
												'imageseo'
											)}
											<ExternalLink
												style={{ marginLeft: 5 }}
												href="https://imageseo.io/terms-conditions/"
											>
												{__('Read more', 'imageseo')}
											</ExternalLink>
										</Text>
									}
									checked={form.terms}
									onChange={(value) =>
										handleFormChange('terms', value)
									}
								/>
								<CheckboxControl
									label={__(
										'I want to receive news and updates',
										'imageseo'
									)}
									help={__(
										'You can unsubscribe at any time',
										'imageseo'
									)}
									checked={form.news}
									onChange={(value) =>
										handleFormChange('news', value)
									}
								/>
								<Flex>
									<FlexItem>
										<Button
											isPrimary
											disabled={loadingRequest}
											onClick={registerAccount}
										>
											{__('Create account', 'imageseo')}
										</Button>
									</FlexItem>
									<FlexItem>
										<Button
											isTertiary
											disabled={loadingRequest}
											onClick={() =>
												setAlreadyRegistered(true)
											}
										>
											{__(
												'I already have an account',
												'imageseo'
											)}
										</Button>
									</FlexItem>
								</Flex>
							</div>
						)}
					</>
				)}
			</Animate>
		</div>
	);
};
