import { toJpeg } from "html-to-image";
import jQuery from "jquery";

document.addEventListener("DOMContentLoaded", function() {
	toJpeg(document.getElementById("imageseo-preview-image"), {
		width: 1200,
		height: 630
	}).then(function(toJpeg) {
		var fd = new FormData();
		fd.append("action", "imageseo_upload_social_image");
		fd.append("image", toJpeg);

		jQuery
			.ajax({
				type: "POST",
				url: "/wp-admin/admin-ajax.php",
				data: fd,
				processData: false,
				contentType: false
			})
			.done(function(data) {
				console.log(data);
			});
	});
});
