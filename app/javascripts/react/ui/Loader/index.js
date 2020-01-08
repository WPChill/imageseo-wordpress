import React from "react";
function Loader({ percent = 0 }) {
	return (
		<div className="imageseo-loader">
			<div
				className="imageseo-loader__step"
				style={{
					width: `${percent}%`
				}}
			></div>
		</div>
	);
}

export default Loader;
