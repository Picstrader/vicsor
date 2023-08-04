let chat_companion_tmId = setTimeout(function chat_companions_auto_refresh() {
	const request = new XMLHttpRequest();
	request.onreadystatechange = () => {
		if (request.readyState === 4 && request.status === 200) {
			document.getElementById('chat__companions').innerHTML = request.responseText;
			console.log('companions Ok');
		} else {
			console.log('companions Not ready yet.');
		}
	};
	var url_str = 'chat_companions_auto_refresh.php?chat_with_user_id=' + chat_with_user_id + '&cur_user_id=' + cur_user_id;
	request.open('GET', url_str, true);
	request.send();
	chat_companion_tmId = setTimeout(chat_companions_auto_refresh, 5000);
}, 5000);