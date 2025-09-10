import { PanelBody, ToggleControl, SelectControl } from "@wordpress/components";
import { useBlockProps, InspectorControls } from "@wordpress/block-editor";
import { __ } from "@wordpress/i18n";

import "./editor.scss";

export default function Edit({ attributes, setAttributes }) {
	const { showSections, accordionStyle } = attributes;

	const blockProps = useBlockProps();

	// Preview content (static for editor)
	const previewContent = (
		<div className="faq-display-preview">
			<div className="faq-section-preview">
				<h3 className="faq-section-title">{__("Beispiel Abschnitt", "faq-display")}</h3>
				<div className="faq-item-preview">
					<div className="faq-question">{__("Beispiel Frage?", "faq-display")}</div>
					<div className="faq-answer">{__("Beispiel Antwort: Dieser Block zeigt FAQs aus dem CPT an.", "faq-display")}</div>
				</div>
			</div>
		</div>
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={__("FAQ Display Einstellungen", "faq-display")}>
					<ToggleControl label={__("Abschnitte anzeigen", "faq-display")} checked={showSections} onChange={(value) => setAttributes({ showSections: value })} />
					<SelectControl
						label={__("Akkordeon Stil", "faq-display")}
						value={accordionStyle}
						options={[
							{ label: __("Standard", "faq-display"), value: "default" },
							{ label: __("Modern", "faq-display"), value: "modern" },
						]}
						onChange={(value) => setAttributes({ accordionStyle: value })}
					/>
				</PanelBody>
			</InspectorControls>
			<div {...blockProps}>
				{previewContent}
				<p className="faq-preview-note">{__("Vorschau: FAQs werden auf der Frontend-Seite dynamisch geladen.", "faq-display")}</p>
			</div>
		</>
	);
}
