/**
 * FAQ Display Block Frontend JavaScript
 * Implements accessible accordion functionality according to WCAG standards
 */

document.addEventListener("DOMContentLoaded", function () {
	// Get all FAQ accordions
	const accordions = document.querySelectorAll(".wp-block-dki-wiki-faq-display .faq-accordion");

	accordions.forEach((accordion) => {
		// Get all buttons in this accordion
		const buttons = accordion.querySelectorAll(".faq-question-button");

		buttons.forEach((button, index) => {
			// Add click event listener
			button.addEventListener("click", function () {
				toggleAccordion(this, accordion);
			});

			// Add keyboard event listener
			button.addEventListener("keydown", function (e) {
				// Enter or Space: Toggle the panel
				if (e.key === "Enter" || e.key === " ") {
					e.preventDefault();
					toggleAccordion(this, accordion);
				}

				// Arrow keys: Navigate between headers
				if (e.key === "ArrowDown" || e.key === "ArrowUp") {
					e.preventDefault();
					const buttonsArray = Array.from(buttons);
					let nextIndex;

					if (e.key === "ArrowDown") {
						nextIndex = (index + 1) % buttonsArray.length;
					} else {
						// ArrowUp
						nextIndex = (index - 1 + buttonsArray.length) % buttonsArray.length;
					}

					buttonsArray[nextIndex].focus();
				}

				// Home key: Focus first header
				if (e.key === "Home") {
					e.preventDefault();
					buttons[0].focus();
				}

				// End key: Focus last header
				if (e.key === "End") {
					e.preventDefault();
					buttons[buttons.length - 1].focus();
				}
			});
		});
	});

	/**
	 * Toggle accordion panel visibility
	 * @param {HTMLElement} button - The button that was clicked
	 * @param {HTMLElement} accordion - The accordion container
	 */
	function toggleAccordion(button, accordion) {
		// Get the associated content panel
		const contentId = button.getAttribute("aria-controls");
		const content = document.getElementById(contentId);

		// Check if content is currently expanded
		const isExpanded = button.getAttribute("aria-expanded") === "true";

		// For this implementation, we allow collapsing all panels
		// If you want to enforce one panel always open, modify this logic

		if (isExpanded) {
			// Collapse the content
			content.hidden = true;
			button.setAttribute("aria-expanded", "false");
			button.classList.remove("is-open");
		} else {
			// Expand the content
			content.hidden = false;
			button.setAttribute("aria-expanded", "true");
			button.classList.add("is-open");
		}
	}

	/**
	 * Copy to Clipboard functionality
	 */
	const copyButtons = document.querySelectorAll(".faq-copy-link-button");
	copyButtons.forEach((copyButton) => {
		copyButton.addEventListener("click", function () {
			const faqId = this.getAttribute("data-faq-id");
			const url = new URL(window.location.href);
			const faqUrl = `${url.origin}${url.pathname}?faq=${faqId}#faq-question-${faqId}`;
			url.searchParams.set("faq", faqId);
			navigator.clipboard.writeText(faqUrl).then(() => {
				this.classList.add("copy-success");
				setTimeout(() => {
					this.classList.remove("copy-success");
				}, 2000);
			});
		});
	});
});
