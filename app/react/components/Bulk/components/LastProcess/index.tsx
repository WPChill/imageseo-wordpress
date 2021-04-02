import React, { useContext } from "react";
import Swal from "sweetalert2";
import { get } from "lodash";

//@ts-ignore
const { __ } = wp.i18n;

import { BulkSettingsContext } from "../../../../contexts/BulkSettingsContext";
import { BulkProcessContext } from "../../../../contexts/BulkProcessContext";
import { UserContext } from "../../../../contexts/UserContext";

import { restartBulkProcess } from "../../../../services/ajax/current-bulk";
import LimitExcedeed from "../LimitExcedeed";
import { SVGPlay } from "../../../../svg/Play";

const BulkLastProcess = () => {
	const { state, dispatch } = useContext(BulkProcessContext);
	const { state: userState } = useContext(UserContext);
	const { state: settings } = useContext(BulkSettingsContext);

	const total_ids_optimized = get(
		state,
		"lastProcess.id_images_optimized",
		[]
	).length;

	const total_images = get(state, "lastProcess.total_images", 0);

	const percent = Number((total_ids_optimized * 100) / total_images).toFixed(
		2
	);

	const handleRestartBulk = () => {
		Swal.fire({
			title: __("Are you sure?", "imageseo"),
			text: __("We will pick up where you left off.", "imageseo"),
			icon: "info",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			confirmButtonText: __("Restart process", "imageseo"),
		}).then(async (result) => {
			if (result.value) {
				const { data, success } = await restartBulkProcess();
				if (!success) {
					Swal.fire({
						title: __("Oups!", "imageseo"),
						text: __("Limit excedeed.", "imageseo"),
						icon: "error",
						showCancelButton: true,
					});
				}
				dispatch({ type: "START_CURRENT_PROCESS", payload: data });
			}
		});
	};

	//@ts-ignore
	const limitExcedeed = get(IMAGESEO_DATA, "LIMIT_EXCEDEED", false)
		? true
		: false;
	const optimizeAlt = get(state, "lastProcess.settings.optimizeAlt", false);
	const optimizeFilename = get(
		state,
		"lastProcess.settings.optimizeFile",
		false
	);

	return (
		<>
			<div className="bg-white rounded-lg border max-w-5xl p-6">
				<h2 className="text-base font-bold mb-2 flex items-center">
					{__("Bulk optimization paused", "imageseo")}
				</h2>
				<p className="text-sm font-bold">
					{total_ids_optimized} / {total_images} images - {percent}%
				</p>

				<div className="relative my-2">
					<div className="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200">
						<div
							style={{
								width: `${percent}%`,
							}}
							className="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500"
						></div>
					</div>
				</div>
				{!limitExcedeed && (
					<div
						className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 cursor-pointer"
						onClick={handleRestartBulk}
					>
						<SVGPlay className="mr-2 h-4 w-4" />
						{__("Restart", "imageseo")}
					</div>
				)}
			</div>
			{limitExcedeed && (
				<div className="mt-4">
					<LimitExcedeed percent={percent} />
				</div>
			)}
		</>
	);
};

export default BulkLastProcess;