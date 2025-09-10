/**
 * FAQ Search Block - Frontend functionality
 * Implements fuzzy search using Fuse.js and filters FAQ display
 */

import Fuse from "fuse.js";

document.addEventListener("DOMContentLoaded", function () {
	// Find all FAQ search inputs
	const searchInputs = document.querySelectorAll(".faq-search-input");

	searchInputs.forEach((input) => {
		const wrapper = input.closest(".faq-search-wrapper");
		const blockId = wrapper.getAttribute("data-block-id");
		const targetDisplayId = wrapper.getAttribute("data-target-display");
		const faqDataJson = wrapper.getAttribute("data-faq-data");

		if (!faqDataJson) return;

		const faqData = JSON.parse(faqDataJson);
		const targetDisplay = document.getElementById(targetDisplayId) || document.querySelector(".faq-display-wrapper");

		if (!targetDisplay) {
			console.warn("FAQ Display target not found for search block:", blockId);
			return;
		}

		// Initialize Fuse.js for fuzzy search
		const fuseOptions = {
			keys: ["title", "content", "excerpt", "section"],
			threshold: 0.4, // Fuzzy matching threshold
			includeScore: true,
			ignoreLocation: true,
			useExtendedSearch: true,
		};

		const fuse = new Fuse(faqData, fuseOptions);

		// Results count element
		const resultsCount = wrapper.querySelector(".faq-search-results-count");
		const noResults = document.createElement("div");
		noResults.className = "faq-search-no-results";
		noResults.textContent = '<?php echo esc_js( __( "Keine Ergebnisse gefunden.", "faq-search" ) ); ?>';
		wrapper.appendChild(noResults);

		let allFaqItems = Array.from(targetDisplay.querySelectorAll(".faq-item-accordion, .faq-section-accordion"));
		let visibleItems = [...allFaqItems];

		// Function to filter and show results
		function filterResults() {
			const query = input.value.trim().toLowerCase();

			if (query.length === 0) {
				// Show all
				allFaqItems.forEach((item) => {
					item.style.display = "";
				});
				resultsCount.style.display = "none";
				noResults.style.display = "none";
				return;
			}

			const results = fuse.search(query);
			const resultIds = results.map((result) => result.item.id);
			const resultCount = results.length;

			// Update count
			resultsCount.innerHTML = '<?php echo esc_js( __( "%d Ergebnisse", "faq-search" ) ); ?>'.replace("%d", resultCount);
			resultsCount.style.display = "block";

			// Hide all items
			allFaqItems.forEach((item) => {
				item.style.display = "none";
			});

			// Show matching items
			if (resultCount > 0) {
				noResults.style.display = "none";
				resultIds.forEach((id) => {
					const matchingItem =
						targetDisplay.querySelector(`[data-faq-id="${id}"]`) ||
						targetDisplay.querySelectorAll(".faq-item-accordion, .faq-section-accordion").forEach((item) => {
							const title = item.querySelector(".faq-question, .faq-section-title")?.textContent.toLowerCase();
							if (title && title.includes(query)) {
								item.style.display = "";
							}
						});
				});
			} else {
				noResults.style.display = "block";
			}
		}

		// Debounced search
		let timeout;
		input.addEventListener("input", function () {
			clearTimeout(timeout);
			timeout = setTimeout(filterResults, 300);
		});

		// Initial state
		filterResults();

		// Keyboard accessibility
		input.addEventListener("keydown", function (e) {
			if (e.key === "Escape") {
				input.value = "";
				filterResults();
				input.blur();
			}
		});
	});
});
