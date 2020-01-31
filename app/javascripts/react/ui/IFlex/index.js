import React from "react";

function IFlexNumber({ number, children }) {
	return <div className={`fl-${number}`}>{children}</div>;
}

function IFlex({ children, style = {} }) {
	return (
		<div className="imageseo-flex" style={style}>
			{children}
		</div>
	);
}

export { IFlexNumber };

export default IFlex;
