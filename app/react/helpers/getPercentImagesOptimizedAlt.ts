
//@ts-ignore
export const getPercentImagesOptimizedAlt = (total: number, totalNoAlt: number): number => 100 - ((totalNoAlt * 100) / total).toFixed(1);
