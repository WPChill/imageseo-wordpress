import React from "react";
import { get, isNaN, isNull } from "lodash";
import { getPercentImagesOptimizedAlt } from "../../helpers/getPercentImagesOptimizedAlt";
import useCountImages from "../../hooks/useCountImages";
import useOptimizedTimeEstimated from "../../hooks/useOptimizedTimeEstimated";
import { AlertSimple, IconsAlert } from "../Alerts/Simple";

//@ts-ignore
const { __ } = wp.i18n;

const OverviewCountImages = ({ withLinks = true, withAlert = false }) => {
	const data = useCountImages();

	const optimizedTime = useOptimizedTimeEstimated();

	if (isNull(data)) {
		return <>{__("Data being loaded...", "imageseo")}</>;
	}

	const percentOptimized = getPercentImagesOptimizedAlt(
		Number(data.total_images),
		Number(data.total_images_no_alt)
	);

	const missPercent =
		Number(data.total_images) === 0
			? 0
			: (100 - percentOptimized).toFixed(2);

	return (
		<>
			<div className="bg-white border rounded-lg">
				<div className="px-4 py-5 sm:p-6">
					{!isNaN(missPercent) && (
						<p className="text-sm leading-6 font-medium text-gray-900">
							{__("There are", "imageseo")}{" "}
							<span className="text-blue-500 font-bold">
								{data.total_images}
							</span>{" "}
							{__("images in your media library and", "imageseo")}{" "}
							<span className="text-blue-500 font-bold">
								{data.total_images_no_alt}
							</span>{" "}
							{__("don't have an alternative text.", "imageseo")}
						</p>
					)}
					<div className="relative my-2">
						<div className="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200">
							<div
								style={{
									width: `${percentOptimized}%`,
								}}
								className="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500"
							></div>
						</div>
					</div>
					<div className="flex -mt-1">
						{!isNaN(percentOptimized) && (
							<p className="text-sm flex-1">
								<strong>
									{Number(percentOptimized).toFixed(2)}%
								</strong>{" "}
								{__(
									"of all alternative texts optimized.",
									"imageseo"
								)}
							</p>
						)}
						{withLinks && (
							<a
								className="text-sm underline text-blue-500"
								//@ts-ignore
								href={IMAGESEO_DATA.URL_RECOUNT}
							>
								{__(
									"Recalculating the images counter",
									"imageseo"
								)}
							</a>
						)}
					</div>
					{withAlert && (
						<div className="mt-4">
							{!isNull(optimizedTime) &&
								get(optimizedTime, "minutes_by_human", 0) >
									1 && (
									<AlertSimple
										yellow
										icon={IconsAlert.EXCLAMATION}
									>
										<p className="text-sm">
											{__(
												`You would approximately need`,
												`imageseo`
											)}{" "}
											<strong>
												{optimizedTime.minutes_by_human}{" "}
												{__("minutes", "imageseo")}
											</strong>{" "}
											{__(
												"to manually do it or you can do it automatically start a bulk optimization. 🚀",
												"imageseo"
											)}
										</p>
									</AlertSimple>
								)}
						</div>
					)}
				</div>
			</div>
		</>
	);
};

export default OverviewCountImages;
