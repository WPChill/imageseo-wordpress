import { get } from "lodash";

//@ts-ignore
export const getApiKey = () => get(IMAGESEO_DATA, "API_KEY", "");

export default getApiKey;
