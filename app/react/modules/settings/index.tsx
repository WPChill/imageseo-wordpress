import React from "react";
import GlobalSettings from "../../components/ModuleSettings/GlobalSettings";
import SocialSettings from "../../components/ModuleSettings/SocialSettings";

//@ts-ignore
const { __ } = wp.i18n;

const Settings = () => {
	return (
		<>
			<div className="border rounded-md overflow-hidden">
				<div className="bg-white p-6">
					<h2 className="text-lg leading-6 font-medium text-blue-900 mb-4">
						{__("Settings - On-upload optimization", "imageseo")}
					</h2>
					<GlobalSettings />
				</div>
			</div>
			<div className="border rounded-md overflow-hidden mt-6">
				<div className="bg-white p-6">
					<h2 className="text-lg leading-6 font-medium text-blue-900 mb-4">
						{__(
							"Settings – Social Media Cards Generator",
							"imageseo"
						)}
					</h2>
					<SocialSettings />
				</div>
			</div>
		</>
	);
};

export default Settings;
