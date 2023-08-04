
var titles = document.querySelectorAll('.how_it_works_faqs__item-title');

for (var i = 0; i < titles.length; i++) {
	titles[i].setAttribute('data-faq-num', i);
	titles[i].onclick = show_item_content;
}

var imgs = document.querySelectorAll('.how_it_works_faqs__item-title > img');

for (var i = 0; i < imgs.length; i++) {
	imgs[i].setAttribute('data-faq-img', i);
}

var blocks = document.querySelectorAll('.how_it_works_faqs__item-description');

for (var i = 0; i < blocks.length; i++) {
	blocks[i].setAttribute('data-faq-block', i);
}

function show_item_content() {
	i = this.getAttribute('data-faq-num');
	tmp_str = '[data-faq-img="' + i + '"]';
	var img = document.querySelector(tmp_str);
	tmp_str = '[data-faq-block="' + i + '"]';
	var block = document.querySelector(tmp_str);

	if ((block.style.display == '') || (block.style.display == 'none')) {
		img.style.transform = 'matrix(1, 0, 0, -1, 0, 0)';
		block.style.display = 'block';
	} else {
		img.style.transform = '';
		block.style.display = 'none';
	}
}