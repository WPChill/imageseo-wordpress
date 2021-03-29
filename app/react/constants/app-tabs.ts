// @ts-ignore
const { __ } = wp.i18n;

export enum TABS {
	OVERVIEW,
	BULK_OPTIMIZATION,
	SOCIAL_CARD,
	ON_UPLOAD_SETTINGS
}

export enum MAIN_CONTENT {
	OVERVIEW,
	BULK_OPTIMIZATION,
	SOCIAL_CARD,
	ON_UPLOAD_SETTINGS

}


export default [
	{
		key: TABS.OVERVIEW,
		label: __("Dashboard", "imageseo"),
		order: 1,
		description: __("Metrics, get help", "imageseo"),
		mainContent: MAIN_CONTENT.OVERVIEW,
		icon: "icon-overview.svg",
	},
	{
		key: TABS.ON_UPLOAD_SETTINGS,
		label: __("Settings", "imageseo"),
		order: 1,
		description: __("Time saver", "imageseo"),
		mainContent: MAIN_CONTENT.ON_UPLOAD_SETTINGS,
		icon: "icon-settings.svg",
	},
	{
		key: TABS.BULK_OPTIMIZATION,
		label: __("Bulk optimization", "imageseo"),
		order: 1,
		description: __("Improve your SEO", "imageseo"),
		mainContent: MAIN_CONTENT.BULK_OPTIMIZATION,
		icon: "icon-bulk.svg",
	},
	{
		key: TABS.SOCIAL_CARD,
		label: __("Social Card", "imageseo"),
		order: 1,
		description: __("More url reshare", "imageseo"),
		mainContent: MAIN_CONTENT.SOCIAL_CARD,
		icon: "icon-social.svg",
	},
];
