import { PanelBody, TextControl } from "@wordpress/components";
import { useBlockProps, InspectorControls } from "@wordpress/block-editor";
import { __ } from "@wordpress/i18n";

import "./editor.scss";

export default function Edit({ attributes, setAttributes }) {
	const { targetDisplayId, placeholder } = attributes;

	const blockProps = useBlockProps();

	const handlePlaceholderChange = (newPlaceholder) => {
		setAttributes({ placeholder: newPlaceholder });
	};

	const handleTargetIdChange = (newId) => {
		setAttributes({ targetDisplayId: newId });
	};

	// Preview content
	const previewContent = (
		<div className="faq-search-preview">
			<input type="search" className="faq-search-input" placeholder={placeholder || __("Suchen Sie in den FAQs...", "faq-search")} readOnly />
		</div>
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={__("FAQ Search Einstellungen", "faq-search")}>
					<TextControl label={__("Target Display Block ID", "faq-search")} value={targetDisplayId} onChange={handleTargetIdChange} help={__("ID des FAQ Display Blocks zum Filtern (z.B. faq-display-abc123)", "faq-search")} />
					<TextControl label={__("Platzhalter Text", "faq-search")} value={placeholder} onChange={handlePlaceholderChange} placeholder={__("Suchen Sie in den FAQs...", "faq-search")} />
				</PanelBody>
			</InspectorControls>
			<div {...blockProps}>
				{previewContent}
				<p className="faq-search-preview-note">{__("Vorschau: Die Suche filtert FAQs client-side mit Fuse.js.", "faq-search")}</p>
			</div>
		</>
	);
}
