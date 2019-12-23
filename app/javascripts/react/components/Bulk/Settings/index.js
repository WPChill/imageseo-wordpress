import React, { useContext } from "react";
import styled from "styled-components";

import SubTitle from "../../../ui/Block/Subtitle";
import { Row, Col } from "../../../ui/Flex";
import { BulkSettingsContext } from "../../../contexts/BulkSettingsContext";
import IFlex, { IFlexNumber } from "../../../ui/IFlex";

const SCContentSettings = styled.div`
	border-left: 1px solid #3139cc;
	padding-left: 15px;
`;

function BulkSettings() {
	const { settings, actions } = useContext(BulkSettingsContext);
	console.log("[settings]", settings);
	return (
		<>
			<SubTitle>Global settings</SubTitle>
			<SCContentSettings>
				<div className="imageseo-mb-3">
					<IFlex>
						<div className="imageseo-mr-2">
							<input
								type="checkbox"
								id="smallImages"
								name="smallImages"
								checked={settings.smallImages}
								onChange={() =>
									actions.setSmallImages(
										!settings.smallImages
									)
								}
							/>
						</div>
						<IFlexNumber number={1}>
							<label
								htmlFor="smallImages"
								className="imageseo-label"
							>
								Optimize small sized images
							</label>
							<p>
								Image under 48x48px are hard to resume and often
								doesn’t need alt tag (like icon)
							</p>
						</IFlexNumber>
					</IFlex>
				</div>
				<div className="imageseo-mb-3">
					<IFlex>
						<div className="imageseo-mr-2">
							<input
								type="checkbox"
								name="wantValidateResult"
								id="wantValidateResult"
								value={settings.wantValidateResult}
								onChange={() =>
									actions.setWantValidateResult(
										!settings.wantValidateResult
									)
								}
							/>
						</div>
						<IFlexNumber number={1}>
							<label
								htmlFor="wantValidateResult"
								className="imageseo-label"
							>
								I want to validate the results
							</label>
							<p>
								If you want to check the result and validate
								them before we overwrite the text
							</p>
						</IFlexNumber>
					</IFlex>
				</div>
				<Row className="imageseo-mb-5">
					<Col>
						<label htmlFor="language" className="imageseo-label">
							I want to validate the results
						</label>
						<select
							value={settings.language}
							onChange={event =>
								actions.setLanguage(event.target.value)
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
			</SCContentSettings>
			<SubTitle>Alt tag settings</SubTitle>
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
									actions.setOptimizeAlt(
										!settings.optimizeAlt
									)
								}
							/>
						</div>
						<IFlexNumber number={1}>
							<label
								htmlFor="optimizeAlt"
								className="imageseo-label"
							>
								Optimize alt tag
							</label>
						</IFlexNumber>
					</IFlex>
					{settings.optimizeAlt && (
						<>
							<Row className="imageseo-mt-3">
								<Col span={6}>
									<label
										htmlFor="altFilter"
										className="imageseo-label"
									>
										Images that will be optimized
									</label>
									<select id="altFilter">
										<option>All</option>
									</select>
								</Col>
								<Col span={6}>
									<label
										htmlFor="altFill"
										className="imageseo-label"
									>
										Alt tag that will be optimized
									</label>
									<select id="altFill">
										<option>All</option>
										<option>Only empty</option>
									</select>
								</Col>
							</Row>
							<div className="imageseo-mt-3">
								<div className="imageseo-label">Format</div>
								{IMAGESEO_DATA.ALT_FORMATS.map(
									(format, key) => {
										return (
											<label
												className="imageseo-radio imageseo-mt-2"
												key={`format_${key}`}
											>
												<input
													type="radio"
													name="formatAlt"
													value={format}
													checked={
														format ===
														settings.formatAlt
													}
													onChange={event => {
														console.log(event);
														actions.setAltFormat(
															event.target.value
														);
													}}
												/>
												<span className="imageseo-ml-1">
													{format}
												</span>
											</label>
										);
									}
								)}
							</div>
						</>
					)}
				</div>
			</SCContentSettings>
			<SubTitle>Rename images settings</SubTitle>
			<SCContentSettings>
				<div className="imageseo-mb-3">
					<IFlex>
						<div className="imageseo-mr-2">
							<input
								type="checkbox"
								name="optimizeFile"
								id="optimizeFile"
								value={settings.optimizeFile}
								onChange={() =>
									actions.setOptimizeFile(
										!settings.optimizeFile
									)
								}
							/>
						</div>
						<IFlexNumber number={1}>
							<label
								htmlFor="optimizeFile"
								className="imageseo-label"
							>
								Rename the files
							</label>
							<p>
								May be a bit longer (10s/img) but is better for
								SEO
							</p>
						</IFlexNumber>
					</IFlex>
				</div>
			</SCContentSettings>
		</>
	);
}

export default BulkSettings;
