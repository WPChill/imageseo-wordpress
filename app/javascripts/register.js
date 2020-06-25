import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", function () {
	const $ = jQuery;

	$("#submit-form-register").on("click", function (e) {
		e.preventDefault();
		$("#submit-form-register").prop("disabled", "disabled");
		$("#submit-form-register .text").hide();
		$("#submit-form-register .loader").show();
		$.ajax({
			url: ajaxurl,
			method: "POST",
			data: {
				action: "imageseo_register",
				email: $("#register-email").val(),
				password: $("#register-password").val(),
			},
			success: (response) => {
				const { success, data } = response;
				$("#submit-form-register .loader").hide();
				$("#submit-form-register .text").show();
				if (!success) {
					$("#submit-form-register").prop("disabled", "");
					Swal.fire({
						title: "Error!",
						text:
							"Registration did not work, please visit https://app.imageseo.io/register",
						icon: "error",
						confirmButtonText: "Register",
						onClose: () => {
							window.open(
								"https://app.imageseo.io/register",
								"_blank"
							);
						},
					});
				}

				$("#api_key").val(data.user.project_create.api_key);
				$("#js-imageseo-register #form-register").hide();
				$("#js-imageseo-register .imageseo-separator").hide();
				$("#js-imageseo-register h3").html(
					"You are registered and we have generated your API key. </br > Click on 'Validate your API Key'!"
				);
				Swal.fire({
					title: "Great!",
					text:
						"You are registered and we have configured your API key",
					icon: "success",
					confirmButtonText: "Close",
				});
			},
		});
	});
});
