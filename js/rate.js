let modal_complain = document.getElementById("modalComplain");
if (modal_complain != null) {
  window.onclick = function (event) {
    if (event.target == modal_complain) {
      modal_complain.style.display = "none";
    }
  }
}

function hideModalComplain() {
  modal_complain.style.display = "none";
}

function showModalComplain() {
  modal_complain.style.display = "block";
}

function sendComplain(complain) {
  //let image = $('.rate-slider-cover-current-img .rate-slider-current-img').first();
  let image = $('.rate-carousel-item-3').first();
  let image_id = image.data('id');
  let set = image.data('set');
  let form_data = new FormData();
  form_data.append('image_id', image_id);
  form_data.append('set', set);
  form_data.append('complain', complain);
  form_data.append('action', 'send_complain');
  let xhr = new XMLHttpRequest();
  xhr.open("POST", location.origin + "/rate-ajax.php", true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      hideModalComplain();
    } else if (xhr.readyState === 4 && xhr.status === 400) {
      hideModalComplain();
    }
  };
  xhr.send(form_data);
}

function searchSets() {
  if (timer != null) {
    return;
  }
  $('.rate-sets__set_image').removeClass('trade_gallery__selected');
  clearTimeout(timer);
  timer = setTimeout(() => { searchSetsAjax() }, 1000);
}

function searchSetsAjax() {
  let search_hash = document.getElementById('rate-search');
  let form_data = new FormData();
  form_data.append('search', search_hash.value);
  form_data.append('action', 'search_sets');
  let xhr = new XMLHttpRequest();
  xhr.open("POST", location.origin + "/rate-ajax.php", true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      reInitRateSlider();
      // destroySlickSlider();
      // getSliderImages();
    } else if (xhr.readyState === 4 && xhr.status === 400) {
    }
  };
  xhr.send(form_data);
}

function redirectLogin() {
  window.location.href = '/login.php';
}

function prepareHtmlSliderAll(images) {
  // $('.slider-nav').html('');
  // let slider_under = document.querySelector('.slider-nav');
  // images.forEach((image, index) => {
  //   let div = document.createElement('div');
  //   div.className = (index == 2) && image.src_thumbnail ? 'rate-slider-cover-all-img-choose' : 'rate-slider-cover-all-img';
  //   let img = document.createElement('span');
  //   img.className = 'dot';
  //   img.setAttribute('data-id', `${image.id}`);
  //   img.setAttribute('data-set', `${image.set}`);
  //   //img.src = image.src_thumbnail ? image.src_thumbnail : './inc/assets/img/empty-dot.png';
  //   div.append(img);
  //   slider_under.append(div);
  // });
  $('.slider-nav').html('');
  let slider_under = document.querySelector('.slider-nav');
  for (let i = 0; i < 3; i++) {
    let div = document.createElement('div');
    let dot = document.createElement('span');
    dot.className = (i == 0) ? 'dot-current' : 'dot';
    div.append(dot);
    slider_under.append(div);
  }
}

function htmlSliderMove(move, amount) {
  switch (move) {
    case 'next':
      moveNext();
      addNewDot(amount);
      break;
    case 'back':
      addAllNewDots(amount);
      moveBack();
      break;

    default:
      break;
  }
}

function moveNext() {
  try {
    let current = document.querySelector('.dot-current');
    let next = current.parentNode.nextSibling.querySelector('.dot');
    current.className = 'dot';
    next.className = 'dot-current';
  } catch (error) {
    let current = document.querySelector('.dot-current');
    current.className = 'dot';
    let first = current.parentNode.parentNode.firstChild.querySelector('.dot');
    first.className = 'dot-current';
  }
}

function moveBack() {
  try {
    let current = document.querySelector('.dot-current');
    let back = current.parentNode.previousSibling.querySelector('.dot');
    current.className = 'dot';
    back.className = 'dot-current';
  } catch (error) {
    let current = document.querySelector('.dot-current');
    current.className = 'dot';
    let first = current.parentNode.parentNode.lastChild.querySelector('.dot');
    first.className = 'dot-current';
  }
}

function addNewDot(amount) {
  let current_length = document.querySelector('.slider-nav').childNodes.length;
  if (current_length < amount) {
    //for (let i = 0; i < amount - current_length; i++) {
    let slider_under = document.querySelector('.slider-nav');
    let div = document.createElement('div');
    let dot = document.createElement('span');
    dot.className = 'dot';
    div.append(dot);
    slider_under.append(div);
    //}
  }
}

function addAllNewDots(amount) {
  let current_length = document.querySelector('.slider-nav').childNodes.length;
  if (current_length < amount) {
    for (let i = 0; i < amount - current_length; i++) {
      let slider_under = document.querySelector('.slider-nav');
      let div = document.createElement('div');
      let dot = document.createElement('span');
      dot.className = 'dot';
      div.append(dot);
      slider_under.append(div);
    }
  }
}

function addNewSlideImageAll(image) {
  let div = document.createElement('div');
  div.className = 'rate-slider-cover-all-img';
  let img = document.createElement('img');
  img.className = 'rate-slider-all-img';
  img.setAttribute('data-id', `${image.id}`);
  img.setAttribute('data-set', `${image.set}`);
  img.src = image.src_thumbnail;
  div.append(img);
  let slider_under = document.querySelector('.slider-nav');
  slider_under.append(div);
}

function focusOnCurrentSlider() {
  $(".rate-slider-cover-all-img-choose").removeClass("rate-slider-cover-all-img-choose");
  let liked_image = document.querySelector('.rate-carousel-item-3');
  let image_id = liked_image.getAttribute('data-id');
  $(".slider-nav img[data-id=" + image_id + "]").first().parent().addClass("rate-slider-cover-all-img-choose");
}

function info() {
  showModalComplain();
}

function reInitRateSlider() {
  document.querySelector('.rate-carousel').innerHTML = "";
  $('.slider-nav').html('');
  const el = document.querySelector('.rate-carousel');
  if (el) {
    // Create a new carousel object
    const exampleCarousel = new Carousel(el);
    // Setup carousel and methods
    exampleCarousel.mounted();
  }
}

function hideSlidersForMobile() {
  // for (let i = 0; i < 5; i++) {
  //   if (i == 2) {
  //     let slide = document.querySelector('.rate-carousel-item-' + (i + 1));
  //     if (slide) {
  //       slide.style.display = 'flex';
  //     }
  //     continue;
  //   }
  //   let slide = document.querySelector('.rate-carousel-item-' + (i + 1));
  //   if (slide) {
  //     slide.style.display = 'none';
  //   }
  // }
}

function getInputRange() {
  let str = '<div class="main_form__time_min"><output class="main_form__sect-title">1</output></div>' +
    '<div><input class="range__input" type="range" id="my-rate" value="1" min="1"' +
    'max="10" step="1" oninput="num.value = this.value;" /></div>' +
    '<div class="main_form__time_max"><output id="num" class="main_form__sect-title">10</output></div>';
  return str;
}

function resetInputRange() {
  let input = document.querySelector('#my-rate');
  input.value = 1;
  let num = document.querySelector('#num');
  num.value = 1;
}