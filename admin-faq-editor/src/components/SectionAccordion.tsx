import React, { useState, useCallback } from "react";
import { Draggable, Droppable } from "react-beautiful-dnd";
import classNames from "classnames";
import { __ } from "@wordpress/i18n";
import { useFAQ } from "../context/FAQContext";
import { sectionsAPI } from "../services/api";
import FAQItemAccordion from "./FAQItemAccordion";
import CreateFAQForm from "./CreateFAQForm";
import { FAQSection, FAQItem } from "../types";
import "./SectionAccordion.scss";

interface SectionAccordionProps {
	section: FAQSection;
	index: number;
	faqItems: FAQItem[];
}

const SectionAccordion: React.FC<SectionAccordionProps> = ({ section, index, faqItems }) => {
	const { dispatch, setError } = useFAQ();
	const [isExpanded, setIsExpanded] = useState(false);
	const [isEditing, setIsEditing] = useState(false);
	const [editTitle, setEditTitle] = useState(section.name);
	const [showCreateFAQ, setShowCreateFAQ] = useState(false);
	const [isDeleting, setIsDeleting] = useState(false);

	console.log("Rendering SectionAccordion:", section.id, "index:", index, "Draggable setup check");

	const handleToggle = useCallback(
		(e?: React.MouseEvent) => {
			// Prevent toggle when clicking drag handle or action buttons
			if (e && (e.target as HTMLElement).closest(".section-accordion__drag-handle, .section-accordion__actions")) {
				return;
			}
			setIsExpanded(!isExpanded);
		},
		[isExpanded]
	);

	const handleEditStart = useCallback(() => {
		setIsEditing(true);
		setEditTitle(section.name);
	}, [section.name]);

	const handleEditCancel = useCallback(() => {
		setIsEditing(false);
		setEditTitle(section.name);
	}, [section.name]);

	const handleEditSave = useCallback(async () => {
		if (!editTitle.trim()) {
			setError(__("Section title cannot be empty.", "dki-wiki"));
			return;
		}

		try {
			const updatedSection = await sectionsAPI.update({
				id: section.id,
				name: editTitle.trim(),
			});

			dispatch({ type: "UPDATE_SECTION", payload: updatedSection });
			setIsEditing(false);
		} catch (error) {
			console.error("Error updating section:", error);
			setError(__("Failed to update section. Please try again.", "dki-wiki"));
		}
	}, [section.id, editTitle, dispatch, setError]);

	const handleDelete = useCallback(async () => {
		if (!window.confirm(__("Are you sure you want to delete this section and all its FAQ items?", "dki-wiki"))) {
			return;
		}

		setIsDeleting(true);
		try {
			await sectionsAPI.delete(section.id);
			dispatch({ type: "DELETE_SECTION", payload: section.id });
		} catch (error) {
			console.error("Error deleting section:", error);
			setError(__("Failed to delete section. Please try again.", "dki-wiki"));
			setIsDeleting(false);
		}
	}, [section.id, dispatch, setError]);

	const handleCreateFAQ = useCallback(() => {
		setShowCreateFAQ(true);
		if (!isExpanded) {
			setIsExpanded(true);
		}
	}, [isExpanded]);

	const handleFAQCreated = useCallback(
		(faqItem: FAQItem) => {
			dispatch({ type: "ADD_FAQ_ITEM", payload: faqItem });
			setShowCreateFAQ(false);
		},
		[dispatch]
	);

	const handleKeyDown = useCallback(
		(e: React.KeyboardEvent) => {
			if (isEditing && e.key === "Enter") {
				e.preventDefault();
				handleEditSave();
			} else if (isEditing && e.key === "Escape") {
				handleEditCancel();
			}
		},
		[isEditing, handleEditSave, handleEditCancel]
	);

	return (
		<Draggable draggableId={`section-${section.id}`} index={index}>
			{(provided, snapshot) => (
				<div
					ref={provided.innerRef}
					{...provided.draggableProps}
					className={classNames("section-accordion", {
						"section-accordion--expanded": isExpanded,
						"section-accordion--dragging": snapshot.isDragging,
						"section-accordion--deleting": isDeleting,
					})}
				>
					<div className="section-accordion__header" onClick={(e) => handleToggle(e)}>
						<div {...provided.dragHandleProps} className="section-accordion__drag-handle" title={__("Drag to reorder section", "dki-wiki")}>
							<span className="dashicons dashicons-move"></span>
						</div>

						<button
							className="section-accordion__toggle"
							onClick={(e) => {
								e.stopPropagation();
								handleToggle();
							}}
							aria-expanded={isExpanded}
							aria-label={__("Toggle section", "dki-wiki")}
						>
							<span
								className={classNames("dashicons", {
									"dashicons-arrow-down-alt2": !isExpanded,
									"dashicons-arrow-up-alt2": isExpanded,
								})}
							></span>
						</button>

						<div className="section-accordion__title-container">
							{isEditing ? <input type="text" value={editTitle} onChange={(e) => setEditTitle(e.target.value)} onKeyDown={handleKeyDown} onBlur={handleEditSave} className="section-accordion__title-input" onClick={(e) => e.stopPropagation()} autoFocus /> : <h3 className="section-accordion__title">{section.name}</h3>}

							<div className="section-accordion__meta">
								<span className="section-accordion__count">
									{faqItems.length} {faqItems.length === 1 ? __("item", "dki-wiki") : __("items", "dki-wiki")}
								</span>
							</div>
						</div>

						<div className="section-accordion__actions" onClick={(e) => e.stopPropagation()}>
							{!isEditing ? (
								<>
									<button
										className="button button-small"
										onClick={(e) => {
											e.stopPropagation();
											handleEditStart();
										}}
										title={__("Edit section title", "dki-wiki")}
									>
										<span className="dashicons dashicons-edit"></span>
									</button>
									<button
										className="button button-small button-link-delete"
										onClick={(e) => {
											e.stopPropagation();
											handleDelete();
										}}
										disabled={isDeleting}
										title={__("Delete section", "dki-wiki")}
									>
										<span className="dashicons dashicons-trash"></span>
									</button>
								</>
							) : (
								<>
									<button
										className="button button-small button-primary"
										onClick={(e) => {
											e.stopPropagation();
											handleEditSave();
										}}
										title={__("Save changes", "dki-wiki")}
									>
										<span className="dashicons dashicons-yes"></span>
									</button>
									<button
										className="button button-small"
										onClick={(e) => {
											e.stopPropagation();
											handleEditCancel();
										}}
										title={__("Cancel editing", "dki-wiki")}
									>
										<span className="dashicons dashicons-no"></span>
									</button>
								</>
							)}
						</div>
					</div>

					{isExpanded && (
						<div className="section-accordion__content">
							<Droppable droppableId={`section-${section.id}`} type="FAQ_ITEM">
								{(provided, snapshot) => (
									<div
										ref={provided.innerRef}
										{...provided.droppableProps}
										className={classNames("section-accordion__faq-list", {
											"section-accordion__faq-list--drag-over": snapshot.isDraggingOver,
										})}
									>
										{faqItems.map((faqItem, faqIndex) => (
											<FAQItemAccordion key={faqItem.id} faqItem={faqItem} index={faqIndex} sectionId={section.id} />
										))}
										{provided.placeholder}

										{faqItems.length === 0 && !showCreateFAQ && (
											<div className="section-accordion__empty-state">
												<p>{__("No FAQ items in this section yet.", "dki-wiki")}</p>
											</div>
										)}
									</div>
								)}
							</Droppable>

							<div className="section-accordion__footer">
								{showCreateFAQ ? (
									<CreateFAQForm sectionId={section.id} onFAQCreated={handleFAQCreated} onCancel={() => setShowCreateFAQ(false)} />
								) : (
									<button className="button button-secondary" onClick={handleCreateFAQ}>
										<span className="dashicons dashicons-plus"></span>
										{__("Add FAQ Item", "dki-wiki")}
									</button>
								)}
							</div>
						</div>
					)}
				</div>
			)}
		</Draggable>
	);
};

export default SectionAccordion;
