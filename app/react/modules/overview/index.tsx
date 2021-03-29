import React, { useState, useContext, useEffect, Suspense } from "react";
import { isNull, get, isEmpty } from "lodash";
import Swal from "sweetalert2";
import styled from "styled-components";
import getApiKey from "../../helpers/getApiKey";
import { AlertSimple, IconsAlert } from "../../components/Alerts/Simple";

//@ts-ignore
const { __ } = wp.i18n;

function Overview() {
	return (
		<>
			<AlertSimple icon={IconsAlert.INFORMATION} blue>
				<p className="imageseo-mb-0">
					<strong>
						{__(
							"SEO Fact : More than 20% of Google Organic Traffic comes from image searches",
							"imageseo"
						)}
					</strong>
				</p>
				<p>
					{__(
						"Start optimizing your image alt texts and names and grow your traffic!",
						"imageseo"
					)}
				</p>
			</AlertSimple>
			{/* {!isEmpty(getApiKey()) && (
				<SCContainerSideOverview>
					<Suspense
						fallback={
							<div className="mt-10">
								<Skeleton />
								<Skeleton />
							</div>
						}
					>
						<div className="mb-15">
							<AccountInfoSuspense />
						</div>
						<ValidateApiKey alignButton={false} />
					</Suspense>
				</SCContainerSideOverview>
			)} */}
		</>
	);
}

export default Overview;
