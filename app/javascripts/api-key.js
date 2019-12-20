import Swal from "sweetalert2";
import { isNull } from "lodash";

document.addEventListener("DOMContentLoaded", function() {
	const $ = jQuery;

	$("#js-api-key button").on("click", function(e) {
		e.preventDefault();
		$("#js-api-key button").prop("disabled", "disabled");
		$.ajax({
			url: ajaxurl,
			method: "POST",
			data: {
				action: "imageseo_valid_api_key",
				api_key: $("#js-api-key input").val()
			},
			success: response => {
				const { success, data } = response;
				$("#submit-form-register .loader").hide();
				$("#submit-form-register .text").show();
				if (!success || isNull(data.user)) {
					$("#js-api-key button").prop("disabled", "");
					Swal.fire({
						title: "Error!",
						text: "Your API key is not valid",
						icon: "error",
						confirmButtonText: "Close"
					});
					return;
				}

				Swal.fire({
					title: "Great!",
					text: "Your API key is registered",
					icon: "success",
					confirmButtonText: "Close"
				});
			}
		});
	});
});
