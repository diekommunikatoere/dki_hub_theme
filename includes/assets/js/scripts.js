/* scroll to top button */
jQuery(document).ready(function ($) {
	//Check to see if the window is top if not then display button
	$(window).scroll(function () {
		if ($(this).scrollTop() > 100) {
			$("#scroll-top-button").fadeIn();
		} else {
			$("#scroll-top-button").fadeOut();
		}
	});

	//Click event to scroll to top
	$("#scroll-top-button").click(function () {
		$("html, body").animate({ scrollTop: 0 }, 500);
		return false;
	});
});
