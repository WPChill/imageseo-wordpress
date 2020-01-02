import React, { useContext, useEffect, useState } from "react";
import { get, isEmpty, isNil, memoize } from "lodash";

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
import { previewAlt, updateAlt } from "../../../../services/ajax/update-alt";
import {
	previewFilename,
	renameFilename
} from "../../../../services/ajax/rename-file";
import { BulkSettingsContext } from "../../../../contexts/BulkSettingsContext";
import Button from "../../../../ui/Button";
import getFilenameWithoutExtension from "../../../../helpers/getFilenameWithoutExtension";
import getFilenamePreview from "../../../../helpers/getFilenamePreview";

function BulkResultsItem({ attachment }) {
	const { state, dispatch } = useContext(BulkProcessContext);
	const { settings } = useContext(BulkSettingsContext);
	const [loading, setLoading] = useState(true);
	const [loadingHandleResult, setLoadingHandleResult] = useState(false);
	const [fileinfos, setFileInfos] = useState("");
	const [alt, setAlt] = useState("");
	const [altOptimizationIsValid, setAltOptimizationIsValid] = useState(false);
	const [
		filenameOptimizationIsValid,
		setFilenameOptimizationIsValid
	] = useState(false);

	useEffect(() => {
		if (isNil(state.reports[attachment.ID])) {
			return;
		}

		const fetchOptimization = async () => {
			if (settings.optimizeAlt && isEmpty(alt)) {
				const { success, data: newAlt } = await previewAlt(
					attachment.ID,
					settings.formatAlt
				);

				if (isEmpty(newAlt)) {
					setAlt(null);
				} else if (success) {
					setAlt(newAlt);
					dispatch({
						type: "ADD_ALT_PREVIEW",
						payload: {
							ID: attachment.ID,
							alt: newAlt
						}
					});
				}
			}

			if (settings.optimizeFile && isEmpty(fileinfos)) {
				const filenames = selectors.getAllFilenamesPreview(state);
				const { data: newFileinfos } = await previewFilename(
					attachment.ID,
					filenames
				);

				if (isEmpty(newFileinfos.filename)) {
					setFileInfos(null);
				} else {
					setFileInfos(newFileinfos);
					dispatch({
						type: "ADD_FILENAME_PREVIEW",
						payload: {
							ID: attachment.ID,
							file: newFileinfos
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
				<Col span={6}>
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
					<Col span={6}>
						<BlockTableLineItem>
							<strong>{alt}</strong>
							<br />
							<BlockTableLineOldValue>
								Currently alt tag : {attachment.alt}
							</BlockTableLineOldValue>
						</BlockTableLineItem>
					</Col>
				)}

				{settings.optimizeFile && (
					<Col span={6}>
						<BlockTableLineItem>
							<strong>{filename}</strong>
							<br />
							<BlockTableLineOldValue>
								Currently filename : {attachment.filename}
							</BlockTableLineOldValue>
						</BlockTableLineItem>
					</Col>
				)}
				<Col span={6}>
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
