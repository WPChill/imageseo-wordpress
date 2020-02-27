import React, { useContext, useEffect, useState } from "react";
import { get, isEmpty, isNil } from "lodash";
import styled from "styled-components";

import { Row, Col } from "../../../../ui/Flex";
import BlockTableLine, {
	BlockTableLineItem,
	BlockTableLineImage,
	BlockTableLineOldValue
} from "../../../../ui/Block/TableLine";
import {
	BulkProcessContext,
	selectors
} from "../../../../contexts/BulkProcessContext";
import { updateAlt } from "../../../../services/ajax/update-alt";
import { renameFilename } from "../../../../services/ajax/rename-file";
import { BulkSettingsContext } from "../../../../contexts/BulkSettingsContext";
import Button from "../../../../ui/Button";
import getFilenameWithoutExtension from "../../../../helpers/getFilenameWithoutExtension";
import getFilenamePreview from "../../../../helpers/getFilenamePreview";
import { previewDataReport } from "../../../../services/ajax/preview-data-report";

const SCEdit = styled.div`
	margin-left: 10px;
	border: solid 1px #c8d0dd;
	padding: 4px;
	border-radius: 4px;
	display: flex;
	align-items: center;
	justify-content: center;
	&:hover {
		cursor: pointer;
	}
`;

