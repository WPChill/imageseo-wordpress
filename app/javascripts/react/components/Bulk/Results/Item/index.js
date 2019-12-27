import React, { useContext, useEffect, useState } from "react";
import { get, isEmpty, isNil } from "lodash";

import { Row, Col } from "../../../../ui/Flex";
import BlockTableLine, {
	BlockTableLineItem,
	BlockTableLineImage,
	BlockTableLineOldValue
} from "../../../../ui/Block/TableLine";
import { BulkProcessContext } from "../../../../contexts/BulkProcessContext";
import { previewAlt, updateAlt } from "../../../../services/ajax/update-alt";
import {
	previewFilename,
	renameFilename
} from "../../../../services/ajax/rename-file";
import { BulkSettingsContext } from "../../../../contexts/BulkSettingsContext";
import Button from "../../../../ui/Button";

function BulkResultsItem({ attachment }) {
	const { state } = useContext(BulkProcessContext);
	const { settings } = useContext(BulkSettingsContext);
	const [loading, setLoading] = useState(true);
	const [loadingHandleResult, setLoadingHandleResult] = useState(false);
	const [fileinfos, setFileInfos] = useState("");
	const [alt, setAlt] = useState("");
	const [resultIsValid, setResultIsValid] = useState(
		!settings.wantValidateResult
	);

	console.log("[resultIsValid]", resultIsValid);
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
					return;
				}
				if (success) {
					setAlt(newAlt);
				}
			}

			if (settings.optimizeFile && isEmpty(fileinfos)) {
				const { data: newFileinfos } = await previewFilename(
					attachment.ID
				);

				if (isEmpty(newFileinfos.filename)) {
					setFileInfos(null);
					return;
				}
				setFileInfos(newFileinfos);
			}

			if (!settings.wantValidateResult) {
				handleValidateResult();
			}

			setLoading(false);
		};

		fetchOptimization();
	}, [state.reports]);

	const handleValidateResult = async () => {
		if (resultIsValid || isEmpty(alt) || isEmpty(fileinfos)) {
			return;
		}

		if (settings.optimizeAlt) {
			await updateAlt(attachment.ID, alt);
		}

		if (settings.optimizeFile) {
			await renameFilename(attachment.ID, fileinfos.filename);
		}

		if (loadingHandleResult) {
			setLoadingHandleResult(false);
		}

		setResultIsValid(true);
	};

	let filename = "";
	if (!isEmpty(fileinfos)) {
		filename = fileinfos.filename;
		if (filename.indexOf(".") < 0) {
			filename = `${fileinfos.filename}.${fileinfos.extension}`;
		}
	}

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
							(!isEmpty(filename) || !isEmpty(alt)) &&
							settings.wantValidateResult &&
							resultIsValid && (
								<img
									src={`${IMAGESEO_URL_DIST}/images/check.svg`}
								/>
							)}

						{settings.wantValidateResult &&
							!resultIsValid &&
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
