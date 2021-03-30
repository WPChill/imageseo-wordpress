import React from "react";

export const SVGCross = ({ style = {}, className = "" }) => {
	return (
		<svg
			xmlns="http://www.w3.org/2000/svg"
			width="24"
			height="24"
			viewBox="0 0 24 24"
			style={style}
			className={className}
		>
			<path
				fill="#6A6B6E"
				d="M4.293 4.293a1 1 0 011.414 0L12 10.585l6.293-6.292a1 1 0 011.32-.083l.094.083a1 1 0 010 1.414L13.415 12l6.292 6.293a1 1 0 01.083 1.32l-.083.094a1 1 0 01-1.414 0L12 13.415l-6.293 6.292a1 1 0 01-1.32.083l-.094-.083a1 1 0 010-1.414L10.585 12 4.293 5.707a1 1 0 01-.083-1.32z"
			/>
		</svg>
	);
};
