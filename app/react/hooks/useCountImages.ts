
import { get } from "lodash";
import React, { useEffect, useState } from "react";

interface Data {
	total_images: number,
	total_images_no_alt: number
}

const useCountImages = (): Data | null => {

	const [data, setData] = useState(null)

	useEffect(() => {
		const fetchData = async () => {
			const formData = new FormData();
			formData.append("action", "imageseo_get_count_images");

			//@ts-ignore
			const response = await fetch(IMAGESEO_DATA.ADMIN_AJAX, {
				method: "POST",
				body: formData,
			});

			const dataResult = await response.json();
			setData(get(dataResult, "data", null))
		}

		fetchData()
	}, [])

	return data
};

export default useCountImages;
