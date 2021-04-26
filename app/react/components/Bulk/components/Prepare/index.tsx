import React, { useState, useContext, useEffect } from "react";
import Swal from "sweetalert2";
import { get, isNil } from "lodash";

//@ts-ignore
const { __ } = wp.i18n;

import { BulkSettingsContext } from "../../../../contexts/BulkSettingsContext";
import BulkSettings from "../Settings";
import { BulkProcessContext } from "../../../../contexts/BulkProcessContext";
import { UserContext } from "../../../../contexts/UserContext";
import queryImages from "../../../../services/ajax/query-images";
import { getImagesLeft } from "../../../../services/user";

import { startBulkProcess } from "../../../../services/ajax/current-bulk";
import { AlertSimple, IconsAlert } from "../../../Alerts/Simple";

const BulkPrepare = () => {
	const { state, dispatch } = useContext(BulkProcessContext);
	const { state: userState } = useContext(UserContext);
	const { state: settings } = useContext(BulkSettingsContext);

	const [loadingImages, setLoadingImages] = useState(false);

	const userImagesLeft = getImagesLeft(userState.user_infos);
	let numberCreditsNeed = get(state, "allIds", []).length;
	if (numberCreditsNeed < 0) {
		numberCreditsNeed = 0;
	}

	// QUERY IMAGES
	useEffect(() => {
		handleQueryImages({
			filters: {
				alt_filter: settings.altFilter,
				alt_fill: settings.altFill,
			},
		});
	}, [settings.altFilter, settings.altFill]);

	const handleQueryImages = async (filters = {}) => {
		setLoadingImages(true);
		const { success, data } = await queryImages(filters);
		if (!success || !data) {
			return;
		}

		dispatch({
			type: "UPDATE_QUERY_IMAGES",
			payload: {
				allIds: data.ids,
				allIdsOptimized: data.ids_optimized,
			},
		});

		setLoadingImages(false);
	};

	// Start a new bulk
	const handleStartBulk = async () => {
		if (get(state, "allIds", []).length === 0) {
			Swal.fire({
				title: __("Oups !", "imageseo"),
				text: __("There are no images to optimize", "imageseo"),
				icon: "info",
				confirmButtonText: __("Close", "imageseo"),
			});
			return;
		}

		if (settings.optimizeAlt && isNil(settings.formatAlt)) {
			Swal.fire({
				title: __("Oups !", "imageseo"),
				text: __(
					__(
						"Please select the format of your alternative text (alt)",
						"imageseo"
					),
					"imageseo"
				),
				icon: "info",
				confirmButtonText: __("Close", "imageseo"),
			});
			return;
		}

		Swal.fire({
			title: __("Are you sure?", "imageseo"),
			text: __(
				__(
					"You're about to launch a bulk optimization. You can pause it and resume it at any time.",
					"imageseo"
				),
				"imageseo"
			),
			icon: "info",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			confirmButtonText: __("Yes, let's go!", "imageseo"),
		}).then(async (result) => {
			if (result.value) {
				const { data } = await startBulkProcess(state.allIds, settings);
				dispatch({ type: "START_CURRENT_PROCESS", payload: data });
			}
		});
	};

	return (
		<>
			<div className="grid grid-cols-5 gap-4 mt-4">
				<div className="col-span-3">
					<BulkSettings />
					{!state.bulkActive && (
						<>
							<div className="my-4">
								<AlertSimple icon={IconsAlert.INFORMATION} blue>
									{__(
										"You will consume one credit for each image optimized.",
										"imageseo"
									)}
								</AlertSimple>
							</div>
							<div className="text-center">
								<button
									disabled={state.bulkActive}
									onClick={handleStartBulk}
									className="inline-block items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mt-4 mx-auto"
								>
									{__(
										"Start a new bulk optimization",
										"imageseo"
									)}
								</button>
							</div>
						</>
					)}
				</div>
				<div className="col-span-2">
					<div className="shadow rounded-md overflow-hidden">
						<div className="bg-white py-6 px-4 p-6">
							<h3 className="font-bold text-lg">
								{__(
									"Forecast: with your current settings",
									"imageseo"
								)}{" "}
								{state.allIds.length}{" "}
								{__("images will be optimized.", "imageseo")}
							</h3>
							{userImagesLeft < 10 && (
								<div className="my-2">
									<AlertSimple
										yellow
										icon={IconsAlert.EXCLAMATION}
									>
										<p className="text-sm">
											{__("You have", "imageseo")}{" "}
											<strong>{userImagesLeft} </strong>
											{__(
												"credit(s) left in your account.",
												"imageseo"
											)}{" "}
										</p>
									</AlertSimple>
								</div>
							)}
							{userImagesLeft >= 10 && (
								<p className="text-sm mt-2">
									{__("You have", "imageseo")}{" "}
									<strong>{userImagesLeft} </strong>
									{__(
										"credit(s) left in your account.",
										"imageseo"
									)}{" "}
								</p>
							)}

							{get(state, "allIdsOptimized", []).length > 0 && (
								<p className="text-sm mt-2">
									<strong>
										{
											get(state, "allIdsOptimized", [])
												.length
										}{" "}
									</strong>
									{__(
										"optimizations have already been done!",
										"imageseo"
									)}
								</p>
							)}

							{numberCreditsNeed > userImagesLeft && (
								<div className="text-center">
									<a
										className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mt-4"
										href="https://app.imageseo.io/plan"
										target="_blank"
									>
										{__("Get more credits", "imageseo")}
									</a>
								</div>
							)}
						</div>
					</div>
				</div>
			</div>
		</>
	);
};

export default BulkPrepare;
