import React from "react";
import { isNil, get } from "lodash";
import useSWR from "swr";
import { getProjectOwnerPath } from "../services/api/project";

const useOwner = () => {
	const { data, error } = useSWR(getProjectOwnerPath(), {
		suspense: true,
	});

	if (isNil(data)) {
		return null;
	}

	const dataResult = get(data, "data", false);

	if (
		!dataResult ||
		get(dataResult, "success", true) === false ||
		get(dataResult, "code", null) === "not_exist"
	) {
		return null;
	}

	return dataResult;
};

export default useOwner;
