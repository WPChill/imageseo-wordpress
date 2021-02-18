import React, { useState, useContext, useEffect } from "react";
import styled from "styled-components";
import Swal from "sweetalert2";
import { get, isNil } from "lodash";
const { __ } = wp.i18n;

import Block from "../../../ui/Block";
import BlockContentInner, {
	BlockContentInnerTitle,
} from "../../../ui/Block/ContentInner";

import { BulkSettingsContext } from "../../../contexts/BulkSettingsContext";
import BulkSettings from "../../../components/Bulk/Settings";
import BlockFooter from "../../../ui/Block/Footer";
import Button from "../../../ui/Button";
import { BulkProcessContext } from "../../../contexts/BulkProcessContext";
import { canLaunchBulk } from "../../../services/bulk";
import { UserContext } from "../../../contexts/UserContext";
import queryImages from "../../../services/ajax/query-images";
import { getImagesLeft } from "../../../services/user";

import { restartBulkProcess } from "../../../services/ajax/current-bulk";
import LoadingImages from "../../../components/LoadingImages";
import LimitExcedeed from "../../../components/Bulk/LimitExcedeed";

const SCContainerProcess = styled.div`
	padding: 32px;
	border-radius: 8px;
	border: solid 1px #00081a;
	background-color: #fafafc;
	position: relative;
	z-index: 3;
	margin-bottom: 50px;
	&:after {
		position: absolute;
		content: "";
		z-index: 1;
		height: 100%;
		opacity: 0.1;
		background-color: #5b2222;
		width: ${({ percent }) => Number(percent).toFixed(0)}%;
		top: 0;
		left: 0;
		border-top-left-radius: 8px;
		border-bottom-left-radius: 8px;
	}
	h2 {
		margin: 0 0 8px;
		font-size: 24px;
		font-weight: bold;
		color: #001f59;
	}
	.infos__images {
		font-size: 14px;
		color: #001f59;
	}
	.progress__bar {
		width: 100%;
		height: 8px;
		border-radius: 15px;
		background-color: #dadae0;
		overflow: hidden;
		&--content {
			height: 8px;
			border-top-left-radius: 15px;
			border-bottom-left-radius: 15px;
			background-color: #0e214d;
			width: ${({ percent }) => {
				if (Number(percent).toFixed(0) === 0) {
					return 0;
				}
				return `calc(${Number(percent).toFixed(0)}% + 32px)`;
			}};
		}
	}
	.btn__action {
		font-size: 14px;
		font-weight: bold;
		text-align: center;
		border: 1px solid #2b68d9;
		color: #fff;
		padding: 8px 16px;
		border-radius: 4px;
		display: flex;
		align-items: center;
		justify-content: center;
		background-color: #2b68d9;
		position: absolute;
		top: 32px;
		right: 32px;
		z-index: 5;
		img {
			margin-right: 5px;
		}
		&:hover {
			cursor: pointer;
		}
	}
`;

const BulkLastProcess = () => {
	const { state, dispatch } = useContext(BulkProcessContext);
	const { state: userState } = useContext(UserContext);
	const { state: settings } = useContext(BulkSettingsContext);

	const total_ids_optimized = get(
		state,
		"lastProcess.id_images_optimized",
		[]
	).length;

	const total_images = get(state, "lastProcess.total_images", 0);

	const percent = Number((total_ids_optimized * 100) / total_images).toFixed(
		2
	);

	const handleRestartBulk = () => {
		Swal.fire({
			title: __("Are you sure?", "imageseo"),
			text: __("We will pick up where you left off.", "imageseo"),
			icon: "info",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			confirmButtonText: __("Restart process", "imageseo"),
		}).then(async (result) => {
			if (result.value) {
				const { data, success } = await restartBulkProcess();
				if (!success) {
					Swal.fire({
						title: __("Oups!", "imageseo"),
						text: __("Limit excedeed.", "imageseo"),
						icon: "error",
						showCancelButton: true,
					});
				}
				dispatch({ type: "START_CURRENT_PROCESS", payload: data });
			}
		});
	};

	const limitExcedeed = get(IMAGESEO_DATA, "LIMIT_EXCEDEED", false)
		? true
		: false;

	return (
		<>
			<SCContainerProcess percent={percent}>
				<h2>{__("Bulk optimization paused", "imageseo")}</h2>
				<p className="infos__images">
					{total_ids_optimized} / {total_images} images - {percent}%
				</p>
				<div className="progress__bar">
					<div className="progress__bar--content"></div>
				</div>
				{!limitExcedeed && (
					<div className="btn__action" onClick={handleRestartBulk}>
						<img
							src={`${IMAGESEO_URL_DIST}/images/icon-play.svg`}
							alt=""
						/>
						{__("Restart", "imageseo")}
					</div>
				)}
			</SCContainerProcess>
			{limitExcedeed && <LimitExcedeed percent={percent} />}
		</>
	);
};

export default BulkLastProcess;
