jQuery(document).ready(function ($) {
	if (typeof faqReorder !== "undefined" && faqReorder.isReorderPage) {
		// Reorder subpage: Nested sortable for sections and FAQs
		$("#sections-sortable")
			.sortable({
				items: ".section-item",
				cursor: "move",
				opacity: 0.8,
				update: function (event, ui) {
					// Visual feedback, but save on button click
					updateOrderDisplays();
				},
			})
			.disableSelection();

		// Nested sortable for FAQs per section
		$(".faqs-sortable").each(function () {
			$(this)
				.sortable({
					items: ".faq-item",
					cursor: "move",
					opacity: 0.8,
					connectWith: false, // No cross-section drag
					update: function (event, ui) {
						updateOrderDisplays();
					},
				})
				.disableSelection();
		});

		// Save button click
		$("#save-faq-reorder").on("click", function () {
			var sectionsOrder = [];
			$("#sections-sortable .section-item").each(function () {
				sectionsOrder.push($(this).data("term-id"));
			});

			var faqsPerSection = {};
			$(".faqs-sortable").each(function () {
				var sectionId = $(this).data("section-id");
				faqsPerSection[sectionId] = [];
				$(this)
					.find(".faq-item")
					.each(function () {
						faqsPerSection[sectionId].push($(this).data("post-id"));
					});
			});

			$.post(
				ajaxurl,
				{
					action: faqReorder.saveAction,
					sections_order: sectionsOrder,
					faqs_per_section: faqsPerSection,
					nonce: faqReorder.nonce,
				},
				function (response) {
					var messageEl = $("#reorder-message");
					if (response.success) {
						messageEl.html('<span class="success">' + response.data + "</span>");
						updateOrderDisplays(); // Refresh displayed orders
					} else {
						messageEl.html('<span class="error">' + (response.data || "Fehler beim Speichern der Reihenfolge.") + "</span>");
					}
					setTimeout(function () {
						messageEl.empty();
					}, 5000);
				}
			);
		});

		function updateOrderDisplays() {
			// Update section order displays
			$("#sections-sortable .section-item").each(function (index) {
				$(this)
					.find(".section-order")
					.text(index + 1);
			});
			// Update FAQ order displays
			$(".faqs-sortable").each(function () {
				$(this)
					.find(".faq-item")
					.each(function (index) {
						$(this)
							.find(".faq-order")
							.text(index + 1);
					});
			});
		}
	} else {
		// List view: Disable drag to remove indicator
		console.log("Drag-and-drop disabled on list view");
	}

	// For section taxonomy list (if needed in future)
	if ($("body").hasClass("taxonomy-faq_section") && $(".wp-list-table").length) {
		console.log("Section list: Manual order via edit form");
	}
});
