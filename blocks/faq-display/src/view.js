/**
 * FAQ Display Block - Frontend functionality
 * Handles accordion toggle enhancements and icon animations
 */

document.addEventListener("DOMContentLoaded", function () {
	// Enhance accordion functionality for better UX
	const accordions = document.querySelectorAll(".faq-display-wrapper details");

	accordions.forEach((accordion) => {
		const summary = accordion.querySelector("summary");
		const content = accordion.querySelector(".faq-section-content, .faq-item-content");
		const toggleIcon = accordion.querySelector(".faq-toggle-icon");

		if (summary && content && toggleIcon) {
			// Add click handler to summary for toggle
			summary.addEventListener("click", function (e) {
				e.preventDefault();
				accordion.toggleAttribute("open");
				updateToggleIcon();
			});

			// Function to update toggle icon
			function updateToggleIcon() {
				if (accordion.open) {
					toggleIcon.textContent = "−";
					toggleIcon.style.transform = "rotate(180deg)";
				} else {
					toggleIcon.textContent = "+";
					toggleIcon.style.transform = "rotate(0deg)";
				}
			}

			// Initial state
			updateToggleIcon();

			// Handle keyboard accessibility
			summary.addEventListener("keydown", function (e) {
				if (e.key === "Enter" || e.key === " ") {
					e.preventDefault();
					accordion.toggleAttribute("open");
					updateToggleIcon();
				}
			});
		}
	});

	// Close all accordions except the first one on page load (optional UX)
	// accordions.forEach((acc, index) => {
	// 	if (index > 0) {
	// 		acc.removeAttribute('open');
	// 	}
	// });
});
