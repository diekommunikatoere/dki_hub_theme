import { useBlockProps } from "@wordpress/block-editor";

export default function Edit() {
	return (
		<div {...useBlockProps()}>
			<p>FAQ Display Block Editor View</p>
		</div>
	);
}
