import React, { useState, useContext, useEffect } from "react";
import Swal from "sweetalert2";
import { get, isNil } from "lodash";
const { __ } = wp.i18n;

import Block from "../../../ui/Block";
import BlockContentInner, {
	BlockContentInnerTitle,
} from "../../../ui/Block/ContentInner";

import { BulkSettingsContext } from "../../../contexts/BulkSettingsContext";
import BulkSettings from "../../../components/Bulk/Settings";
import BlockFooter from "../../../ui/Block/Footer";
import Button from "../../../ui/Button";
import { BulkProcessContext } from "../../../contexts/BulkProcessContext";
import { canLaunchBulk } from "../../../services/bulk";
import { UserContext } from "../../../contexts/UserContext";
import queryImages from "../../../services/ajax/query-images";
import { getImagesLeft } from "../../../services/user";

import { startBulkProcess } from "../../../services/ajax/current-bulk";
import LoadingImages from "../../../components/LoadingImages";

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
				text: "Please select at least one optimization",
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
					"Please select the format of your alternative text (alt)",
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
				"You're about to launch a bulk optimization. You can pause it and resume it at any time.",
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
			<Block>
				{loadingImages && <LoadingImages />}
				<BlockContentInner
					isHead
					withAction
					style={{
						alignItems: "center",
					}}
				>
					<BlockContentInnerTitle>
						<h2>{__("Bulk optimization settings", "imageseo")}</h2>
					</BlockContentInnerTitle>
				</BlockContentInner>
				<BlockContentInner>
					<BulkSettings handleQueryImages={handleQueryImages} />
				</BlockContentInner>
				<BlockFooter>
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
						<Button
							primary
							style={{ marginRight: 15 }}
							disabled={state.bulkActive}
							onClick={handleStartBulk}
						>
							{__("Start a new bulk optimization", "imageseo")}
						</Button>
					)}
					{numberCreditsNeed > userImagesLeft && (
						<Button
							simple
							onClick={() => {
								window.open(
									"https://app.imageseo.io/plan",
									"_blank"
								);
							}}
						>
							{__("Get more credits", "imageseo")}
						</Button>
					)}
				</BlockFooter>
			</Block>
		</>
	);
};

export default BulkPrepare;
