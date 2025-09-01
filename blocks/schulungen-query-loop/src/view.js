// add click event listener to the buttons with the class "schulung-read-status"
/* document.addEventListener("DOMContentLoaded", function () {
	const buttons = document.querySelectorAll(".schulung-read-status");
	buttons.forEach(function (button) {
		button.addEventListener("click", function () {
			const schulungId = button.getAttribute("data-post-id");
			const status = button.getAttribute("data-read-set-status");
			updateSchulungReadStatus(schulungId, status);
		});
	});
}); */

// add URL parameters to the URL of the current page and reload the page to update the read status
/* function updateSchulungReadStatus(schulungId, status) {
	const url = new URL(window.location.href);
	url.searchParams.set("schulung_id", schulungId);
	url.searchParams.set("mark_as", status);

	window.location.replace(url.toString());
}
 */
