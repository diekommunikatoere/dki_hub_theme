import { computePosition, flip, shift, offset, arrow } from "https://cdn.jsdelivr.net/npm/@floating-ui/dom@1.6.4/+esm";

const glossaryTerms = document.querySelectorAll(".glossary-term");
const tooltips = document.querySelectorAll(".glossary-tooltip");
const arrowElements = document.querySelectorAll(".arrow");
console.log("floatingUI.js geladen");

function update(term, index) {
	computePosition(term, tooltips[index], {
		placement: "top",
		middleware: [offset(0), flip(), shift({ padding: 8 }), arrow({ element: arrowElements[index] })],
	}).then(({ x, y, placement, middlewareData }) => {
		Object.assign(tooltips[index].style, {
			left: `${x}px`,
			top: `${y}px`,
		});

		// Accessing the data
		const { x: arrowX, y: arrowY } = middlewareData.arrow;

		const staticSide = {
			top: "bottom",
			right: "left",
			bottom: "top",
			left: "right",
		}[placement.split("-")[0]];

		Object.assign(arrowElements[index].style, {
			left: arrowX != null ? `${arrowX}px` : "",
			top: arrowY != null ? `${arrowY}px` : "",
			right: "",
			bottom: "",
			[staticSide]: "-4px",
		});
	});
}

function showTooltip(event, index) {
	console.log("Index: " + index);
	tooltips[index].style.display = "flex";
	update(this, index);
}

function hideTooltip(event, index) {
	tooltips[index].style.display = "";
}

glossaryTerms.forEach((element, index) => {
	element.addEventListener("mouseenter", (e) => {
		showTooltip.call(element, e, index);
		tooltips[index].addEventListener("mouseleave", hideTooltip.bind(null, e, index));
	});

	element.addEventListener("mouseleave", (e) => {
		if (!tooltips[index].contains(e.relatedTarget)) {
			hideTooltip.call(null, e, index);
		}
	});
	element.addEventListener("click", (e) => e.stopPropagation());
});

document.querySelectorAll('a[href^="glossar/#"]').forEach((anchor) => {
	anchor.addEventListener("click", function (e) {
		e.preventDefault();

		/* document.querySelector(this.getAttribute("href")).scrollIntoView({
			behavior: "smooth",
			block: "start",
		}); */
		setTimeout(() => {
			window.scrollTo({
				top: 350,
			});
		}, 500);
	});
});
