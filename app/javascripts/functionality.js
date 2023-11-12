const {__} = wp.i18n;

class imageSEO_Bulk {
	constructor() {
		this.init();
	}

	async init() {
		const instance = this;
		// Start bulk process
		jQuery('#start_bulk_process').on('click', function (e) {
			e.preventDefault();
			instance.startBulkProcess(imageseo_bulk_images.ids, imageseo_bulk_images.options);
		});
		// Show bulk preview
		jQuery('#get_bulk_process').on('click', function (e) {
			e.preventDefault();
			const button   = jQuery(this),
				  $wrapper = button.closest('.notice');
			button.attr('disabled', 'disabled');
			instance.getCurrentBulk($wrapper, button);
		});
		// Stop bulk process
		jQuery('#stop_bulk_process').on('click', function (e) {
			e.preventDefault();
			const button   = jQuery(this),
				  $wrapper = button.closest('.notice');
			button.attr('disabled', 'disabled');
			instance.stopCurrentProcess($wrapper, button);
		});
	}

	async startBulkProcess(
		data,
		{
			formatAlt,
			formatAltCustom,
			language,
			optimizeAlt,
			optimizeFile,
			altFilter
		}
	) {
		const formData = new FormData();

		formData.append("action", "imageseo_start_bulk");
		formData.append("data", data);
		formData.append("formatAlt", formatAlt);
		formData.append("formatAltCustom", formatAltCustom);
		formData.append("language", language);
		formData.append("optimizeAlt", optimizeAlt);
		formData.append("optimizeFile", optimizeFile);
		formData.append("altFilter", altFilter);
		formData.append(
			"_wpnonce",
			document
				.querySelector("#_wpnonce")
				.getAttribute("value")
		);
		//@ts-ignore
		formData.append("wantValidateResult", false);
		//@ts-ignore
		const response      = await fetch(ajaxurl, {
			method: "POST",
			body  : formData,
		});
		const json_response = await response.json();
		if (json_response.success) {
			window.location.reload()
		}
	}

	async restartBulkProcess() {
		const formData = new FormData();

		formData.append("action", "imageseo_restart_bulk");
		formData.append(
			"_wpnonce",
			document
				.querySelector("#_wpnonce")
				.getAttribute("value")
		);
		//@ts-ignore
		const response = await fetch(ajaxurl, {
			method: "POST",
			body  : formData,
		});

		return await response.json();
	}

	async getPreviewBulk() {
		const formData = new FormData();

		formData.append("action", "imageseo_get_preview_bulk");
		formData.append(
			"_wpnonce",
			document
				.querySelector("#_wpnonce")
				.getAttribute("value")
		);
		//@ts-ignore
		const response = await fetch(ajaxurl, {
			method: "POST",
			body  : formData,
		});

		return await response.json();
	};

	async getCurrentBulk($wrapper, button) {
		const formData = new FormData();

		formData.append("action", "imageseo_get_current_bulk");
		formData.append(
			"_wpnonce",
			document
				.querySelector("#_wpnonce")
				.getAttribute("value")
		);
		//@ts-ignore
		const response = await fetch(ajaxurl, {
			method: "POST",
			body  : formData,
		});

		let json_response = await response.json();
		button.removeAttr('disabled');
		if (json_response.success) {
			const optimized = json_response.data.current.id_images_optimized;
			$wrapper.find('.imageseo-bulk-process-status').remove();
			if ('undefined' !== typeof optimized) {
				$wrapper.append('<p class="imageseo-bulk-process-status">' + __('Optimized images:', 'imageseo') + optimized.length + '</p>');
			}
		}
	}

	async stopCurrentProcess($wrapper, button) {
		const formData = new FormData();

		formData.append("action", "imageseo_stop_bulk");
		formData.append(
			"_wpnonce",
			document
				.querySelector("#_wpnonce")
				.getAttribute("value")
		);
		//@ts-ignore
		const response = await fetch(ajaxurl, {
			method: "POST",
			body  : formData,
		});

		let json_response = await response.json();
		button.removeAttr('disabled');
		if (json_response.success) {
			$wrapper.append('<p>' + __('Bulk process stopped.', 'imageseo') + '</p>');
		}
	}

	async queryImages(options) {
		const formData = new FormData();

		formData.append("action", "imageseo_query_images");
		formData.append("filters", JSON.stringify(get(options, "filters", {})));
		formData.append(
			"_wpnonce",
			document
				.querySelector("#_wpnonce")
				.getAttribute("value")
		);

		const response = await fetch(ajaxurl, {
			method: "POST",
			body  : formData
		});

		return await response.json();
	}
}

