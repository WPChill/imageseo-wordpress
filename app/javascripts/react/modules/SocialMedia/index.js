import React, { useState, useContext, useEffect } from "react";

import { SketchPicker } from "react-color";
import styled from "styled-components";
import Swal from "sweetalert2";
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
import BlockFooter from "../../ui/Block/Footer";
import Button from "../../ui/Button";
import { saveSocialMediaSettings } from "../../services/ajax/social-media-settings";

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

	const handleSaveSocialMediaSettings = async () => {
		await saveSocialMediaSettings(settings);
		Swal.fire({
			title: "Great!",
			text: "Your options have been saved",
			icon: "success",
			confirmButtonText: "Close"
		});
	};

	return (
		<Row>
			<Col span={11} gutter={16}>
				<Block>
					<BlockContentInner isHead>
						<BlockContentInnerTitle>
							<h2>Social Media Card Template</h2>
						</BlockContentInnerTitle>
					</BlockContentInner>
					<BlockContentInner>
						<SubTitle>Data displayed</SubTitle>
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
											Author or Product price (WooCommerce
											only) - Subtitle
										</label>
										<p>
											Show the price product or author
											depending on the page
										</p>
									</IFlexNumber>
								</IFlex>
							</div>
							<div className="imageseo-mb-3">
								<IFlex>
									<div className="imageseo-mr-2">
										<input
											type="checkbox"
											name="visibilitySubTitleTwo"
											id="visibilitySubTitleTwo"
											checked={
												settings.visibilitySubTitleTwo
											}
											value={
												settings.visibilitySubTitleTwo
											}
											onChange={() =>
												dispatch({
													type: "UPDATE_OPTION",
													payload: {
														key:
															"visibilitySubTitleTwo",
														value: !settings.visibilitySubTitleTwo
													}
												})
											}
										/>
									</div>
									<IFlexNumber number={1}>
										<label
											htmlFor="visibilitySubTitleTwo"
											className="imageseo-label"
										>
											Reading time or Number of reviews
											(WooCommerce only) - Subtitle 2
										</label>
										<p>
											Show the reading time of an article
											or the number of reviews.
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
											Stars rating
										</label>
										<p>
											Show the stars linked to a review of
											your product for example.{" "}
											<strong>
												(Only use for WooCommerce
												Product)
											</strong>
										</p>
									</IFlexNumber>
								</IFlex>
							</div>
							<div className="imageseo-mb-3">
								<IFlex>
									<div className="imageseo-mr-2">
										<input
											type="checkbox"
											name="visibilityAvatar"
											id="visibilityAvatar"
											checked={settings.visibilityAvatar}
											value={settings.visibilityAvatar}
											onChange={() =>
												dispatch({
													type: "UPDATE_OPTION",
													payload: {
														key: "visibilityAvatar",
														value: !settings.visibilityAvatar
													}
												})
											}
										/>
									</div>
									<IFlexNumber number={1}>
										<label
											htmlFor="visibilityAvatar"
											className="imageseo-label"
										>
											Display the author avatar
										</label>
										<p>Only use for post content</p>
									</IFlexNumber>
								</IFlex>
							</div>
						</SCContentSettings>
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
								<label
									htmlFor="textAlignment"
									className="imageseo-label"
								>
									Text alignment
								</label>
								<select
									id="textAlignment"
									value={settings.textAlignment}
									onChange={event =>
										dispatch({
											type: "UPDATE_OPTION",
											payload: {
												key: "textAlignment",
												value: event.target.value
											}
										})
									}
								>
									<option value="top">Top</option>
									<option value="center">Center</option>
									<option value="bottom">Bottom</option>
								</select>
							</div>
							<div className="imageseo-mb-3">
								<IFlex
									style={{
										alignItems: "center"
									}}
								>
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
									</IFlexNumber>
								</IFlex>
							</div>
							<div className="imageseo-mb-3">
								<IFlex
									style={{
										alignItems: "center"
									}}
								>
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
									</IFlexNumber>
								</IFlex>
							</div>
							{settings.visibilityRating && (
								<div className="imageseo-mb-3">
									<IFlex
										style={{
											alignItems: "center"
										}}
									>
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
																	value:
																		color.hex
																}
															});
														}}
													/>
												</div>
											)}
										</div>
										<IFlexNumber number={1}>
											<label className="imageseo-label">
												Stars color
											</label>
										</IFlexNumber>
									</IFlex>
								</div>
							)}
						</SCContentSettings>

						<SubTitle>Images</SubTitle>
						<SCContentSettings>
							<div className="imageseo-mb-3">
								<label
									htmlFor="logoUrl"
									className="imageseo-label"
								>
									Your logo :
								</label>
								<span>URL : </span>
								<input
									type="url"
									name="logoUrl"
									id="logoUrl"
									value={settings.logoUrl}
									style={{ width: "100%" }}
									placeholder="Please, use an URL"
									onChange={e =>
										dispatch({
											type: "UPDATE_OPTION",
											payload: {
												key: "logoUrl",
												value: e.target.value
											}
										})
									}
								/>
							</div>
							<div className="imageseo-mb-3">
								<label
									htmlFor="defaultBgImg"
									className="imageseo-label"
								>
									Default background image :
								</label>
								<p>
									This image will only be used if your
									page/article doesn’t have image
								</p>
								<span>URL : </span>
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
							</div>
						</SCContentSettings>
					</BlockContentInner>
					<BlockFooter>
						<Button
							primary
							style={{ fontSize: 20 }}
							onClick={handleSaveSocialMediaSettings}
						>
							Save configuration
						</Button>
					</BlockFooter>
				</Block>
			</Col>
			<Col span={13}>
				<Block
					style={{
						height: "auto",
						position: "sticky",
						top: "42px",
						background: "none"
					}}
				>
					<BlockContentInner
						isHead
						style={{
							backgroundColor: "#fff",
							borderRadius: "12px"
						}}
					>
						<BlockContentInnerTitle>
							<h2>Preview</h2>
						</BlockContentInnerTitle>
					</BlockContentInner>
					<BlockContentInner
						style={{ paddingLeft: 0, paddingRight: 0 }}
					>
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
