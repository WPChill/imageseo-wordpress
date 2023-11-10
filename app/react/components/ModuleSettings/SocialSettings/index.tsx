import { filter, find, get, groupBy, isNil } from "lodash";
import React, { useContext, useState } from "react";
import Swal from "sweetalert2";
import { SVGLoader } from "../../../svg/Loader";
import { AlertSimple, IconsAlert } from "../../Alerts/Simple";

import { Toggle } from "../../Toggle";

//@ts-ignore
const { __ } = wp.i18n;

function SocialSettings() {
	const [loading, setLoading] = useState(false);
	//@ts-ignore
	const [options, setOptions] = useState(IMAGESEO_DATA.OPTIONS);

	const handleClickSocial = (name) => {
		let socials = isNil(options.social_media_post_types)
			? []
			: options.social_media_post_types;
		if (socials.indexOf(name) >= 0) {
			socials = filter(socials, (item) => item !== name);
		} else {
			socials = [...socials, name];
		}
		setOptions({
			...options,
			social_media_post_types: socials,
		});
	};

	const handleSubmit = async () => {
		setLoading(true);
		const formData = new FormData();

		formData.append("action", "imageseo_save_social_settings");
		formData.append(
			"_wpnonce",
			document
				.querySelector("#_nonce_imageseo_save_social_settings")
				.getAttribute("value")
		);
		formData.append(
			"social_media_post_types",
			options.social_media_post_types
		);

		//@ts-ignore
		const response = await fetch(IMAGESEO_DATA.ADMIN_AJAX, {
			method: "POST",
			body: formData,
		});

		await response.json();
		setLoading(false);

		Swal.fire({
			title: __("Great!", "imageseo"),
			text: __("Social settings saved!", "imageseo"),
			icon: "success",
			confirmButtonText: __("Close", "imageseo"),
		});
	};

	const listSocials = get(options, "social_media_post_types", []);

	const socials = isNil(listSocials) ? [] : listSocials;

	return (
		<>
			<p className="text-sm mb-4">
				{__(
					"Do you want to enable the automatic generation of Social Media Card for your:",
					"imageseo"
				)}
			</p>

			{
				//@ts-ignore
				Object.values(IMAGESEO_DATA.SOCIAL_POST_TYPES).map(
					(postType, key) => {
						return (
							<div className="mb-4" key={key}>
								<label
									//@ts-ignore
									htmlFor={postType.name}
									className="flex items-center text-sm font-bold"
									onClick={() =>
										//@ts-ignore
										handleClickSocial(postType.name)
									}
								>
									<Toggle
										active={
											socials.indexOf(
												//@ts-ignore
												postType.name
											) >= 0
										}
									/>

									<span className="ml-2">
										{
											//@ts-ignore
											postType.label
										}
									</span>
								</label>
							</div>
						);
					}
				)
			}
			<p className="mt-2 mb-4 text-sm">
				{__(
					"Please make sure that you have created a template and that your post and or page have a picture!",
					"imageseo"
				)}
			</p>

			<button
				className="mb-4 w-full inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 max-w-2xl"
				onClick={handleSubmit}
			>
				{loading && <SVGLoader className="mr-2" />}
				{__("Save social settings", "imageseo")}
			</button>

			<AlertSimple icon={IconsAlert.INFORMATION} blue>
				{__(
					"You will consume one credit by Socia Media Cards created (1 page = 1 Social media card working on Twitter, Facebook and LinkedIn).",
					"imageseo"
				)}
			</AlertSimple>
		</>
	);
}

export default SocialSettings;
