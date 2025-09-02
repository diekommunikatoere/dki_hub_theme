// Add filter function to the $buchstaben query
// Add click event to the .glossary-filter__link to filter the query
// Vanilla JS

document.addEventListener("DOMContentLoaded", function () {
	// Add click event to the .glossary-filter__link to filter the query
	const $filterLink = document.querySelectorAll(".glossary-filter__link:not(.inactive)");
	// Add filter function to the $buchstaben query
	const $glossary_section = document.querySelectorAll(".glossary-section");

	$filterLink.forEach(function (link) {
		link.addEventListener("click", function (e) {
			e.preventDefault();
			const letter = link.getAttribute("data-letter");

			// If the letter is "all", show all sections
			// If the letter is not "all", hide all sections that don't match the letter
			// Remove the active class from all links
			// Add the active class to the clicked link
			// Show the sections that match the letter, but hide the rest

			if (letter === "all") {
				$glossary_section.forEach(function (section) {
					section.classList.remove("hidden");
				});
			} else {
				$glossary_section.forEach(function (section) {
					if (section.getAttribute("data-letter") === letter) {
						section.classList.remove("hidden");
					} else {
						section.classList.add("hidden");
					}
				});
			}
			$filterLink.forEach(function (link) {
				link.classList.remove("active");
			});
			link.classList.add("active");
		});
	});
});
