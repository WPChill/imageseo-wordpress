import classNames from "classnames";
import React, { MouseEventHandler } from "react";
import { SVGToggleCross } from "../../svg/ToggleCross";
import { SVGToggleValid } from "../../svg/ToggleValid";

interface Props {
	onClick?: MouseEventHandler<HTMLButtonElement>;
	active: boolean;
}

export const Toggle = ({ onClick, active }: Props) => {
	return (
		<button
			onClick={onClick ? onClick : null}
			type="button"
			className={classNames(
				{
					"bg-indigo-600": active,
					"bg-gray-200": !active,
				},
				"relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
			)}
			aria-pressed="false"
		>
			<span className="sr-only">Use setting</span>
			<span
				className={classNames(
					{
						"translate-x-5": active,
						"translate-x-0": !active,
					},
					"translate-x-0 pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"
				)}
			>
				<span
					className={classNames(
						{
							"opacity-0 ease-out duration-100": active,
							"opacity-100 ease-in duration-200": !active,
						},
						"opacity-100 ease-in duration-200 absolute inset-0 h-full w-full flex items-center justify-center transition-opacity"
					)}
					aria-hidden="true"
				>
					<SVGToggleCross />
				</span>
				<span
					className={classNames(
						{
							"opacity-100 ease-in duration-200": active,
							"opacity-0 ease-out duration-100": !active,
						},
						"opacity-0 ease-out duration-100 absolute inset-0 h-full w-full flex items-center justify-center transition-opacity"
					)}
					aria-hidden="true"
				>
					<SVGToggleValid />
				</span>
			</span>
		</button>
	);
};
