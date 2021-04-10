import React from "react";
import { get, isNull } from "lodash";
import { getPercentImagesOptimizedAlt } from "../../helpers/getPercentImagesOptimizedAlt";
import useCountImages from "../../hooks/useCountImages";
import useOwner from "../../hooks/useOwner";

//@ts-ignore
const { __ } = wp.i18n;

const OverviewCredit = () => {
	const user = useOwner();

	if (isNull(user)) {
		return <>{__("Data being loaded...", "imageseo")}</>;
	}

	const limitImages =
		get(user, "plan.limit_images", 10) +
		get(user, "bonus_stock_images", 0) -
		get(user, "current_request_images", 0);

	const usageCreditPercent = Math.ceil(
		(get(user, "current_request_images", 0) * 100) /
			(get(user, "plan.limit_images", 10) +
				get(user, "bonus_stock_images", 0))
	);

	return (
		<div className="bg-white border rounded-lg">
			<div className="px-4 py-5 sm:p-6">
				<p className="text-sm leading-6 font-medium text-gray-900">
					{__(`${limitImages} credits left.`, "imageseo")}
				</p>

				<div className="relative my-2">
					<div className="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200">
						<div
							style={{
								width: `${usageCreditPercent}%`,
							}}
							className="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500"
						></div>
					</div>
				</div>
				<div className="flex  -mt-1">
					<p className="text-sm flex-1">
						<strong>{usageCreditPercent}%</strong>{" "}
						{__("credits used", "imageseo")}
					</p>
					<a
						href="https://app.imageseo.io/plan"
						target="_blank"
						className="text-blue-500 underline"
					>
						{__("Buy more credit", "imageseo")}
					</a>
				</div>
			</div>
		</div>
	);
};

export default OverviewCredit;
