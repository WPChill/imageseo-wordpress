import React, { useState, useContext } from "react";
import { isNull } from "lodash";
import styled from "styled-components";
import classNames from "classnames";

import { SocialSettingsContext } from "../../../contexts/SocialSettingsContext";

const SCContainer = styled.div`
	display: flex;
	height: 300px;
`;

const SCContainerImage = styled.div`
	flex: 2;
`;

const SCContainerContent = styled.div`
	flex: 1;
	padding: 20px;
`;

const SCTitle = styled.div`
	font-size: 30px;
	color: #000;
	line-height: 1.5;
`;

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
	toDataUrl(
		"http://imageseo.local/wp-content/uploads/2020/01/recrutement-developpeur-2-1024x768-1.jpg",
		base64 => setBackgroundImage(base64)
	);
	return (
		<>
			<SCContainer
				id="imageseo-preview-image"
				className={classNames({
					"imageseo-media__layout--card-left":
						settings.layout === "CARD_LEFT",
					"imageseo-media__layout--card-right":
						settings.layout === "CARD_RIGHT"
				})}
				style={{
					backgroundColor: settings.contentBackgroundColor
				}}
			>
				<SCContainerImage
					style={{
						backgroundColor: "#ccc",
						backgroundImage: isNull(backgroundImage)
							? null
							: `url(${backgroundImage}`,
						backgroundPosition: "center center",
						backgroundSize: "cover",
						backgroundRepeat: "no-repeat"
					}}
				/>

				<SCContainerContent>
					<SCTitle style={{ color: settings.textColor }}>
						Let's go bobby
					</SCTitle>
				</SCContainerContent>
			</SCContainer>
		</>
	);
}

export default SocialMediaImagePreview;
