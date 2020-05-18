import React, { useState, useContext, useEffect } from "react";
import Swal from "sweetalert2";
import { get, isNil, find, difference, isEmpty } from "lodash";
import { format } from "date-fns";

import Block from "../../ui/Block";
import BlockContentInner, {
	BlockContentInnerTitle,
	BlockContentInnerAction,
} from "../../ui/Block/ContentInner";
import IconChevron from "../../ui/Icons/Chevron";

import BulkSettingsContextProvider, {
	BulkSettingsContext,
} from "../../contexts/BulkSettingsContext";
import BulkSettings from "../../components/Bulk/Settings";
import BlockFooter from "../../ui/Block/Footer";
import Button from "../../ui/Button";
import BulkResults from "../../components/Bulk/Results";
import BulkProcessContextProvider, {
	BulkProcessContext,
} from "../../contexts/BulkProcessContext";
import {
	canLaunchBulk,
	getAttachmentIdWithProcess,
	getPercentBulk,
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
import {
	deleteCurrentBulk,
	saveCurrentBulk,
	startBulkProcess,
	getCurrentProcessDispatch,
	stopCurrentProcess,
} from "../../services/ajax/current-bulk";
import LimitExcedeed from "../../components/Bulk/LimitExcedeed";
import useInterval from "../../hooks/useInterval";

const defaultCurrentProcess = {
	bulk_process: {
		current_index_image: -1,
		id_images: [],
	},
};

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
	const [loadingImages, setLoadingImages] = useState(false);
	const [currentProcess, setCurrentProcess] = useState({
		...defaultCurrentProcess,
		bulk_process: {
			id_images: get(IMAGESEO_DATA, "CURRENT_PROCESSED.id_images", []),
			current_index_image: get(
				IMAGESEO_DATA,
				"CURRENT_PROCESSED.current_index_image",
				-1
			),
		},
	});
	const [attachmentIdsView, setAttachmentIdsView] = useState([]);

	const userImagesLeft = getImagesLeft(userState.user_infos);
	let numberCreditsNeed =
		get(state, "allIds", []).length -
		get(state, "allIdsOptimized", []).length;
	if (numberCreditsNeed < 0) {
		numberCreditsNeed = 0;
	}
	console.log("State : ", state);
	// QUERY IMAGES
	useEffect(() => {
		handleQueryImages({
			filters: {
				alt_filter: settings.altFilter,
				alt_fill: settings.altFill,
			},
		});
	}, [settings.altFilter, settings.altFill]);

	let processInterval = null;

	useInterval(async () => {
		if (!state.bulkActive || state.bulkFinish || state.bulkPause) {
			return;
		}

		const { data } = await getCurrentProcessDispatch();
		console.log("[data]", data);
		if (!data.is_running) {
			dispatch({
				type: "FINISH_BULK",
				payload: null,
			});
			setCurrentProcess(data);
			return;
		}

		if (
			get(currentProcess, "bulk_process.current_index_image", null) !==
			get(data, "bulk_process.current_index_image", 0)
		) {
			setCurrentProcess(data);
		}
	}, 3000);

	console.log("[current]:", currentProcess);

	// Call an attachment
	useEffect(() => {
		if (!currentProcess.is_running || state.bulkFinish || state.bulkPause) {
			return;
		}

		if (hasLimitExcedeed(userState.user_infos)) {
			Swal.fire({
				title: "Oups !",
				text:
					"You have no credit left. We've stopped the bulk process.",
				icon: "info",
				confirmButtonText: "Close",
			});
			dispatch({
				type: "FINISH_BULK",
				payload: null,
			});
			return;
		}

		const idsAttachment = difference(
			currentProcess.bulk_process.id_images.slice(
				0,
				currentProcess.bulk_process.current_index_image + 1
			),
			attachmentIdsView
		);
		console.log("[idsAttachment]", idsAttachment);
		const fetchAttachment = async (idsAttachment) => {
			for (let index = 0; index < idsAttachment.length; index++) {
				const { data: attachment } = await getAttachement(
					idsAttachment[index]
				);
				if (get(attachment, "code", false) === "not_exist") {
					dispatch({
						type: "ATTACHMENT_NOT_FOUND",
						payload: idsAttachment[index],
					});
				} else {
					dispatch({
						type: "ADD_ATTACHMENT",
						payload: attachment,
					});
				}

				setAttachmentIdsView([
					...attachmentIdsView,
					idsAttachment[index],
				]);
			}
		};

		fetchAttachment(idsAttachment);
	}, [currentProcess]);

	// Query reports
	useInterval(async () => {
		if (
			(!currentProcess.is_running ||
				state.bulkFinish ||
				state.bulkPause) &&
			Object.keys(state.reports).length ===
				Object.keys(state.attachments).length
		) {
			return;
		}
		let idsAttachment = [];

		if (
			currentProcess.is_running &&
			!state.bulkFinish &&
			!state.bulkPause
		) {
			idsAttachment = difference(
				currentProcess.bulk_process.id_images.slice(
					0,
					currentProcess.bulk_process.current_index_image
				),
				Object.keys(state.reports)
			);
		} else if (!currentProcess.is_running || state.bulkFinish) {
			idsAttachment = difference(
				Object.keys(state.attachments),
				Object.keys(state.reports)
			);
		}

		console.log("[attachmentIdsOptimized]", idsAttachment);

		const fetchReport = async (idsAttachment) => {
			for (let index = 0; index < idsAttachment.length; index++) {
				console.log("Go report : ", idsAttachment[index]);
				dispatch({
					type: "ADD_REPORT",
					payload: {
						ID: idsAttachment[index],
						canGetReport: true,
					},
				});

				if (userState.user_infos.bonus_stock_images > 0) {
					dispatchUser({
						type: "DECREASE_BONUS_STOCK_IMAGES",
					});
				} else {
					dispatchUser({
						type: "INCREASE_CURRENT_REQUEST_IMAGES",
					});
				}
			}
		};

		if (!isEmpty(idsAttachment)) {
			fetchReport(idsAttachment);
		}
	}, 4000);

	const handleQueryImages = async (filters = {}) => {
		setLoadingImages(true);
		const { success, data } = await queryImages(filters);
		if (!success || !data) {
			return;
		}

		dispatch({ type: "UPDATE_ALL_IDS", payload: data.ids });
		dispatch({
			type: "UPDATE_ALL_IDS_OPTIMIZED",
			payload: data.ids_optimized,
		});
		setLoadingImages(false);
	};

	// Start a new bulk
	const handleStartBulk = async () => {
		if (!canLaunchBulk(settings)) {
			Swal.fire({
				title: "Oups !",
				text: "Please select at least one optimization",
				icon: "info",
				confirmButtonText: "Close",
			});
			return;
		}

		if (get(state, "allIds", []).length === 0) {
			Swal.fire({
				title: "Oups !",
				text: "There are no images to optimize",
				icon: "info",
				confirmButtonText: "Close",
			});
			return;
		}

		if (settings.optimizeAlt && isNil(settings.formatAlt)) {
			Swal.fire({
				title: "Oups !",
				text: "Please select the format of your alternative text (alt)",
				icon: "info",
				confirmButtonText: "Close",
			});
			return;
		}

		Swal.fire({
			title: "Are you sure?",
			text:
				"You're about to launch a bulk optimization. You can pause it and resume it at any time.",
			icon: "info",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			confirmButtonText: "Yes, let's go!",
		}).then(async (result) => {
			if (result.value) {
				console.log(settings);
				await startBulkProcess(state.allIds, settings);
				dispatch({
					type: "START_BULK",
					payload: null,
				});
			}
		});
	};

	const handleRestartBulk = (e) => {
		e.preventDefault();
		if (isNil(IMAGESEO_DATA.LAST_PROCESSED)) {
			return;
		}

		Swal.fire({
			title: "Are you sure?",
			text:
				"You're going to take over a bulk optimization that was in progress. ",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			confirmButtonText: "Yes, let's go!",
		}).then(async (result) => {
			if (result.value) {
				const ids = IMAGESEO_DATA.LAST_PROCESSED.id_images.slice(
					Number(IMAGESEO_DATA.LAST_PROCESSED.current_index_image) + 1
				);

				await startBulkProcess(
					ids,
					IMAGESEO_DATA.LAST_PROCESSED.settings
				);
				dispatch({
					type: "START_BULK",
					payload: null,
				});
				dispatchSettings({
					type: "NEW_OPTIONS",
					payload: {
						...IMAGESEO_DATA.LAST_PROCESSED.settings,
					},
				});
			}
		});
	};

	console.log("[render state] : ", state);

	return (
		<>
			{!isNil(IMAGESEO_DATA.LAST_PROCESSED) &&
				get(IMAGESEO_DATA, "LAST_PROCESSED.id_images", []).length > 0 &&
				isNil(IMAGESEO_DATA.CURRENT_PROCESSED) &&
				!state.bulkActive &&
				!state.bulkFinish && (
					<div className="imageseo-mb-4">
						<Block>
							<BlockContentInner>
								<Row align="center">
									<Col flex="1">
										<h2>
											Paused Bulk Optimization (
											{Number(
												IMAGESEO_DATA.LAST_PROCESSED
													.current_index_image
											) + 1}
											/
											{
												IMAGESEO_DATA.LAST_PROCESSED
													.id_images.length
											}
											)
										</h2>
										{/* <p>
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
										</p> */}
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
											{IMAGESEO_DATA.LAST_PROCESSED
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
															.LAST_PROCESSED
															.settings.language,
												}).name
											}
										</li>
										<li>
											<strong>Optimize alt :</strong>{" "}
											{IMAGESEO_DATA.LAST_PROCESSED
												.settings.optimizeAlt
												? "Yes"
												: "No"}{" "}
											{IMAGESEO_DATA.LAST_PROCESSED
												.settings.optimizeAlt && (
												<>
													(Format :{" "}
													{IMAGESEO_DATA
														.LAST_PROCESSED.settings
														.formatAlt ===
													"CUSTOM_FORMAT"
														? IMAGESEO_DATA
																.LAST_PROCESSED
																.settings
																.formatAltCustom
														: IMAGESEO_DATA
																.LAST_PROCESSED
																.settings
																.formatAlt}
													)
												</>
											)}
										</li>
										<li>
											<strong>Optimize filename :</strong>{" "}
											{IMAGESEO_DATA.LAST_PROCESSED
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
				{loadingImages && (
					<div
						style={{
							position: "absolute",
							top: 0,
							left: 0,
							width: "100%",
							height: "100%",
							backgroundColor: "rgba(0,0,0,0.2)",
							zIndex: 500,
							borderRadius: 12,
							display: "flex",
							justifyContent: "center",
							alignItems: "center",
						}}
					>
						<img
							src={`${IMAGESEO_URL_DIST}/images/rotate-cw.svg`}
							style={{
								width: 100,
								marginRight: 10,
								animation:
									"imageseo-rotation 1s infinite linear",
							}}
						/>
					</div>
				)}
				<BlockContentInner
					isHead
					withAction
					style={{
						alignItems: "center",
						borderBottom: openOptimization
							? "1px solid #C8D0DD"
							: "none",
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
								onClick={(e) => {
									handleStartBulk();
								}}
							>
								Start a new bulk optimization
							</Button>
						)}
						{numberCreditsNeed > userImagesLeft && (
							<Button
								simple
								onClick={(e) => {
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
									{Number(
										currentProcess.bulk_process
											.current_index_image
									) + 1}
									/
									{
										currentProcess.bulk_process.id_images
											.length
									}
									)
								</h2>
							</Col>
							<Col>
								<Loader
									percent={getPercentBulk(currentProcess)}
								/>
							</Col>
							<Col span={7}>
								{!hasLimitExcedeed(userState.user_infos) && (
									<>
										{state.bulkActive && !state.bulkPause && (
											<Button
												simple
												onClick={(e) => {
													stopCurrentProcess();
													dispatch({
														type: "FINISH_BULK",
													});
												}}
											>
												Stop bulk
											</Button>
										)}
										{/* {state.bulkActive && state.bulkPause && (
											<Button
												simple
												onClick={(e) => {
													dispatch({
														type: "PLAY_BULK",
													});
												}}
											>
												Play
											</Button>
										)} */}
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
						limit_images: IMAGESEO_DATA.LIMIT_IMAGES,
					}}
				>
					<BulkWithProviders />
				</UserContextProvider>
			</BulkProcessContextProvider>
		</BulkSettingsContextProvider>
	);
}

export default Bulk;
