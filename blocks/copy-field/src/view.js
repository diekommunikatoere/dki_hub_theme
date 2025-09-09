/**
 * Copy Field Block - Frontend functionality
 * Handles clipboard operations, syntax highlighting with CodeMirror, and user interactions
 */
import { EditorView, lineNumbers } from "@codemirror/view";
import { EditorState } from "@codemirror/state";
import { html } from "@codemirror/lang-html";
import { javascript } from "@codemirror/lang-javascript";
import { css } from "@codemirror/lang-css";
import { php } from "@codemirror/lang-php";
import { json } from "@codemirror/lang-json";
import { sql } from "@codemirror/lang-sql";
import { python } from "@codemirror/lang-python";
import { markdown } from "@codemirror/lang-markdown";
import { oneDark } from "@codemirror/theme-one-dark";

document.addEventListener("DOMContentLoaded", function () {
	// Initialize CodeMirror for syntax highlighting
	const codeBlocks = document.querySelectorAll(".copy-field-code-block");

	codeBlocks.forEach((block) => {
		const language = block.getAttribute("data-language");
		const content = block.textContent;

		// Clear the block content
		block.innerHTML = "";

		const getLanguageExtension = () => {
			switch (language) {
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
				EditorView.editable.of(false), // Read-only mode
				EditorView.theme({
					"&": {
						height: "auto",
						fontFamily: "monospace",
						fontSize: "14px",
						lineHeight: "1.4",
					},
					".cm-content": {
						padding: "8px",
					},
					".cm-scroller": {
						overflow: "hidden",
					},
				}),
			],
		});

		const view = new EditorView({
			state,
			parent: block,
		});
	});

	// Find all copy field buttons
	const copyButtons = document.querySelectorAll(".copy-field-button");

	copyButtons.forEach((button) => {
		button.addEventListener("click", handleCopy);
	});

	// Handle copy button click
	function handleCopy(event) {
		const button = event.currentTarget;
		const targetSelector = button.getAttribute("data-copy-target");
		const copyText = button.getAttribute("data-copy-text");
		const copiedText = button.getAttribute("data-copied-text");
		const errorText = button.getAttribute("data-error-text");
		const buttonTextSpan = button.querySelector(".copy-button-text");

		if (!targetSelector) {
			console.error("Copy target not specified");
			return;
		}

		const targetElement = document.querySelector(targetSelector);
		if (!targetElement) {
			console.error("Copy target element not found:", targetSelector);
			return;
		}

		// Get content based on element type
		let textToCopy = getContentToCopy(targetElement);

		if (!textToCopy.trim()) {
			console.warn("No content to copy");
			return;
		}

		// Attempt to copy to clipboard
		copyToClipboard(textToCopy)
			.then(() => {
				// Success feedback
				if (buttonTextSpan) {
					buttonTextSpan.textContent = copiedText;
				}
				button.classList.add("copied");

				// Reset button after 2 seconds
				setTimeout(() => {
					if (buttonTextSpan) {
						buttonTextSpan.textContent = copyText;
					}
					button.classList.remove("copied");
				}, 2000);
			})
			.catch((error) => {
				console.error("Copy failed:", error);

				// Error feedback
				if (buttonTextSpan) {
					buttonTextSpan.textContent = errorText;
				}
				button.classList.add("error");

				// Reset button after 2 seconds
				setTimeout(() => {
					if (buttonTextSpan) {
						buttonTextSpan.textContent = copyText;
					}
					button.classList.remove("error");
				}, 2000);
			});
	}

	// Extract content from different element types
	function getContentToCopy(element) {
		const tagName = element.tagName.toLowerCase();

		if (tagName === "textarea") {
			return element.value;
		} else if (element.classList.contains("copy-field-code-block")) {
			// For CodeMirror code blocks, get the original text content
			return element.textContent;
		} else if (element.classList.contains("copy-field-richtext")) {
			// For rich text, preserve HTML content
			return element.innerHTML;
		} else {
			// Fallback to text content
			return element.textContent || element.innerText || element.value || "";
		}
	}

	// Copy text to clipboard with fallbacks
	function copyToClipboard(text) {
		// Modern clipboard API (preferred)
		if (navigator.clipboard && window.isSecureContext) {
			const htmlContent = text;
			const data = [new ClipboardItem({ "text/html": htmlContent })];
			return navigator.clipboard.write(data);
		}

		// Fallback for older browsers
		return new Promise((resolve, reject) => {
			const textArea = document.createElement("textarea");
			textArea.value = text;
			textArea.style.position = "fixed";
			textArea.style.left = "-999999px";
			textArea.style.top = "-999999px";
			document.body.appendChild(textArea);

			try {
				textArea.focus();
				textArea.select();
				const successful = document.execCommand("copy");
				document.body.removeChild(textArea);

				if (successful) {
					resolve();
				} else {
					reject(new Error("execCommand copy failed"));
				}
			} catch (error) {
				document.body.removeChild(textArea);
				reject(error);
			}
		});
	}

	// Handle contenteditable rich text fields
	const richTextFields = document.querySelectorAll(".copy-field-richtext");
	richTextFields.forEach((field) => {
		// Update copy button state when content changes
		field.addEventListener("input", function () {
			const wrapper = field.closest(".copy-field-wrapper");
			const copyButton = wrapper ? wrapper.querySelector(".copy-field-button") : null;

			if (copyButton) {
				const hasContent = field.textContent.trim().length > 0;
				copyButton.disabled = !hasContent;
			}
		});

		// Show placeholder when empty
		field.addEventListener("blur", function () {
			if (!field.textContent.trim()) {
				field.classList.add("empty");
			} else {
				field.classList.remove("empty");
			}
		});

		// Initialize placeholder state
		if (!field.textContent.trim()) {
			field.classList.add("empty");
		}
	});

	// Handle code fields (CodeMirror blocks)
	const codeMirrorBlocks = document.querySelectorAll(".copy-field-code-block");
	codeMirrorBlocks.forEach((block) => {
		const wrapper = block.closest(".copy-field-wrapper");
		const copyButton = wrapper ? wrapper.querySelector(".copy-field-button") : null;

		if (copyButton) {
			const hasContent = block.textContent.trim().length > 0;
			copyButton.disabled = !hasContent;
		}
	});
});
