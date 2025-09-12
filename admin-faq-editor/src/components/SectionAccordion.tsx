import React, { useState, useCallback, useEffect, useRef } from "react";
import { draggable, dropTargetForElements } from "@atlaskit/pragmatic-drag-and-drop/element/adapter";
import { setCustomNativeDragPreview } from "@atlaskit/pragmatic-drag-and-drop/element/set-custom-native-drag-preview";
import { attachClosestEdge, extractClosestEdge, Edge } from "@atlaskit/pragmatic-drag-and-drop-hitbox/closest-edge";
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
	onSectionDrop?: (sourceIndex: number, destinationIndex: number) => void;
	onFAQDrop?: (sourceIndex: number, destinationIndex: number, sectionId: number) => void;
}

const SectionAccordion: React.FC<SectionAccordionProps> = ({ section, index, faqItems, onSectionDrop, onFAQDrop }) => {
	const { dispatch, setError } = useFAQ();
	const [isExpanded, setIsExpanded] = useState(false);
	const [isEditing, setIsEditing] = useState(false);
	const [editTitle, setEditTitle] = useState(section.name);
	const [showCreateFAQ, setShowCreateFAQ] = useState(false);
	const [isDeleting, setIsDeleting] = useState(false);
	const [isDragging, setIsDragging] = useState(false);
	const [closestEdge, setClosestEdge] = useState<Edge | null>(null);
	const elementRef = useRef<HTMLDivElement>(null);
	const dragHandleRef = useRef<HTMLDivElement>(null);

	// Set up draggable and drop target
	useEffect(() => {
		const element = elementRef.current;
		const dragHandle = dragHandleRef.current;
		if (!element || !dragHandle) return;

		const draggableCleanup = draggable({
			element: dragHandle,
			getInitialData: () => ({ type: "section", sectionId: section.id.toString(), index: index.toString() }),
			onDragStart: () => setIsDragging(true),
			onDrop: () => setIsDragging(false),
		});

		const dropTargetCleanup = dropTargetForElements({
			element,
			// Allow dropping only when dragging a section and not onto itself
			canDrop: ({ source }) => source.data.type === "section" && source.data.sectionId !== section.id.toString(),
			getIsSticky: () => true,
			getData: ({ input, element, source }) => {
				// Determine allowed edges based on what's being dragged
				let allowedEdges: Edge[] = ["bottom"];

				if (index === 0) {
					// First section always allows top and bottom
					allowedEdges = ["top", "bottom"];
				} else if (index === 1 && source.data.index === "0") {
					// Second section allows top when first section is being dragged
					allowedEdges = ["top", "bottom"];
				}

				return attachClosestEdge(
					{
						type: "section",
						sectionId: section.id.toString(),
						index: index.toString(),
					},
					{
						input,
						element,
						allowedEdges,
					}
				);
			},
			onDrag: ({ source, self }) => {
				// Avoid showing indicators on the dragged element itself
				if (source.data.sectionId === section.id.toString()) {
					setClosestEdge(null);
					return;
				}
				const edge = extractClosestEdge(self.data);
				setClosestEdge(edge);
			},
			onDragLeave: () => setClosestEdge(null),
			onDrop: ({ source, self }) => {
				setClosestEdge(null);

				if (!onSectionDrop) {
					return;
				}

				const sourceIndex = parseInt(source.data.index as string);
				let destinationIndex = parseInt(self.data.index as string);

				const closestEdge = extractClosestEdge(self.data);

				if (closestEdge === "bottom") {
					destinationIndex++;
				}

				onSectionDrop(sourceIndex, destinationIndex);
			},
		});

		return () => {
			draggableCleanup();
			dropTargetCleanup();
		};
	}, [section.id, index]);

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
		<div
			ref={elementRef}
			className={classNames("section-accordion", {
				"section-accordion--expanded": isExpanded,
				"section-accordion--dragging": isDragging,
				"section-accordion--deleting": isDeleting,
				"section-accordion--drop-target-top": closestEdge === "top",
				"section-accordion--drop-target-bottom": closestEdge === "bottom",
			})}
		>
			<div className="section-accordion__header" onClick={(e) => handleToggle(e)}>
				<div ref={dragHandleRef} className="section-accordion__drag-handle" title={__("Drag to reorder section", "dki-wiki")} style={{ cursor: isDragging ? "grabbing" : "grab" }}>
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
								onClick={(e) => {
									e.stopPropagation();
									handleEditStart();
								}}
								title={__("Edit section title", "dki-wiki")}
							>
								<span className="dashicons dashicons-edit"></span>
							</button>
							<button
								className="button-delete"
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
								className="button-save"
								onClick={(e) => {
									e.stopPropagation();
									handleEditSave();
								}}
								title={__("Save changes", "dki-wiki")}
							>
								<span className="dashicons dashicons-yes"></span>
							</button>
							<button
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
					<div className="section-accordion__faq-list">
						{faqItems.map((faqItem, faqIndex) => (
							<FAQItemAccordion key={faqItem.id} faqItem={faqItem} index={faqIndex} sectionId={section.id} onFAQDrop={onFAQDrop} />
						))}

						{faqItems.length === 0 && !showCreateFAQ && (
							<div className="section-accordion__empty-state">
								<p>{__("No FAQ items in this section yet.", "dki-wiki")}</p>
							</div>
						)}
					</div>

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
	);
};

export default SectionAccordion;
