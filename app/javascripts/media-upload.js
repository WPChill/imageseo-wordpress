const slugify = require("slugify");
const Swal = require("sweetalert2");
const { __ } = wp.i18n;

jQuery(document).ready(function ($) {
	function imageseo_alt_field(attachment, alert = false) {
		const alt_text = $("#imageseo-alt-" + attachment).val();
		$.post(
			ajaxurl,
			{
				action: "imageseo_media_alt_update",
				post_id: attachment,
				alt: alt_text,
			},
			function () {
				if (alert) {
					Swal.fire({
						title: __("Alternative text updated!", "imageseo"),
						text: "",
						icon: "success",
						confirmButtonText: __("Close", "imageseo"),
					});
				}
				setTimeout(() => {
					$(
						`#wrapper-imageseo-alt-${attachment} .imageseo-loading`
					).hide();
					$(`#wrapper-imageseo-alt-${attachment} button span`).show();
				}, 500);
			}
		);
	}
	$(this)
		.on("keydown", "input.imageseo-alt-ajax", function (event) {
			if (event.keyCode === 13) {
				$(this).blur();
				return false;
			}
		})
		.on("blur", "input.imageseo-alt-ajax", function () {
			const id = $(this).data("id");
			$("#wrapper-imageseo-alt-" + id + " button span").hide();
			$("#wrapper-imageseo-alt-" + id + " .imageseo-loading").show();
			imageseo_alt_field(id);
			return false;
		});
	$(".wrapper-imageseo-input-alt button").on("click", function (e) {
		e.preventDefault();
		const id = $(this).data("id");
		$("#wrapper-imageseo-alt-" + id + " button span").hide();
		$("#wrapper-imageseo-alt-" + id + " .imageseo-loading").show();
		imageseo_alt_field(id, true);
	});

	$(".wrapper-imageseo-input-filename button").on("click", function (e) {
		e.preventDefault();
		const id = $(this).data("id");
		$("#wrapper-imageseo-filename-" + id + " button span").hide();
		$("#wrapper-imageseo-filename-" + id + " .imageseo-loading").show();

		const slugifyFilename = slugify($("#imageseo-filename-" + id).val(), {
			replacement: "-",
			remove: /[*+~.()'"!:@]/g,
		});

		$("#imageseo-filename-" + id).val(slugifyFilename);

		$.post(
			ajaxurl,
			{
				action: "imageseo_optimize_filename",
				attachmentId: id,
				filename: slugifyFilename,
			},
			function ({ success, data }) {
				if (success) {
					$(`#imageseo-filename-${id}`).val(data.filename);
					if (data.filename !== slugifyFilename) {
						Swal.fire({
							title: __("Successful renaming!", "imageseo"),
							text: __(
								"We had to change the name because a file already exists on the one you tried. You can reload the page to see the change",
								"imageseo"
							),
							icon: "success",
							confirmButtonText: __("Close", "imageseo"),
						});
					} else {
						Swal.fire({
							title: __("Successful renaming!", "imageseo"),
							text: __(
								"You can reload the page to see the change.",
								"imageseo"
							),
							icon: "success",
							confirmButtonText: __("Close", "imageseo"),
						});
					}
				} else {
					Swal.fire({
						title: __("Oups!", "imageseo"),
						html: `It seems that we have failed to rename your image. Please feel free to contact support.`,
						icon: "error",
						confirmButtonText: __("Close", "imageseo"),
					});
				}
				setTimeout(() => {
					$(
						`#wrapper-imageseo-filename-${id} .imageseo-loading`
					).hide();
					$(`#wrapper-imageseo-filename-${id} button span`).show();
				}, 500);
			}
		);
	});
});
