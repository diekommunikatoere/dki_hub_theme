/**
 * FAQ Search Block Registration
 * Registers a new block for FAQ search functionality
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

// Search icon for the block
const searchIcon = (
	<svg id="uuid-89ded76f-674b-4d18-b48f-80469107f77f" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
		<path class="uuid-98ed824d-d9c0-4b1f-8290-4b7cef102585" d="M9.5,16c-1.82,0-3.35-.63-4.61-1.89s-1.89-2.8-1.89-4.61.63-3.35,1.89-4.61,2.8-1.89,4.61-1.89,3.35.63,4.61,1.89,1.89,2.8,1.89,4.61c0,.73-.12,1.43-.35,2.08s-.55,1.23-.95,1.73l5.6,5.6c.18.18.28.42.28.7s-.09.52-.28.7-.42.28-.7.28-.52-.09-.7-.28l-5.6-5.6c-.5.4-1.08.72-1.73.95s-1.34.35-2.08.35ZM9.5,14c1.25,0,2.31-.44,3.19-1.31s1.31-1.94,1.31-3.19-.44-2.31-1.31-3.19-1.94-1.31-3.19-1.31-2.31.44-3.19,1.31-1.31,1.94-1.31,3.19.44,2.31,1.31,3.19,1.94,1.31,3.19,1.31Z" />
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
	icon: searchIcon,
});