class imageseo_Settings {
	socialCard   = jQuery('#imageseo-preview-image');
	colorPickers = '.imageseo-colorpicker';

	constructor() {
		this.init();
		this.setConditions();
		this.createFilePicker();
	}

	init() {
		const instance = this;

		jQuery('#setting-visibilitySubTitle').on('change', function (e) {
			const $this = jQuery(this);
			if ($this.is(':checked')) {
				instance.socialCard.find('.imageseo-media__content__sub-title').show();
			} else {
				instance.socialCard.find('.imageseo-media__content__sub-title').hide();
			}
		});
		jQuery('#setting-visibilitySubTitleTwo').on('change', function (e) {
			const $this = jQuery(this);
			if ($this.is(':checked')) {
				instance.socialCard.find('.imageseo-media__content__sub-title-two').show();
			} else {
				instance.socialCard.find('.imageseo-media__content__sub-title-two').hide();
			}
		});
		jQuery('#setting-visibilityRating').on('change', function (e) {
			const $this = jQuery(this);
			if ($this.is(':checked')) {
				instance.socialCard.find('.imageseo-media__content__stars').show();
				jQuery('tr[data-setting="starColor"]').show();
			} else {
				instance.socialCard.find('.imageseo-media__content__stars').hide();
				jQuery('tr[data-setting="starColor"]').hide();
			}
		});
		jQuery('#setting-visibilityAvatar').on('change', function (e) {
			const $this = jQuery(this);
			if ($this.is(':checked')) {
				instance.socialCard.find('.imageseo-media__content__avatar').show();
			} else {
				instance.socialCard.find('.imageseo-media__content__avatar').hide();
			}
		});
		jQuery('#setting-optimizeAlt').on('change', function (e) {
			const $this = jQuery(this);
			if ($this.is(':checked')) {
				jQuery('tr[data-setting="altFill"],tr[data-setting="formatAlt"],tr[data-setting="formatAltCustom"]').show();
			} else {
				jQuery('tr[data-setting="altFill"],tr[data-setting="formatAlt"],tr[data-setting="formatAltCustom"]').hide();
			}
		});
		jQuery('#setting-layout').on('change', function (e) {
			const $this = jQuery(this),
				  $val  = $this.val();
			if ('CARD_LEFT' === $val) {
				instance.socialCard.removeClass('imageseo-media__layout--card-right').addClass('imageseo-media__layout--card-left');
			} else {
				instance.socialCard.removeClass('imageseo-media__layout--card-left').addClass('imageseo-media__layout--card-right');
			}
		});
		jQuery('#setting-logoUrl').on('change', function (e) {
			const $this = jQuery(this),
				  $val  = $this.val();
			instance.socialCard.find('.imageseo-media__content__logo').attr('src', $val);
		});
		jQuery('#setting-defaultBgImg').on('change', function (e) {
			const $this = jQuery(this),
				  $val  = $this.val();
			instance.socialCard.find('.imageseo-media__container__image').css('background-image', 'url( ' + $val + ')');
		});

		instance.setColorPickers();

		jQuery('table.imageseo-bulk_optimizations').find('input,select').on('change', function (e) {
			jQuery('.imageseo-settings-warning').remove();
			jQuery('#start_bulk_process').attr('disabled', true).addClass('disabled').after('<span class="imageseo-settings-warning">' + __('Please save changes in order to start the optimization process.', 'imageseo') + '</span>');
		});

		jQuery('#register_account').on('click', function (e) {
			e.preventDefault();
			const button   = jQuery(this),
				  data     = {
					  firstname  : jQuery('#setting-register_first_name').val(),
					  lastname   : jQuery('#setting-register_last_name').val(),
					  password   : jQuery('#setting-register_password').val(),
					  email      : jQuery('#setting-register_email').val(),
					  terms      : jQuery('#setting-terms').is(':checked'),
					  newsletters: jQuery('#setting-newsletter').is(':checked'),
				  }
			const response = instance.checkRegisterFormData(data);
			if (!response.success) {
				jQuery('p.imageseo-register-form-error').remove();
				button.after('<p class="imageseo-register-form-error">' + response.code + '</p>');
				return;
			}
			instance.register(data).then(function (response) {
				if (response.success) {
					const api_key = response.data.user.project_create.api_key;
					jQuery('#setting-api_key').val(api_key);
					instance.validateApiKey(api_key);
				} else {
					if ('unknown_error' !== response.data.code) {
						button.after('<p>' + response.data.code + '</p>');
					} else {
						button.after('<p>' + __('There was an error processing your request. Please try again later.', 'imageseo') + '</p>');
					}
				}
			});
		});

		jQuery('#validate_api_key').on('click', function (e) {
			e.preventDefault();
			const key = jQuery('#setting-api_key').val();
			instance.validateApiKey(key);
		});
	}

