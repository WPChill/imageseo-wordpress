import { get } from "lodash";

//@ts-ignore
export const getApiKey = () => get(IMAGESEO, "API_KEY", "");

export default getApiKey;
