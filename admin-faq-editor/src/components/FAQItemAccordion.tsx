import React, { useState, useCallback, useRef, useEffect } from "react";
import { Draggable } from "react-beautiful-dnd";
import classNames from "classnames";
import { __ } from "@wordpress/i18n";
import { useFAQ } from "../context/FAQContext";
import { faqItemsAPI } from "../services/api";
import WYSIWYGEditor from "./WYSIWYGEditor";
import { FAQItem } from "../types";
import "./FAQItemAccordion.scss";

interface FAQItemAccordionProps {
	faqItem: FAQItem;
	index: number;
	sectionId: number;
}

const FAQItemAccordion: React.FC<FAQItemAccordionProps> = ({ faqItem, index, sectionId }) => {
	const { dispatch, setError } = useFAQ();
	const [isExpanded, setIsExpanded] = useState(false);
	const [isEditing, setIsEditing] = useState(false);
	const [editTitle, setEditTitle] = useState(faqItem.title);
	const [editContent, setEditContent] = useState(faqItem.content);
	const [isDeleting, setIsDeleting] = useState(false);
	const [isSaving, setIsSaving] = useState(false);
	const titleInputRef = useRef<HTMLInputElement>(null);

	useEffect(() => {
		if (isEditing && titleInputRef.current) {
			titleInputRef.current.focus();
			titleInputRef.current.select();
		}
	}, [isEditing]);

	const handleToggle = useCallback(() => {
		if (!isEditing) {
			setIsExpanded(!isExpanded);
		}
	}, [isExpanded, isEditing]);

	const handleEditStart = useCallback(() => {
		setIsEditing(true);
		setEditTitle(faqItem.title);
		setEditContent(faqItem.content);
		if (!isExpanded) {
			setIsExpanded(true);
		}
	}, [faqItem.title, faqItem.content, isExpanded]);

	const handleEditCancel = useCallback(() => {
		setIsEditing(false);
		setEditTitle(faqItem.title);
		setEditContent(faqItem.content);
	}, [faqItem.title, faqItem.content]);

	const handleEditSave = useCallback(async () => {
		if (!editTitle.trim()) {
			setError(__("FAQ title cannot be empty.", "dki-wiki"));
			return;
		}

		setIsSaving(true);
		try {
			const updatedFAQItem = await faqItemsAPI.update({
				id: faqItem.id,
				title: editTitle.trim(),
				content: editContent,
			});

			dispatch({ type: "UPDATE_FAQ_ITEM", payload: updatedFAQItem });
			setIsEditing(false);
		} catch (error) {
			console.error("Error updating FAQ item:", error);
			setError(__("Failed to update FAQ item. Please try again.", "dki-wiki"));
		} finally {
			setIsSaving(false);
		}
	}, [faqItem.id, editTitle, editContent, dispatch, setError]);

	const handleDelete = useCallback(async () => {
		if (!window.confirm(__("Are you sure you want to delete this FAQ item?", "dki-wiki"))) {
			return;
		}

		setIsDeleting(true);
		try {
			await faqItemsAPI.delete(faqItem.id);
			dispatch({
				type: "DELETE_FAQ_ITEM",
				payload: { itemId: faqItem.id, sectionId: sectionId },
			});
		} catch (error) {
			console.error("Error deleting FAQ item:", error);
			setError(__("Failed to delete FAQ item. Please try again.", "dki-wiki"));
			setIsDeleting(false);
		}
	}, [faqItem.id, sectionId, dispatch, setError]);

	const handleKeyDown = useCallback(
		(e: React.KeyboardEvent) => {
			if (isEditing && e.key === "Enter" && !e.shiftKey) {
				e.preventDefault();
				handleEditSave();
			} else if (isEditing && e.key === "Escape") {
				handleEditCancel();
			}
		},
		[isEditing, handleEditSave, handleEditCancel]
	);

	const handleContentChange = useCallback((content: string) => {
		setEditContent(content);
	}, []);

	return (
		<Draggable draggableId={`faq-${faqItem.id}`} index={index}>
			{(provided, snapshot) => (
				<div
					ref={provided.innerRef}
					{...provided.draggableProps}
					className={classNames("faq-item-accordion", {
						"faq-item-accordion--expanded": isExpanded,
						"faq-item-accordion--editing": isEditing,
						"faq-item-accordion--dragging": snapshot.isDragging,
						"faq-item-accordion--deleting": isDeleting,
					})}
				>
					<div className="faq-item-accordion__header">
						<div {...provided.dragHandleProps} className="faq-item-accordion__drag-handle" title={__("Drag to reorder FAQ item", "dki-wiki")}>
							<span className="dashicons dashicons-move"></span>
						</div>

						<button className="faq-item-accordion__toggle" onClick={handleToggle} aria-expanded={isExpanded} aria-label={__("Toggle FAQ item", "dki-wiki")} disabled={isEditing}>
							<span
								className={classNames("dashicons", {
									"dashicons-arrow-down-alt2": !isExpanded,
									"dashicons-arrow-up-alt2": isExpanded,
								})}
							></span>
						</button>

						<div className="faq-item-accordion__title-container">
							{isEditing ? (
								<input ref={titleInputRef} type="text" value={editTitle} onChange={(e) => setEditTitle(e.target.value)} onKeyDown={handleKeyDown} className="faq-item-accordion__title-input" disabled={isSaving} />
							) : (
								<h4 className="faq-item-accordion__title" onClick={handleToggle}>
									{faqItem.title}
								</h4>
							)}

							<div className="faq-item-accordion__meta">
								<span className="faq-item-accordion__status">{faqItem.status === "publish" ? __("Published", "dki-wiki") : __("Draft", "dki-wiki")}</span>
								<span className="faq-item-accordion__modified">
									{__("Modified", "dki-wiki")}: {new Date(faqItem.dateModified).toLocaleDateString()}
								</span>
							</div>
						</div>

						<div className="faq-item-accordion__actions">
							{!isEditing ? (
								<>
									<button className="button button-small" onClick={handleEditStart} title={__("Edit FAQ item", "dki-wiki")}>
										<span className="dashicons dashicons-edit"></span>
									</button>
									<button className="button button-small button-link-delete" onClick={handleDelete} disabled={isDeleting} title={__("Delete FAQ item", "dki-wiki")}>
										<span className="dashicons dashicons-trash"></span>
									</button>
								</>
							) : (
								<>
									<button className="button button-small button-primary" onClick={handleEditSave} disabled={isSaving || !editTitle.trim()} title={__("Save changes", "dki-wiki")}>
										{isSaving ? <span className="spinner is-active" style={{ float: "none" }}></span> : <span className="dashicons dashicons-yes"></span>}
									</button>
									<button className="button button-small" onClick={handleEditCancel} disabled={isSaving} title={__("Cancel editing", "dki-wiki")}>
										<span className="dashicons dashicons-no"></span>
									</button>
								</>
							)}
						</div>
					</div>

					{isExpanded && (
						<div className="faq-item-accordion__content">
							{isEditing ? (
								<div className="faq-item-accordion__editor">
									<WYSIWYGEditor value={editContent} onChange={handleContentChange} placeholder={__("Enter the FAQ answer...", "dki-wiki")} disabled={isSaving} />
								</div>
							) : (
								<div className="faq-item-accordion__content-display" dangerouslySetInnerHTML={{ __html: faqItem.content }} />
							)}
						</div>
					)}
				</div>
			)}
		</Draggable>
	);
};

export default FAQItemAccordion;
