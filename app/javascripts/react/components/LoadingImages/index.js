import React from "react";

function LoadingImages() {
	return (
		<div
			style={{
				position: "absolute",
				top: 0,
				left: 0,
				width: "100%",
				height: "100%",
				backgroundColor: "rgba(0,0,0,0.2)",
				zIndex: 500,
				borderRadius: 12,
				display: "flex",
				justifyContent: "center",
				alignItems: "center",
			}}
		>
			<img
				src={`${IMAGESEO_URL_DIST}/images/rotate-cw.svg`}
				style={{
					width: 100,
					marginRight: 10,
					animation: "imageseo-rotation 1s infinite linear",
				}}
			/>
		</div>
	);
}

export default LoadingImages;
