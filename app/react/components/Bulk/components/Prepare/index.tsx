import React, { useState, useContext, useEffect } from "react";
import Swal from "sweetalert2";
import { get, isNil } from "lodash";

//@ts-ignore
const { __ } = wp.i18n;

import { BulkSettingsContext } from "../../../../contexts/BulkSettingsContext";
import BulkSettings from "../Settings";
import { BulkProcessContext } from "../../../../contexts/BulkProcessContext";
import { canLaunchBulk } from "../../../../services/bulk";
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
		if (!canLaunchBulk(settings)) {
			Swal.fire({
				title: "Oups !",
				text: __("Please select at least one optimization", "imageseo"),
				icon: "info",
				confirmButtonText: "Close",
			});
			return;
		}

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
			<AlertSimple icon={IconsAlert.INFORMATION} blue>
				<h3 className="text-lg leading-6 font-medium text-gray-900">
					{__("Configuration", "imageseo")}
				</h3>
				<p className="mt-2 max-w-4xl text-sm text-gray-500">
					{__(
						"Prepare the configuration of your bulk optimization",
						"imageseo"
					)}
				</p>
			</AlertSimple>

			<BulkSettings />
			<div>
				<h3>
					{__("Forecast: with your current settings", "imageseo")}{" "}
					{state.allIds.length}{" "}
					{__("images will be optimized.", "imageseo")}
				</h3>
				<p>
					{__("You have", "imageseo")} {userImagesLeft}{" "}
					{__("credit(s) left in your account.", "imageseo")}{" "}
				</p>
				{get(state, "allIdsOptimized", []).length > 0 && (
					<p>
						{get(state, "allIdsOptimized", []).length}{" "}
						{__(
							"optimizations have already been done!",
							"imageseo"
						)}
					</p>
				)}
				{!state.bulkActive && (
					<button
						style={{ marginRight: 15 }}
						disabled={state.bulkActive}
						onClick={handleStartBulk}
					>
						{__("Start a new bulk optimization", "imageseo")}
					</button>
				)}
				{numberCreditsNeed > userImagesLeft && (
					<button
						onClick={() => {
							window.open(
								"https://app.imageseo.io/plan",
								"_blank"
							);
						}}
					>
						{__("Get more credits", "imageseo")}
					</button>
				)}
			</div>
		</>
	);
};

export default BulkPrepare;
