import React, { useState, useContext, useEffect } from "react";
import { toJpeg } from "html-to-image";
import { SketchPicker } from "react-color";
import styled from "styled-components";

import { Row, Col } from "../../ui/Flex";
import SocialMediaImagePreview from "../../components/SocialMedia/ImagePreview";
import Block from "../../ui/Block";
import BlockContentInner, {
	BlockContentInnerTitle
} from "../../ui/Block/ContentInner";
import SocialSettingsContextProvider, {
	SocialSettingsContext
} from "../../contexts/SocialSettingsContext";

const SCPicker = styled.div`
	width: 40px;
	height: 40px;
	border: 1px solid #dedede;
	padding: 1px;
	&:hover {
		cursor: pointer;
	}
`;

function SocialMediaWithProviders() {
	const { state: settings, dispatch } = useContext(SocialSettingsContext);
	const [currentTextColor, setCurrentTextColor] = useState(
		settings.textColor
	);
	const [currentBackgroundColor, setCurrentBackgroundColor] = useState(
		settings.contentBackgroundColor
	);
	const [textColorPickerOpen, setTextColorPickerOpen] = useState(false);
	const [backgroundColorPickerOpen, setBackgroundColorPickerOpen] = useState(
		false
	);

	const handleClickTextColorPicker = () => {
		setTextColorPickerOpen(true);
		document.addEventListener("keydown", handleKeyDownTextColorPicker);
	};

	const handleCloseTextColorPicker = () => {
		document.removeEventListener("keydown", handleKeyDownTextColorPicker);
		setTextColorPickerOpen(false);
	};

	const handleKeyDownTextColorPicker = e => {
		if (e.charCode == 13 || e.keyCode == 13) {
			handleCloseTextColorPicker();
		}
	};

	const handleClickBackgroundColorPicker = () => {
		setBackgroundColorPickerOpen(true);
		document.addEventListener(
			"keydown",
			handleKeyDownBackgroundColorPicker
		);
	};

	const handleCloseBackgroundColorPicker = () => {
		document.removeEventListener(
			"keydown",
			handleKeyDownBackgroundColorPicker
		);
		setBackgroundColorPickerOpen(false);
	};

	const handleKeyDownBackgroundColorPicker = e => {
		if (e.charCode == 13 || e.keyCode == 13) {
			handleCloseBackgroundColorPicker();
		}
	};

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
						<div className="imageseo-mb-3">
							<label htmlFor="layout" className="imageseo-label">
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
								<option value="CARD_RIGHT">Card right</option>
							</select>
						</div>
						<div className="imageseo-mb-3">
							<label htmlFor="layout" className="imageseo-label">
								Text color
							</label>
							<SCPicker
								style={{
									backgroundColor: currentTextColor
								}}
								onClick={handleClickTextColorPicker}
							/>
							{textColorPickerOpen && (
								<div
									style={{
										position: "absolute",
										zIndex: "2"
									}}
								>
									<div
										style={{
											position: "fixed",
											top: "0px",
											right: "0px",
											bottom: "0px",
											left: "0px"
										}}
										onClick={handleCloseTextColorPicker}
									/>
									<SketchPicker
										disableAlpha={true}
										color={currentTextColor}
										onChange={color =>
											setCurrentTextColor(color.hex)
										}
										onChangeComplete={color => {
											dispatch({
												type: "UPDATE_OPTION",
												payload: {
													key: "textColor",
													value: color.hex
												}
											});
										}}
									/>
								</div>
							)}
						</div>
						<div className="imageseo-mb-3">
							<label htmlFor="layout" className="imageseo-label">
								Background color
							</label>
							<SCPicker
								style={{
									backgroundColor: currentBackgroundColor
								}}
								onClick={handleClickBackgroundColorPicker}
							/>
							{backgroundColorPickerOpen && (
								<div
									style={{
										position: "absolute",
										zIndex: "2"
									}}
								>
									<div
										style={{
											position: "fixed",
											top: "0px",
											right: "0px",
											bottom: "0px",
											left: "0px"
										}}
										onClick={
											handleCloseBackgroundColorPicker
										}
									/>
									<SketchPicker
										disableAlpha={true}
										color={currentTextColor}
										onChange={color =>
											setCurrentBackgroundColor(color.hex)
										}
										onChangeComplete={color => {
											dispatch({
												type: "UPDATE_OPTION",
												payload: {
													key:
														"contentBackgroundColor",
													value: color.hex
												}
											});
										}}
									/>
								</div>
							)}
						</div>
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
