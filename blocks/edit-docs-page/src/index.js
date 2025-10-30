/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from "@wordpress/blocks";

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

const editIcon = (
	<svg
		xmlns="http://www.w3.org/2000/svg"
		width="24"
		height="24"
		viewBox="0 0 24 24"
	>
		<path
			class="uuid-dd210f2b-4e74-488e-a0a3-987861d78487"
			d="M12.5,21v-1.65c0-.13.03-.26.08-.39s.13-.24.23-.34l5.23-5.2c.15-.15.32-.26.5-.33s.37-.1.55-.1c.2,0,.39.04.58.11s.35.19.5.34l.93.93c.13.15.24.32.31.5s.11.37.11.55-.03.37-.1.56-.18.36-.33.51l-5.2,5.2c-.1.1-.21.18-.34.23s-.25.08-.39.08h-1.65c-.28,0-.52-.1-.71-.29s-.29-.43-.29-.71ZM20,15.43l-.93-.93.93.93ZM14,20.5h.95l3.03-3.05-.93-.93-3.05,3.03v.95ZM4.5,22c-.55,0-1.02-.2-1.41-.59s-.59-.86-.59-1.41V4c0-.55.2-1.02.59-1.41s.86-.59,1.41-.59h7.18c.27,0,.52.05.76.15s.45.24.64.43l4.85,4.85c.18.18.33.4.43.64s.15.5.15.76v1.43c0,.28-.1.52-.29.71s-.43.29-.71.29-.52-.1-.71-.29-.29-.43-.29-.71v-1.25h-4c-.28,0-.52-.1-.71-.29s-.29-.43-.29-.71V4h-7v16h5c.28,0,.52.1.71.29s.29.43.29.71-.1.52-.29.71-.43.29-.71.29h-5ZM4.5,20V4v16ZM17.53,16.98l-.48-.45.93.93-.45-.48Z"
		/>
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

	icon: editIcon,
});
