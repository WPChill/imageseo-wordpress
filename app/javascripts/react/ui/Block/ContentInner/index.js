import React from "react";
import classNames from "classnames";

function BlockContentInnerTitle({ children }) {
	return <div className="imageseo-block__inner__title">{children}</div>;
}
function BlockContentInnerSubTitle({ children }) {
	return <div className="imageseo-block__inner__subtitle">{children}</div>;
}

function BlockContentInnerAction({ children }) {
	return <div className="imagese-block__inner__actions">{children}</div>;
}

function BlockContentInner({ children, isHead, withAction, style }) {
	return (
		<div
			className={classNames(
				{
					"imageseo-block__inner--head": isHead,
					"imageseo-block__inner--actions": withAction
				},
				"imageseo-block__inner"
			)}
			style={style}
		>
			{children}
		</div>
	);
}

export {
	BlockContentInnerAction,
	BlockContentInner,
	BlockContentInnerTitle,
	BlockContentInnerSubTitle
};

export default BlockContentInner;
