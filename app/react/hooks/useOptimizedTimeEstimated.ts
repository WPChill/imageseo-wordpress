
import { get } from "lodash";
import React, { useEffect, useState } from "react";

interface Data {
	minutes_by_human: number,
	string_time_estimated: string
}

const useOptimizedTimeEstimated = (): Data | null => {

	const [data, setData] = useState(null)

	useEffect(() => {
		const fetchData = async () => {
			const formData = new FormData();
			formData.append("action", "imageseo_get_optimized_time_estimated");

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

export default useOptimizedTimeEstimated;

