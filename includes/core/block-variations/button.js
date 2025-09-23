wp.blocks.registerBlockStyle("core/button", {
	name: "primary",
	label: "Standard",
	isDefault: true,
});

wp.blocks.registerBlockStyle("core/button", {
	name: "neutral",
	label: "Neutral",
});

wp.blocks.registerBlockStyle("core/button", {
	name: "save",
	label: "Speichern",
});

wp.blocks.registerBlockStyle("core/button", {
	name: "error",
	label: "Warnung",
});

wp.domReady(function () {
	wp.blocks.unregisterBlockStyle("core/button", "fill");
});
wp.domReady(function () {
	wp.blocks.unregisterBlockStyle("core/button", "outline");
});
