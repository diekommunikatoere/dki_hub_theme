import React, { useEffect, useState } from "react";
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
	const handleSectionDrop = async (sourceIndex: number, destinationIndex: number) => {
		if (sourceIndex === destinationIndex) return;

		// Correct destination index when moving downwards:
		// after removing the source, indices shift left for items after source
		let targetIndex = destinationIndex;
		if (sourceIndex < destinationIndex) {
			targetIndex -= 1;
		}

		// Reorder sections
		const newSections = Array.from(state.sections);
		const [reorderedSection] = newSections.splice(sourceIndex, 1);

		// Clamp index for safety
		const clampedIndex = Math.max(0, Math.min(targetIndex, newSections.length));
		newSections.splice(clampedIndex, 0, reorderedSection);

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
	};

	// Handle drag and drop for FAQ items within sections
	const handleFAQDrop = async (sourceIndex: number, destinationIndex: number, sectionId: number) => {
		if (sourceIndex === destinationIndex) return;

		const sectionFAQs = state.faqItems[sectionId] || [];
		if (sectionFAQs.length === 0) return;

		// Correct destination index when moving downwards:
		// after removing the source, indices shift left for items after source
		let targetIndex = destinationIndex;
		if (sourceIndex < destinationIndex) {
			targetIndex -= 1;
		}

		// Reorder FAQ items within the section
		const newFAQs = Array.from(sectionFAQs);
		const [reorderedFAQ] = newFAQs.splice(sourceIndex, 1);

		// Clamp index for safety
		const clampedIndex = Math.max(0, Math.min(targetIndex, newFAQs.length));
		newFAQs.splice(clampedIndex, 0, reorderedFAQ);

		// Update order values
		const updatedFAQs = newFAQs.map((faq, index) => ({
			...faq,
			order: index + 1,
		}));

		dispatch({ type: "REORDER_FAQ_ITEMS", payload: { sectionId, items: updatedFAQs } });
		setHasUnsavedChanges(true);

		try {
			await faqItemsAPI.reorder(
				sectionId,
				updatedFAQs.map((f) => f.id)
			);
			setHasUnsavedChanges(false);
		} catch (error) {
			console.error("Error reordering FAQ items:", error);
			setError(__("Failed to save FAQ item order. Please try again.", "dki-wiki"));
		}
	};

	// Removed container-level drop target. Drop logic is now handled in SectionAccordion.

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
						<div className="faq-sections">
							{state.sections.map((section, index) => (
								<SectionAccordion key={section.id} section={section} index={index} faqItems={state.faqItems[section.id] || []} onSectionDrop={handleSectionDrop} onFAQDrop={handleFAQDrop} />
							))}
						</div>

						<div className="faq-editor__actions">
							<button className="button button-secondary" onClick={handleCreateSection}>
								{__("Add New Section", "dki-wiki")}
							</button>
						</div>
					</>
				)}

				{showCreateSection && <CreateSectionForm onSectionCreated={handleSectionCreated} onCancel={() => setShowCreateSection(false)} />}
			</div>
		</div>
	);
};

export default FAQEditor;
