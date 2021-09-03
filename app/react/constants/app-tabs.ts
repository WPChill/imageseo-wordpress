// @ts-ignore
const { __ } = wp.i18n;

export enum TABS {
	OVERVIEW,
	BULK_OPTIMIZATION,
	SOCIAL_CARD,
	SETTINGS
}

export enum MAIN_CONTENT {
	OVERVIEW,
	BULK_OPTIMIZATION,
	SOCIAL_CARD,
	SETTINGS

}


export default [
	{
		key: TABS.OVERVIEW,
		label: __("Welcome on board", "imageseo"),
		order: 1,
		description: __("Metrics, get help", "imageseo"),
		mainContent: MAIN_CONTENT.OVERVIEW,
		icon: "icon-overview.svg",
	},
	{
		key: TABS.SETTINGS,
		label: __("Settings", "imageseo"),
		order: 1,
		description: __("Upon upload optimization", "imageseo"),
		mainContent: MAIN_CONTENT.SETTINGS,
		icon: "icon-settings.svg",
	},
	{
		key: TABS.BULK_OPTIMIZATION,
		label: __("Bulk optimization", "imageseo"),
		order: 1,
		description: __("Existing images optimization", "imageseo"),
		mainContent: MAIN_CONTENT.BULK_OPTIMIZATION,
		icon: "icon-bulk.svg",
	},
	{
		key: TABS.SOCIAL_CARD,
		label: __("Social Card", "imageseo"),
		order: 1,
		description: __("More engagement on social media", "imageseo"),
		mainContent: MAIN_CONTENT.SOCIAL_CARD,
		icon: "icon-social.svg",
	},
];
