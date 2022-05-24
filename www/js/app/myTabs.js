$("ul.nav-tabs > li > a").on("shown.bs.tab", function (e) {
	var id = $(e.target).attr("href").substr(1);
	window.location.hash = id;
});

// on load of the page: switch to the currently selected tab
var hash = window.location.hash;

if (hash) {
	var target = $(`${hash}`);
	var targetLink = $(`#myTab a[href="${hash}"]`);
	var parentPanes = [];
	target.parentsUntil("body").each(function () {
		var $dom = $(this);
		if ($dom.hasClass("tab-pane") && $dom.attr("id")) {
			parentPanes.push($dom.attr("id"));
		}
	});
	parentPanes.reduceRight(function (pre, parentId) {
		$(`#myTab a[href="#${parentId}"]`).tab('show');
	}, "");
	targetLink.tab('show');
}