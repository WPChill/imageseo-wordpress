import React from "react";
import classNames from "classnames";

function Button({ simple, primary, large, children, style, loading, ...rest }) {
	return (
		<button
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
		</button>
	);
}

export default Button;
