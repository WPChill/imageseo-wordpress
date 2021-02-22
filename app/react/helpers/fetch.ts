import { first } from "lodash";
export const fetchWithToken = (
	{ method = "GET", token } // POST for admin ajax
) =>
	async function (...args) {
		const url = first(args);

		const res = await fetch(url, {
			method,
			headers: {
				"Content-Type": "application/json",
				Authorization: `${token}`,
			},
		});

		try {
			return await res.json();
		} catch (error) {
			return null;
		}
	};

export const fetchAdminPost = () =>
	async function (...args) {
		const url = first(args);
		const formData = new FormData();
		formData.append("action", "wp_health_proxy");
		formData.append(
			"_wpnonce",
			document
				.querySelector("#_nonce_wp_health_proxy")
				.getAttribute("value")
		);

		const res = await fetch(url, {
			method: "POST",
			body: formData,
		});

		try {
			return await res.json();
		} catch (error) {
			return null;
		}
	};

export const fetchWithoutToken = async function (...args) {
	//@ts-ignore
	const res = await fetch(...args);
	return await res.json();
};

export default {
	fetchWithoutToken,
	fetchWithToken,
};
