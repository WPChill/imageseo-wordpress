document.addEventListener("DOMContentLoaded", function() {
	const $ = jQuery;
	let handlePingCurrent;
	function pingCurrentProcess(postId) {
		$.ajax({
			url: ajaxurl,
			method: "POST",
			data: {
				action: "imageseo_check_current_process",
				post_id: postId,
				_wpnonce:imageseo_ajax_nonce
			},
			success: response => {
				const {
					data: { current_process, url }
				} = response;

				if (current_process) {
					return;
				}

				$("#imageseo-social-media-image").attr("src", url);
				setTimeout(() => {
					$(`#imageseo-social-media[data-id='${postId}']`).prop(
						"disabled",
						""
					);
					$(`#imageseo-social-media[data-id='${postId}']`)
						.find("img")
						.hide();
				}, 600);
				clearInterval(handlePingCurrent);
			}
		});
	}

	function bindSinglePostGenerateSocial() {
		$("#imageseo-social-media").on("click", function(e) {
			e.preventDefault();
			$(this).prop("disabled", "disabled");
			$(this)
				.find("img")
				.show();
			$.ajax({
				url: ajaxurl,
				method: "POST",
				data: {
					action: "imageseo_generate_social_media",
					post_id: $(this).data("id"),
					_wpnonce:imageseo_ajax_nonce
				},
				success: () => {
					handlePingCurrent = setInterval(
						pingCurrentProcess,
						2500,
						$(this).data("id")
					);
				}
			});
		});
	}

	if ($("#imageseo-social-media").length > 0) {
		bindSinglePostGenerateSocial();
	}
});
