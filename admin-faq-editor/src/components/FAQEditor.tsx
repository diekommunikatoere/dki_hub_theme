import React, { useEffect, useState } from "react";
import { DragDropContext, Droppable, DropResult } from "react-beautiful-dnd";
import { __ } from "@wordpress/i18n";
import { useFAQ } from "../context/FAQContext";
import { sectionsAPI, faqItemsAPI, bulkAPI } from "../services/api";
import SectionAccordion from "./SectionAccordion";
import CreateSectionForm from "./CreateSectionForm";
import LoadingSpinner from "./LoadingSpinner";
import ErrorMessage from "./ErrorMessage";
import { FAQSection, FAQItem } from "../types";
import "./FAQEditor.scss";

const FAQEditor: React.FC = () => {
	const { state, dispatch, setLoading, setError, setSections, setFAQItems } = useFAQ();
	const [showCreateSection, setShowCreateSection] = useState(false);
	const [hasUnsavedChanges, setHasUnsavedChanges] = useState(false);

	// Load initial data
	useEffect(() => {
		const loadData = async () => {
			setLoading(true);
			try {
				// Load sections
				const sections = await sectionsAPI.getAll();
				setSections(sections);

				// Load FAQ items for each section
				for (const section of sections) {
					const items = await faqItemsAPI.getBySection(section.id);
					setFAQItems(section.id, items);
				}
			} catch (error) {
				console.error("Error loading FAQ data:", error);
				setError(__("Failed to load FAQ data. Please refresh the page and try again.", "dki-wiki"));
			} finally {
				setLoading(false);
			}
		};

		loadData();
	}, [setLoading, setError, setSections, setFAQItems]);

	// Handle drag and drop for sections
	const handleSectionDrop = async (result: DropResult) => {
		if (!result.destination) return;

		const { source, destination } = result;

		if (source.droppableId === "sections" && destination.droppableId === "sections") {
			// Reorder sections
			const newSections = Array.from(state.sections);
			const [reorderedSection] = newSections.splice(source.index, 1);
			newSections.splice(destination.index, 0, reorderedSection);

			// Update order values
			const updatedSections = newSections.map((section, index) => ({
				...section,
				order: index + 1,
			}));

			dispatch({ type: "REORDER_SECTIONS", payload: updatedSections });
			setHasUnsavedChanges(true);

			try {
				await sectionsAPI.reorder(updatedSections.map((s) => s.id));
				setHasUnsavedChanges(false);
			} catch (error) {
				console.error("Error reordering sections:", error);
				setError(__("Failed to save section order. Please try again.", "dki-wiki"));
			}
		} else if (source.droppableId.startsWith("section-") && destination.droppableId.startsWith("section-")) {
			// Handle FAQ item reordering within sections
			const sourceSectionId = parseInt(source.droppableId.replace("section-", ""));
			const destinationSectionId = parseInt(destination.droppableId.replace("section-", ""));

			const sourceFAQs = state.faqItems[sourceSectionId] || [];
			const destinationFAQs = sourceSectionId === destinationSectionId ? sourceFAQs : state.faqItems[destinationSectionId] || [];

			// Move FAQ item
			const newSourceFAQs = Array.from(sourceFAQs);
			const [movedItem] = newSourceFAQs.splice(source.index, 1);

			let newDestinationFAQs;
			if (sourceSectionId === destinationSectionId) {
				newDestinationFAQs = newSourceFAQs;
				newDestinationFAQs.splice(destination.index, 0, {
					...movedItem,
					sectionId: destinationSectionId,
				});
			} else {
				newDestinationFAQs = Array.from(destinationFAQs);
				newDestinationFAQs.splice(destination.index, 0, {
					...movedItem,
					sectionId: destinationSectionId,
				});
			}

			// Update order values
			const updatedSourceFAQs = newSourceFAQs.map((item, index) => ({
				...item,
				order: index + 1,
			}));

			const updatedDestinationFAQs = newDestinationFAQs.map((item, index) => ({
				...item,
				order: index + 1,
			}));

			dispatch({ type: "REORDER_FAQ_ITEMS", payload: { sectionId: sourceSectionId, items: updatedSourceFAQs } });
			if (sourceSectionId !== destinationSectionId) {
				dispatch({ type: "REORDER_FAQ_ITEMS", payload: { sectionId: destinationSectionId, items: updatedDestinationFAQs } });
			}

			setHasUnsavedChanges(true);

			try {
				await faqItemsAPI.reorder(
					sourceSectionId,
					updatedSourceFAQs.map((f) => f.id)
				);
				if (sourceSectionId !== destinationSectionId) {
					await faqItemsAPI.reorder(
						destinationSectionId,
						updatedDestinationFAQs.map((f) => f.id)
					);
				}
				setHasUnsavedChanges(false);
			} catch (error) {
				console.error("Error reordering FAQ items:", error);
				setError(__("Failed to save FAQ order. Please try again.", "dki-wiki"));
			}
		}
	};

	const handleCreateSection = () => {
		setShowCreateSection(true);
	};

	const handleSectionCreated = (section: FAQSection) => {
		dispatch({ type: "ADD_SECTION", payload: section });
		setFAQItems(section.id, []);
		setShowCreateSection(false);
	};

	if (state.loading) {
		return <LoadingSpinner message={__("Loading FAQ data...", "dki-wiki")} />;
	}

	return (
		<div className="faq-editor">
			<div className="faq-editor__header">
				<h1>{__("FAQ Manager", "dki-wiki")}</h1>
				<p>{__("Create and organize your FAQ sections and items. Drag and drop to reorder.", "dki-wiki")}</p>
				{hasUnsavedChanges && <div className="faq-editor__unsaved-notice">{__("Changes are being saved automatically...", "dki-wiki")}</div>}
			</div>

			{state.error && <ErrorMessage message={state.error} onDismiss={() => setError(null)} />}

			<DragDropContext
				onDragStart={(result) => {
					console.log("Drag started:", result);
					console.log("Draggable ID:", result.draggableId);
					console.log("Source droppable:", result.source.droppableId);
				}}
				onDragEnd={(result) => {
					console.log("Drag ended:", result);
					handleSectionDrop(result); // Keep existing handler
				}}
			>
				<div className="faq-editor__content">
					{state.sections.length === 0 ? (
						<div className="faq-editor__empty-state">
							<h2>{__("No FAQ sections yet", "dki-wiki")}</h2>
							<p>{__("Create your first FAQ section to get started.", "dki-wiki")}</p>
							<button className="button button-primary" onClick={handleCreateSection}>
								{__("Create First Section", "dki-wiki")}
							</button>
						</div>
					) : (
						<>
							<Droppable droppableId="sections" type="SECTION">
								{(provided) => (
									<div className="faq-sections" {...provided.droppableProps} ref={provided.innerRef}>
										{state.sections.map((section, index) => (
											<SectionAccordion key={section.id} section={section} index={index} faqItems={state.faqItems[section.id] || []} />
										))}
										{provided.placeholder}
									</div>
								)}
							</Droppable>

							<div className="faq-editor__actions">
								<button className="button button-secondary" onClick={handleCreateSection}>
									{__("Add New Section", "dki-wiki")}
								</button>
							</div>
						</>
					)}

					{showCreateSection && <CreateSectionForm onSectionCreated={handleSectionCreated} onCancel={() => setShowCreateSection(false)} />}
				</div>
			</DragDropContext>
		</div>
	);
};

export default FAQEditor;
