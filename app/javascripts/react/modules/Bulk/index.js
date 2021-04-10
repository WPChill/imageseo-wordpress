import React, { useState, useContext, useEffect } from "react";
import Swal from "sweetalert2";
import { get, isNil, find, difference, isEmpty } from "lodash";
const { __ } = wp.i18n;

import Block from "../../ui/Block";
import BlockContentInner, {
	BlockContentInnerTitle,
	BlockContentInnerAction,
} from "../../ui/Block/ContentInner";
import IconChevron from "../../ui/Icons/Chevron";

import BulkSettingsContextProvider, {
	BulkSettingsContext,
} from "../../contexts/BulkSettingsContext";
import BulkSettings from "../../components/Bulk/Settings";
import BlockFooter from "../../ui/Block/Footer";
import Button from "../../ui/Button";
import BulkResults from "../../components/Bulk/Results";
import BulkProcessContextProvider, {
	BulkProcessContext,
} from "../../contexts/BulkProcessContext";
import { canLaunchBulk, getPercentBulk } from "../../services/bulk";
import BulkSummary from "../../components/Bulk/Summary";
import UserContextProvider, { UserContext } from "../../contexts/UserContext";
import queryImages from "../../services/ajax/query-images";
import { getImagesLeft, hasLimitExcedeed } from "../../services/user";
import getAttachement from "../../services/ajax/get-attachement";

import Loader from "../../ui/Loader";
import { Col, Row } from "../../ui/Flex";
import SubTitle from "../../ui/Block/Subtitle";
import {
	startBulkProcess,
	getCurrentProcessDispatch,
	stopCurrentProcess,
	finishCurrentProcess,
} from "../../services/ajax/current-bulk";
import LimitExcedeed from "../../components/Bulk/LimitExcedeed";
import useInterval from "../../hooks/useInterval";
import { fromUnixTime } from "date-fns/esm";
import { differenceInSeconds } from "date-fns";
import LoadingImages from "../../components/LoadingImages";
import BulkPrepare from "./Prepare";
import BulkInProcess from "./InProcess";
import BulkLastProcess from "./LastProcess";

function BulkWithProviders() {
	const { state } = useContext(BulkProcessContext);

	//@ts-ignore
	const limitExcedeed = get(IMAGESEO_DATA, "LIMIT_EXCEDEED", false)
		? true
		: false;

	if (state.bulkActive) {
		return <BulkInProcess />;
	}

	return (
		<>
			{!isNil(state.lastProcess) && <BulkLastProcess />}

			{!limitExcedeed && <BulkPrepare />}
		</>
	);
}

function Bulk() {
	return (
		<BulkSettingsContextProvider>
			<BulkProcessContextProvider>
				<UserContextProvider
					initialState={{
						default_language_ia:
							IMAGESEO_DATA.OPTIONS.default_language_ia,
						user_infos: IMAGESEO_DATA.USER_INFOS,
						limit_images: IMAGESEO_DATA.LIMIT_IMAGES,
					}}
				>
					<BulkWithProviders />
				</UserContextProvider>
			</BulkProcessContextProvider>
		</BulkSettingsContextProvider>
	);
}

export default Bulk;
