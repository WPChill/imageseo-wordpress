import isShallowEqual from "@wordpress/is-shallow-equal";
import usePrevious from "./usePrevious";

const useCompare = val => {
	const prevVal = usePrevious(val);
	return !isShallowEqual(prevVal, val);
};

export default useCompare;
