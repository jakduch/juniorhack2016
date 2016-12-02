function showFlashMessage(message)
{
	$.magnificPopup.open({
		items: [
			{
				src: $('<div class="popup" style="text-align: center;"><div class="x_panel"><div class="x_content"><span style="color: black; font-size: 15px;">' + message + '</span> <br /></div></div><button class="btn btn-success">Zavřít</button></div>'),
			}
		],
		closeOnContentClick: true,
		closeBtnInside: true
	});


}