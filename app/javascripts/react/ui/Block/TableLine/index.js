import React from "react";

function BlockTableLineItem({ children }) {
	return <div className="imageseo-block__table-line__item">{children}</div>;
}
function BlockTableLine({ children }) {
	return <div className="imageseo-block__table-line">{children}</div>;
}

export { BlockTableLineItem };

export default BlockTableLine;
