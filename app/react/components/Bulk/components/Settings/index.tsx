import React, { useContext } from "react";

import { BulkSettingsContext } from "../../../../contexts/BulkSettingsContext";
import { Toggle } from "../../../Toggle";

//@ts-ignore
const { __ } = wp.i18n;

function BulkSettings() {
	const { state: settings, dispatch } = useContext(BulkSettingsContext);

	return (
		<>
			<div className="shadow rounded-md overflow-hidden mt-4">
				<div className="bg-white py-6 px-4 p-6">
					<div>
						<h2 className="text-lg leading-6 font-medium text-blue-900">
							{__("Global settings")}
						</h2>
					</div>

					<div className="mt-6 grid grid-cols-4 gap-6">
						<div className="col-span-2">
							<label
								htmlFor="language"
								className="block text-sm font-medium text-gray-700"
							>
								{__(
									"In which language should we write your filenames and alternative texts?",
									"imageseo"
								)}
							</label>
							<select
								id="language"
								className="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-gray-900 focus:border-gray-900 text-sm"
								value={settings.language}
								onChange={(event) =>
									dispatch({
										type: "UPDATE_OPTION",
										payload: {
											key: "language",
											value: event.target.value,
										},
									})
								}
							>
								{
									//@ts-ignore
									IMAGESEO_DATA.LANGUAGES.map((language) => {
										return (
											<option
												key={language.code}
												value={language.code}
											>
												{language.name}
											</option>
										);
									})
								}
							</select>
						</div>

						<div className="col-span-2">
							<label
								htmlFor="altFilter"
								className="block text-sm font-medium text-gray-700"
							>
								{__(
									"Which images do you want to optimize?",
									"imageseo"
								)}
							</label>
							<select
								id="altFilter"
								className="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-gray-900 focus:border-gray-900 text-sm"
								value={settings.altFilter}
								onChange={(event) =>
									dispatch({
										type: "UPDATE_OPTION",
										payload: {
											key: "altFilter",
											value: event.target.value,
										},
									})
								}
							>
								{
									//@ts-ignore
									IMAGESEO_DATA.ALT_SPECIFICATION.map(
										(item, key) => {
											return (
												<option
													key={`alt_filter_${key}`}
													value={item.id}
												>
													{item.label}
												</option>
											);
										}
									)
								}
							</select>
						</div>
					</div>
				</div>
			</div>

			<div className="shadow rounded-md overflow-hidden mt-4">
				<div className="bg-white py-6 px-4 p-6">
					<div className="flex">
						<h2 className="text-lg leading-6 font-medium text-blue-900">
							{__("ALT text settings", "imageseo")}
						</h2>
					</div>

					<div className="mt-6 max-w-4xl">
						<div>
							<label
								htmlFor="altFill"
								className="block text-sm font-medium text-gray-700"
							>
								{__(
									"Which alt texts do you want to optimize?",
									"imageseo"
								)}
							</label>

							<select
								id="altFill"
								className="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-gray-900 focus:border-gray-900 text-sm"
								value={settings.altFill}
								onChange={(event) =>
									dispatch({
										type: "UPDATE_OPTION",
										payload: {
											key: "altFill",
											value: event.target.value,
										},
									})
								}
							>
								{
									//@ts-ignore
									IMAGESEO_DATA.ALT_FILL_TYPE.map(
										(item, key) => {
											return (
												<option
													key={`alt_fill_${key}`}
													value={item.id}
												>
													{item.label}
												</option>
											);
										}
									)
								}
							</select>
						</div>
						<div className="mt-6">
							<p className="block text-sm font-medium text-blue-900 mb-2">
								{__("Format", "imageseo")}
							</p>

							{
								//@ts-ignore
								IMAGESEO_DATA.ALT_FORMATS.map((value, key) => {
									return (
										<div
											key={`format_${key}`}
											className="mb-4"
										>
											<label className="text-sm">
												<div className="flex items-center">
													<input
														type="radio"
														className="h-4 w-4 text-gray-200 border-gray-300 focus:ring-gray-900"
														name="formatAlt"
														value={value.format}
														checked={
															value.format ===
															settings.formatAlt
														}
														onChange={(event) => {
															dispatch({
																type:
																	"UPDATE_OPTION",
																payload: {
																	key:
																		"formatAlt",
																	value:
																		event
																			.target
																			.value,
																},
															});
														}}
													/>
													<span className="imageseo-ml-1">
														{value.format}
													</span>
												</div>
												{value.description && (
													<p className="mt-1 text-sm">
														{value.description}
													</p>
												)}
											</label>
										</div>
									);
								})
							}
							<label className="mt-2">
								<p className="text-sm mb-2">
									{__(
										"Custom template. You can use multiple shortcode or what you want. Only for advanced user",
										"imageseo"
									)}
								</p>

								<div className="flex items-center">
									<input
										type="radio"
										className="h-4 w-4 text-gray-200 border-gray-300 focus:ring-gray-900 mr-4"
										name="formatAlt"
										value="CUSTOM_FORMAT"
										onChange={(event) => {
											dispatch({
												type: "UPDATE_OPTION",
												payload: {
													key: "formatAlt",
													value: event.target.value,
												},
											});
										}}
									/>

									<input
										type="text"
										name="formatAltCustom"
										className="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-gray-900 focus:border-gray-900 sm:text-sm"
										value={settings.formatAltCustom}
										style={{
											minWidth: "40%",
										}}
										onChange={(event) => {
											dispatch({
												type: "UPDATE_OPTION",
												payload: {
													key: "formatAltCustom",
													value: event.target.value,
												},
											});
										}}
									/>
								</div>
							</label>
						</div>
					</div>
				</div>
			</div>
			{/*
			<h2>{__("Image name settings", "imageseo")}</h2>
			<div>
				<div className="imageseo-mb-3">
					<div>
						<div className="imageseo-mr-2">
							<input
								type="checkbox"
								name="optimizeFile"
								id="optimizeFile"
								value={settings.optimizeFile}
								onChange={() =>
									dispatch({
										type: "UPDATE_OPTION",
										payload: {
											key: "optimizeFile",
											value: !settings.optimizeFile,
										},
									})
								}
							/>
						</div>

						<div>
							<label
								htmlFor="optimizeFile"
								className="imageseo-label"
							>
								{__(
									"I want to optimize my image names for SEO",
									"imageseo"
								)}
							</label>
							<p>
								{__(
									"It might slow the process (+10sec by images) but it's really worth for SEO.",
									"imageseo"
								)}
							</p>
						</div>
					</div>
				</div>
			</div> */}
		</>
	);
}

export default BulkSettings;
