import React, { useState, useContext, useEffect } from "react";
import { isNull, isEmpty } from "lodash";
import classNames from "classnames";

import { SocialSettingsContext } from "../../../contexts/SocialSettingsContext";

const toDataUrl = (url, callback) => {
	var xhr = new XMLHttpRequest();
	xhr.onload = function () {
		var reader = new FileReader();
		reader.onloadend = function () {
			callback(reader.result);
		};
		reader.readAsDataURL(xhr.response);
	};
	xhr.open("GET", url);
	xhr.responseType = "blob";
	xhr.send();
};

const hexToRGB = (hex, alpha) => {
	var r = parseInt(hex.slice(1, 3), 16),
		g = parseInt(hex.slice(3, 5), 16),
		b = parseInt(hex.slice(5, 7), 16);

	if (alpha) {
		return "rgba(" + r + ", " + g + ", " + b + ", " + alpha + ")";
	} else {
		return "rgb(" + r + ", " + g + ", " + b + ")";
	}
};

function SocialMediaImagePreview() {
	const { state: settings } = useContext(SocialSettingsContext);
	const [backgroundImage, setBackgroundImage] = useState(null);

	useEffect(() => {
		toDataUrl(settings.defaultBgImg, (base64) =>
			setBackgroundImage(base64)
		);
	}, [settings.defaultBgImg]);

	const stars = [1, 2, 3, 4, 5];
	return (
		<>
			<div
				id="imageseo-preview-image"
				className={classNames(
					{
						"imageseo-media__layout--card-left":
							settings.layout === "CARD_LEFT",
						"imageseo-media__layout--card-right":
							settings.layout === "CARD_RIGHT",
					},
					"imageseo-media__container sticky",
					"imageseo-media__container--preview"
				)}
				style={{
					border: "1px solid #999",
					maxWidth: "80%",
					margin: "0 auto",
					backgroundColor: settings.contentBackgroundColor,
				}}
			>
				<div
					className="imageseo-media__container__image"
					style={{
						backgroundColor: "#ccc",
						backgroundImage: isNull(backgroundImage)
							? null
							: `url(${backgroundImage})`,
						backgroundPosition: "center center",
						backgroundSize: "cover",
						backgroundRepeat: "no-repeat",
					}}
				/>

				<div
					className={classNames(
						{
							"imageseo-media__container__content--center":
								settings.textAlignment === "center",
							"imageseo-media__container__content--top":
								settings.textAlignment === "top",
							"imageseo-media__container__content--bottom":
								settings.textAlignment === "bottom",
						},
						"imageseo-media__container__content"
					)}
				>
					{!isEmpty(settings.logoUrl) && (
						<img
							className="imageseo-media__content__logo"
							src={settings.logoUrl}
						/>
					)}
					<div
						className="imageseo-media__content__title"
						style={{ color: settings.textColor }}
					>
						Lorem ipsum (post_title)
					</div>
					{settings.visibilitySubTitle && (
						<div
							className="imageseo-media__content__sub-title"
							style={{ color: settings.textColor }}
						>
							Sub title (like price or author)
						</div>
					)}
					{settings.visibilitySubTitleTwo && (
						<div
							className="imageseo-media__content__sub-title-two"
							style={{ color: hexToRGB(settings.textColor, 0.5) }}
						>
							Sub title 2 (like reading time)
						</div>
					)}
					{settings.visibilityAvatar && (
						<img
							className="imageseo-media__content__avatar"
							//@ts-ignore
							src={`${IMAGESEO_DATA.URL_DIST}/images/avatar-default.jpg`}
						/>
					)}
					{settings.visibilityRating && (
						<div className="imageseo-media__content__stars flex">
							{stars.map((itm, key) => {
								return (
									<svg
										key={`star_${key}`}
										xmlns="http://www.w3.org/2000/svg"
										width="24"
										height="24"
										viewBox="0 0 24 24"
										fill={`${settings.starColor}`}
										stroke={`${settings.starColor}`}
										strokeWidth="2"
										strokeLinecap="round"
										strokeLinejoin="round"
									>
										<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
									</svg>
								);
							})}
						</div>
					)}
				</div>
			</div>
		</>
	);
}

export default SocialMediaImagePreview;
