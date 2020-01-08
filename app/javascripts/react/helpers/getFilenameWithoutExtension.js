import { dropRight } from "lodash";

const getFilenameWithoutExtension = filename => {
	return dropRight(filename.split(".")).join("-");
};

export default getFilenameWithoutExtension;
