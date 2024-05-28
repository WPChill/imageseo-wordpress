import { __ } from '@wordpress/i18n';
import AvatarDefault from './../../../images/avatar-default.jpg';
import useSettings from '../../hooks/useSettings';
import { __experimentalHeading as Heading } from '@wordpress/components';
import { Star } from './star';

export const Previewer = () => {
	const { options } = useSettings();
	const socialMediaSettings = options?.socialMediaSettings || {};

	const {
		layout,
		defaultBgImg,
		logoUrl,
		textColor,
		visibilitySubTitle,
		visibilitySubTitleTwo,
		visibilityRating,
		visibilityAvatar,
		contentBackgroundColor,
		starColor,
	} = socialMediaSettings;

	return (
		<div className="previewer">
			{layout === 'CARD_LEFT' && (
				<div
					className="bg-image"
					style={{
						backgroundImage: `url(${defaultBgImg})`,
					}}
				/>
			)}
			<div
				className="content"
				style={{ backgroundColor: contentBackgroundColor }}
			>
				<div className="padded">
					<img src={logoUrl} alt="logo" className="logo" />
					<Heading
						level={2}
						lineHeight={1.5}
						style={{ color: textColor }}
					>
						{__('Lorem ipsum (post title)', 'imageseo')}
					</Heading>
					{visibilitySubTitle && (
						<Heading
							level={3}
							lineHeight={1.5}
							style={{ color: textColor }}
						>
							{__('Lorem ipsum (sub title)', 'imageseo')}
						</Heading>
					)}
					{visibilitySubTitleTwo && (
						<Heading
							level={4}
							lineHeight={1.5}
							style={{ color: textColor }}
						>
							{__('Lorem ipsum (sub title two)', 'imageseo')}
						</Heading>
					)}
					{visibilityAvatar && (
						<img
							src={AvatarDefault}
							alt="default avatar"
							className="avatar"
						/>
					)}
					{visibilityRating && (
						<div>
							<Star color={starColor} />
							<Star color={starColor} />
							<Star color={starColor} />
							<Star color={starColor} />
							<Star color={starColor} />
						</div>
					)}
				</div>
			</div>
			{layout === 'CARD_RIGHT' && (
				<div
					className="bg-image"
					style={{
						backgroundImage: `url(${defaultBgImg})`,
					}}
				/>
			)}
		</div>
	);
};
