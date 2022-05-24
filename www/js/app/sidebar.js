/* Store sidebar state */
$('.sidebar-minimizer').click(function(event) {
	event.preventDefault();
	if (Boolean(localStorage.getItem('sidebar-minimizer'))) {
		localStorage.setItem('sidebar-minimizer', '');
	 } else {
		localStorage.setItem('sidebar-minimizer', '1');
	 }
 });

/* Recover sidebar state */
if (Boolean(localStorage.getItem('sidebar-minimizer'))) {
	var body = document.getElementsByTagName('body')[0];
	body.className = body.className + ' brand-minimized sidebar-minimized';
}
