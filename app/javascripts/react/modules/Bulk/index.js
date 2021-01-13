import React, { useState, useContext, useEffect } from "react";
import Swal from "sweetalert2";
import { get, isNil, find, difference, isEmpty } from "lodash";
const { __ } = wp.i18n;

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
import { canLaunchBulk, getPercentBulk } from "../../services/bulk";
import BulkSummary from "../../components/Bulk/Summary";
import UserContextProvider, { UserContext } from "../../contexts/UserContext";
import queryImages from "../../services/ajax/query-images";
import { getImagesLeft, hasLimitExcedeed } from "../../services/user";
import getAttachement from "../../services/ajax/get-attachement";

import Loader from "../../ui/Loader";
import { Col, Row } from "../../ui/Flex";
import SubTitle from "../../ui/Block/Subtitle";
import {
	startBulkProcess,
	getCurrentProcessDispatch,
	stopCurrentProcess,
	finishCurrentProcess,
} from "../../services/ajax/current-bulk";
import LimitExcedeed from "../../components/Bulk/LimitExcedeed";
import useInterval from "../../hooks/useInterval";
import { fromUnixTime } from "date-fns/esm";
import { differenceInSeconds } from "date-fns";
import LoadingImages from "../../components/LoadingImages";

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
				await startBulkProcess(state.allIds, settings);
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
							onClick={() => {
								handleStartBulk();
							}}
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
