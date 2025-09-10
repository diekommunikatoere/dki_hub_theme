/**
 * FAQ Search Block Frontend JavaScript
 * Implements fuzzy search functionality using Fuse.js
 */

import Fuse from "fuse.js";

document.addEventListener("DOMContentLoaded", function () {
	// Get the search input and clear button
	const searchInput = document.querySelector(".wp-block-dki-wiki-faq-search .faq-search-input");
	const clearButton = document.querySelector(".wp-block-dki-wiki-faq-search .faq-search-clear");

	// Get the FAQ display block
	const faqDisplay = document.querySelector(".wp-block-dki-wiki-faq-display");

	// If any required elements are missing, exit
	if (!searchInput || !clearButton || !faqDisplay) {
		return;
	}

	// Prepare data for Fuse.js
	const faqData = [];
	const sections = faqDisplay.querySelectorAll(".faq-section");

	sections.forEach((section) => {
		const sectionId = section.dataset.sectionId;
		const sectionTitle = section.querySelector(".faq-section-title").textContent;
		const faqItems = section.querySelectorAll(".faq-item");

		faqItems.forEach((item) => {
			const question = item.querySelector(".faq-question-text").textContent;
			const answer = item.querySelector(".faq-answer-content").textContent;

			faqData.push({
				sectionId: sectionId,
				sectionTitle: sectionTitle,
				question: question,
				answer: answer,
				element: item,
			});
		});
	});

	// Initialize Fuse.js
	const fuseOptions = {
		keys: ["sectionTitle", "question", "answer"],
		threshold: 0.5, // Adjust for desired fuzziness
		includeScore: true,
	};

	const fuse = new Fuse(faqData, fuseOptions);

	// Add event listeners
	searchInput.addEventListener("input", function () {
		const searchTerm = this.value.trim();

		// Show/hide clear button
		clearButton.hidden = !searchTerm;

		// If search term is empty, show all items
		if (!searchTerm) {
			showAllItems(sections);
			return;
		}

		// Perform search
		const results = fuse.search(searchTerm);
		const matchedItems = results.map((result) => result.item);

		// Filter items
		filterItems(sections, matchedItems);
	});

	clearButton.addEventListener("click", function () {
		searchInput.value = "";
		clearButton.hidden = true;
		showAllItems(sections);
		searchInput.focus();
	});

	/**
	 * Show all FAQ items
	 */
	function showAllItems(sections) {
		sections.forEach((section) => {
			// Show section
			section.hidden = false;

			// Show all items in section
			const items = section.querySelectorAll(".faq-item");
			items.forEach((item) => {
				item.hidden = false;

				// Also show the section title if it was hidden
				const sectionTitle = section.querySelector(".faq-section-title");
				if (sectionTitle) {
					sectionTitle.hidden = false;
				}
			});
		});
	}

	/**
	 * Filter FAQ items based on search results
	 */
	function filterItems(sections, matchedItems) {
		// Create a set of section IDs that have matches
		const matchedSectionIds = new Set(matchedItems.map((item) => item.sectionId));

		// Create a set of matched item elements for quick lookup
		const matchedElements = new Set(matchedItems.map((item) => item.element));

		sections.forEach((section) => {
			const sectionId = section.dataset.sectionId;

			// If section has no matches, hide it
			if (!matchedSectionIds.has(sectionId)) {
				section.hidden = true;
				return;
			}

			// Section has matches, show it
			section.hidden = false;

			// Get all items in this section
			const items = section.querySelectorAll(".faq-item");

			// Check if all items in this section are matched
			const allItemsMatched = Array.from(items).every((item) => matchedElements.has(item));

			// If all items are matched, show section title
			// Otherwise, hide section title (since we're filtering)
			const sectionTitle = section.querySelector(".faq-section-title");
			if (sectionTitle) {
				sectionTitle.hidden = !allItemsMatched;
			}

			// Show/hide individual items
			items.forEach((item) => {
				item.hidden = !matchedElements.has(item);
			});
		});
	}
});
