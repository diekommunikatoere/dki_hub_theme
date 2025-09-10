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
	<svg id="uuid-b2f0952d-cb8e-491f-a878-967a4a35e6cc" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
		<path class="uuid-35e114ca-caa4-470a-9dc1-ebf46ab6dcfd" d="M13,21c-.28,0-.52-.1-.71-.29-.19-.19-.29-.43-.29-.71s.1-.52.29-.71.43-.29.71-.29h6V5h-6c-.28,0-.52-.1-.71-.29s-.29-.43-.29-.71.1-.52.29-.71.43-.29.71-.29h6c.55,0,1.02.2,1.41.59s.59.86.59,1.41v14c0,.55-.2,1.02-.59,1.41-.39.39-.86.59-1.41.59h-6ZM11.18,13h-7.18c-.28,0-.52-.1-.71-.29-.19-.19-.29-.43-.29-.71s.1-.52.29-.71c.19-.19.43-.29.71-.29h7.18l-1.88-1.88c-.18-.18-.28-.41-.28-.68s.09-.5.28-.7.42-.3.7-.31.53.09.73.29l3.58,3.58c.2.2.3.43.3.7s-.1.5-.3.7l-3.58,3.58c-.2.2-.44.3-.71.29s-.51-.11-.71-.31c-.18-.2-.27-.44-.26-.71s.1-.5.29-.69l1.85-1.85Z" />
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
