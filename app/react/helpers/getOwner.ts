import { get } from 'lodash'

//@ts-ignore
export const getOwner = () => get(IMAGESEO, 'USER', null);

export default getOwner;
