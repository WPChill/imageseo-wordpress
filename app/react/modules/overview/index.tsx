import { get, isEmpty, isNull } from "lodash";
import React, { useState, useContext, Suspense } from "react";
import { AlertSimple, IconsAlert } from "../../components/Alerts/Simple";
import FormRegister from "../../components/Forms/Register";
import FormValidateApiKey from "../../components/Forms/ValidateApiKey";
import OverviewCountImages from "../../components/OverviewCountImages";
import OverviewCredit from "../../components/OverviewCredit";
import SeoFact from "../../components/SeoFact";
import { TABS } from "../../constants/app-tabs";
import { PageContext } from "../../contexts/PageContext";
import getApiKey from "../../helpers/getApiKey";
import useOptimizedTimeEstimated from "../../hooks/useOptimizedTimeEstimated";

//@ts-ignore
const { __ } = wp.i18n;

const OverviewConnected = () => {
	const optimizedTime = useOptimizedTimeEstimated();
	const { actions } = useContext(PageContext);

	return (
		<>
			<OverviewCountImages />
			<div className="mt-4">
				{!isNull(optimizedTime) &&
					get(optimizedTime, "minutes_by_human", 0) > 1 && (
						<AlertSimple yellow icon={IconsAlert.EXCLAMATION}>
							<p className="text-sm">
								{__(`You would approximately need`, `imageseo`)}{" "}
								<strong>
									{optimizedTime.minutes_by_human}{" "}
									{__("minutes", "imageseo")}
								</strong>{" "}
								{__(
									"to manually do it or you can do it automatically start a bulk optimization. 🚀",
									"imageseo"
								)}
							</p>
							<p className="text-sm font-bold mt-1">
								<span
									className="underline font-bold cursor-pointer"
									onClick={() => {
										actions.setTabSelected(
											TABS.BULK_OPTIMIZATION
										);
									}}
								>
									{__(
										"Start a bulk optimization",
										"imageseo"
									)}
								</span>
								{__(
									` to fill out all your missing alt texts with SEO friendly content.`,
									`imageseo`
								)}
							</p>
						</AlertSimple>
					)}
			</div>
			<div className="mt-4">
				<OverviewCredit />
			</div>
		</>
	);
};

const OverviewNeedRegister = () => {
	return (
		<>
			<OverviewCountImages withLinks={false} withAlert={true} />
		</>
	);
};

const Overview = () => {
	const {
		values: { apiKey },
	} = useContext(PageContext);

	return (
		<>
			{isEmpty(getApiKey()) && <OverviewNeedRegister />}
			<div className="mt-4 grid grid-cols-6 gap-8">
				<div className="col-span-4 border-r pr-8">
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
						<Suspense
							fallback={
								<div className="mt-10">
									{__("Data is current loading", "imageseo")}
								</div>
							}
						>
							<>
								<OverviewConnected />
							</>
						</Suspense>
					)}
				</div>
				<div className="col-span-2">
					{!isEmpty(apiKey) && (
						<div className="pb-2 mb-4">
							<h2 className="font-bold text-xl">
								{__("Manage your account", "imageseo")}
							</h2>
							<a
								className="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mt-4"
								href="https://app.imageseo.io/"
								target="_blank"
							>
								{__("Go to the application", "imageseo")}
							</a>
						</div>
					)}

					<div className="pb-2 mb-4">
						<h2 className="font-bold text-xl">
							{isEmpty(apiKey) &&
								__("You already have an API Key?", "imageseo")}
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
