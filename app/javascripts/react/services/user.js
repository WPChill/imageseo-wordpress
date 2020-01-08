import { isNaN } from "lodash";

function getImagesLeft(user) {
	try {
		const number =
			user.bonus_stock_images +
			user.plan.limit_images -
			user.current_request_images;

		if (number < 0) {
			return 0;
		}

		return number;
	} catch (error) {
		return 0;
	}
}

function getImagesAvailable(user) {
	return user.plan.limit_images;
}

function getPercentUse(user) {
	try {
		const leftPercent =
			(getImagesLeft(user) * 100) / user.plan.limit_images;
		const percent = 100 - leftPercent.toFixed(2);
		if (isNaN(percent)) {
			return 0;
		}

		return percent;
	} catch (error) {
		return 0;
	}
}

function hasLimitExcedeed(user) {
	try {
		const imagesLeft = getImagesLeft(user);
		if (imagesLeft === null) {
			return false;
		}

		return imagesLeft <= 0 && user.bonus_stock_images <= 0;
	} catch (error) {
		return true;
	}
}

export { hasLimitExcedeed, getPercentUse, getImagesAvailable, getImagesLeft };
