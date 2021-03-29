import { get } from 'lodash'

//@ts-ignore
export const getOwner = () => get(IMAGESEO_DATA, 'USER', null);

export default getOwner;
