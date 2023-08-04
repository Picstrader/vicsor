	var p_c_min = document.getElementById('p_c_min');
	if(p_c_min !== null){
		var p_c_max = document.getElementById('p_c_max');
		var t_ph_min = document.getElementById('t_ph_min');
		var t_ph_max = document.getElementById('t_ph_max');
		var pur_min = document.getElementById('pur_min');
		var pur_max = document.getElementById('pur_max');
		var ar_a = document.querySelectorAll('#active_sets__pagination > a');



		p_c_min.oninput = changed;
		p_c_max.oninput = changed;
		t_ph_min.oninput = changed;
		t_ph_max.oninput = changed;
		pur_min.oninput = changed;
		pur_max.oninput = changed;
		for (var i = 0; i < ar_a.length; i++) {
			ar_a[i].onclick = changed;
		}
	}
	function changed(){
		p_c_min_val = p_c_min.value;
		p_c_max_val = p_c_max.value;
		t_ph_min_val = t_ph_min.value;
		t_ph_max_val = t_ph_max.value;
		pur_min_val = pur_min.value;
		pur_max_val = pur_max.value;
		var url_str = "./inc/template/active-sets-ajax.php?cost_min=" 
			+ p_c_min_val 
			+ "&cost_max=" + p_c_max_val 
			+ "&ph_in_set_min=" + t_ph_min_val 
			+ "&ph_in_set_max=" + t_ph_max_val 
			+ "&pur_min=" + pur_min_val 
			+ "&pur_max=" + pur_max_val;
		var page =  this.getAttribute('data-page');
		if ((page !== null) && (page !== 'no')) {
			url_str = url_str + "&page=" + page;
		}



		if (page !== 'no') {

			const request = new XMLHttpRequest();
			request.onreadystatechange = () => {
				if (request.readyState === 4 && request.status === 200) {
	    			table_regenerate(request.responseText);
				} else {
	    			console.log('Not ready yet.');
				}
			};
			
			request.open('GET', url_str, true);
			request.send();



		}

		return(false);
	}

	function table_regenerate(html_text) {
		var ar_tb_pagin = JSON.parse(html_text);
		var table_body = document.getElementById('main_tbody');
		table_body.innerHTML = ar_tb_pagin[0];
		var pagin = document.getElementById('active_sets__pagination');
		pagin.innerHTML = ar_tb_pagin[1];

		var ar_a = document.querySelectorAll('#active_sets__pagination > a');
		for (var i = 0; i < ar_a.length; i++) {
			ar_a[i].onclick = changed;
		}
	}
	