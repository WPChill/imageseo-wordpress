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
import SubTitle from "../../ui/Block/Subtitle";
import IFlex, { IFlexNumber } from "../../ui/IFlex";

const SCPicker = styled.div`
	width: 40px;
	height: 40px;
	border: 1px solid #dedede;
	padding: 1px;
	&:hover {
		cursor: pointer;
	}
`;

const SCContentSettings = styled.div`
	border-left: 1px solid #3139cc;
	padding-left: 15px;
`;

function SocialMediaWithProviders() {
	const { state: settings, dispatch } = useContext(SocialSettingsContext);
	const [currentTextColor, setCurrentTextColor] = useState(
		settings.textColor
	);
	const [currentBackgroundColor, setCurrentBackgroundColor] = useState(
		settings.contentBackgroundColor
	);
	const [currentStarColor, setCurrentStarColor] = useState(
		settings.starColor
	);

	const [textColorPickerOpen, setTextColorPickerOpen] = useState(false);
	const [backgroundColorPickerOpen, setBackgroundColorPickerOpen] = useState(
		false
	);
	const [starColorPickerOpen, setStarColorPickerOpen] = useState(false);

	const handleOpenColorPicker = type => {
		switch (type) {
			case "textColor":
				setTextColorPickerOpen(true);
				break;
			case "contentBackgroundColor":
				setBackgroundColorPickerOpen(true);
				break;
			case "starColor":
				setStarColorPickerOpen(true);
				break;
		}
		document.addEventListener("keydown", handleKeyDownColorPicker);
	};

	const handleKeyDownColorPicker = e => {
		if (e.charCode == 13 || e.keyCode == 13) {
			handleCloseColorPicker();
		}
	};

	const handleCloseColorPicker = () => {
		document.removeEventListener("keydown", handleKeyDownColorPicker);
		if (textColorPickerOpen) {
			setTextColorPickerOpen(false);
		}
		if (backgroundColorPickerOpen) {
			setBackgroundColorPickerOpen(false);
		}
		if (starColorPickerOpen) {
			setStarColorPickerOpen(false);
		}
	};

	return (
		<Row>
			<Col span={11} gutter={16}>
				<Block>
					<BlockContentInner isHead>
						<BlockContentInnerTitle>
							<h2>Social Media</h2>
						</BlockContentInnerTitle>
					</BlockContentInner>
					<BlockContentInner>
						<SubTitle>Look & Feel</SubTitle>
						<SCContentSettings>
							<div className="imageseo-mb-3">
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
							</div>
							<div className="imageseo-mb-3">
								<IFlex>
									<div className="imageseo-mr-2">
										<SCPicker
											style={{
												backgroundColor: currentTextColor
											}}
											onClick={() =>
												handleOpenColorPicker(
													"textColor"
												)
											}
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
													onClick={
														handleCloseColorPicker
													}
												/>
												<SketchPicker
													disableAlpha={true}
													color={currentTextColor}
													onChange={color =>
														setCurrentTextColor(
															color.hex
														)
													}
													onChangeComplete={color => {
														dispatch({
															type:
																"UPDATE_OPTION",
															payload: {
																key:
																	"textColor",
																value: color.hex
															}
														});
													}}
												/>
											</div>
										)}
									</div>
									<IFlexNumber number={1}>
										<label className="imageseo-label">
											Text color
										</label>
										<p>
											You can change the color of all the
											text on the card
										</p>
									</IFlexNumber>
								</IFlex>
							</div>
							<div className="imageseo-mb-3">
								<IFlex>
									<div className="imageseo-mr-2">
										<SCPicker
											style={{
												backgroundColor: currentBackgroundColor
											}}
											onClick={() =>
												handleOpenColorPicker(
													"contentBackgroundColor"
												)
											}
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
														handleCloseColorPicker
													}
												/>
												<SketchPicker
													disableAlpha={true}
													color={
														currentBackgroundColor
													}
													onChange={color =>
														setCurrentBackgroundColor(
															color.hex
														)
													}
													onChangeComplete={color => {
														dispatch({
															type:
																"UPDATE_OPTION",
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
									<IFlexNumber number={1}>
										<label className="imageseo-label">
											Background color
										</label>
										<p>
											You can change the background color
											of all the text on the card
										</p>
									</IFlexNumber>
								</IFlex>
							</div>
							<div className="imageseo-mb-3">
								<IFlex>
									<div className="imageseo-mr-2">
										<SCPicker
											style={{
												backgroundColor: currentStarColor
											}}
											onClick={() =>
												handleOpenColorPicker(
													"starColor"
												)
											}
										/>
										{starColorPickerOpen && (
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
														handleCloseColorPicker
													}
												/>
												<SketchPicker
													disableAlpha={true}
													color={currentStarColor}
													onChange={color =>
														setCurrentStarColor(
															color.hex
														)
													}
													onChangeComplete={color => {
														dispatch({
															type:
																"UPDATE_OPTION",
															payload: {
																key:
																	"starColor",
																value: color.hex
															}
														});
													}}
												/>
											</div>
										)}
									</div>
									<IFlexNumber number={1}>
										<label className="imageseo-label">
											Star color
										</label>
										<p>You can change the star color</p>
									</IFlexNumber>
								</IFlex>
							</div>
						</SCContentSettings>
						<SubTitle>Visibility</SubTitle>
						<SCContentSettings>
							<div className="imageseo-mb-3">
								<IFlex>
									<div className="imageseo-mr-2">
										<input
											type="checkbox"
											name="visibilitySubTitle"
											id="visibilitySubTitle"
											checked={
												settings.visibilitySubTitle
											}
											value={settings.visibilitySubTitle}
											onChange={() =>
												dispatch({
													type: "UPDATE_OPTION",
													payload: {
														key:
															"visibilitySubTitle",
														value: !settings.visibilitySubTitle
													}
												})
											}
										/>
									</div>
									<IFlexNumber number={1}>
										<label
											htmlFor="visibilitySubTitle"
											className="imageseo-label"
										>
											I want to get the subtitle
										</label>
										<p>
											If you tick this boxe, we will add
											the reading time of an article or
											its price depending on the page.
										</p>
									</IFlexNumber>
								</IFlex>
							</div>
							<div className="imageseo-mb-3">
								<IFlex>
									<div className="imageseo-mr-2">
										<input
											type="checkbox"
											name="visibilityRating"
											id="visibilityRating"
											checked={settings.visibilityRating}
											value={settings.visibilityRating}
											onChange={() =>
												dispatch({
													type: "UPDATE_OPTION",
													payload: {
														key: "visibilityRating",
														value: !settings.visibilityRating
													}
												})
											}
										/>
									</div>
									<IFlexNumber number={1}>
										<label
											htmlFor="visibilityRating"
											className="imageseo-label"
										>
											I want to get the star rating
										</label>
										<p>
											If you tick this boxe, we will add
											the stars linked to a review of your
											product for example.
										</p>
									</IFlexNumber>
								</IFlex>
							</div>
						</SCContentSettings>
						<SubTitle>Photos</SubTitle>
						<SCContentSettings>
							<label
								htmlFor="defaultBgImg"
								className="imageseo-label"
							>
								Default background image :
							</label>
							<input
								type="url"
								name="defaultBgImg"
								id="defaultBgImg"
								value={settings.defaultBgImg}
								style={{ width: "100%" }}
								placeholder="Please, use an URL"
								onChange={e =>
									dispatch({
										type: "UPDATE_OPTION",
										payload: {
											key: "defaultBgImg",
											value: e.target.value
										}
									})
								}
							/>
						</SCContentSettings>
					</BlockContentInner>
				</Block>
			</Col>
			<Col span={13}>
				<Block style={{ height: "auto" }}>
					<BlockContentInner isHead>
						<BlockContentInnerTitle>
							<h2>Preview</h2>
						</BlockContentInnerTitle>
					</BlockContentInner>
					<BlockContentInner>
						<SocialMediaImagePreview />
					</BlockContentInner>
				</Block>
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
