import React from "react";

export const SVGLoader = ({ color = "#ffffff", className = "" }) => {
	return (
		<svg
			xmlns="http://www.w3.org/2000/svg"
			width="20"
			height="20"
			viewBox="0 0 24 24"
			className={className}
			style={{
				animation: "imageseo-loading 1.2s linear infinite",
			}}
		>
			<path
				fill={color}
				fillRule="evenodd"
				d="M11.735 20.996a9 9 0 0 1-5.103-16.22A1 1 0 0 1 7.826 6.38a7 7 0 1 0 8.258-.066 1 1 0 1 1 1.168-1.623A9 9 0 0 1 12 21v-.001z"
				opacity=".603"
			/>
		</svg>
	);
};
