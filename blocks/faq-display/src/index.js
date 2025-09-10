/**
 * FAQ Display Block Registration
 * Registers a new block for displaying FAQs in nested accordions
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

// Accordion icon for the block
const accordionIcon = (
	<svg id="uuid-f7ba84ac-3c31-4fd5-8ab6-1030fced38af" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
		<path
			class="uuid-126c0d3e-9025-4c67-bbcd-f02af35b8e9b"
			d="M12,22.5c-1.05,0-1.94-.36-2.66-1.09s-1.09-1.61-1.09-2.66c0-.87.26-1.63.78-2.29s1.18-1.1,1.98-1.34v-2.13h-4c-.55,0-1.02-.2-1.41-.59s-.59-.86-.59-1.41v-2h-1.5c-.28,0-.52-.1-.71-.29s-.29-.43-.29-.71V3c0-.28.1-.52.29-.71s.43-.29.71-.29h5c.28,0,.52.1.71.29s.29.43.29.71v5c0,.28-.1.52-.29.71s-.43.29-.71.29h-1.5v2h10v-2.13c-.8-.23-1.46-.68-1.98-1.34s-.78-1.42-.78-2.29c0-1.05.36-1.94,1.09-2.66s1.61-1.09,2.66-1.09,1.94.36,2.66,1.09,1.09,1.61,1.09,2.66c0,.87-.26,1.63-.78,2.29s-1.18,1.1-1.98,1.34v2.13c0,.55-.2,1.02-.59,1.41s-.86.59-1.41.59h-4v2.13c.8.23,1.46.68,1.98,1.34s.78,1.42.78,2.29c0,1.05-.36,1.94-1.09,2.66s-1.61,1.09-2.66,1.09ZM18,7c.48,0,.9-.17,1.24-.51s.51-.75.51-1.24-.17-.9-.51-1.24-.75-.51-1.24-.51-.9.17-1.24.51-.51.75-.51,1.24.17.9.51,1.24.75.51,1.24.51ZM4.5,7h3v-3h-3v3ZM12,20.5c.48,0,.9-.17,1.24-.51s.51-.75.51-1.24-.17-.9-.51-1.24c-.34-.34-.75-.51-1.24-.51s-.9.17-1.24.51c-.34.34-.51.75-.51,1.24s.17.9.51,1.24.75.51,1.24.51Z"
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

	/**
	 * Block icon
	 */
	icon: accordionIcon,
});
