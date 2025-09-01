// Logic for dropdown menu
// target id: navbarDropdown

document.onreadystatechange = function () {
	if (document.readyState === "complete") {
		const dropdown = document.getElementById("navbarDropdown");

		// Event handler for the dropdown menu
		document
			.getElementById("navbarDropdown")
			.addEventListener("click", handleDropdownToggle.bind(null, dropdown));

		// Close the dropdown if the user clicks outside of it
		window.onclick = function (event) {
			// Close the dropdown menu if the user clicks outside of it
			if (
				event.target.id !== dropdown.id &&
				dropdown.attributes["aria-expanded"].value === "true"
			) {
				handleCloseDropdown(dropdown);
			}
		};

		// Close the dropdown if the user presses the escape key
		document.onkeydown = function (event) {
			if (
				event.key === "Escape" &&
				dropdown.attributes["aria-expanded"].value === "true"
			) {
				handleCloseDropdown(dropdown);
			}
		};
	}
};

// Function to toggle the dropdown menu
function handleDropdownToggle(dropdown) {
	// Check if the dropdown menu is open
	if (dropdown.attributes["aria-expanded"].value === "false") {
		// Open the dropdown menu
		handleOpenDropdown(dropdown);
	} else {
		// Close the dropdown menu
		handleCloseDropdown(dropdown);
	}
}

// Function to open the dropdown menu
function handleOpenDropdown(dropdown) {
	dropdown.attributes["aria-expanded"].value = "true";
}

// Function to close the dropdown menu
function handleCloseDropdown(dropdown) {
	dropdown.attributes["aria-expanded"].value = "false";
}
