import React from "react";
import { Row, Col } from "../../ui/Flex";
import Block from "../../ui/Block";

function Bulk() {
	return (
		<Row>
			<Col span={8}>
				<Block>
					<Block.Title>Overview</Block.Title>
				</Block>
			</Col>
			<Col span={3}>
				<Block>
					<Block.Title>Account</Block.Title>
				</Block>
			</Col>
		</Row>
	);
}

export default Bulk;
