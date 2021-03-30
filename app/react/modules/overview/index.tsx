import { isEmpty } from "lodash";
import React, { useState, useContext, Suspense } from "react";
import { AlertSimple, IconsAlert } from "../../components/Alerts/Simple";
import FormRegister from "../../components/Forms/Register";
import FormValidateApiKey from "../../components/Forms/ValidateApiKey";
import { PageContext } from "../../contexts/PageContext";
import getApiKey from "../../helpers/getApiKey";
import useOwner from "../../hooks/useOwner";

//@ts-ignore
const { __ } = wp.i18n;

const OverviewConnected = () => {
	const user = useOwner();
	console.log(user);

	return <div>hello</div>;
};

const Overview = () => {
	const {
		values: { apiKey },
	} = useContext(PageContext);

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
			<div className="mt-4 grid grid-cols-6 gap-16">
				<div className="col-span-4 border-r pr-16">
					{isEmpty(getApiKey()) && (
						<>
							<div className=" pb-2 mb-4">
								<h2 className="font-bold text-xl">
									{__("Create an account", "imageseo")} -{" "}
									<span className="font-bold text-indigo-500 text-lg">
										{__("It's free", "imageseo")}
									</span>
								</h2>
							</div>
							<FormRegister />
						</>
					)}

					{!isEmpty(getApiKey()) && (
						<Suspense fallback={<div className="mt-10">Hello</div>}>
							<OverviewConnected />
						</Suspense>
					)}
				</div>
				<div className="col-span-2">
					<div className=" pb-2 mb-4">
						<h2 className="font-bold text-xl">
							{isEmpty(apiKey) &&
								__("You already have a Key API?", "imageseo")}
							{!isEmpty(apiKey) && __("Your API Key", "imageseo")}
						</h2>
					</div>
					<FormValidateApiKey />
				</div>
			</div>
		</>
	);
};

export default Overview;
