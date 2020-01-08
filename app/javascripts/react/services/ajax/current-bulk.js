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
		body: formData
	});

	return await response.json();
};

const deleteCurrentBulk = async () => {
	const formData = new FormData();

	formData.append("action", "imageseo_delete_current_bulk");

	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData
	});

	return await response.json();
};

export { saveCurrentBulk, deleteCurrentBulk };
