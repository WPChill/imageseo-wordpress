import React from "react";

function IFlexNumber({ number, children }) {
	return <div className={`fl-${number}`}>{children}</div>;
}

function IFlex({ children }) {
	return <div className="imageseo-flex">{children}</div>;
}

export { IFlexNumber };

export default IFlex;
