$(document).ready(function() {
	var prev = '';

	$('.vendorsummary').hide ();

	$('.place img').click (function () {
		var t = $(this).siblings ('input[name=target]').val ();

		if (prev != '')
			$('#' + prev).slideUp ();

		$('#' + t).slideDown ();
		prev = t;
	});
});

