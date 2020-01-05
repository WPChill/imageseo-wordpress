import React, { useContext } from "react";
import { isNaN } from "lodash";
import Block from "../../../ui/Block";
import { BlockContentInner } from "../../../ui/Block/ContentInner";
import { Row, Col } from "../../../ui/Flex";
import Button from "../../../ui/Button";
import { BulkProcessContext } from "../../../contexts/BulkProcessContext";

function LimitExcedeed() {
	const { state } = useContext(BulkProcessContext);

	const percentOptimized = Math.round(
		(Object.values(state.reports).length * 100) / state.allIds.length
	);

	return (
		<Block secondary style={{ margin: 20 }}>
			<BlockContentInner>
				<Row align="center">
					<Col auto style={{ marginRight: 20 }}>
						<img src={`${IMAGESEO_URL_DIST}/images/alert.svg`} />
					</Col>
					<Col flex="1">
						<p style={{ margin: 0 }}>
							This bulk have been automatically paused. You need
							more credits to continue.
						</p>
						{!isNaN(percentOptimized) && (
							<p>
								{percentOptimized}% images have been optimized (
								{Object.values(state.reports).length} out of{" "}
								{state.allIds.length} images)
							</p>
						)}
					</Col>
					<Col auto>
						<Button
							primary
							onClick={e => {
								window.open(
									"https://app.imageseo.io/plan",
									"_blank"
								);
							}}
						>
							Get more credits
						</Button>
					</Col>
				</Row>
			</BlockContentInner>
		</Block>
	);
}

export default LimitExcedeed;
