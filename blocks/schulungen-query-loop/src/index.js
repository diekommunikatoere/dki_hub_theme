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
	<svg id="uuid-c5bcf708-d032-4f04-a47a-91ba39495a62" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
		<path class="uuid-94f0575d-14cf-4599-a166-d14cb66954e8" d="M6.05,17.77c-.33-.18-.59-.43-.78-.74s-.28-.65-.28-1.04v-4.8l-2.4-1.33c-.18-.1-.32-.23-.4-.38s-.13-.32-.13-.5.04-.35.13-.5.22-.28.4-.38L11.05,3.52c.15-.08.3-.15.46-.19s.32-.06.49-.06.33.02.49.06.31.1.46.19l9.53,5.2c.17.08.3.2.39.36s.14.33.14.51v6.4c0,.28-.1.52-.29.71s-.43.29-.71.29-.52-.1-.71-.29-.29-.43-.29-.71v-5.9l-2,1.1v4.8c0,.38-.09.73-.28,1.04s-.44.55-.78.74l-5,2.7c-.15.08-.3.15-.46.19s-.32.06-.49.06-.33-.02-.49-.06-.31-.1-.46-.19l-5-2.7ZM12,12.7l6.85-3.7-6.85-3.7-6.85,3.7,6.85,3.7ZM12,18.72l5-2.7v-3.78l-4.03,2.23c-.15.08-.31.15-.48.19s-.33.06-.5.06-.33-.02-.5-.06-.33-.1-.48-.19l-4.03-2.23v3.78l5,2.7Z" />
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
