import React, { useState } from "react";
import { CheckIcon } from "@heroicons/react/solid";
import { find, isNull } from "lodash";
import FormRegister from "../../components/Forms/Register";
import getLinkImage from "../../helpers/getLinkImage";
import { AlertSimple } from "../../components/Alerts/Simple";
import SeoFact from "../../components/SeoFact";
import useCountImages from "../../hooks/useCountImages";
import { getPercentImagesOptimizedAlt } from "../../helpers/getPercentImagesOptimizedAlt";

//@ts-ignore
const { __ } = wp.i18n;

const steps = [
	{
		key: 0,
		id: "01",
		name: __("Discover ImageSEO", "imageseo"),
		description: __(
			"Let's discover our extension in a few steps",
			"imageseo"
		),
	},
	{
		key: 1,
		id: "02",
		name: __("Bulk Optimization", "imageseo"),
		description: __("Rename your files and fill in your Alts", "imageseo"),
	},
	{
		key: 2,
		id: "03",
		name: __("Social Media", "imageseo"),
		description: __(
			"Create unique images for your social networks",
			"imageseo"
		),
	},
	{
		key: 3,
		id: "04",
		name: __("Register", "imageseo"),
		description: __("Create your account. It's free", "imageseo"),
	},
];

function classNames(...classes) {
	return classes.filter(Boolean).join(" ");
}

const Steps = ({ currentStep, setStep }) => {
	return (
		<div className="lg:border-t lg:border-b lg:border-gray-200">
			<nav aria-label="Progress">
				<ol className="rounded-md overflow-hidden lg:flex lg:border-l lg:border-r lg:border-gray-200 lg:rounded-none">
					{steps.map((step, stepIdx) => (
						<li
							key={step.id}
							className="relative overflow-hidden lg:flex-1"
						>
							<div
								className={classNames(
									"border border-gray-200 overflow-hidden lg:border-0"
								)}
							>
								{step.key < currentStep ? (
									<div
										className="group cursor-pointer"
										onClick={() => setStep(step.key)}
									>
										<span
											className="absolute top-0 left-0 w-1 h-full bg-transparent group-hover:bg-gray-200 lg:w-full lg:h-1 lg:bottom-0 lg:top-auto"
											aria-hidden="true"
										/>
										<span
											className={classNames(
												stepIdx !== 0 ? "lg:pl-9" : "",
												"px-6 py-5 flex items-start text-sm font-medium"
											)}
										>
											<span className="flex-shrink-0">
												<span className="w-10 h-10 flex items-center justify-center bg-indigo-600 rounded-full">
													<CheckIcon
														className="w-6 h-6 text-white"
														aria-hidden="true"
													/>
												</span>
											</span>
											<span className="mt-0.5 ml-4 min-w-0 flex flex-col">
												<span className="text-xs font-semibold tracking-wide uppercase">
													{step.name}
												</span>
												<span className="text-sm font-medium text-gray-500">
													{step.description}
												</span>
											</span>
										</span>
									</div>
								) : step.key === currentStep ? (
									<div
										aria-current="step"
										className="cursor-pointer"
										onClick={() => setStep(step.key)}
									>
										<span
											className="absolute top-0 left-0 w-1 h-full bg-indigo-600 lg:w-full lg:h-1 lg:bottom-0 lg:top-auto"
											aria-hidden="true"
										/>
										<span
											className={classNames(
												stepIdx !== 0 ? "lg:pl-9" : "",
												"px-6 py-5 flex items-start text-sm font-medium"
											)}
										>
											<span className="flex-shrink-0">
												<span className="w-10 h-10 flex items-center justify-center border-2 border-indigo-600 rounded-full">
													<span className="text-indigo-600">
														{step.id}
													</span>
												</span>
											</span>
											<span className="mt-0.5 ml-4 min-w-0 flex flex-col">
												<span className="text-xs font-semibold text-indigo-600 tracking-wide uppercase">
													{step.name}
												</span>
												<span className="text-sm font-medium text-gray-500">
													{step.description}
												</span>
											</span>
										</span>
									</div>
								) : (
									<div
										className="group cursor-pointer"
										onClick={() => setStep(step.key)}
									>
										<span
											className="absolute top-0 left-0 w-1 h-full bg-transparent group-hover:bg-gray-200 lg:w-full lg:h-1 lg:bottom-0 lg:top-auto"
											aria-hidden="true"
										/>
										<span
											className={classNames(
												stepIdx !== 0 ? "lg:pl-9" : "",
												"px-6 py-5 flex items-start text-sm font-medium"
											)}
										>
											<span className="flex-shrink-0">
												<span className="w-10 h-10 flex items-center justify-center border-2 border-gray-300 rounded-full">
													<span className="text-gray-500">
														{step.id}
													</span>
												</span>
											</span>
											<span className="mt-0.5 ml-4 min-w-0 flex flex-col">
												<span className="text-xs font-semibold text-gray-500 tracking-wide uppercase">
													{step.name}
												</span>
												<span className="text-sm font-medium text-gray-500">
													{step.description}
												</span>
											</span>
										</span>
									</div>
								)}

								{stepIdx !== 0 ? (
									<>
										<div
											className="hidden absolute top-0 left-0 w-3 inset-0 lg:block"
											aria-hidden="true"
										>
											<svg
												className="h-full w-full text-gray-300"
												viewBox="0 0 12 82"
												fill="none"
												preserveAspectRatio="none"
											>
												<path
													d="M0.5 0V31L10.5 41L0.5 51V82"
													stroke="currentcolor"
													vectorEffect="non-scaling-stroke"
												/>
											</svg>
										</div>
									</>
								) : null}
							</div>
						</li>
					))}
				</ol>
			</nav>
		</div>
	);
};

