import React from "react";

function IconChevron({ up, down, ...rest }) {
	return (
		<div className="imageseo-icons imageseo-icons--button" {...rest}>
			<img
				src={`${IMAGESEO_URL_DIST}/images/chevron-up.svg`}
				style={{ display: up ? "block" : "none" }}
			/>
			<img
				src={`${IMAGESEO_URL_DIST}/images/chevron-down.svg`}
				style={{ display: down ? "block" : "none" }}
			/>
		</div>
	);
}

export default IconChevron;
