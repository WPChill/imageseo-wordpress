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
	//@ts-ignore
	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData,
	});

	return await response.json();
};

const deleteCurrentBulk = async () => {
	const formData = new FormData();

	formData.append("action", "imageseo_delete_current_bulk");
	//@ts-ignore
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
	}
) => {
	const formData = new FormData();

	formData.append("action", "imageseo_start_bulk");
	formData.append("data", data);
	formData.append("formatAlt", formatAlt);
	formData.append("formatAltCustom", formatAltCustom);
	formData.append("language", language);
	formData.append("optimizeAlt", optimizeAlt);
	formData.append("optimizeFile", optimizeFile);
	//@ts-ignore
	formData.append("wantValidateResult", false);
	//@ts-ignore
	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData,
	});

	return await response.json();
};

export const restartBulkProcess = async () => {
	const formData = new FormData();

	formData.append("action", "imageseo_restart_bulk");
	//@ts-ignore
	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData,
	});

	return await response.json();
};

export const getPreviewBulk = async () => {
	const formData = new FormData();

	formData.append("action", "imageseo_get_preview_bulk");
	//@ts-ignore
	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData,
	});

	return await response.json();
};
export const getCurrentBulk = async () => {
	const formData = new FormData();

	formData.append("action", "imageseo_get_current_bulk");
	//@ts-ignore
	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData,
	});

	return await response.json();
};

const getCurrentProcessDispatch = async () => {
	const formData = new FormData();

	formData.append("action", "imageseo_get_current_dispatch");
	//@ts-ignore
	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData,
	});

	return await response.json();
};

const stopCurrentProcess = async () => {
	const formData = new FormData();

	formData.append("action", "imageseo_stop_bulk");
	//@ts-ignore
	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData,
	});

	return await response.json();
};

const finishCurrentProcess = async () => {
	const formData = new FormData();

	formData.append("action", "imageseo_finish_bulk");
	//@ts-ignore
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
	stopCurrentProcess,
	finishCurrentProcess,
};
