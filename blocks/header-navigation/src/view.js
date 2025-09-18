// Logic for dropdown menu
// target id: navbarDropdown

document.addEventListener("DOMContentLoaded", function () {
	const dropdownToggles = document.querySelectorAll(".dropdown-toggle");

	dropdownToggles.forEach((toggle) => {
		toggle.addEventListener("click", function (e) {
			e.preventDefault();
			const menu = this.nextElementSibling;
			const isExpanded = this.getAttribute("aria-expanded") === "true";

			// Close other dropdowns
			dropdownToggles.forEach((otherToggle) => {
				if (otherToggle !== this) {
					otherToggle.setAttribute("aria-expanded", "false");
					otherToggle.nextElementSibling.style.display = "none";
				}
			});

			if (isExpanded) {
				this.setAttribute("aria-expanded", "false");
				menu.style.display = "none";
			} else {
				this.setAttribute("aria-expanded", "true");
				menu.style.display = "flex";
			}
		});
	});

	// Close dropdowns when clicking outside
	document.addEventListener("click", function (event) {
		const isClickInside = event.target.closest(".dropdown");
		if (!isClickInside) {
			dropdownToggles.forEach((toggle) => {
				toggle.setAttribute("aria-expanded", "false");
				toggle.nextElementSibling.style.display = "none";
			});
		}
	});

	// Close on Escape
	document.addEventListener("keydown", function (event) {
		if (event.key === "Escape") {
			dropdownToggles.forEach((toggle) => {
				toggle.setAttribute("aria-expanded", "false");
				toggle.nextElementSibling.style.display = "none";
			});
		}
	});
});
