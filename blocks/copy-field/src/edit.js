import { PanelBody, TextControl, SelectControl, Button } from "@wordpress/components";
import { useBlockProps, InspectorControls, RichText } from "@wordpress/block-editor";
import { __ } from "@wordpress/i18n";
import { useRef, useEffect } from "@wordpress/element";
import { EditorView, lineNumbers, keymap, drawSelection, highlightActiveLine } from "@codemirror/view";
import { EditorState } from "@codemirror/state";
import { indentWithTab, history, defaultKeymap, historyKeymap } from "@codemirror/commands";
import { html } from "@codemirror/lang-html";
import { javascript } from "@codemirror/lang-javascript";
import { css } from "@codemirror/lang-css";
import { php } from "@codemirror/lang-php";
import { json } from "@codemirror/lang-json";
import { sql } from "@codemirror/lang-sql";
import { python } from "@codemirror/lang-python";
import { markdown } from "@codemirror/lang-markdown";
import { oneDark } from "@codemirror/theme-one-dark";

import "./editor.scss";

export default function Edit({ attributes, setAttributes }) {
	const { inputType, label, placeholder, content, codeLanguage, copyButtonText } = attributes;

	const richTextRef = useRef(null);
	const codeTextareaRef = useRef(null);
	const codeMirrorRef = useRef(null);
	const editorViewRef = useRef(null);

	const handleInputTypeChange = (newInputType) => {
		setAttributes({ inputType: newInputType });
		// Clear content when switching types to avoid format conflicts
		setAttributes({ content: "" });
	};

	const handleLabelChange = (newLabel) => {
		setAttributes({ label: newLabel });
	};

	const handlePlaceholderChange = (newPlaceholder) => {
		setAttributes({ placeholder: newPlaceholder });
	};

	const handleContentChange = (newContent) => {
		setAttributes({ content: newContent });
	};

	const handleCodeLanguageChange = (newLanguage) => {
		setAttributes({ codeLanguage: newLanguage });
	};

	const handleCopyButtonTextChange = (newText) => {
		setAttributes({ copyButtonText: newText });
	};

	const handleTabKey = (e) => {
		if (e.key === "Tab") {
			e.preventDefault();

			if (inputType === "richtext" && richTextRef.current) {
				// For rich text, insert 4 spaces at cursor position
				const selection = window.getSelection();
				const range = selection.getRangeAt(0);
				const span = document.createTextNode("    ");
				range.insertNode(span);
				range.setStartAfter(span);
				selection.removeAllRanges();
				selection.addRange(range);

				// Update content
				const newContent = richTextRef.current.innerHTML;
				setAttributes({ content: newContent });
			} else if (inputType === "code" && codeTextareaRef.current) {
				// For code textarea, insert 4 spaces at cursor position
				const start = codeTextareaRef.current.selectionStart;
				const end = codeTextareaRef.current.selectionEnd;
				const newContent = content.substring(0, start) + "    " + content.substring(end);
				setAttributes({ content: newContent });

				// Set cursor position after the inserted spaces
				setTimeout(() => {
					codeTextareaRef.current.selectionStart = start + 4;
					codeTextareaRef.current.selectionEnd = start + 4;
					codeTextareaRef.current.focus();
				}, 0);
			}
		}
	};

	// Initialize CodeMirror editor for code input
	useEffect(() => {
		if (inputType === "code" && codeMirrorRef.current && !editorViewRef.current) {
			const getLanguageExtension = () => {
				switch (codeLanguage) {
					case "html":
						return html();
					case "javascript":
						return javascript();
					case "css":
						return css();
					case "php":
						return php();
					case "json":
						return json();
					case "sql":
						return sql();
					case "python":
						return python();
					case "markdown":
						return markdown();
					default:
						return html();
				}
			};

			const state = EditorState.create({
				doc: content,
				extensions: [
					getLanguageExtension(),
					lineNumbers(),
					EditorView.lineWrapping,
					oneDark,
					history(),
					drawSelection(),
					highlightActiveLine(),
					keymap.of([indentWithTab, ...historyKeymap, ...defaultKeymap]),
					EditorView.updateListener.of((update) => {
						if (update.docChanged) {
							setAttributes({ content: update.state.doc.toString() });
						}
					}),
					EditorView.theme({
						"&": {
							height: "100%",
							fontFamily: "monospace",
							fontSize: "14px",
							lineHeight: "1.4",
						},
						".cm-content": {
							padding: "8px",
						},
					}),
				],
			});

			editorViewRef.current = new EditorView({
				state,
				parent: codeMirrorRef.current,
			});

			return () => {
				if (editorViewRef.current) {
					editorViewRef.current.destroy();
					editorViewRef.current = null;
				}
			};
		}
	}, [inputType, codeLanguage]);

	// Update CodeMirror content when it changes from outside
	useEffect(() => {
		if (editorViewRef.current && content !== editorViewRef.current.state.doc.toString()) {
			editorViewRef.current.dispatch({
				changes: {
					from: 0,
					to: editorViewRef.current.state.doc.length,
					insert: content,
				},
			});
		}
	}, [content]);

	const renderContentEditor = () => {
		if (inputType === "richtext") {
			return <RichText ref={richTextRef} tagName="div" value={content} onChange={handleContentChange} placeholder={placeholder} className="copy-field-richtext" allowedFormats={["core/bold", "core/italic", "core/link", "core/strikethrough"]} onKeyDown={handleTabKey} />;
		} else if (inputType === "code") {
			return <div ref={codeMirrorRef} className="copy-field-code-editor" />;
		}
	};

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__("Kopierfeld-Einstellungen", "copy-field")}>
					<SelectControl
						label={__("Eingabetyp", "copy-field")}
						value={inputType}
						options={[
							{ label: __("Rich Text (Standard)", "copy-field"), value: "richtext" },
							{ label: __("Code-Block", "copy-field"), value: "code" },
						]}
						onChange={handleInputTypeChange}
					/>

					<TextControl label={__("Feldname", "copy-field")} value={label} onChange={handleLabelChange} />

					<TextControl label={__("Platzhaltertext", "copy-field")} value={placeholder} onChange={handlePlaceholderChange} />

					{inputType === "code" && (
						<SelectControl
							label={__("Code-Sprache", "copy-field")}
							value={codeLanguage}
							options={[
								{ label: "HTML", value: "html" },
								{ label: "JavaScript", value: "javascript" },
								{ label: "CSS", value: "css" },
								{ label: "SCSS", value: "scss" },
								{ label: "LESS", value: "less" },
								{ label: "PHP", value: "php" },
								{ label: "JSON", value: "json" },
								{ label: "SQL", value: "sql" },
								{ label: "Bash/Shell", value: "bash" },
								{ label: "Python", value: "python" },
								{ label: "Apache", value: "apache" },
								{ label: "AsciiDoc", value: "asciidoc" },
								{ label: "GraphQL", value: "graphql" },
								{ label: "HTTP", value: "http" },
								{ label: "Markdown", value: "markdown" },
								{ label: "Nginx", value: "nginx" },
								{ label: "PostgreSQL", value: "pgsql" },
								{ label: "TypeScript", value: "" },
							]}
							onChange={handleCodeLanguageChange}
						/>
					)}

					<TextControl label={__("Button-Text", "copy-field")} value={copyButtonText} onChange={handleCopyButtonTextChange} />
				</PanelBody>
			</InspectorControls>

			<div className="copy-field-wrapper">
				<div className="copy-field-header">
					{label && <label className="copy-field-label">{label}</label>}
					<Button isPrimary className="copy-field-button" disabled>
						{copyButtonText || "Kopieren"}
					</Button>
				</div>

				<div className="copy-field-content-wrapper">{renderContentEditor()}</div>

				{inputType === "code" && codeLanguage && (
					<small className="copy-field-language-label">
						{__("Sprache:", "copy-field")} {codeLanguage}
					</small>
				)}
			</div>
		</div>
	);
}
