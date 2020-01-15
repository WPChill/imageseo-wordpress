import React, { useState, useContext, useEffect } from "react";
import Swal from "sweetalert2";
import { get, isNull, isNil, find } from "lodash";
import { format } from "date-fns";

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
import { getImagesLeft, hasLimitExcedeed } from "../../services/user";
import getAttachement from "../../services/ajax/get-attachement";

import Loader from "../../ui/Loader";
import { Col, Row } from "../../ui/Flex";
import generateReport from "../../services/ajax/generate-report";
import SubTitle from "../../ui/Block/Subtitle";
import { deleteCurrentBulk } from "../../services/ajax/current-bulk";
import LimitExcedeed from "../../components/Bulk/LimitExcedeed";

function BulkWithProviders() {
	const { state, dispatch } = useContext(BulkProcessContext);
	const { state: userState, dispatch: dispatchUser } = useContext(
		UserContext
	);
	const { state: settings, dispatch: dispatchSettings } = useContext(
		BulkSettingsContext
	);
	const [openOptimization, setOpenOptimization] = useState(
		IMAGESEO_DATA.CURRENT_PROCESSED ? false : true
	);

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

		if (hasLimitExcedeed(userState.user_infos)) {
			Swal.fire({
				title: "Oups !",
				text:
					"You have no credit left. We've stopped the bulk process.",
				icon: "info",
				confirmButtonText: "Close"
			});
			dispatch({ type: "PAUSE_BULK" });
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
			dispatch({ type: "FINISH_BULK" });
			return;
		}

		fetchAttachment(attachmentId);
	}, [state.currentProcess, state.bulkPause]);

	// Query reports
	useEffect(() => {
		if (!state.bulkActive || state.bulkPause) {
			return;
		}

		if (hasLimitExcedeed(userState.user_infos)) {
			dispatch({ type: "PAUSE_BULK" });
			return;
		}

		const attachmentId = getAttachmentIdWithProcess(state);

		const fetchReport = async attachmentId => {
			const {
				success,
				data: { need_update_counter, report }
			} = await generateReport(attachmentId, settings.language);

			if (!success) {
				Swal.fire({
					title: "Oups !",
					text:
						"You have no credit left. We've stopped the bulk process.",
					icon: "info",
					confirmButtonText: "Close"
				});
				dispatch({ type: "PAUSE_BULK" });
				return;
			}

			if (need_update_counter) {
				if (userState.user_infos.bonus_stock_images > 0) {
					dispatchUser({
						type: "DECREASE_BONUS_STOCK_IMAGES"
					});
				} else {
					dispatchUser({
						type: "INCREASE_CURRENT_REQUEST_IMAGES"
					});
				}
			}

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

	// Start a new bulk
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

		if (get(state, "allIds", []).length === 0) {
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

		const launchBulk = async () => {
			if (IMAGESEO_DATA.CURRENT_PROCESSED) {
				await deleteCurrentBulk();
				IMAGESEO_DATA.CURRENT_PROCESSED = false;
			}
			dispatch({ type: "START_BULK" });
			dispatch({
				type: "NEW_PROCESS",
				payload: 0
			});
		};

		Swal.fire({
			title: "Are you sure?",
			text:
				"You're about to launch a bulk optimization. You can pause it and resume it at any time.",
			icon: "info",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			confirmButtonText: "Yes, let's go!"
		}).then(result => {
			if (result.value) {
				launchBulk();
			}
		});
	};

	const handleRestartBulk = e => {
		e.preventDefault();
		if (!IMAGESEO_DATA.CURRENT_PROCESSED) {
			return;
		}

		Swal.fire({
			title: "Are you sure?",
			text:
				"You're going to take over a bulk optimization that was in progress. ",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			confirmButtonText: "Yes, let's go!"
		}).then(result => {
			if (result.value) {
				dispatchSettings({
					type: "NEW_OPTIONS",
					payload: {
						...IMAGESEO_DATA.CURRENT_PROCESSED.settings,
						restartBulk: true
					}
				});
			}
		});
	};

	// Resume an old bulk
	useEffect(() => {
		if (!settings.restartBulk) {
			return;
		}

		const fetchRestartBulk = async () => {
			await deleteCurrentBulk();

			dispatch({
				type: "RESTART_BULK",
				payload: {
					...IMAGESEO_DATA.CURRENT_PROCESSED.state,
					bulkActive: true,
					attachments: {},
					reports: {},
					currentProcess:
						Number(
							IMAGESEO_DATA.CURRENT_PROCESSED.state.currentProcess
						) + 1
				}
			});
		};

		fetchRestartBulk();
	}, [settings.restartBulk]);

	useEffect(() => {
		if (!state.bulkFinish) {
			return;
		}
		setTimeout(() => {
			deleteCurrentBulk();
		}, 2000);
	}, [state.bulkFinish]);

	return (
		<>
			{IMAGESEO_DATA.CURRENT_PROCESSED &&
				get(IMAGESEO_DATA, "CURRENT_PROCESSED.state", false) &&
				get(IMAGESEO_DATA, "CURRENT_PROCESSED.count_optimized", 0) <
					get(IMAGESEO_DATA, "CURRENT_PROCESSED.state.allIds", [])
						.length &&
				!state.bulkActive && (
					<div className="imageseo-mb-4">
						<Block>
							<BlockContentInner>
								<Row align="center">
									<Col flex="1">
										<h2>
											Paused Bulk Optimization (
											{get(
												IMAGESEO_DATA,
												"CURRENT_PROCESSED.count_optimized",
												0
											)}
											/
											{
												get(
													IMAGESEO_DATA,
													"CURRENT_PROCESSED.state.allIds",
													[]
												).length
											}
											)
										</h2>
										<p>
											Paused :{" "}
											{get(
												IMAGESEO_DATA,
												"CURRENT_PROCESSED.last_updated",
												null
											) === null
												? format(
														new Date(),
														"dd MMMM yyyy - HH:mm"
												  )
												: format(
														new Date(
															get(
																IMAGESEO_DATA,
																"CURRENT_PROCESSED.last_updated",
																null
															)
														),
														"dd MMMM yyyy - HH:mm"
												  )}
										</p>
									</Col>
									<Col auto>
										<Button
											primary
											onClick={handleRestartBulk}
										>
											Resume this bulk
										</Button>
									</Col>
								</Row>
								<div className="imageseo-mt-3">
									<SubTitle>
										Configuration of this optimization
									</SubTitle>
									<ul>
										<li>
											<strong>Manual Validation :</strong>{" "}
											{IMAGESEO_DATA.CURRENT_PROCESSED
												.settings.wantValidateResult
												? "Yes"
												: "No"}
										</li>
										<li>
											<strong>Language :</strong>{" "}
											{
												find(IMAGESEO_DATA.LANGUAGES, {
													code:
														IMAGESEO_DATA
															.CURRENT_PROCESSED
															.settings.language
												}).name
											}
										</li>
										<li>
											<strong>Optimize alt :</strong>{" "}
											{IMAGESEO_DATA.CURRENT_PROCESSED
												.settings.optimizeAlt
												? "Yes"
												: "No"}{" "}
											{IMAGESEO_DATA.CURRENT_PROCESSED
												.settings.optimizeAlt && (
												<>
													(Format :{" "}
													{IMAGESEO_DATA
														.CURRENT_PROCESSED
														.settings.formatAlt ===
													"CUSTOM_FORMAT"
														? IMAGESEO_DATA
																.CURRENT_PROCESSED
																.settings
																.formatAltCustom
														: IMAGESEO_DATA
																.CURRENT_PROCESSED
																.settings
																.formatAlt}
													)
												</>
											)}
										</li>
										<li>
											<strong>Optimize filename :</strong>{" "}
											{IMAGESEO_DATA.CURRENT_PROCESSED
												.settings.optimizeFile
												? "Yes"
												: "No"}
										</li>
									</ul>
								</div>
							</BlockContentInner>
						</Block>
					</div>
				)}
			<Block>
				<BlockContentInner
					isHead
					withAction
					style={{
						alignItems: "center",
						borderBottom: openOptimization
							? "1px solid #C8D0DD"
							: "none"
					}}
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

				{openOptimization && (
					<BlockContentInner>
						{!state.bulkActive && !state.bulkFinish && (
							<BulkSettings
								handleQueryImages={handleQueryImages}
							/>
						)}
						{(state.bulkActive || state.bulkFinish) && (
							<BulkSummary />
						)}
					</BlockContentInner>
				)}
				{openOptimization && !state.bulkFinish && (
					<BlockFooter>
						<h3>
							Forecast: with your current settings{" "}
							{state.allIds.length} images will be optimized.
						</h3>
						<p>
							You have {userImagesLeft} credit(s) left in your
							account.{" "}
						</p>
						{get(state, "allIdsOptimized", []).length > 0 && (
							<p>
								{get(state, "allIdsOptimized", []).length}{" "}
								optimizations have already been done!
							</p>
						)}
						{!state.bulkActive && (
							<Button
								primary
								style={{ marginRight: 15 }}
								disabled={state.bulkActive}
								onClick={e => {
									handleStartBulk();
								}}
							>
								Start a new bulk optimization
							</Button>
						)}
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
					</BlockFooter>
				)}
			</Block>
			{(state.bulkActive || state.bulkFinish) && (
				<div className="imageseo-mt-4">
					<Block>
						<BlockContentInner
							isHead
							withAction
							style={{ alignItems: "center" }}
						>
							<Col span={10}>
								<h2>
									Bulk process (
									{Object.values(state.attachments).length +
										get(
											IMAGESEO_DATA,
											"CURRENT_PROCESSED.count_optimized",
											0
										)}
									/{state.allIds.length})
								</h2>
							</Col>
							<Col>
								<Loader percent={getPercentBulk(state)} />
							</Col>
							<Col span={7}>
								{!hasLimitExcedeed(userState.user_infos) && (
									<>
										{state.bulkActive && !state.bulkPause && (
											<Button
												simple
												onClick={e => {
													dispatch({
														type: "PAUSE_BULK"
													});
												}}
											>
												Pause bulk
											</Button>
										)}
										{state.bulkActive && state.bulkPause && (
											<Button
												simple
												onClick={e => {
													dispatch({
														type: "PLAY_BULK"
													});
												}}
											>
												Play
											</Button>
										)}
									</>
								)}
							</Col>
						</BlockContentInner>
						{hasLimitExcedeed(userState.user_infos) && (
							<BlockContentInner>
								<LimitExcedeed />
							</BlockContentInner>
						)}
						<BulkResults />
						{Object.values(state.reports).length > 8 &&
							hasLimitExcedeed(userState.user_infos) && (
								<BlockContentInner>
									<LimitExcedeed />
								</BlockContentInner>
							)}
					</Block>
				</div>
			)}
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
