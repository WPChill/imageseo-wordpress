import { get, isNull } from "lodash";

function canLaunchBulk(settings) {
	return settings.optimizeFile || settings.optimizeAlt;
}

function getAttachmentIdWithProcess(state) {
	let currentProcess = get(state, "currentProcess", 0);
	if (isNull(currentProcess)) {
		currentProcess = 0;
	}

	return get(state, ["allIds", currentProcess], null);
}

function getPercentBulk(currentProcess) {
	const countCurrent =
		Number(currentProcess.bulk_process.current_index_image) + 1;
	if (countCurrent === 0) {
		return 0;
	}

	return (countCurrent * 100) / currentProcess.bulk_process.id_images.length;
}

export { getAttachmentIdWithProcess, canLaunchBulk, getPercentBulk };
