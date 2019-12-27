import React, { useContext } from "react";

import BlockTableHead, {
	BlockTableHeadItem
} from "../../../ui/Block/TableHead";
import { Row, Col } from "../../../ui/Flex";

import { BulkProcessContext } from "../../../contexts/BulkProcessContext";
import BulkResultsItem from "./Item";
import { BulkSettingsContext } from "../../../contexts/BulkSettingsContext";

function BulkResults() {
	const { state } = useContext(BulkProcessContext);
	const { settings } = useContext(BulkSettingsContext);
	return (
		<>
			<BlockTableHead>
				<Row>
					<Col span={6}>
						<BlockTableHeadItem>Preview</BlockTableHeadItem>
					</Col>
					{settings.optimizeAlt && (
						<Col span={6}>
							<BlockTableHeadItem>
								Alternative text
							</BlockTableHeadItem>
						</Col>
					)}

					{settings.optimizeFile && (
						<Col span={6}>
							<BlockTableHeadItem>Image name</BlockTableHeadItem>
						</Col>
					)}
					<Col span={6}>
						<BlockTableHeadItem>Status</BlockTableHeadItem>
					</Col>
				</Row>
			</BlockTableHead>
			{Object.values(state.attachments).map(attachment => {
				return (
					<BulkResultsItem
						key={`attachment_${attachment.ID}`}
						attachment={attachment}
					/>
				);
			})}
		</>
	);
}

export default BulkResults;
