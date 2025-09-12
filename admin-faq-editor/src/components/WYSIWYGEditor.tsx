import React, { useMemo } from "react";
import ReactQuill from "react-quill";
import "react-quill/dist/quill.snow.css";
import "./WYSIWYGEditor.scss";

interface WYSIWYGEditorProps {
	value: string;
	onChange: (value: string) => void;
	placeholder?: string;
	disabled?: boolean;
}

const WYSIWYGEditor: React.FC<WYSIWYGEditorProps> = ({ value, onChange, placeholder = "Enter content...", disabled = false }) => {
	// Configure ReactQuill modules
	const modules = useMemo(
		() => ({
			toolbar: [[{ header: [1, 2, 3, false] }], ["bold", "italic", "underline", "strike"], ["blockquote", "code-block"], [{ list: "ordered" }, { list: "bullet" }], [{ script: "sub" }, { script: "super" }], [{ indent: "-1" }, { indent: "+1" }], [{ align: [] }], ["link", "image"], ["clean"]],
			clipboard: {
				// Toggle to add extra line breaks when pasting HTML:
				matchVisual: false,
			},
		}),
		[]
	);

	const formats = useMemo(() => ["header", "font", "size", "bold", "italic", "underline", "strike", "blockquote", "list", "bullet", "indent", "link", "image", "align", "script", "code-block"], []);

	return (
		<div className="wysiwyg-editor">
			<ReactQuill
				theme="snow"
				value={value}
				onChange={onChange}
				placeholder={placeholder}
				readOnly={disabled}
				modules={modules}
				formats={formats}
				style={{
					minHeight: "200px",
				}}
			/>
		</div>
	);
};

export default WYSIWYGEditor;
