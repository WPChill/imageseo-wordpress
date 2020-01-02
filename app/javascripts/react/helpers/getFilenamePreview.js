import { isEmpty } from "lodash";
import getFilenameWithoutExtension from "./getFilenameWithoutExtension";

const getFilenamePreview = fileinfos => {
	let filename = "";
	if (isEmpty(fileinfos)) {
		return filename;
	}

	filename = fileinfos.filename;
	if (filename.indexOf(".") < 0) {
		filename = `${fileinfos.filename}.${fileinfos.extension}`;
	}
	filename = `${getFilenameWithoutExtension(filename)}.${
		fileinfos.extension
	}`;

	return filename;
};
export default getFilenamePreview;
