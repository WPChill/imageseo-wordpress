import React, { useState } from "react";
import Block from "../../ui/Block";
import queryImages from "../../services/ajax/query-images";
import BlockContentInner, {
	BlockContentInnerTitle,
	BlockContentInnerAction
} from "../../ui/Block/ContentInner";
import IconChevron from "../../ui/Icons/Chevron";

import BulkSettingsContextProvider from "../../contexts/BulkSettingsContext";
import BulkSettings from "../../components/Bulk/Settings";
import BlockFooter from "../../ui/Block/Footer";
import Button from "../../ui/Button";
import BulkResults from "../../components/Bulk/Results";

function Bulk() {
	const [openOptimization, setOpenOptimization] = useState(true);

	return (
		<BulkSettingsContextProvider>
			<Block>
				<BlockContentInner
					isHead
					withAction
					style={{ alignItems: "center" }}
				>
					<BlockContentInnerTitle>
						<h2>Bulk optimization settings</h2>
					</BlockContentInnerTitle>
					<BlockContentInnerAction>
						<IconChevron
							up={openOptimization}
							down={!openOptimization}
							onClick={() =>
								setOpenOptimization(!openOptimization)
							}
						/>
					</BlockContentInnerAction>
				</BlockContentInner>
				<BlockContentInner>
					<BulkSettings />
				</BlockContentInner>
				<BlockFooter>
					<p>You will use 100 credit and you have only 7 left.</p>
					<Button primary style={{ marginRight: 15 }}>
						Start New Bulk Optimization
					</Button>
					<Button simple>Get more credits</Button>
				</BlockFooter>
			</Block>
			<div className="imageseo-mt-4">
				<Block>
					<BlockContentInner
						isHead
						withAction
						style={{ alignItems: "center" }}
					>
						<BlockContentInnerTitle>
							<h2>Bulk running</h2>
						</BlockContentInnerTitle>
						<BlockContentInnerAction>
							<Button simple>Pause bulk</Button>
						</BlockContentInnerAction>
					</BlockContentInner>
					<BulkResults />
				</Block>
			</div>
		</BulkSettingsContextProvider>
	);
}

export default Bulk;
