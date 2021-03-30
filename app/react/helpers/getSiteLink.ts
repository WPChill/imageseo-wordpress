import { get } from "lodash";

//@ts-ignore
export const getSiteLink = (path: string): string => `${get(IMAGESEO_DATA, "SITE_URL", "")}${path}`;

export default getSiteLink;
