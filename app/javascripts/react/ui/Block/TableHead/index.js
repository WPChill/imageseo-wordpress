import React from "react";

function BlockTableHeadItem({ children }) {
	return <div className="imageseo-block__table-head__item">{children}</div>;
}

function BlockTableHead({ children }) {
	return <div className="imageseo-block__table-head">{children}</div>;
}

export { BlockTableHeadItem };

export default BlockTableHead;
