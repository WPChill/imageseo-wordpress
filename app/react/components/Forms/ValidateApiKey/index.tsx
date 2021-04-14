import React, { useState, useContext, useEffect } from "react";
import Swal from "sweetalert2";
import { isEmpty, get, isNull } from "lodash";

//@ts-ignore
const { __ } = wp.i18n;

import { PageContext } from "../../../contexts/PageContext";
import { SVGLoader } from "../../../svg/Loader";
import { validateApiKey } from "../../../services/ajax/user";

function FormValidateApiKey() {
	const {
		values: { apiKey },
		actions: { setApiKey },
	} = useContext(PageContext);
	const [loading, setLoading] = useState(false);

	const [apiKeyLocal, setApiKeyLocal] = useState(apiKey);

	useEffect(() => {
		setApiKeyLocal(apiKey);
	}, [apiKey]);

	const handleOnSubmit = async (e) => {
		e.preventDefault();
		setLoading(true);
		const { success, data } = await validateApiKey(apiKeyLocal);
		setLoading(false);

		if (
			!success ||
			isNull(data.user) ||
			get(data, "user.code", null) === "not_exist"
		) {
			Swal.fire({
				title: __("Error!", "imageseo"),
				text: __("Your API key is not valid", "imageseo"),
				icon: "error",
				confirmButtonText: __("Close", "imageseo"),
			});
			return;
		}

		setApiKey(apiKeyLocal);
		Swal.fire({
			title: __("Great!", "imageseo"),
			text: __("Your API key is registered", "imageseo"),
			icon: "success",
			confirmButtonText: __("Close", "imageseo"),
		});
	};

	return (
		<form onSubmit={handleOnSubmit}>
			<label
				htmlFor="api_key"
				className="block text-sm font-medium text-gray-700 mb-1"
			>
				{__("Enter your Api Key", "imageseo")}
			</label>
			<input
				id="api_key"
				type={!isEmpty(apiKey) ? "password" : "text"}
				name="api_key"
				className="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
				value={apiKeyLocal}
				onChange={(e) => setApiKeyLocal(e.target.value)}
				placeholder="Your API KEY - ****"
			/>

			<button
				className="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mt-4"
				type="submit"
			>
				{loading && <SVGLoader className="mr-2" />}
				{__("Validate your API KEY", "imageseo")}
			</button>
		</form>
	);
}

export default FormValidateApiKey;
