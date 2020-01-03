import React from "react";

function Block({ children, style = {} }) {
	return (
		<div className="imageseo-block" style={style}>
			{children}
		</div>
	);
}

export default Block;
