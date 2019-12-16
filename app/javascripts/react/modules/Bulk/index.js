import React from "react";
import { Row, Col } from "../../ui/Flex";
import Block from "../../ui/Block";
import queryImages from "../../services/ajax/query-images";
import Filters from "../../components/Filters";
import FiltersContextProvider from "../../contexts/FiltersContext";

function Bulk() {
	// queryImages({
	// 	filters: []
	// });

	return (
		<Row>
			<Col span={8}>
				<Block>
					<Block.Title>Overview</Block.Title>
					<Block.ContentInner>
						<FiltersContextProvider>
							<Filters />
						</FiltersContextProvider>
					</Block.ContentInner>
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