function WizardWindow() {
	const [step, setStep] = useState(0);
	const data = useCountImages();

	if (isNull(data)) {
		return <>{__("Data being loaded...", "imageseo")}</>;
	}

	const percentOptimized = getPercentImagesOptimizedAlt(
		Number(data.total_images),
		Number(data.total_images_no_alt)
	);

	const missPercent =
		Number(data.total_images) === 0
			? 0
			: (100 - percentOptimized).toFixed(2);

	return (
		<>
			<Steps currentStep={step} setStep={setStep} />
			<div className="max-w-4xl mx-auto my-16 ">
				<div className="bg-white overflow-hidden shadow sm:rounded-lg">
					<div className="px-4 py-5 sm:p-6">
						<div className="border-b pb-4 text-center">
							<h2 className="font-bold text-indigo-500 text-xl">
								{find(steps, { key: step }).name}
							</h2>
							<p className="text-gray-500 text-base">
								{find(steps, { key: step }).description}
							</p>
						</div>
						<div className="py-8 px-12 text-base">
							{step === 0 && (
								<>
									<SeoFact />
									<p className="text-base text-center mt-4 leading-6 font-medium text-gray-900">
										{__("There are", "imageseo")}{" "}
										{data.total_images}{" "}
										{__(
											"images in your library.",
											"imageseo"
										)}
									</p>
									<div className="text-center">
										{percentOptimized < 99 &&
											//@ts-ignore
											!isNaN(missPercent) && (
												<p className="text-base mt-2">
													{__(
														`Did you know that`,
														"imageseo"
													)}{" "}
													<span className="text-blue-500 font-bold">
														{missPercent}%
													</span>{" "}
													{__(
														`alternative texts are missing ?`,
														"imageseo"
													)}
												</p>
											)}
									</div>
									<div
										className="w-1/2  mx-auto flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mt-4 cursor-pointer"
										onClick={() => setStep(1)}
									>
										Continue
									</div>
									<div className="text-center mt-8">
										<a
											//@ts-ignore
											href={IMAGESEO_DATA.SETTINGS_URL}
											className="text-sm text-gray-500"
										>
											{__(
												"Skip setup configuration",
												"imageseo"
											)}
										</a>
									</div>
								</>
							)}
							{step === 1 && (
								<>
									<p className="mb-8 text-center text-base">
										We have developed a{" "}
										<strong>bulk optimisation</strong>{" "}
										system that allows you to run the
										process across your entire media
										library.
									</p>
									<div className="flex">
										<div className="mb-4 flex-shrink-0 sm:mb-0 sm:mr-4">
											<img
												className="h-32 w-32 object-cover border border-gray-300 bg-white text-gray-300"
												src={getLinkImage(
													"wizard/DSC001.jpeg"
												)}
											/>
										</div>
										<div>
											<p className="text-base font-bold">
												This picture shows a Tesla
											</p>
											<p className="mt-1 text-sm">
												Your current file name:
												DSC001.jpeg and your alternative
												text is not filled in.
											</p>
											<p className="mt-1 text-sm">
												Filename by Image SEO:{" "}
												<strong>
													tesla-model-s.jpeg
												</strong>
											</p>
											<p className="mt-1 text-sm">
												Alt Text by Image SEO:{" "}
												<strong>
													Tesla, Inc. - Tesla
												</strong>
											</p>
										</div>
									</div>
									<div className="flex">
										<div className="mb-4 flex-shrink-0 sm:mb-0 sm:mr-4">
											<img
												className="h-32 w-32 object-cover border border-gray-300 bg-white text-gray-300"
												src={getLinkImage(
													"wizard/DSC002.jpeg"
												)}
											/>
										</div>
										<div>
											<p className="text-base font-bold">
												This picture shows a Effeil
												Tower
											</p>
											<p className="mt-1 text-sm">
												Your current file name:
												DSC002.jpeg and your alternative
												text is not filled in.
											</p>
											<p className="mt-1 text-sm">
												Filename by Image SEO:{" "}
												<strong>
													eiffel-tower.jpeg
												</strong>
											</p>
											<p className="mt-1 text-sm">
												Alt Text by Image SEO:{" "}
												<strong>
													{" "}
													Eiffel Tower - Tower
												</strong>
											</p>
										</div>
									</div>
									<div className="flex">
										<div className="mb-4 flex-shrink-0 sm:mb-0 sm:mr-4">
											<img
												className="h-32 w-32 object-cover border border-gray-300 bg-white text-gray-300"
												src={getLinkImage(
													"wizard/DSC003.jpeg"
												)}
											/>
										</div>
										<div>
											<p className="text-base font-bold">
												This picture shows Central Park
												(NYC)
											</p>
											<p className="mt-1 text-sm">
												Your current file name:
												DSC003.jpeg and your alternative
												text is not filled in.
											</p>
											<p className="mt-1 text-sm">
												Filename by Image SEO:{" "}
												<strong>
													central-park.jpeg
												</strong>
											</p>
											<p className="mt-1 text-sm">
												Alt Text by Image SEO:{" "}
												<strong>
													Central Park - Central Park
													Tower
												</strong>
											</p>
										</div>
									</div>
									<p className="mt-4 text-center text-base">
										You can download these images to test
										them directly in your media library
									</p>
									<div
										className="w-1/2  mx-auto flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mt-4 cursor-pointer"
										onClick={() => setStep(2)}
									>
										Continue
									</div>
									<div className="text-center mt-8">
										<a
											//@ts-ignore
											href={IMAGESEO_DATA.SETTINGS_URL}
											className="text-sm text-gray-500"
										>
											{__(
												"Skip setup configuration",
												"imageseo"
											)}
										</a>
									</div>
								</>
							)}
							{step === 2 && (
								<>
									<p className="mb-8 text-center text-base">
										You can configure the appearance of your
										images for social networks.
									</p>
									<img
										src={getLinkImage(
											"wizard/social-media-config.png"
										)}
										className="mx-auto w-9/12 my-8"
									/>
									<img
										src={getLinkImage(
											"wizard/social-media.png"
										)}
										className="mx-auto w-9/12 my-8"
									/>
									<div
										className="w-1/2  mx-auto flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mt-4 cursor-pointer"
										onClick={() => setStep(3)}
									>
										Continue
									</div>
									<div className="text-center mt-8">
										<a
											//@ts-ignore
											href={IMAGESEO_DATA.SETTINGS_URL}
											className="text-sm text-gray-500"
										>
											{__(
												"Skip setup configuration",
												"imageseo"
											)}
										</a>
									</div>
								</>
							)}
							{step === 3 && (
								<>
									<div className="max-w-2xl mx-auto">
										<FormRegister
											afterSubmit={() => {
												window.location.href =
													//@ts-ignore
													IMAGESEO_DATA.SETTINGS_URL;
											}}
										/>
									</div>
								</>
							)}
						</div>
					</div>
				</div>
			</div>
		</>
	);
}

export default WizardWindow;
