import React, { useState, useContext, useEffect } from "react";
import Swal from "sweetalert2";
import { get, isNil, find, difference, isEmpty } from "lodash";

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
						<h2>Bulk optimization settings</h2>
					</BlockContentInnerTitle>
				</BlockContentInner>
				<BlockContentInner>
					<BulkSettings handleQueryImages={handleQueryImages} />
				</BlockContentInner>
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
							onClick={() => {
								handleStartBulk();
							}}
						>
							Start a new bulk optimization
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
							Get more credits
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
