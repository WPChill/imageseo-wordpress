import React, { useState, useContext } from "react";
import Swal from "sweetalert2";
import classNames from "classnames";
import { isEmpty, isNil, find, get } from "lodash";

//@ts-ignore
const { __ } = wp.i18n;

import useFormData from "../../../hooks/useFormData";
import useErrorsData from "../../../hooks/useErrorsData";
import getLinkImage from "../../../helpers/getLinkImage";
import { PageContext } from "../../../contexts/PageContext";
import { SVGEye } from "../../../svg/Eye";
import { SVGEyeClosed } from "../../../svg/EyeClosed";
import getSiteLink from "../../../helpers/getSiteLink";
import { SVGLoader } from "../../../svg/Loader";
import { register } from "../../../services/ajax/user";

function FormRegister() {
	const {
		actions: { setApiKey },
	} = useContext(PageContext);
	const [loading, setLoading] = useState(false);

	const [errors, actions] = useErrorsData({
		firstname: {
			code: null,
			error: false,
		},
		lastname: {
			code: null,
			error: false,
		},
		email: {
			code: null,
			error: false,
		},
		password: {
			code: null,
			error: false,
		},
		terms: {
			code: null,
			error: false,
		},
		not_available: {
			code: null,
			error: false,
		},
		missed_parameters: {
			code: null,
			error: false,
		},
	});

	const [values, handleInputChange] = useFormData({
		firstname: "",
		lastname: "",
		email: "",
		password: "",
		terms: [],
		newsletters: [],
	});

	const [viewPassword, setViewPassword] = useState(false);

	const handleOnSubmit = async (e) => {
		e.preventDefault();
		setLoading(true);

		let prepareErrors = {
			firstname: {
				code: isEmpty(values.firstname) ? "empty" : null,
				error: isEmpty(values.firstname),
			},
			lastname: {
				code: isEmpty(values.lastname) ? "empty" : null,
				error: isEmpty(values.lastname),
			},
			email: {
				code: isEmpty(values.email) ? "empty" : null,
				error: isEmpty(values.email),
			},
			password: {
				code: isEmpty(values.password) ? "empty" : null,
				error: isEmpty(values.password),
			},
			terms: {
				code: values.terms.length === 0 ? "empty" : null,
				error: values.terms.length === 0,
			},
			not_available: {
				code: null,
				error: false,
			},
			missed_parameters: {
				code: null,
				error: false,
			},
		};
		actions.setErrors(prepareErrors);

		if (!actions.allIsValid(prepareErrors)) {
			setLoading(false);
			return;
		}

		const { data } = await register({
			...values,
			newsletters: !isEmpty(values.newsletters),
		});

		if (!get(data, "user", false)) {
			if (get(data, "code", false) === "missed_parameters") {
				prepareErrors = {
					...prepareErrors,
					missed_parameters: {
						...prepareErrors.missed_parameters,
						code: "missed_parameters",
						error: true,
					},
				};
				setLoading(false);
				actions.setErrors(prepareErrors);
				return;
			} else if (get(data, "code", false) === "not_available") {
				prepareErrors = {
					...prepareErrors,
					not_available: {
						...prepareErrors.not_available,
						code: "not_available",
						error: true,
					},
				};
				setLoading(false);
				actions.setErrors(prepareErrors);

				Swal.fire({
					title: __("Error!", "imageseo"),
					html: `This email is not available`,
					icon: "error",
					confirmButtonText: __("Close", "imageseo"),
				});
				return;
			}
		}

		setLoading(false);

		setApiKey(get(data, "user.project_create.api_key", null));
		if (get(data, "code", null)) {
			Swal.fire({
				title: __("Error!", "imageseo"),
				html: `Registration did not work, <a href='mailto:support@imageseo.io'>please contact support </a>`,
				icon: "error",
				confirmButtonText: __("Close", "imageseo"),
			});
		} else {
			Swal.fire({
				title: __("Great!", "imageseo"),
				text: __(
					"You are registered and we have configured your API key",
					"imageseo"
				),
				icon: "success",
				confirmButtonText: __("Close", "imageseo"),
			});
		}
	};

	return (
		<form onSubmit={handleOnSubmit}>
			<div className="flex mb-6">
				<div className="flex-1 mr-8">
					<label
						htmlFor="firstname"
						className="block text-sm font-medium text-gray-700 mb-1"
					>
						{__("First name", "imageseo")}
					</label>
					<input
						id="firstname"
						type="text"
						name="firstname"
						className="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
						value={values.firstname}
						onChange={handleInputChange}
						placeholder="John"
					/>
					{errors.firstname.error && (
						<p className="flex items-center my-2">
							<img
								src={getLinkImage("warning.svg")}
								className="mr-2"
							/>
							{__(
								"It's nicer if we have your first name :)",
								"imageseo"
							)}
						</p>
					)}
				</div>
				<div className="flex-1">
					<label
						htmlFor="lastname"
						className="block text-sm font-medium text-gray-700 mb-1"
					>
						{__("Last name", "imageseo")}
					</label>
					<input
						id="lastname"
						type="text"
						name="lastname"
						className="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
						value={values.lastname}
						onChange={handleInputChange}
						placeholder="Doe"
					/>
					{errors.lastname.error && (
						<p className="flex items-center my-2">
							<img
								src={getLinkImage("warning.svg")}
								className="mr-2"
							/>
							{__("You must enter your name", "imageseo")}
						</p>
					)}
				</div>
			</div>
			<div className="flex mb-6">
				<div className="flex-1 mr-8">
					<label
						htmlFor="email"
						className="block text-sm font-medium text-gray-700 mb-1"
					>
						Email
					</label>
					<input
						id="email"
						type="email"
						name="email"
						className="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
						value={values.email}
						onChange={handleInputChange}
						placeholder="john.doe@gmail.com"
					/>
					{errors.email.error && (
						<p className="flex items-center my-2">
							<img
								src={getLinkImage("warning.svg")}
								className="mr-2"
							/>
							{__("No email, no chocolate.", "imageseo")}
						</p>
					)}
				</div>
				<div className="flex-1">
					<label
						htmlFor="password"
						className="block text-sm font-medium text-gray-700"
					>
						Password
					</label>
					<div className="mt-1 flex rounded-md shadow-sm">
						<div className="relative flex items-stretch flex-grow focus-within:z-10">
							<input
								type={viewPassword ? "text" : "password"}
								value={values.password}
								onChange={handleInputChange}
								name="password"
								id="password"
								className="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-l-md"
							/>
						</div>
						<div className="-ml-px relative inline-flex items-center px-2 py-1 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 cursor-pointer">
							<div
								onClick={(e) => setViewPassword(true)}
								style={{
									display: viewPassword ? "none" : "inline",
								}}
							>
								<SVGEye className="h-4 w-4" />
							</div>
							<div
								onClick={(e) => setViewPassword(false)}
								style={{
									display: viewPassword ? "inline" : "none",
								}}
							>
								<SVGEyeClosed className="h-4 w-4" />
							</div>
						</div>
					</div>
					{errors.password.error && (
						<p className="flex items-center my-2">
							<img
								src={getLinkImage("warning.svg")}
								className="mr-2"
							/>
							{__("You need to have a password.", "imageseo")}
						</p>
					)}
				</div>
			</div>
			<div className="flex items-center mb-4">
				<input
					id="terms"
					name="terms"
					type="checkbox"
					checked={!isEmpty(values.terms)}
					value="terms"
					onChange={handleInputChange}
					className="h-4 w-4 focus:ring-indigo-500 border-gray-300 rounded text-white"
				/>
				<label
					htmlFor="terms"
					className="ml-2 block text-sm text-gray-900"
				>
					{__("I agree to", "imageseo")}{" "}
					<a
						href={`${getSiteLink("/terms-conditions")}`}
						target="_blank"
						className="underline text-blue-500"
					>
						{__("ImageSEO's Terms of Service", "imageseo")}
					</a>
				</label>
				{errors.terms.error && (
					<p className="flex items-center my-2">
						<img
							src={getLinkImage("warning.svg")}
							className="mr-2"
						/>
						{__(
							"You must accept the terms to validate your registration.",
							"imageseo"
						)}
					</p>
				)}
			</div>

			<div className="flex items-center">
				<input
					id="newsletter"
					name="newsletter"
					type="checkbox"
					checked={!isEmpty(values.newsletter)}
					value="terms"
					onChange={handleInputChange}
					className="h-4 w-4 focus:ring-indigo-500 border-gray-300 rounded text-white"
				/>
				<label
					htmlFor="newsletter"
					className="ml-2 block text-sm text-gray-900"
				>
					{__(
						"News and features updates, as well as occasional company announcements.",
						"imageseo"
					)}
				</label>
			</div>

			{errors.missed_parameters.error && (
				<p className="flex items-center my-2">
					<img src={getLinkImage("warning.svg")} className="mr-2" />
					{__("The registration form is not complete.", "imageseo")}
				</p>
			)}
			{errors.not_available.error && (
				<p className="flex items-center my-2">
					<img src={getLinkImage("warning.svg")} className="mr-2" />
					{__("This email is not available", "imageseo")}
				</p>
			)}

			<button
				className="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mt-4"
				type="submit"
			>
				{loading && <SVGLoader className="mr-2" />}
				{__("Register", "imageseo")}
			</button>
		</form>
	);
}

export default FormRegister;
