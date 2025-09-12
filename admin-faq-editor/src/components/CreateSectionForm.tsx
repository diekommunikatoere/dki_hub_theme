import React, { useState } from "react";
import { __ } from "@wordpress/i18n";
import { sectionsAPI } from "../services/api";
import { FAQSection } from "../types";

interface CreateSectionFormProps {
	onSectionCreated: (section: FAQSection) => void;
	onCancel: () => void;
}

const CreateSectionForm: React.FC<CreateSectionFormProps> = ({ onSectionCreated, onCancel }) => {
	const [title, setTitle] = useState("");
	const [description, setDescription] = useState("");
	const [isLoading, setIsLoading] = useState(false);
	const [error, setError] = useState<string | null>(null);

	const handleSubmit = async (e: React.FormEvent) => {
		e.preventDefault();

		if (!title.trim()) {
			setError(__("Section title is required.", "dki-wiki"));
			return;
		}

		setIsLoading(true);
		setError(null);

		try {
			const newSection = await sectionsAPI.create({
				name: title.trim(),
				description: description.trim() || undefined,
			});

			onSectionCreated(newSection);
			setTitle("");
			setDescription("");
		} catch (error) {
			console.error("Error creating section:", error);
			setError(__("Failed to create section. Please try again.", "dki-wiki"));
		} finally {
			setIsLoading(false);
		}
	};

	return (
		<div className="create-section-form">
			<div className="create-section-form__overlay" onClick={onCancel} />
			<div className="create-section-form__modal">
				<h3>{__("Create New Section", "dki-wiki")}</h3>

				{error && (
					<div className="notice notice-error">
						<p>{error}</p>
					</div>
				)}

				<form onSubmit={handleSubmit}>
					<table className="form-table">
						<tbody>
							<tr>
								<th scope="row">
									<label htmlFor="section-title">
										{__("Section Title", "dki-wiki")} <span className="required">*</span>
									</label>
								</th>
								<td>
									<input type="text" id="section-title" value={title} onChange={(e) => setTitle(e.target.value)} className="regular-text" required disabled={isLoading} autoFocus />
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label htmlFor="section-description">
										{__("Description", "dki-wiki")} <span className="optional">({__("optional", "dki-wiki")})</span>
									</label>
								</th>
								<td>
									<textarea id="section-description" value={description} onChange={(e) => setDescription(e.target.value)} className="large-text" rows={3} disabled={isLoading} />
									<p className="description">{__("Brief description of what this section contains.", "dki-wiki")}</p>
								</td>
							</tr>
						</tbody>
					</table>

					<div className="create-section-form__actions">
						<button type="submit" className="button button-primary" disabled={isLoading || !title.trim()}>
							{isLoading ? __("Creating...", "dki-wiki") : __("Create Section", "dki-wiki")}
						</button>
						<button type="button" className="button" onClick={onCancel} disabled={isLoading}>
							{__("Cancel", "dki-wiki")}
						</button>
					</div>
				</form>
			</div>
		</div>
	);
};

export default CreateSectionForm;
