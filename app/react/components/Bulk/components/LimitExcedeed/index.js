import React, { useContext } from "react";
import { isNaN, get } from "lodash";
import Block from "../../../ui/Block";
import { BlockContentInner } from "../../../ui/Block/ContentInner";
import { Row, Col } from "../../../ui/Flex";
import Button from "../../../ui/Button";
import { BulkProcessContext } from "../../../contexts/BulkProcessContext";

function LimitExcedeed({ percent }) {
	return (
		<Block secondary style={{ margin: 20 }}>
			<BlockContentInner>
				<Row align="center">
					<div style={{ marginRight: 20 }}>
						<img src={`${IMAGESEO_URL_DIST}/images/alert.svg`} />
					</div>
					<div style={{ marginRight: 20 }}>
						<p style={{ margin: 0 }}>
							The bulk have been paused. You need more credits to
							resume it.
						</p>
						{!isNaN(percent) && (
							<p>{percent}% images have been optimized.</p>
						)}
					</div>
					<div>
						<Button
							primary
							onClick={(e) => {
								window.open(
									"https://app.imageseo.io/plan",
									"_blank"
								);
							}}
						>
							Get more credits
						</Button>
					</div>
				</Row>
			</BlockContentInner>
		</Block>
	);
}

export default LimitExcedeed;
