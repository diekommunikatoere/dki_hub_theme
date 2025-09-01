// LOGIN FORM HANDLER
jQuery(document).ready(function ($) {
	const ajaxUrl = login_ajax_object.ajax_url;
	const loginNonce = login_ajax_object.nonce;
	const homeUrl = login_ajax_object.home_url;

	$("#login-form > form.login-form")
		.off("submit")
		.on("submit", function (e) {
			e.preventDefault();
			e.stopPropagation();

			// Get redirect_to parameter from URL if it exists
			var urlParams = new URLSearchParams(window.location.search);
			var redirect_to = urlParams.get("redirect_to");

			var redirectUrl = redirect_to ? redirect_to : homeUrl;

			var formData = {
				username: $("#username").val(),
				password: $("#password").val(),
				remember: $("#remember-me").is(":checked"),
				nonce: loginNonce,
			};

			console.log("AJAX data: ", formData);

			$.ajax({
				url: ajaxUrl,
				type: "POST",
				data: {
					action: "perform_login",
					formData: formData,
				},
				success: function (response) {
					console.log("AJAX response:", response);
					if (response.success) {
						$("#login-message").removeClass("error").addClass("success").html("Login erfolgreich! Weiterleitung...");
						window.location.href = redirectUrl;
					} else {
						$("#login-message").removeClass("success").addClass("error").html(response.data);
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.error("AJAX error:", textStatus, errorThrown);
					$("#login-message").removeClass("success").addClass("error").html("Ein Fehler ist aufgetreten. Bitte versuch es noch einmal.");
				},
			});
		});
});

// SCHULUNGEN MARK AS HANDLER
(function ($) {
	$(document).ready(function () {
		const ajaxUrl = schulungen_ajax_object.ajax_url;
		const markAsReadNonce = schulungen_ajax_object.nonce_mark_as_read_nonce;
		const markAsUnreadNonce = schulungen_ajax_object.nonce_mark_as_unread_nonce;

		let isUpdating = false;

		function debounce(func, wait) {
			let timeout;
			return function executedFunction(...args) {
				const later = () => {
					clearTimeout(timeout);
					func(...args);
				};
				clearTimeout(timeout);
				timeout = setTimeout(later, wait);
			};
		}

		// Mark a schulung as read or unread in the profile list
		function updateSchulungStatus(schulungId, setReadStatusTo, nonce) {
			if (isUpdating) {
				return;
			}

			isUpdating = true;

			const formData = {
				schulungId: schulungId,
				setReadStatusTo: setReadStatusTo,
				nonce: nonce,
			};

			$.ajax({
				url: ajaxUrl,
				type: "POST",
				data: {
					action: "schulungen_mark_as_read_unread",
					formData: formData,
				},
				dataType: "json",
				success: function (response) {
					if (response.success && response.data && response.data.status) {
						updateUI(schulungId, response.data.status);
					} else {
						console.error("AJAX error:", response.data ? response.data.message : "Unknown error");
						alert("Failed to update Schulung status. Please try again.");
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.error("AJAX error:", textStatus, errorThrown);
					console.log("Full error response:", jqXHR.responseText);
					alert("An error occurred. Please check the console for more information.");
				},
				complete: function () {
					isUpdating = false;
				},
			});
		}

		// Admin function to reset the status of a schulung on each schulung page
		function resetSchulungStatus(schulungId, setReadStatusTo) {}

		const debouncedUpdateSchulungStatus = debounce(updateSchulungStatus, 300);

		function updateUI(schulungId, setReadStatusTo) {
			const schulungElement = $(`.schulung[data-post-id="${schulungId}"]`);
			const todoList = $(".schulungen-wrapper.todo .schulungen-list");
			const doneList = $(".schulungen-wrapper.done .schulungen-list");

			if (setReadStatusTo === "read") {
				// Update the data attribute of the schulung element
				schulungElement[0].attributes["data-read-status"].value = "read";
				// Toggle show class on icon
				schulungElement.find(".schulung-read-status").find(".icon").removeClass("show");
				schulungElement.find(".schulung-read-status").find(".icon")[1].classList.add("show");
				// Change the class and attributes of the button
				schulungElement.find(".schulung-read-status").removeClass("mark-as-read").addClass("mark-as-unread").data("set-read-status-to", "unread").attr("title", "Als ungelesen markieren");
				// Move the element to the done list
				doneList.append(schulungElement);
			} else {
				// Update the data attribute of the schulung element
				schulungElement[0].attributes["data-read-status"].value = "unread";
				// Toggle show class on icon
				schulungElement.find(".schulung-read-status").find(".icon").removeClass("show");
				schulungElement.find(".schulung-read-status").find(".icon")[0].classList.add("show");
				// Change the class and attributes of the button
				schulungElement.find(".schulung-read-status").removeClass("mark-as-unread").addClass("mark-as-read").data("set-read-status-to", "read").attr("title", "Als gelesen markieren");
				// Move the element to the todo list
				todoList.append(schulungElement);
			}
		}

		$(document)
			.off("click", ".schulung-read-status")
			.on("click", ".schulung-read-status", function (e) {
				// If schulung-read-status has the class mark-as-read or mark-as-unread, it is clickable
				if ($(this).hasClass("mark-as-read") || $(this).hasClass("mark-as-unread")) {
					e.preventDefault();
					e.stopPropagation();

					const schulungId = $(this).data("post-id");
					const setReadStatusTo = $(this).data("set-read-status-to");
					const nonce = setReadStatusTo === "read" ? markAsReadNonce : markAsUnreadNonce;

					debouncedUpdateSchulungStatus(schulungId, setReadStatusTo, nonce);
				}
			});
	});
})(jQuery);
