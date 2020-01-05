import Swal from "sweetalert2";
import { isNull } from "lodash";

document.addEventListener("DOMContentLoaded", function() {
	const $ = jQuery;

	$("#js-api-key button").on("click", function(e) {
		e.preventDefault();
		console.log("btich");
		$("#js-api-key button").prop("disabled", "disabled");
		$("#js-api-key button img").show();
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
				$("#js-api-key button").prop("disabled", "");
				$("#js-api-key button img").hide();
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
