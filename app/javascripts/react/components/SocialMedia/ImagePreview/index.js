import React, { useState, useContext, useEffect } from "react";
import { isNull } from "lodash";
import classNames from "classnames";

import { SocialSettingsContext } from "../../../contexts/SocialSettingsContext";

const toDataUrl = (url, callback) => {
	var xhr = new XMLHttpRequest();
	xhr.onload = function() {
		var reader = new FileReader();
		reader.onloadend = function() {
			callback(reader.result);
		};
		reader.readAsDataURL(xhr.response);
	};
	xhr.open("GET", url);
	xhr.responseType = "blob";
	xhr.send();
};

function SocialMediaImagePreview() {
	const { state: settings } = useContext(SocialSettingsContext);
	const [backgroundImage, setBackgroundImage] = useState(null);

	useEffect(() => {
		toDataUrl(settings.defaultBgImg, base64 => setBackgroundImage(base64));
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
							settings.layout === "CARD_RIGHT"
					},
					"imageseo-media__container",
					"imageseo-media__container--preview"
				)}
				style={{
					backgroundColor: settings.contentBackgroundColor
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
						backgroundRepeat: "no-repeat"
					}}
				/>

				<div className="imageseo-media__container__content">
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
							Sub title (like price)
						</div>
					)}
					{settings.visibilityRating && (
						<div className="imageseo-media__content__stars">
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
