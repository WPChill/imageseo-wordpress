import React, { useContext } from "react";
import styled from "styled-components";
import { countBy, get } from "lodash";

import SubTitle from "../../../ui/Block/Subtitle";
import { Row, Col } from "../../../ui/Flex";
import { BulkSettingsContext } from "../../../contexts/BulkSettingsContext";
import IFlex, { IFlexNumber } from "../../../ui/IFlex";

const SCContentSettings = styled.div`
	border-left: 1px solid #3139cc;
	padding-left: 15px;
`;

function BulkSettings() {
	const { state: settings, dispatch } = useContext(BulkSettingsContext);
	const blockOptimizeFile = countBy(
		IMAGESEO_DATA.INFOS,
		item => item === true
	);
	console.log(blockOptimizeFile);
	return (
		<>
			<SubTitle>Global settings</SubTitle>
			<SCContentSettings>
				<div className="imageseo-mb-3">
					<IFlex>
						<div className="imageseo-mr-2">
							<input
								type="checkbox"
								name="wantValidateResult"
								id="wantValidateResult"
								value={settings.wantValidateResult}
								onChange={() =>
									dispatch({
										type: "UPDATE_OPTION",
										payload: {
											key: "wantValidateResult",
											value: !settings.wantValidateResult
										}
									})
								}
							/>
						</div>
						<IFlexNumber number={1}>
							<label
								htmlFor="wantValidateResult"
								className="imageseo-label"
							>
								I want to validate the results manually.
							</label>
							<p>
								If you tick this boxe, you will have the
								possibility to valide our alt texts and names
								suggestion. You can stop the bulk, untick this
								boxe and restart the bulk at any moment.
							</p>
						</IFlexNumber>
					</IFlex>
				</div>
				<Row className="imageseo-mb-3">
					<Col>
						<label htmlFor="language" className="imageseo-label">
							In which language should we write your filenames and
							alternative texts?
						</label>
						<select
							value={settings.language}
							onChange={event =>
								dispatch({
									type: "UPDATE_OPTION",
									payload: {
										key: "language",
										value: event.target.value
									}
								})
							}
						>
							{IMAGESEO_DATA.LANGUAGES.map(language => {
								return (
									<option
										key={language.code}
										value={language.code}
									>
										{language.name}
									</option>
								);
							})}
						</select>
					</Col>
				</Row>
				<Row className="imageseo-mb-5">
					<Col span={8}>
						<label htmlFor="altFilter" className="imageseo-label">
							Which images do you want to optimize?
						</label>
						<select
							id="altFilter"
							value={settings.altFilter}
							onChange={event =>
								dispatch({
									type: "UPDATE_OPTION",
									payload: {
										key: "altFilter",
										value: event.target.value
									}
								})
							}
						>
							{IMAGESEO_DATA.ALT_SPECIFICATION.map(
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
							)}
						</select>
					</Col>
				</Row>
			</SCContentSettings>
			<SubTitle>ALT text settings</SubTitle>
			<SCContentSettings>
				<div className="imageseo-mb-3">
					<IFlex>
						<div className="imageseo-mr-2">
							<input
								type="checkbox"
								name="optimizeAlt"
								id="optimizeAlt"
								value={settings.optimizeAlt}
								onChange={() =>
									dispatch({
										type: "UPDATE_OPTION",
										payload: {
											key: "optimizeAlt",
											value: !settings.optimizeAlt
										}
									})
								}
							/>
						</div>
						<IFlexNumber number={1}>
							<label
								htmlFor="optimizeAlt"
								className="imageseo-label"
							>
								I want to fill out and optimize my ALT Texts for
								SEO and Accessibility.
							</label>
						</IFlexNumber>
					</IFlex>
					{settings.optimizeAlt && (
						<>
							<Row className="imageseo-mt-3">
								<Col span={8}>
									<label
										htmlFor="altFill"
										className="imageseo-label"
									>
										Which alt texts do you want to optimize?
									</label>
									<select
										id="altFill"
										value={settings.altFill}
										onChange={event =>
											dispatch({
												type: "UPDATE_OPTION",
												payload: {
													key: "altFill",
													value: event.target.value
												}
											})
										}
									>
										{IMAGESEO_DATA.ALT_FILL_TYPE.map(
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
										)}
									</select>
								</Col>
							</Row>
							<div className="imageseo-mt-3">
								<div className="imageseo-label">Format</div>
								{IMAGESEO_DATA.ALT_FORMATS.map((value, key) => {
									return (
										<label
											className="imageseo-radio imageseo-mt-2"
											key={`format_${key}`}
										>
											<input
												type="radio"
												name="formatAlt"
												value={value.format}
												checked={
													value.format ===
													settings.formatAlt
												}
												onChange={event => {
													dispatch({
														type: "UPDATE_OPTION",
														payload: {
															key: "formatAlt",
															value:
																event.target
																	.value
														}
													});
												}}
											/>
											<span className="imageseo-ml-1">
												{value.format}
											</span>
											{value.description && (
												<p
													style={{
														marginTop: 10,
														fontSize: 14
													}}
												>
													{value.description}
												</p>
											)}
										</label>
									);
								})}
								<label className="imageseo-radio imageseo-mt-2">
									<p
										style={{
											marginTop: 15,
											fontSize: 14
										}}
									>
										Custom template. You can use multiple
										shortcode or what you want. Only for
										advanced user
									</p>

									<input
										type="radio"
										name="formatAlt"
										value="CUSTOM_FORMAT"
										onChange={event => {
											dispatch({
												type: "UPDATE_OPTION",
												payload: {
													key: "formatAlt",
													value: event.target.value
												}
											});
										}}
									/>

									<span className="imageseo-ml-1">
										<input
											type="text"
											name="formatAltCustom"
											value={settings.formatAltCustom}
											style={{
												minWidth: "40%"
											}}
											onChange={event => {
												dispatch({
													type: "UPDATE_OPTION",
													payload: {
														key: "formatAltCustom",
														value:
															event.target.value
													}
												});
											}}
										/>
									</span>
								</label>
							</div>
						</>
					)}
				</div>
			</SCContentSettings>
			{/* <SubTitle>Image name settings</SubTitle>
			<SCContentSettings>
				<div className="imageseo-mb-3">
					{get(blockOptimizeFile, "true", 0) > 0 && (
						<div
							style={{
								borderRadius: 4,
								backgroundColor: "#e64c2e",
								padding: 15,
								marginBottom: 10
							}}
						>
							{IMAGESEO_DATA.INFOS.IS_NGINX &&
							get(blockOptimizeFile, "true", 0) === 1 ? (
								<>
									<p
										style={{
											marginTop: 0,
											marginBottom: 0,
											color: "#fff"
										}}
									>
										Warning: your server is hosted on a
										Nginx. If you rename the files, we won't
										be able to redirect 301 of your images.
										In any case, we fill in the redirection
										rules in your .htaccess file.
									</p>
								</>
							) : (
								<>
									<p
										style={{
											marginTop: 0,
											marginBottom: 0,
											color: "#fff"
										}}
									>
										We're sorry but for security reasons, we
										prefer not to allow you to enable the
										option to rename files.
									</p>
									<p
										style={{
											color: "#fff",
											marginBottom: 0
										}}
									>
										It looks like you are using a page
										builder and they work differently than
										WordPress. If we rename the files, we
										may break your images on your site.
									</p>
									<p
										style={{
											marginBottom: 0,
											color: "#fff"
										}}
									>
										If you still want to take the risk, the
										support team can help you:
										support@imageseo.io.
									</p>
								</>
							)}
						</div>
					)}
					<IFlex>
						<div className="imageseo-mr-2">
							<input
								type="checkbox"
								name="optimizeFile"
								id="optimizeFile"
								disabled={
									get(blockOptimizeFile, "true", 0) === 1 &&
									get(blockOptimizeFile, "true", 0) > 0
								}
								value={settings.optimizeFile}
								onChange={() =>
									dispatch({
										type: "UPDATE_OPTION",
										payload: {
											key: "optimizeFile",
											value: !settings.optimizeFile
										}
									})
								}
							/>
						</div>

						<IFlexNumber number={1}>
							<label
								htmlFor="optimizeFile"
								className="imageseo-label"
							>
								I want to optimize my image names for SEO
							</label>
							<p>
								It might slow the process (+10sec by images) but
								it's really worth for SEO.
							</p>
						</IFlexNumber>
					</IFlex></div>
			</SCContentSettings>
				 */}
		</>
	);
}

export default BulkSettings;
