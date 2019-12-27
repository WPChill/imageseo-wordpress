import React, { useState, useContext, useEffect } from "react";
import Swal from "sweetalert2";
import { get, isNull, isNil } from "lodash";

import Block from "../../ui/Block";
import BlockContentInner, {
	BlockContentInnerTitle,
	BlockContentInnerAction
} from "../../ui/Block/ContentInner";
import IconChevron from "../../ui/Icons/Chevron";

import BulkSettingsContextProvider, {
	BulkSettingsContext
} from "../../contexts/BulkSettingsContext";
import BulkSettings from "../../components/Bulk/Settings";
import BlockFooter from "../../ui/Block/Footer";
import Button from "../../ui/Button";
import BulkResults from "../../components/Bulk/Results";
import BulkProcessContextProvider, {
	BulkProcessContext
} from "../../contexts/BulkProcessContext";
import {
	canLaunchBulk,
	getAttachmentIdWithProcess,
	getPercentBulk
} from "../../services/bulk";
import BulkSummary from "../../components/Bulk/Summary";
import UserContextProvider, { UserContext } from "../../contexts/UserContext";
import queryImages from "../../services/ajax/query-images";
import { getImagesLeft } from "../../services/user";
import getAttachement from "../../services/ajax/get-attachement";

import Loader from "../../ui/Loader";
import { Col } from "../../ui/Flex";
import generateReport from "../../services/ajax/generate-report";

function BulkWithProviders() {
	const [openOptimization, setOpenOptimization] = useState(true);
	const { state, dispatch } = useContext(BulkProcessContext);
	const { state: userState } = useContext(UserContext);
	const { settings } = useContext(BulkSettingsContext);
	console.log("[state]", state);

	const userImagesLeft = getImagesLeft(userState.user_infos);
	let numberCreditsNeed =
		get(state, "allIds", []).length -
		get(state, "allIdsOptimized", []).length;
	if (numberCreditsNeed < 0) {
		numberCreditsNeed = 0;
	}

	// QUERY IMAGES
	useEffect(() => {
		handleQueryImages({
			filters: {
				alt_filter: settings.altFilter,
				alt_fill: settings.altFill
			}
		});
	}, [settings.altFilter, settings.altFill]);

	// Call an attachment
	useEffect(() => {
		if (
			!state.bulkActive ||
			isNull(state.currentProcess) ||
			state.bulkPause
		) {
			return;
		}

		const attachmentId = getAttachmentIdWithProcess(state);
		const fetchAttachment = async attachmentId => {
			const { data: attachment } = await getAttachement(attachmentId);

			dispatch({
				type: "ADD_ATTACHMENT",
				payload: attachment
			});
		};

		// No attachement
		if (isNull(attachmentId)) {
			dispatch({ type: "STOP_BULK" });
			return;
		}

		fetchAttachment(attachmentId);
	}, [state.currentProcess, state.bulkPause]);

	// Query reports
	useEffect(() => {
		if (!state.bulkActive || state.bulkPause) {
			return;
		}

		const attachmentId = getAttachmentIdWithProcess(state);

		const fetchReport = async attachmentId => {
			const { data: report } = await generateReport(attachmentId);

			dispatch({
				type: "ADD_REPORT",
				payload: report
			});
			dispatch({
				type: "NEW_PROCESS",
				payload: state.currentProcess + 1
			});
		};

		fetchReport(attachmentId);
	}, [state.attachments]);

	const handleQueryImages = async (filters = {}) => {
		const { success, data } = await queryImages(filters);
		if (!success || !data) {
			return;
		}

		dispatch({ type: "UPDATE_ALL_IDS", payload: data.ids });
		dispatch({
			type: "UPDATE_ALL_IDS_OPTIMIZED",
			payload: data.ids_optimized
		});
	};

	const handleStartBulk = async () => {
		if (!canLaunchBulk(settings)) {
			Swal.fire({
				title: "Oups !",
				text: "Please select at least one optimization",
				icon: "info",
				confirmButtonText: "Close"
			});
			return;
		}

		if (state.allIds.length === 0) {
			Swal.fire({
				title: "Oups !",
				text: "There are no images to optimize",
				icon: "info",
				confirmButtonText: "Close"
			});
			return;
		}

		if (settings.optimizeAlt && isNil(settings.formatAlt)) {
			Swal.fire({
				title: "Oups !",
				text: "Please select the format of your alternative text (alt)",
				icon: "info",
				confirmButtonText: "Close"
			});
			return;
		}

		dispatch({ type: "START_BULK" });
		dispatch({
			type: "NEW_PROCESS",
			payload: 0
		});
	};

	return (
		<>
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
					{!state.bulkActive && (
						<BulkSettings handleQueryImages={handleQueryImages} />
					)}
					{state.bulkActive && <BulkSummary />}
				</BlockContentInner>
				<BlockFooter>
					{!state.bulkActive && (
						<>
							<h3>{state.allIds.length} images to optimize</h3>
							<p>
								You have {userImagesLeft} credits left in your
								account and{" "}
								<strong>
									you are going to consume {numberCreditsNeed}{" "}
									credits.
								</strong>
							</p>
							{get(state, "allIdsOptimized", []).length > 0 && (
								<p>
									There are already 10 optimizations that have
									already been done
								</p>
							)}
							<Button
								primary
								style={{ marginRight: 15 }}
								disabled={state.bulkActive}
								onClick={e => {
									handleStartBulk();
								}}
							>
								Start New Bulk Optimization
							</Button>
							{numberCreditsNeed > userImagesLeft && (
								<Button
									simple
									onClick={e => {
										window.open(
											"https://app.imageseo.io/plan",
											"_blank"
										);
									}}
								>
									Get more credits
								</Button>
							)}
						</>
					)}
				</BlockFooter>
			</Block>
			<div className="imageseo-mt-4">
				<Block>
					<BlockContentInner
						isHead
						withAction
						style={{ alignItems: "center" }}
					>
						<Col span={10}>
							<h2>
								Bulk running (
								{Object.values(state.attachments).length}/
								{state.allIds.length})
							</h2>
						</Col>
						<Col>
							<Loader percent={getPercentBulk(state)} />
						</Col>
						<Col span={7}>
							{state.bulkActive && !state.bulkPause && (
								<Button
									simple
									onClick={e => {
										dispatch({ type: "PAUSE_BULK" });
									}}
								>
									Pause bulk
								</Button>
							)}
							{state.bulkActive && state.bulkPause && (
								<Button
									simple
									onClick={e => {
										dispatch({ type: "PLAY_BULK" });
									}}
								>
									Play
								</Button>
							)}
						</Col>
					</BlockContentInner>
					<BulkResults />
				</Block>
			</div>
		</>
	);
}

function Bulk() {
	return (
		<BulkSettingsContextProvider>
			<BulkProcessContextProvider>
				<UserContextProvider
					initialState={{
						default_language_ia:
							IMAGESEO_DATA.OPTIONS.default_language_ia,
						user_infos: IMAGESEO_DATA.USER_INFOS,
						limit_images: IMAGESEO_DATA.LIMIT_IMAGES
					}}
				>
					<BulkWithProviders />
				</UserContextProvider>
			</BulkProcessContextProvider>
		</BulkSettingsContextProvider>
	);
}

export default Bulk;
