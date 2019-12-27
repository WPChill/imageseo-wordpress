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

function getPercentBulk(state) {
	const countCurrent = Object.values(state.attachments).length;
	if (countCurrent === 0) {
		return 0;
	}

	return (countCurrent * 100) / state.allIds.length;
}

export { getAttachmentIdWithProcess, canLaunchBulk, getPercentBulk };
