import React, { useState, useContext, useEffect, Suspense } from "react";
import { isNull, get, isEmpty } from "lodash";
import Swal from "sweetalert2";
import styled from "styled-components";
import getApiKey from "../../helpers/getApiKey";

//@ts-ignore
const { __ } = wp.i18n;

function Overview() {
	return (
		<>
			Overview
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
