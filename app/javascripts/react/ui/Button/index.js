import React from "react";
import classNames from "classnames";

function Button({ simple, primary, large, children, style, ...rest }) {
	return (
		<div
			className={classNames(
				{
					"imageseo-btn--simple": simple,
					"imageseo-btn--primary": primary,
					"imageseo-btn--large": large
				},
				"imageseo-btn"
			)}
			style={style}
			{...rest}
		>
			{children}
		</div>
	);
}

export default Button;
