/**
 * Copy Field Block Registration
 * Registers a new block for copying formatted text and code snippets
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from "@wordpress/blocks";
import { registerFormatType } from "@wordpress/rich-text";
import { RichTextToolbarButton } from "@wordpress/block-editor";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./style.scss";

/**
 * Internal dependencies
 */
import Edit from "./edit";
import metadata from "./block.json";

// Copy icon for the block
const copyIcon = (
	<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
		<path class="uuid-5cb8475b-17c5-477b-817f-fe796bdc22c9" d="M9.5,18c-.55,0-1.02-.2-1.41-.59-.39-.39-.59-.86-.59-1.41V4c0-.55.2-1.02.59-1.41s.86-.59,1.41-.59h9c.55,0,1.02.2,1.41.59s.59.86.59,1.41v12c0,.55-.2,1.02-.59,1.41-.39.39-.86.59-1.41.59h-9ZM9.5,16h9V4h-9v12ZM5.5,22c-.55,0-1.02-.2-1.41-.59-.39-.39-.59-.86-.59-1.41V7c0-.28.1-.52.29-.71.19-.19.43-.29.71-.29s.52.1.71.29.29.43.29.71v13h10c.28,0,.52.1.71.29s.29.43.29.71-.1.52-.29.71-.43.29-.71.29H5.5ZM9.5,16V4v12Z" />
	</svg>
);

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType(metadata.name, {
	/**
	 * @see ./edit.js
	 */
	edit: Edit,

	/**
	 * Block icon
	 */
	icon: copyIcon,
});
