import React from "react";
import classNames from "classnames";

function Block({ children, style = {}, secondary }) {
	return (
		<div
			className={classNames(
				{
					"imageseo-block--secondary": secondary
				},
				"imageseo-block"
			)}
			style={style}
		>
			{children}
		</div>
	);
}

export default Block;
