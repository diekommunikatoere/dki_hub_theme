/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from "@wordpress/i18n";

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useEffect, useState, useMemo } from "@wordpress/element";
import { useSelect } from "@wordpress/data";
import { useBlockProps, InspectorControls } from "@wordpress/block-editor";
import { PanelBody, SelectControl, Placeholder, Spinner } from "@wordpress/components";
import { parse } from "@wordpress/blocks";
import { store as coreDataStore } from "@wordpress/core-data";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
	const { selectedMenuId } = attributes;
	const [currentUser, setCurrentUser] = useState(null);

	// Fetch all wp_navigation CPT posts
	const [menus, menusLoading] = useSelect((select) => {
		return [select(coreDataStore).getEntityRecords("postType", "wp_navigation"), select(coreDataStore).isResolving("getEntityRecords", ["postType", "wp_navigation"])];
	});

	const selectedMenuPost = useSelect(
		(select) => {
			if (!selectedMenuId) {
				return null;
			}
			return select(coreDataStore).getEntityRecord("postType", "wp_navigation", selectedMenuId);
		},
		[selectedMenuId]
	);

	// Fetch the content of the selected wp_navigation CPT post
	const menuItems = useMemo(() => {
		if (!selectedMenuPost) {
			return [];
		}

		// Parse the raw content to get the blocks
		const blocks = parse(selectedMenuPost.content.raw);
		const items = blocks.filter((block) => block.name === "core/navigation-link");

		const menuItemMap = items.map((block) => ({
			id: block.clientId,
			title: block.attributes.label,
			url: block.attributes.url,
			target: block.attributes.target,
		}));

		setAttributes({ menuItems: menuItemMap });
		return menuItemMap;
	}, [selectedMenuPost]);

	// Update block attributes when menu items are fetched
	if (JSON.stringify(attributes.menuItems) !== JSON.stringify(menuItems)) {
		setAttributes({ menuItems });
	}

	const menuOptions = menus
		? menus.map((menu) => ({
				label: menu.title.raw,
				value: menu.id,
		  }))
		: [];

	const onMenuSelect = (value) => {
		setAttributes({
			selectedMenuId: parseInt(value, 10) || 0,
		});
	};

	// Get current user info for preview
	useEffect(() => {
		const fetchCurrentUser = async () => {
			try {
				const response = await fetch("/wp-json/wp/v2/users/me", {
					credentials: "same-origin",
					headers: {
						"X-WP-Nonce": wpApiSettings?.nonce || "",
					},
				});
				if (response.ok) {
					const userData = await response.json();
					setCurrentUser(userData);
				}
			} catch (error) {
				console.error("Error fetching user data:", error);
				// Fallback user data for preview
				setCurrentUser({
					name: "Current User",
					avatar_urls: { 32: "https://via.placeholder.com/32" },
				});
			}
		};
		fetchCurrentUser();
	}, []);

	const blockProps = useBlockProps({
		className: "header-navigation-editor",
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title={__("Navigation Settings", "header-navigation")} initialOpen={true}>
					{menusLoading ? <Spinner /> : <SelectControl label={__("Select Navigation Menu", "header-navigation")} value={selectedMenuId} options={menuOptions} onChange={onMenuSelect} />}
				</PanelBody>
			</InspectorControls>
			{/* <div {...blockProps}>
				{!menus && <Spinner />}
				{menus && !selectedMenuId && <p>Please select a menu from the block settings.</p>}
				{selectedMenuId && menuItems.length > 0 && selectedMenuPost && (
					<ul>
						{menuItems.map((item) => (
							<li key={item.id}>
								<a href={item.url} target={item.target}>
									{item.title}
								</a>
							</li>
						))}
					</ul>
				)}
				{selectedMenuId && menuItems.length === 0 && menus && <p>This menu has no items.</p>}
			</div> */}

			<div {...blockProps}>
				{selectedMenuId === 0 ? (
					<Placeholder icon="menu" label={__("Header Navigation", "header-navigation")} instructions={__("Select a navigation menu from the block settings panel to get started.", "header-navigation")} />
				) : (
					<div className="header-navigation-wrapper">
						<div className="header-navigation-wrapper-inner">
							<a href="#" className="header-navigation-logo-link" aria-label="Zur Startseite" style={{ pointerEvents: "none" }}>
								<img src="/wp-content/uploads/2024/06/DKI_Wiki_logo.svg" className="header-navigation-logo" />
								<span>die kommunikatöre® Hub</span>
							</a>

							<div className="header-navigation-preview">
								<div className="navigation-menu-preview">
									{selectedMenuPost ? (
										<ul className="menu-items-preview">
											{menuItems.map((item) => (
												<li key={item.id} className="menu-item-preview">
													{item.title}
												</li>
											))}
											{selectedMenuPost.length > 5 && <li className="menu-item-preview more-items">{__("...and %d more items", "header-navigation").replace("%d", selectedMenuPost.length - 5)}</li>}
										</ul>
									) : (
										<div className="menu-loading">
											<Spinner />
											{__("Loading menu items...", "header-navigation")}
										</div>
									)}
								</div>

								<div className="profile-nav-preview">
									{currentUser ? (
										<div className="profile-preview">
											<img src={"/wp-content/uploads/ultimatemember/" + currentUser.id + "/profile_photo-190x190.jpg" || "https://via.placeholder.com/32"} alt={__(`Profilbild von ${currentUser.name}`, "header-navigation")} width="32" height="32" className="avatar-preview" />
										</div>
									) : (
										<div className="profile-loading">
											<Spinner />
										</div>
									)}
								</div>
							</div>
						</div>
					</div>
				)}
			</div>
		</>
	);
}
