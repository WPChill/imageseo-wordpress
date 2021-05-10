import React, { useContext, useState } from "react";
import Swal from "sweetalert2";
import { get, isNaN } from "lodash";
import { fromUnixTime } from "date-fns";
import { differenceInSeconds } from "date-fns/esm";

//@ts-ignore
const { __ } = wp.i18n;

import LimitExcedeed from "../LimitExcedeed";
import { BulkProcessContext } from "../../../../contexts/BulkProcessContext";
import {
	getCurrentBulk,
	stopCurrentProcess,
} from "../../../../services/ajax/current-bulk";
import { SVGPause } from "../../../../svg/Pause";
import ViewDataBulk from "../ViewData";
import classNames from "classnames";

const BulkInProcess = () => {
	const { state, dispatch } = useContext(BulkProcessContext);

	let total_ids_optimized = 0;
	let total_images = 1;

	if (!state.bulkIsFinish) {
		total_ids_optimized = get(
			state,
			"currentProcess.id_images_optimized",
			[]
		).length;

		total_images = get(state, "currentProcess.total_images", 0);
	} else {
		total_ids_optimized = get(
			state,
			"finishProcess.id_images_optimized",
			[]
		).length;

		total_images = get(state, "finishProcess.total_images", 0);
	}

	const percent = Number((total_ids_optimized * 100) / total_images).toFixed(
		2
	);

	const [isOpen, setIsOpen] = useState(false);
	const [reload, setReload] = useState(false);
	const [nextProcessed, setNextProcessed] = useState(
		//@ts-ignore
		get(IMAGESEO_DATA, "NEXT_SCHEDULED", false)
	);
	//@ts-ignore
	const limit = get(IMAGESEO_DATA, "LIMIT_EXCEDEED", false);

	const [limitExcedeed, setLimitExcedeed] = useState(limit ? true : false);

	const handleStopBulk = () => {
		Swal.fire({
			title: __("Are you sure?", "imageseo"),
			text: __(
				"You will always be able to pick up where the process left off.",
				"imageseo"
			),
			icon: "info",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			confirmButtonText: __("Stop process", "imageseo"),
		}).then(async (result) => {
			if (result.value) {
				const { data } = await stopCurrentProcess();
				dispatch({ type: "STOP_CURRENT_PROCESS", payload: data });
			}
		});
	};

	const handleRefreshData = async () => {
		setReload(true);
		const { data } = await getCurrentBulk();
		if (get(data, "current", false)) {
			dispatch({
				type: "UPDATE_CURRENT_PROCESS",
				payload: get(data, "current"),
			});
			setNextProcessed(get(data, "scheduled", false));
		} else if (get(data, "finish", false)) {
			dispatch({
				type: "FINISH_CURRENT_PROCESS",
				payload: get(data, "finish"),
			});
		}

		if (get(data, "limit_excedeed", false)) {
			setLimitExcedeed(true);
			dispatch({
				type: "UPDATE_LIMIT_EXCEDEED",
				payload: true,
			});
		}

		setReload(false);
	};

	let diffSeconds = false;
	try {
		if (nextProcessed) {
			const now = new Date();
			//@ts-ignore
			diffSeconds = differenceInSeconds(fromUnixTime(nextProcessed), now);
			//@ts-ignore
			if (diffSeconds < 0) {
				//@ts-ignore
				diffSeconds = Math.abs(diffSeconds);
			}
		}
	} catch (error) {}

	return (
		<>
			<div
				className={classNames(
					{
						"bg-white": !state.bulkIsFinish,
						"bg-green-100 border-green-300": state.bulkIsFinish,
					},
					"rounded-lg border max-w-5xl p-6"
				)}
			>
				<div className="flex">
					<h2 className="text-base font-bold mb-2 flex items-center flex-1">
						{!state.bulkIsFinish && !limitExcedeed && (
							<img
								//@ts-ignore
								src={`${IMAGESEO_DATA.URL_DIST}/images/rotate-cw.svg`}
								style={{
									animation:
										"imageseo-rotation 1s infinite linear",
								}}
								className="mr-4"
							/>
						)}
						{__("Bulk optimization in progress", "imageseo")}{" "}
					</h2>
					{!state.bulkIsFinish && !limitExcedeed && (
						<div
							className="inline-flex items-center px-4 py-2 border rounded-md shadow-sm text-sm font-medium text-black bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 cursor-pointer mr-4"
							onClick={handleRefreshData}
						>
							{__("Click here to refresh data", "imageseo")}
							{reload && (
								<img
									//@ts-ignore
									src={`${IMAGESEO_DATA.URL_DIST}/images/rotate-cw.svg`}
									style={{
										marginLeft: 5,
										animation:
											"imageseo-rotation 1s infinite linear",
									}}
								/>
							)}
						</div>
					)}
				</div>

				{!isNaN(diffSeconds) &&
					Number(diffSeconds) > 0 &&
					Number(diffSeconds) < 60 && (
						<p className="text-sm mb-2">
							{__("Next process in few seconds", "imageseo")} (
							{diffSeconds}s){" "}
						</p>
					)}
				<p className="text-sm font-bold">
					{total_ids_optimized} / {total_images} images{" "}
					{!isNaN(percent) && <>- {percent}%</>}
				</p>

				<div className="relative my-2">
					<div
						className={classNames(
							{
								"bg-blue-200": !state.bulkIsFinish,
								"bg-green-200": state.bulkIsFinish,
							},
							"overflow-hidden h-2 mb-4 text-xs flex rounded"
						)}
					>
						<div
							style={{
								width: `${percent}%`,
							}}
							className={classNames(
								{
									"bg-blue-500": !state.bulkIsFinish,
									"bg-green-500": state.bulkIsFinish,
								},
								"shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center"
							)}
						></div>
					</div>
				</div>
				<div className="flex items-center">
					<div
						className="inline-flex items-center px-4 py-2 border rounded-md shadow-sm text-sm font-medium text-black bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 cursor-pointer mr-4"
						onClick={() => setIsOpen(true)}
					>
						{__("View results", "imageseo")}
					</div>
					{!state.bulkIsFinish && !limitExcedeed && (
						<div
							className="inline-flex items-center px-4 py-2 border rounded-md shadow-sm text-sm font-medium text-black bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 cursor-pointer"
							onClick={handleStopBulk}
						>
							<SVGPause className="mr-2 h-4 w-4" />
							{__("Pause", "imageseo")}
						</div>
					)}
				</div>
			</div>
			{state.bulkIsFinish && (
				<>
					<div className="bg-green-100 rounded-lg border border-green-200 max-w-5xl p-6 mt-4">
						<p className="text-sm font-bold mb-1">
							{__("The process ended well.", "imageseo")}
						</p>
						<p className="text-sm">
							{__(
								"You can edit and view all your results in your media library in 'list' mode.",
								"imageseo"
							)}
						</p>
						<a
							className="inline-block items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mt-4 mx-auto"
							//@ts-ignore
							href={IMAGESEO_DATA.LIBRARY_URL}
						>
							{__("View medias", "imageseo")}
						</a>
					</div>
				</>
			)}
			{isOpen && (
				<div className="mt-4">
					<ViewDataBulk state={state} />
				</div>
			)}
			{limitExcedeed && (
				<div className="mt-4">
					<LimitExcedeed />
				</div>
			)}
		</>
	);
};

export default BulkInProcess;
