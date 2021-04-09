import React from "react";
import classNames from "classnames";
import { SVGExclamation } from "../../../svg/Exclamation";
import { SVGInformationCircle } from "../../../svg/InformationCircle";

export enum IconsAlert {
	EXCLAMATION = "EXCLAMATION",
	INFORMATION = "INFORMATION",
}

interface Props {
	children: React.ReactNode;
	yellow?: boolean;
	red?: boolean;
	blue?: boolean;
	small?: boolean;
	icon: IconsAlert;
}

export const AlertSimple = ({
	children,
	yellow,
	red,
	blue,
	small,
	icon,
}: Props) => (
	<div
		className={classNames(
			{
				"bg-yellow-50 border-yellow-400": yellow,
				"bg-red-50 border-red-400": red,
				"bg-blue-50 border-blue-400": blue,
				"p-4": !small,
				"p-2": small,
			},
			"border-l-4"
		)}
	>
		<div className="flex">
			<div className="flex-shrink-0">
				{icon === IconsAlert.EXCLAMATION && (
					<SVGExclamation yellow={yellow} red={red} blue={blue} />
				)}
				{icon === IconsAlert.INFORMATION && <SVGInformationCircle />}
			</div>
			<div className="ml-3">
				<div
					className={classNames(
						{ "text-yellow-700": yellow, "text-red-700": red },
						"text-sm"
					)}
				>
					{children}
				</div>
			</div>
		</div>
	</div>
);
