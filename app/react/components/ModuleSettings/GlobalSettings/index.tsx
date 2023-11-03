import {filter, find, get, groupBy} from "lodash";
import React, {useContext, useState, useEffect} from "react";
import Swal from "sweetalert2";
import {SVGLoader} from "../../../svg/Loader";
import {AlertSimple, IconsAlert} from "../../Alerts/Simple";

import {Toggle} from "../../Toggle";

//@ts-ignore
const {__} = wp.i18n;

function /**/GlobalSettings() {
	const [loading, setLoading] = useState(false);
	// Get options from th database using REST API and fetch
	const dbOpts = fetch('https://raldea.ro/wp-json/imageseo/v1/settings_db_options')
		.then(response => {
			if (!response.ok) {
				throw new Error('Network response was not ok');
			}
			return response.json();
		});

	//@ts-ignore
	const [options, setOptions] = useState(dbOpts);

	const handleClickAltWriteUpload = () => {

		setOptions({
			...options,
			active_alt_write_upload: !get(
				options,
				"active_alt_write_upload",
				true
			),
		});

	};

	const handleClickRenameWriteUpload = () => {
		setOptions({
			...options,
			active_rename_write_upload: !get(
				options,
				"active_rename_write_upload",
				true
			),
		});
	};

	const handleSubmit = async () => {
		setLoading(true);
		const formData = new FormData();

		formData.append("action", "imageseo_save_global_settings");
		formData.append(
			"_wpnonce",
			document
				.querySelector("#_nonce_imageseo_save_global_settings")
				.getAttribute("value")
		);
		formData.append(
			"active_alt_write_upload",
			options.active_alt_write_upload
		);
		formData.append(
			"active_rename_write_upload",
			options.active_rename_write_upload
		);
		formData.append("default_language_ia", options.default_language_ia);

		//@ts-ignore
		const response = await fetch(IMAGESEO_DATA.ADMIN_AJAX, {
			method: "POST",
			body: formData,
		});

		await response.json();
		setLoading(false);
		Swal.fire({
			title: __("Great!", "imageseo"),
			text: __("Global settings saved!", "imageseo"),
			icon: "success",
			confirmButtonText: __("Close", "imageseo"),
		});

	};

	return (
		<>
			<div className="flex">
				<Toggle
					onClick={handleClickAltWriteUpload}
					active={get(options, "active_alt_write_upload", true)}
				/>
				<div className="ml-4">
					<label onClick={handleClickAltWriteUpload}>
						<p className="text-sm font-bold">
							{__(
								"Automatically fill out ALT Texts when you upload an image",
								"imageseo"
							)}
						</p>
					</label>
					<p>
						{__(
							"If you tick this box, the plugin will automatically write an alternative to the images you will upload.",
							"imageseo"
						)}
					</p>
				</div>
			</div>
			<div className=" mt-6">
				<div className="flex">
					<Toggle
						onClick={handleClickRenameWriteUpload}
						active={get(
							options,
							"active_rename_write_upload",
							true
						)}
					/>
					<div className="ml-4">
						<label
							className="flex"
							onClick={handleClickRenameWriteUpload}
						>
							<p className="text-sm font-bold">
								{__(
									"Automatically rename your files when you upload a media",
									"imageseo"
								)}
							</p>
						</label>
						<p>
							{__(
								"If you tick this box, the plugin will automatically rewrite with SEO friendly content the name of the images you will upload.",
								"imageseo"
							)}
						</p>
					</div>
				</div>
			</div>
			<div className="my-4">
				<label className="font-bold text-sm">
					{__("Language", "imageseo")}
				</label>
				<select
					id="language"
					className="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-gray-900 focus:border-gray-900 text-sm"
					value={get(options, "default_language_ia", "en")}
					onChange={(event) => {
						setOptions({
							...options,
							default_language_ia: event.target.value,
						});
					}}
				>
					{
						//@ts-ignore
						IMAGESEO_DATA.LANGUAGES.map((language) => {
							return (
								<option
									key={language.code}
									value={language.code}
								>
									{language.name}
								</option>
							);
						})
					}
				</select>
				<p className="text-sm font-bold mt-4">
					{__(
						"In which language should we write your filenames and alternative texts.",
						"imageseo"
					)}
				</p>
			</div>

			<button
				className="mb-4 w-full inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 max-w-2xl"
				onClick={handleSubmit}
			>
				{loading && <SVGLoader className="mr-2"/>}
				{__("Save global settings", "imageseo")}
			</button>

			<AlertSimple icon={IconsAlert.INFORMATION} blue>
				{__(
					"You will consume one credit for each image optimized.",
					"imageseo"
				)}
			</AlertSimple>
		</>
	);
}

export default GlobalSettings;
