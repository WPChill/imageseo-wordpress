import { pick } from "lodash";

const saveCurrentBulk = async (settings, state, countOptimized) => {
	const formData = new FormData();

	formData.append("action", "imageseo_save_current_bulk");
	formData.append("countOptimized", countOptimized);
	formData.append("settings", JSON.stringify(settings));
	formData.append(
		"state",
		JSON.stringify(
			pick(state, ["currentProcess", "allIds", "allIdsOptimized"])
		)
	);

	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData,
	});

	return await response.json();
};

const deleteCurrentBulk = async () => {
	const formData = new FormData();

	formData.append("action", "imageseo_delete_current_bulk");

	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData,
	});

	return await response.json();
};

const startBulkProcess = async (
	data,
	{
		formatAlt,
		formatAltCustom,
		language,
		optimizeAlt,
		optimizeFile,
		wantValidateResult,
	}
) => {
	const formData = new FormData();

	formData.append("action", "imageseo_dispatch_bulk");
	formData.append("data", data);
	formData.append("formatAlt", formatAlt);
	formData.append("formatAltCustom", formatAltCustom);
	formData.append("language", language);
	formData.append("optimizeAlt", optimizeAlt);
	formData.append("optimizeFile", optimizeFile);
	formData.append("wantValidateResult", wantValidateResult);

	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData,
	});

	return await response.json();
};

const getCurrentProcessDispatch = async () => {
	const formData = new FormData();

	formData.append("action", "imageseo_get_current_dispatch");
	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData,
	});

	return await response.json();
};

export {
	saveCurrentBulk,
	deleteCurrentBulk,
	startBulkProcess,
	getCurrentProcessDispatch,
};
