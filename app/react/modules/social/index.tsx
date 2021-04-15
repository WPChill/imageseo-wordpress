import React, { useState, useContext, useEffect } from "react";
import { SketchPicker } from "react-color";
import styled from "styled-components";
import Swal from "sweetalert2";
import SocialMediaImagePreview from "../../components/SocialMedia/ImagePreview";

import SocialSettingsContextProvider, {
	SocialSettingsContext,
} from "../../contexts/SocialSettingsContext";
import { saveSocialMediaSettings } from "../../services/ajax/social-media-settings";

//@ts-ignore
const { __ } = wp.i18n;

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
	const [currentStarColor, setCurrentStarColor] = useState(
		settings.starColor
	);

	const [textColorPickerOpen, setTextColorPickerOpen] = useState(false);
	const [backgroundColorPickerOpen, setBackgroundColorPickerOpen] = useState(
		false
	);
	const [starColorPickerOpen, setStarColorPickerOpen] = useState(false);

	const handleOpenColorPicker = (type) => {
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

	const handleKeyDownColorPicker = (e) => {
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
			text: __("Your options have been saved", "imageseo"),
			icon: "success",
			confirmButtonText: "Close",
		});
	};

	return (
		<div className="flex">
			<div className="border rounded-md overflow-hidden w-5/12 mr-2">
				<div className="bg-white p-6">
					<h2 className="text-lg leading-6 font-medium text-blue-900">
						{__("Social Media Card Template", "imageseo")}
					</h2>
					<div className="">
						<div className=" mr-8">
							<p className="mt-2 mb-4 text-sm font-bold">
								{__("Data displayed", "imageseo")}
							</p>

							<div className="mb-4">
								<div className="flex">
									<input
										type="checkbox"
										className="focus:ring-indigo-500 h-4 w-4 text-white border-gray-300 rounded mr-4"
										name="visibilitySubTitle"
										id="visibilitySubTitle"
										checked={settings.visibilitySubTitle}
										value={settings.visibilitySubTitle}
										onChange={() =>
											dispatch({
												type: "UPDATE_OPTION",
												payload: {
													key: "visibilitySubTitle",
													value: !settings.visibilitySubTitle,
												},
											})
										}
									/>
									<div>
										<label
											htmlFor="visibilitySubTitle"
											className="text-sm font-bold"
										>
											{__(
												"Author or Product price (WooCommerce only) - Subtitle",
												"imageseo"
											)}
										</label>
										<p className="text-sm">
											{__(
												"Show the price product or author depending on the page",
												"imageseo"
											)}
										</p>
									</div>
								</div>
							</div>
							<div className="mb-4">
								<div className="flex">
									<input
										type="checkbox"
										className="focus:ring-indigo-500 h-4 w-4 text-white border-gray-300 rounded mr-4"
										name="visibilitySubTitleTwo"
										id="visibilitySubTitleTwo"
										checked={settings.visibilitySubTitleTwo}
										value={settings.visibilitySubTitleTwo}
										onChange={() =>
											dispatch({
												type: "UPDATE_OPTION",
												payload: {
													key:
														"visibilitySubTitleTwo",
													value: !settings.visibilitySubTitleTwo,
												},
											})
										}
									/>
									<div>
										<label
											htmlFor="visibilitySubTitleTwo"
											className="text-sm font-bold"
										>
											{__(
												"Reading time or Number of reviews (WooCommerce only) - Subtitle 2",
												"imageseo"
											)}
										</label>
										<p className="text-sm">
											{__(
												"Show the reading time of an article or the number of reviews.",
												"imageseo"
											)}
										</p>
									</div>
								</div>
							</div>
							<div className="mb-4">
								<div className="flex">
									<input
										type="checkbox"
										className="focus:ring-indigo-500 h-4 w-4 text-white border-gray-300 rounded mr-4"
										name="visibilityRating"
										id="visibilityRating"
										checked={settings.visibilityRating}
										value={settings.visibilityRating}
										onChange={() =>
											dispatch({
												type: "UPDATE_OPTION",
												payload: {
													key: "visibilityRating",
													value: !settings.visibilityRating,
												},
											})
										}
									/>
									<div>
										<label
											htmlFor="visibilityRating"
											className="text-sm font-bold"
										>
											{__("Stars rating", "imageseo")}
										</label>
										<p className="text-sm">
											{__(
												"Show the stars linked to a review of your product for example.",
												"imageseo"
											)}
											<strong>
												{__(
													"(Only use for WooCommerce Product)",
													"imageseo"
												)}
											</strong>
										</p>
									</div>
								</div>
							</div>
							<div className="mb-4">
								<div className="flex">
									<input
										type="checkbox"
										className="focus:ring-indigo-500 h-4 w-4 text-white border-gray-300 rounded mr-4"
										name="visibilityAvatar"
										id="visibilityAvatar"
										checked={settings.visibilityAvatar}
										value={settings.visibilityAvatar}
										onChange={() =>
											dispatch({
												type: "UPDATE_OPTION",
												payload: {
													key: "visibilityAvatar",
													value: !settings.visibilityAvatar,
												},
											})
										}
									/>
									<div>
										<label
											htmlFor="visibilityAvatar"
											className="text-sm font-bold"
										>
											{__(
												"Display the author avatar",
												"imageseo"
											)}
										</label>
										<p className="text-sm">
											{__(
												"Only use for post content",
												"imageseo"
											)}
										</p>
									</div>
								</div>
							</div>
						</div>
						<div className="">
							<p className="my-4 text-sm font-bold">
								{__("Look & Feel", "imageseo")}
							</p>

							<div className="flex mb-4">
								<div className="w-1/2 mr-4">
									<label
										htmlFor="layout"
										className="text-sm font-bold"
									>
										{__("Layout", "imageseo")}
									</label>
									<select
										id="layout"
										className="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-gray-900 focus:border-gray-900 text-sm"
										value={settings.layout}
										onChange={(event) =>
											dispatch({
												type: "UPDATE_OPTION",
												payload: {
													key: "layout",
													value: event.target.value,
												},
											})
										}
									>
										<option value="CARD_LEFT">
											Card left
										</option>
										<option value="CARD_RIGHT">
											Card right
										</option>
									</select>
								</div>
								<div className="w-1/2">
									<label
										htmlFor="textAlignment"
										className="text-sm font-bold"
									>
										{__("Text alignment", "imageseo")}
									</label>
									<select
										id="textAlignment"
										className="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-gray-900 focus:border-gray-900 text-sm"
										value={settings.textAlignment}
										onChange={(event) =>
											dispatch({
												type: "UPDATE_OPTION",
												payload: {
													key: "textAlignment",
													value: event.target.value,
												},
											})
										}
									>
										<option value="top">Top</option>
										<option value="center">Center</option>
										<option value="bottom">Bottom</option>
									</select>
								</div>
							</div>
							<div className="mb-4">
								<div className="flex items-center">
									<div className="mr-2 border p-1 rounded">
										<SCPicker
											style={{
												backgroundColor: currentTextColor,
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
												}}
											>
												<div
													style={{
														position: "fixed",
														top: "0px",
														right: "0px",
														bottom: "0px",
														left: "0px",
													}}
													onClick={
														handleCloseColorPicker
													}
												/>
												<SketchPicker
													disableAlpha={true}
													color={currentTextColor}
													onChange={(color) =>
														setCurrentTextColor(
															color.hex
														)
													}
													onChangeComplete={(
														color
													) => {
														dispatch({
															type:
																"UPDATE_OPTION",
															payload: {
																key:
																	"textColor",
																value:
																	color.hex,
															},
														});
													}}
												/>
											</div>
										)}
									</div>
									<label className="text-sm">
										{__("Text color", "imageseo")}
									</label>
								</div>
							</div>
							<div className="mb-4">
								<div className="flex items-center">
									<div className="mr-2 border p-1 rounded">
										<SCPicker
											style={{
												backgroundColor: currentBackgroundColor,
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
												}}
											>
												<div
													style={{
														position: "fixed",
														top: "0px",
														right: "0px",
														bottom: "0px",
														left: "0px",
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
													onChange={(color) =>
														setCurrentBackgroundColor(
															color.hex
														)
													}
													onChangeComplete={(
														color
													) => {
														dispatch({
															type:
																"UPDATE_OPTION",
															payload: {
																key:
																	"contentBackgroundColor",
																value:
																	color.hex,
															},
														});
													}}
												/>
											</div>
										)}
									</div>
									<label className="text-sm">
										{__("Background color", "imageseo")}
									</label>
								</div>
							</div>
							{settings.visibilityRating && (
								<div className="mb-4">
									<div className="flex items-center">
										<div className="mr-2 border p-1 rounded">
											<SCPicker
												style={{
													backgroundColor: currentStarColor,
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
													}}
												>
													<div
														style={{
															position: "fixed",
															top: "0px",
															right: "0px",
															bottom: "0px",
															left: "0px",
														}}
														onClick={
															handleCloseColorPicker
														}
													/>
													<SketchPicker
														disableAlpha={true}
														color={currentStarColor}
														onChange={(color) =>
															setCurrentStarColor(
																color.hex
															)
														}
														onChangeComplete={(
															color
														) => {
															dispatch({
																type:
																	"UPDATE_OPTION",
																payload: {
																	key:
																		"starColor",
																	value:
																		color.hex,
																},
															});
														}}
													/>
												</div>
											)}
										</div>
										<label className="text-sm">
											{__("Stars color", "imageseo")}
										</label>
									</div>
								</div>
							)}
						</div>
					</div>

					<p className="mt-2 mb-4 text-sm font-bold">
						{__("Images", "imageseo")}
					</p>

					<div className="mb-4 ">
						<div className="mr-4 ">
							<label
								htmlFor="logoUrl"
								className="text-sm mb-1 block"
							>
								{__("Your logo :", "imageseo")}
							</label>
							<input
								type="url"
								name="logoUrl"
								id="logoUrl"
								className="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
								value={settings.logoUrl}
								style={{ width: "100%" }}
								placeholder="Please, use an URL"
								onChange={(e) =>
									dispatch({
										type: "UPDATE_OPTION",
										payload: {
											key: "logoUrl",
											value: e.target.value,
										},
									})
								}
							/>
						</div>
						<div className="">
							<label
								htmlFor="defaultBgImg"
								className="text-sm block mb-1"
							>
								{__("Default background image :", "imageseo")}
							</label>

							<input
								type="url"
								className="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
								name="defaultBgImg"
								id="defaultBgImg"
								value={settings.defaultBgImg}
								style={{ width: "100%" }}
								placeholder="Please, use an URL"
								onChange={(e) =>
									dispatch({
										type: "UPDATE_OPTION",
										payload: {
											key: "defaultBgImg",
											value: e.target.value,
										},
									})
								}
							/>
							<p className="text-sm mt-2">
								{__(
									"This image will only be used if your page/article doesn’t have a featured image",
									"imageseo"
								)}
							</p>
						</div>
					</div>
					<button
						className="flex justify-center mx-auto py-2 px-16 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mt-6"
						onClick={handleSaveSocialMediaSettings}
					>
						{__("Save configuration", "imageseo")}
					</button>
				</div>
			</div>
			<div className="border rounded-md bg-white p-4 max-w-5xl mx-auto w-7/12">
				<h2 className="text-lg leading-6 font-medium text-blue-900 mb-4">
					{__("Preview", "imageseo")}
				</h2>
				<SocialMediaImagePreview />
			</div>
		</div>
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
