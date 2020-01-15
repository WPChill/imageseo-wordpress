import React, { useState, useContext, useEffect } from "react";
import { toJpeg } from "html-to-image";

import { Row, Col } from "../../ui/Flex";
import SocialMediaImagePreview from "../../components/SocialMedia/ImagePreview";
import Block from "../../ui/Block";
import BlockContentInner, {
	BlockContentInnerTitle
} from "../../ui/Block/ContentInner";
import SocialSettingsContextProvider, {
	SocialSettingsContext
} from "../../contexts/SocialSettingsContext";
import IFlex from "../../ui/IFlex";

function SocialMediaWithProviders() {
	const { state: settings, dispatch } = useContext(SocialSettingsContext);
	return (
		<Row>
			<Col span={12}>
				<Block>
					<BlockContentInner isHead>
						<BlockContentInnerTitle>
							<h2>Social Media</h2>
						</BlockContentInnerTitle>
					</BlockContentInner>
					<BlockContentInner>
						<Row className="imageseo-mb-5">
							<Col span={12}>
								<label
									htmlFor="layout"
									className="imageseo-label"
								>
									Layout
								</label>
								<select
									id="layout"
									value={settings.layout}
									onChange={event =>
										dispatch({
											type: "UPDATE_OPTION",
											payload: {
												key: "layout",
												value: event.target.value
											}
										})
									}
								>
									<option value="CARD_LEFT">Card left</option>
									<option value="CARD_RIGHT">
										Card right
									</option>
								</select>
							</Col>
						</Row>
						<button
							onClick={() => {
								toJpeg(
									document.getElementById(
										"imageseo-preview-image"
									),
									{
										width: 1200,
										height: 630
									}
								).then(function(dataUrl) {
									jQuery("#image_base64").val(dataUrl);
								});
							}}
						>
							Generate
						</button>
					</BlockContentInner>
				</Block>
			</Col>
			<Col span={12}>
				<SocialMediaImagePreview />
			</Col>
		</Row>
	);
}

function SocialMedia() {
	return (
		<SocialSettingsContextProvider>
			<SocialMediaWithProviders />
		</SocialSettingsContextProvider>
	);
}

export default SocialMedia;
