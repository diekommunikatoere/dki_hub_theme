/** @type {import("stylelint").Config} */
export default {
	extends: ["stylelint-config-standard", "stylelint-config-standard-scss"],
	rules: {
		"custom-property-pattern": [
			"^(wp--)?([a-z][a-z0-9]*)(-[a-z0-9]+)*$",
			{
				message: (name) => `Expected custom property name "${name}" to be kebab-case with an optional "wp--" prefix`,
			},
		],
		"scss/dollar-variable-pattern": null,
		"custom-property-pattern": [
			"^(wp|dki|--dki){1}(--[a-z0-9]+)(--[a-z0-9]+-?[a-z0-9]+)+((-[a-z0-9]+)+|(--[a-z0-9]+)+|(--[a-z0-9]+-[a-z0-9-]+)+)?$",
			{
				message: (name) => `Expected custom property name "${name}" to be kebab-case with format "dki--<property>--<name>[-<modifier>]" or "wp--[preset|custom|global]--<property--<name>[-<modifier>]"`,
			},
		],
		"selector-class-pattern": [
			// wp-block-button, wp-block-button__link, wp-block-button--large
			// and wp-block-button__link--large
			// dki-button--large
			// betterdocs--something
			"^([a-z0-9]+)$|([a-z0-9]+)-([a-z0-9]+|-[a-z0-9]+)(-[a-z0-9]+|--[a-z0-9]+|__[a-z0-9]+)*$",
			{
				message: (name) => `Expected class selector "${name}" to be kebab-case with a "wp-block-" or "dki-" or "betterdocs--" prefix`,
			},
		],
		"scss/at-mixin-pattern": [
			"^(-?[a-z][a-z0-9]*)(-[a-z0-9]+|--[a-z0-9]+)*$",
			{
				message: "Expected mixin name to be kebab-case",
			},
		],
		"shorthand-property-no-redundant-values": true,
		"no-descending-specificity": null,
		"font-family-no-missing-generic-family-keyword": null,
	},
};