function BulkResultsItem({ attachment }) {
	const { state, dispatch } = useContext(BulkProcessContext);
	const { state: settings } = useContext(BulkSettingsContext);
	const [loading, setLoading] = useState(true);
	const [loadingHandleResult, setLoadingHandleResult] = useState(false);
	const [fileinfos, setFileInfos] = useState("");
	const [editAlt, setEditAlt] = useState(false);
	const [editFilename, setEditFilename] = useState(false);
	const [alt, setAlt] = useState("");
	const [altOptimizationIsValid, setAltOptimizationIsValid] = useState(false);
	const [
		filenameOptimizationIsValid,
		setFilenameOptimizationIsValid
	] = useState(false);

	// Preview alt and filename
	useEffect(() => {
		if (isNil(state.reports[attachment.ID])) {
			return;
		}

		if (
			!isNil(state.altPreviews[attachment.ID]) ||
			!isNil(state.filePreviews[attachment.ID])
		) {
			return;
		}

		const fetchOptimization = async () => {
			const excludeFilenames = selectors.getAllFilenamesPreview(state);
			const template =
				settings.formatAlt === "CUSTOM_FORMAT"
					? settings.formatAltCustom
					: settings.formatAlt;

			const { success, data } = await previewDataReport(
				attachment.ID,
				template,
				excludeFilenames
			);

			if (settings.optimizeAlt && isEmpty(alt)) {
				if (isEmpty(data.alt)) {
					setAlt(null);
				} else if (success) {
					setAlt(data.alt);
					dispatch({
						type: "ADD_ALT_PREVIEW",
						payload: {
							ID: attachment.ID,
							alt: data.alt
						}
					});
				}
			}

			if (settings.optimizeFile && isEmpty(fileinfos)) {
				if (isEmpty(data.filename)) {
					setFileInfos(null);
				} else {
					setFileInfos({
						filename: data.filename,
						extension: data.extension
					});
					dispatch({
						type: "ADD_FILENAME_PREVIEW",
						payload: {
							ID: attachment.ID,
							file: {
								filename: data.filename,
								extension: data.extension
							}
						}
					});
				}
			}

			setLoading(false);
		};

		fetchOptimization();
	}, [state.reports]);

	// Optimize alt auto
	useEffect(() => {
		if (settings.wantValidateResult || altOptimizationIsValid) {
			return;
		}

		if (!settings.optimizeAlt || isEmpty(alt) || isNil(alt)) {
			return;
		}

		const fetchUpdateAlt = async () => {
			await updateAlt(attachment.ID, alt);
			setAltOptimizationIsValid(true);
		};

		fetchUpdateAlt();
	}, [alt]);

	// Optimize file auto
	useEffect(() => {
		if (settings.wantValidateResult || filenameOptimizationIsValid) {
			return;
		}

		if (!settings.optimizeFile || isEmpty(fileinfos) || isNil(fileinfos)) {
			return;
		}

		const fetchUpdateFile = async () => {
			await renameFilename(
				attachment.ID,
				getFilenameWithoutExtension(getFilenamePreview(fileinfos))
			);
			setFilenameOptimizationIsValid(true);
		};

		fetchUpdateFile();
	}, [fileinfos]);

	const handleValidateResult = async () => {
		if (filenameOptimizationIsValid && altOptimizationIsValid) {
			return;
		}

		if (settings.optimizeAlt && !isEmpty(alt)) {
			await updateAlt(attachment.ID, alt);
		}

		if (
			settings.optimizeFile &&
			!isNil(fileinfos) &&
			!isEmpty(fileinfos.filename)
		) {
			await renameFilename(
				attachment.ID,
				getFilenameWithoutExtension(getFilenamePreview(fileinfos))
			);
		}

		if (loadingHandleResult) {
			setLoadingHandleResult(false);
		}

		setFilenameOptimizationIsValid(true);
		setAltOptimizationIsValid(true);
	};

	let filename = getFilenamePreview(fileinfos);

	return (
		<BlockTableLine key={`attachment_${attachment.ID}`}>
			<Row align="center">
				<Col span={3}>
					<BlockTableLineItem>
						<BlockTableLineImage
							src={get(
								attachment,
								"thumbnail[0]",
								attachment.guid
							)}
						/>
					</BlockTableLineItem>
				</Col>
				{settings.optimizeAlt && (
					<Col span={8}>
						<BlockTableLineItem>
							<Row align="center">
								{editAlt && (
									<input
										type="text"
										value={alt}
										onChange={e => setAlt(e.target.value)}
										style={{
											flex: 1
										}}
									/>
								)}
								{!editAlt && (
									<Col auto>
										{!editAlt && <strong>{alt}</strong>}
									</Col>
								)}
								<Col auto>
									{!loading &&
										settings.wantValidateResult &&
										!editAlt && (
											<SCEdit
												onClick={() =>
													setEditAlt(!editAlt)
												}
											>
												<img
													src={`${IMAGESEO_URL_DIST}/images/edit.svg`}
												/>
											</SCEdit>
										)}
									{!loading &&
										settings.wantValidateResult &&
										editAlt && (
											<SCEdit
												onClick={() => {
													setEditAlt(!editAlt);
												}}
											>
												<img
													src={`${IMAGESEO_URL_DIST}/images/check.svg`}
												/>
											</SCEdit>
										)}
								</Col>
							</Row>
							<BlockTableLineOldValue>
								Current alt text: : {attachment.alt}
							</BlockTableLineOldValue>
						</BlockTableLineItem>
					</Col>
				)}

				{settings.optimizeFile && (
					<Col span={8}>
						<BlockTableLineItem>
							<Row align="center">
								{editFilename && (
									<input
										type="text"
										value={fileinfos.filename}
										onChange={e => {
											setFileInfos({
												...fileinfos,
												filename: e.target.value
											});
										}}
										style={{
											flex: 1
										}}
									/>
								)}
								{!editFilename && (
									<Col auto>
										{!editFilename && (
											<strong>{filename}</strong>
										)}
									</Col>
								)}
								<Col auto>
									{!loading &&
										settings.wantValidateResult &&
										!editFilename && (
											<SCEdit
												onClick={() =>
													setEditFilename(
														!editFilename
													)
												}
											>
												<img
													src={`${IMAGESEO_URL_DIST}/images/edit.svg`}
												/>
											</SCEdit>
										)}
									{!loading &&
										settings.wantValidateResult &&
										editFilename && (
											<SCEdit
												onClick={() => {
													setEditFilename(
														!editFilename
													);
													setFileInfos({
														...fileinfos,
														filename: slugify(
															fileinfos.filename,
															{
																replacement:
																	"-",
																remove: /[*+~.()'"!:@]/g
															}
														)
													});
												}}
											>
												<img
													src={`${IMAGESEO_URL_DIST}/images/check.svg`}
												/>
											</SCEdit>
										)}
								</Col>
							</Row>
							<BlockTableLineOldValue>
								Current filename: {attachment.filename}
							</BlockTableLineOldValue>
						</BlockTableLineItem>
					</Col>
				)}
				<Col span={5}>
					<BlockTableLineItem>
						{!loading &&
							(!isEmpty(filename) || !isEmpty(alt)) &&
							!settings.wantValidateResult && (
								<img
									src={`${IMAGESEO_URL_DIST}/images/check.svg`}
								/>
							)}

						{loading && (
							<img
								src={`${IMAGESEO_URL_DIST}/images/rotate-cw.svg`}
								style={{
									animation:
										"imageseo-rotation 1s infinite linear"
								}}
							/>
						)}

						{!loading &&
							(altOptimizationIsValid ||
								filenameOptimizationIsValid) &&
							(!isEmpty(filename) || !isEmpty(alt)) &&
							settings.wantValidateResult &&
							filenameOptimizationIsValid &&
							altOptimizationIsValid && (
								<img
									src={`${IMAGESEO_URL_DIST}/images/check.svg`}
								/>
							)}

						{settings.wantValidateResult &&
							(!filenameOptimizationIsValid ||
								!altOptimizationIsValid) &&
							(!isEmpty(filename) || !isEmpty(alt)) &&
							!loading && (
								<Button
									simple
									style={{
										marginLeft: 20,
										display: "flex",
										alignItems: "center",
										padding: "7px 15px"
									}}
									onClick={() => {
										setLoadingHandleResult(true);
										handleValidateResult();
									}}
									disabled={loadingHandleResult}
								>
									{loadingHandleResult && (
										<img
											src={`${IMAGESEO_URL_DIST}/images/rotate-cw.svg`}
											style={{
												marginRight: 10,
												animation:
													"imageseo-rotation 1s infinite linear"
											}}
										/>
									)}
									Validate this result
								</Button>
							)}
					</BlockTableLineItem>
				</Col>
			</Row>
		</BlockTableLine>
	);
}

export default BulkResultsItem;
