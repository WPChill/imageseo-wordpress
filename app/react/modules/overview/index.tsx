import { isEmpty, isNull } from "lodash";
import React, { useState, useContext, Suspense } from "react";
import { AlertSimple, IconsAlert } from "../../components/Alerts/Simple";
import FormRegister from "../../components/Forms/Register";
import FormValidateApiKey from "../../components/Forms/ValidateApiKey";
import OverviewCountImages from "../../components/OverviewCountImages";
import { PageContext } from "../../contexts/PageContext";
import getApiKey from "../../helpers/getApiKey";
import useCountImages from "../../hooks/useCountImages";
import useOptimizedTimeEstimated from "../../hooks/useOptimizedTimeEstimated";

//@ts-ignore
const { __ } = wp.i18n;

const OverviewConnected = () => {
	const data = useOptimizedTimeEstimated();

	return (
		<>
			<div className="mb-4">
				{!isNull(data) && (
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
							{__(`We could certainly do it in`, `imageseo`)}{" "}
							<strong>{data.string_time_estimated}</strong>.
						</p>
					</AlertSimple>
				)}
			</div>
			<OverviewCountImages />
		</>
	);
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
