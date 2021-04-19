import React from "react";
import { isNaN, isNull } from "lodash";
import { getPercentImagesOptimizedAlt } from "../../helpers/getPercentImagesOptimizedAlt";
import useCountImages from "../../hooks/useCountImages";

//@ts-ignore
const { __ } = wp.i18n;

const OverviewCountImages = () => {
	const data = useCountImages();

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
		<div className="bg-white border rounded-lg">
			<div className="px-4 py-5 sm:p-6">
				<p className="text-sm leading-6 font-medium text-gray-900">
					{__("There are", "imageseo")} {data.total_images}
					{__("images in your library.", "imageseo")}
				</p>
				{percentOptimized < 99 && !isNaN(missPercent) && (
					<p className="text-sm mt-2">
						{__(`Did you know that`, "imageseo")}{" "}
						<span className="text-blue-500 font-bold">
							{missPercent}%
						</span>{" "}
						{__(`alternative texts are missing ?`, "imageseo")}
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
					<p className="text-sm flex-1">
						<strong>{percentOptimized}%</strong>{" "}
						{__("completed", "imageseo")}
					</p>
					<a
						className="text-sm underline text-blue-500"
						//@ts-ignore
						href={IMAGESEO_DATA.URL_RECOUNT}
					>
						{__("Recalculating the images counter", "imageseo")}
					</a>
				</div>
			</div>
		</div>
	);
};

export default OverviewCountImages;
