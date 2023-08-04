function chat_make_msg_read(mess_id) {
	const request = new XMLHttpRequest();
	request.onreadystatechange = () => {
		if (request.readyState === 4 && request.status === 200) {
		} else {
			console.log('Not msgs-refresh');
		}
	};
	url_str = 'chat_make_msg_read.php?mess_id=' + mess_id;
	request.open('GET', url_str, true);
	request.send();
}
