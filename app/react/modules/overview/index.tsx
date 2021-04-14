import { get, isEmpty, isNull } from "lodash";
import React, { useState, useContext, Suspense } from "react";
import { AlertSimple, IconsAlert } from "../../components/Alerts/Simple";
import FormRegister from "../../components/Forms/Register";
import FormValidateApiKey from "../../components/Forms/ValidateApiKey";
import OverviewCountImages from "../../components/OverviewCountImages";
import OverviewCredit from "../../components/OverviewCredit";
import SeoFact from "../../components/SeoFact";
import { PageContext } from "../../contexts/PageContext";
import getApiKey from "../../helpers/getApiKey";
import useOptimizedTimeEstimated from "../../hooks/useOptimizedTimeEstimated";

//@ts-ignore
const { __ } = wp.i18n;

const OverviewConnected = () => {
	const data = useOptimizedTimeEstimated();

	return (
		<>
			<div className="mb-4">
				{!isNull(data) && get(data, "minutes_by_human", 0) > 1 && (
					<AlertSimple yellow icon={IconsAlert.EXCLAMATION}>
						<p className="text-sm">
							{__(
								`Estimated time if you had to fill out your alternative texts and manually rewrite your file names`,
								`imageseo`
							)}{" "}
							<strong>
								{data.minutes_by_human}{" "}
								{__("minutes", "imageseo")}.
							</strong>
						</p>
						<p className="text-sm font-bold mt-1">
							{__(
								`Start a bulk optimization to fill out all your missing alt texts with SEO friendly content`,
								`imageseo`
							)}
						</p>
					</AlertSimple>
				)}
			</div>
			<OverviewCountImages />
			<div className="mt-4">
				<OverviewCredit />
			</div>
		</>
	);
};

const Overview = () => {
	const {
		values: { apiKey },
	} = useContext(PageContext);

	return (
		<>
			{isEmpty(getApiKey()) && <SeoFact />}
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
						<Suspense
							fallback={
								<div className="mt-10">
									Data is current loading
								</div>
							}
						>
							<>
								<div className="mb-4">
									<SeoFact />
								</div>
								<OverviewConnected />
							</>
						</Suspense>
					)}
				</div>
				<div className="col-span-2">
					<div className=" pb-2 mb-4">
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
