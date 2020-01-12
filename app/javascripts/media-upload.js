const slugify = require("slugify");
jQuery(document).ready(function($) {
	function imageseo_alt_field(attachment) {
		const alt_text = $("#imageseo-alt-" + attachment).val();
		$.post(
			ajaxurl,
			{
				action: "imageseo_media_alt_update",
				post_id: attachment,
				alt: alt_text
			},
			function() {
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
		.on("keydown", "input.imageseo-alt-ajax", function(event) {
			if (event.keyCode === 13) {
				$(this).blur();
				return false;
			}
		})
		.on("blur", "input.imageseo-alt-ajax", function() {
			const id = $(this).data("id");
			$("#wrapper-imageseo-alt-" + id + " button span").hide();
			$("#wrapper-imageseo-alt-" + id + " .imageseo-loading").show();
			imageseo_alt_field(id);
			return false;
		});
	$(".wrapper-imageseo-input-alt button").on("click", function(e) {
		e.preventDefault();
		const id = $(this).data("id");
		$("#wrapper-imageseo-alt-" + id + " button span").hide();
		$("#wrapper-imageseo-alt-" + id + " .imageseo-loading").show();
		imageseo_alt_field(id);
	});

	$(".wrapper-imageseo-input-filename button").on("click", function(e) {
		e.preventDefault();
		const id = $(this).data("id");
		$("#wrapper-imageseo-filename-" + id + " button span").hide();
		$("#wrapper-imageseo-filename-" + id + " .imageseo-loading").show();

		const slugifyFilename = slugify($("#imageseo-filename-" + id).val(), {
			replacement: "-",
			remove: /[*+~.()'"!:@]/g
		});

		$("#imageseo-filename-" + id).val(slugifyFilename);

		$.post(
			ajaxurl,
			{
				action: "imageseo_optimize_filename",
				attachmentId: id,
				filename: slugifyFilename
			},
			function() {
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
