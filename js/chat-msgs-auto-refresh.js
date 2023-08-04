let chat_msgs_tmId = setTimeout(function fn_chat_update() {
	const request = new XMLHttpRequest();
	request.onreadystatechange = () => {
		if (request.readyState === 4 && request.status === 200) {
			console.log('msgs');
			if (request.responseText != 'false') {
				document.getElementById('chat__communication-msgs').innerHTML = request.responseText;
				console.log('Msgs Ok');
			}
		} else {
			console.log('Messages Not ready yet.');
		}
	};
	var url_str = 'chat-msgs-auto-refresh.php?chat_with_user_id=' + chat_with_user_id + '&cur_user_id=' + cur_user_id;
	request.open('GET', url_str, true);
	request.send();
	chat_msgs_tmId = setTimeout(fn_chat_update, 5000);
}, 5000);