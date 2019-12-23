import React from "react";
import BlockTableHead, {
	BlockTableHeadItem
} from "../../../ui/Block/TableHead";
import { Row, Col } from "../../../ui/Flex";
import BlockTableLine, {
	BlockTableLineItem
} from "../../../ui/Block/TableLine";

function BulkResults() {
	return (
		<>
			<BlockTableHead>
				<Row>
					<Col span={6}>
						<BlockTableHeadItem>Preview</BlockTableHeadItem>
					</Col>
					<Col span={6}>
						<BlockTableHeadItem>
							Alternative text
						</BlockTableHeadItem>
					</Col>
					<Col span={6}>
						<BlockTableHeadItem>Image name</BlockTableHeadItem>
					</Col>
					<Col span={6}>
						<BlockTableHeadItem>Status</BlockTableHeadItem>
					</Col>
				</Row>
			</BlockTableHead>
			<BlockTableLine>
				<Row align="center">
					<Col span={6}>
						<BlockTableLineItem>Img</BlockTableLineItem>
					</Col>
					<Col span={6}>
						<BlockTableLineItem>Alternative tag</BlockTableLineItem>
					</Col>
					<Col span={6}>
						<BlockTableLineItem>name</BlockTableLineItem>
					</Col>
					<Col span={6}>
						<BlockTableLineItem>Status</BlockTableLineItem>
					</Col>
				</Row>
			</BlockTableLine>
		</>
	);
}

export default BulkResults;
