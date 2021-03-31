import React from "react";
import { isNull } from "lodash";
import { getPercentImagesOptimizedAlt } from "../../helpers/getPercentImagesOptimizedAlt";
import useCountImages from "../../hooks/useCountImages";

//@ts-ignore
const { __ } = wp.i18n;

const OverviewCountImages = () => {
	const data = useCountImages();

	if (isNull(data)) {
		return <>Données en cours de chargement...</>;
	}

	const percentOptimized = getPercentImagesOptimizedAlt(
		data.total_images,
		data.total_images_no_alt
	);

	return (
		<div className="border rounded-lg p-4">
			<p className="text-sm mb-2">
				{__(
					`There are ${data.total_images} images in your library.`,
					"imageseo"
				)}
			</p>
			{percentOptimized < 99 && (
				<p className="font-bold text-base">
					{__(`Did you know that`, "imageseo")}{" "}
					<span className="text-blue-500">
						{100 - percentOptimized}%
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
			<p className="text-sm">
				<strong>{percentOptimized}%</strong>{" "}
				{__("completed", "imageseo")}
			</p>
		</div>
	);
};

export default OverviewCountImages;