	setColorPickers() {
		const instance = this;
		jQuery(instance.colorPickers).each(function () {
			const $this = jQuery(this),
				  input = $this.closest('tr').find('input');
			input.wpColorPicker(
				{
					change: function (event, ui) {
						const $color  = ui.color.toString(),
							  setting = $this.closest('tr').data('setting');
						input.val($color);
						switch (setting) {
							case 'textColor':
								instance.socialCard.find('.imageseo-media__container__content > *').css('color', $color);
								break;
							case 'contentBackgroundColor':
								instance.socialCard.find('.imageseo-media__container__content').css('background-color', $color);
								break;
							case 'starColor':
								instance.socialCard.find('svg').attr('fill', $color).attr('stroke', $color);
								break;
						}
					}
				}
			);
		});
	}

	async validateApiKey(apiKey) {
		const formData = new FormData();

		formData.append("action", "imageseo_valid_api_key");
		formData.append(
			"_wpnonce",
			document
				.querySelector("#_wpnonce")
				.getAttribute("value")
		);
		formData.append("api_key", apiKey);

		//@ts-ignore
		const response = await fetch(ajaxurl, {
			method: "POST",
			body  : formData,
		});

		const json_response = await response.json();
		if (json_response.success) {
			if (json_response.data.user.is_active) {
				jQuery('.imageseo-settings-warning').remove();
				jQuery('#setting-api_key').after('<span class="imageseo-settings-warning">' + __('API key is valid.', 'imageseo') + '</span>');
				setTimeout(function () {
					window.location.reload();
				}, 1500);
			}
		}
	}

	checkRegisterFormData(data) {
		const instance = this;
		if (!data.email) {
			return {success: false, code: __('Please enter your email', 'imageseo')};
		}
		if (!instance.checkIfEmail(data.email)) {
			return {success: false, error: true, code: __('Please enter a valid email address', 'imageseo')};
		}
		if (!data.password) {
			return {success: false, code: __('Please enter your password', 'imageseo')};
		}
		if (!data.terms) {
			return {success: false, code: __('Please accept the terms and conditions', 'imageseo')};
		}

		return {success: true};
	}

	checkIfEmail(str) {
		// Regular expression to check if string is email
		const regexExp = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/gi;

		return regexExp.test(str);
	}

	async register(data) {
		const formData = new FormData();

		formData.append("action", "imageseo_register");
		formData.append(
			"_wpnonce",
			document
				.querySelector("#_wpnonce")
				.getAttribute("value")
		);
		formData.append("firstname", data.firstname);
		formData.append("lastname", data.lastname);
		formData.append("email", data.email);
		formData.append("password", data.password);
		if (data.newsletters) {
			formData.append("newsletters", data.newsletters);
		}

//@ts-ignore
		const response = await fetch(ajaxurl, {
			method: "POST",
			body  : formData,
		});

		return await response.json();
	};

	setConditions() {
		if (!jQuery('#setting-visibilityRating').is(':checked')) {
			jQuery('tr[data-setting="starColor"]').hide();
		}
		if (!jQuery('#setting-optimizeAlt').is(':checked')) {
			jQuery('tr[data-setting="altFill"],tr[data-setting="formatAlt"],tr[data-setting="formatAltCustom"]').hide();
		}
	}

	createFilePicker() {
		jQuery('.imageseo-file-picker').click(function (e) {
			e.preventDefault();
			const button = jQuery(this),
				  frame  = wp.media(
					  {
						  button  : {
							  text: 'Select'
						  },
						  multiple: false
					  }
				  );

			// When a file is selected, run a callback
			frame.on('select', function () {
				const attachment = frame.state().get('selection').first().toJSON();
				// Do something with the selected file URL, e.g., insert it into a text field
				button.closest('.imageseo-file-picker-wrapper').find('.imageseo-file-picker-src').val(attachment.url).change();
				button.closest('.imageseo-file-picker-wrapper').find('.imageseo-file-picker-image').attr('src', attachment.url).change();
			});

			frame.open();
		});
	}
}

jQuery(function ($) {
	// Bulk optimization functionality
	const imageseo_fn       = new imageSEO_Bulk();
	// Social Card functionality
	const imageseo_settings = new imageseo_Settings();
});
