'use strict';

class Carousel {
  constructor(el) {
    this.el = el;
    this.startX = 0;
    this.endX = 0;
    this.empty = false;
    this.start_amount = 5;
    this.carouselOptions = ['back', 'like', 'dislike', 'next'];
    this.carouselData = [];
    this.carouselInView = [1, 2, 3, 4, 5];
    this.carouselContainer;
    this.carouselPlayState;
    this.lastPushElementIndex;
    this.startPosition = 0;
  }

  fillCarouselData() {
    if (!this.empty) {
      while (this.carouselData.length < this.start_amount) {
        for (let i = 0; i < this.carouselData.length; i++) {
          if (this.carouselData.length >= this.start_amount) {
            break;
          }
          let new_obj = Object.assign({}, this.carouselData[i]);
          this.carouselData.push(new_obj);
        }
      }
    }
  }

  mounted() {
    let form_data = new FormData();
    form_data.append('search', document.getElementById('rate-search').value);
    form_data.append('action', 'load_images');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/rate-ajax.php", true);
    xhr.onreadystatechange = () => {
      if (xhr.readyState === 4 && xhr.status === 200) {
        let json = xhr.responseText;
        let images = JSON.parse(json);
        if (images.length <= 0) {
          this.empty = true;
        }
        //images.unshift(...this.carouselData);
        this.carouselData = images;
        if (this.carouselData.length < this.start_amount && this.carouselData.length > 0) {
          this.fillCarouselData();
        }
        this.lastPushElementIndex = this.carouselData.length - 1;
        this.setupCarousel();
        prepareHtmlSliderAll(this.carouselData);
      } else if (xhr.readyState === 4 && xhr.status === 400) {
      }
    };
    xhr.send(form_data);
  }

  // Build carousel html
  setupCarousel() {
    document.querySelector('.rate-carousel').innerHTML = "";
    const container = document.createElement('div');
    const controls = document.createElement('div');

    // Add container for carousel items and controls
    this.el.append(container/*, controls*/);
    container.className = 'rate-carousel-container';
    controls.className = 'rate-carousel-controls';
    if (!this.empty) {
      // Take dataset array and append items to container
      this.carouselData.forEach((item, index) => {
        const carouselItem = document.createElement('img');
        container.append(carouselItem);
        // Add item attributes
        carouselItem.className = `rate-carousel-item rate-carousel-item-${index + 1}`;
        carouselItem.src = './inc/assets/img/' + item.name;
        carouselItem.setAttribute('data-id', `${item.id}`);
        carouselItem.setAttribute('data-set', `${item.set}`);
        carouselItem.setAttribute('loading', 'lazy');
        // Used to keep track of carousel items, infinite items possible in carousel however min 5 items required
        carouselItem.setAttribute('data-index', `${index + 1}`);
      });
    } else {
      // Take dataset array and append items to container
      for (let index = 0; index < 5; index++) {
        const carouselItem = document.createElement('div');
        container.append(carouselItem);
        // Add item attributes
        carouselItem.className = `empty-image rate-carousel-item rate-carousel-item-${index + 1}`;
        carouselItem.setAttribute('loading', 'lazy');
        // Used to keep track of carousel items, infinite items possible in carousel however min 5 items required
        carouselItem.setAttribute('data-index', `${index + 1}`);
        if (index == 2) {
          const search = document.querySelector('#rate-search').value;
          if (search != '') {
            const empty_text = document.createElement('div');
            let message = fs["Nothing found for your request"];
            message = message.replace('{{search}}', search);
            empty_text.innerHTML = message;
            empty_text.className = 'empty-slider-text';
            carouselItem.append(empty_text);
          } else {
            const empty_text = document.createElement('div');
            empty_text.innerHTML = fs["No new images"];
            empty_text.className = 'empty-slider-text';
            carouselItem.append(empty_text);
          }
          const goto_text = document.createElement('div');
          goto_text.innerHTML = fs['Go to the Trading section to create a set'];
          goto_text.className = 'empty-slider-text';
          carouselItem.append(goto_text);
        }
      }
    }
    if (isMobile()) {
      hideSlidersForMobile();
      $(".rate-carousel-item").css("transition", "none");
      // $(".rate-carousel-item-1").css("opacity", "0");
      // $(".rate-carousel-item-2").css("opacity", "0");
      // $(".rate-carousel-item-4").css("opacity", "0");
      // $(".rate-carousel-item-5").css("opacity", "0");
    }
    // After rendering carousel to our DOM, setup carousel controls' event listeners
    this.setControls([...controls.children]);

    // Set container property
    this.carouselContainer = container;
  }

  setControls(controls) {
    if (!isMobile()) {
      document.querySelector('.rate-carousel-control-back').parentNode.style.display = 'flex';
      document.querySelector('.rate-carousel-control-next').parentNode.style.display = 'flex';
    }
    document.querySelector('.rate-range-cover').style.display = 'flex';
    if (this.empty) {
      document.querySelector('.rate-carousel-control-like').style.display = 'none';
      return;
    }
    document.querySelector('.rate-carousel-control-back').onclick = '';
    document.querySelector('.rate-carousel-control-next').onclick = '';
    document.querySelector('.rate-carousel-control-like').onclick = '';
    document.querySelector('.rate-carousel-control-dislike').onclick = '';
    document.querySelector('.rate-carousel-control-back').onclick = (event) => {
      event.preventDefault();
      let onclick_event = event.target.onclick;
      event.target.onclick = '';
      this.controlManager(document.querySelector('.rate-carousel-control-back').dataset.name, event.target, onclick_event);
    }
    document.querySelector('.rate-carousel-control-next').onclick = (event) => {
      event.preventDefault();
      let onclick_event = event.target.onclick;
      event.target.onclick = '';
      this.controlManager(document.querySelector('.rate-carousel-control-next').dataset.name, event.target, onclick_event);
    }
    document.querySelector('.rate-carousel-control-like').onclick = (event) => {
      event.preventDefault();
      let onclick_event = event.target.onclick;
      event.target.onclick = '';
      this.controlManager(document.querySelector('.rate-carousel-control-like').dataset.name, event.target, onclick_event);
    }
    document.querySelector('.rate-carousel-control-dislike').onclick = (event) => {
      event.preventDefault();
      let onclick_event = event.target.onclick;
      event.target.onclick = '';
      this.controlManager(document.querySelector('.rate-carousel-control-dislike').dataset.name, event.target, onclick_event);
    }
    if (isMobile()) {
      let swipeContainer = document.querySelector('.rate-carousel-container');
      swipeContainer.addEventListener('mousedown', this.handleSwipeStart);
      swipeContainer.addEventListener('touchstart', this.handleSwipeStart);

      swipeContainer.addEventListener('mousemove', this.handleSwipeMove);
      swipeContainer.addEventListener('touchmove', this.handleSwipeMove);

      swipeContainer.addEventListener('mouseup', this.handleSwipeEnd);
      swipeContainer.addEventListener('touchend', this.handleSwipeEnd);

    }
  }

  animateImageToUp(event_target = null, onclick_event = null, action) {
    var image = document.querySelector('.rate-carousel-item-3');
    var currentPosition = image.getBoundingClientRect().top;
    var targetPosition = -image.offsetHeight;
    this.startPosition = currentPosition;

    var moveImage = () => {
      currentPosition -= 75; // Изменить скорость анимации, изменяя значение здесь
      image.style.top = currentPosition + 'px';
      if (currentPosition > targetPosition) {
        requestAnimationFrame(moveImage);
      } else {
        switch(action) {
          case 'like':
            this.like(event_target, onclick_event);
            break;
          case 'dislike':
            this.dislike(event_target, onclick_event);
            break;
          case 'next_image':
            this.next_image(event_target, onclick_event);
            break;
        }
      }
    }

    moveImage();
  }

  animateImageFromDown() {
    var image = document.querySelector('.rate-carousel-item-3');
    image.style.top = this.startPosition + 'px';
    image.style.top = image.getBoundingClientRect().bottom + 'px';
    var currentPosition = image.getBoundingClientRect().top;
    var targetPosition = /*image.parentNode.getBoundingClientRect().top*/0;

    function moveImage() {
      currentPosition -= 75; // Изменить скорость анимации, изменяя значение здесь
      image.style.top = currentPosition + 'px';

      if (currentPosition > targetPosition) {
        requestAnimationFrame(moveImage);
      } else {
        image.style.top = 0 + 'px';
      }
    }

    moveImage();
  }

  animateImageToDown(event_target = null, onclick_event = null) {
    var image = document.querySelector('.rate-carousel-item-3');
    var currentPosition = image.getBoundingClientRect().top;
    var containerHeight = image.parentNode.offsetHeight;
    var targetPosition = containerHeight;
    this.startPosition = currentPosition;
    var moveImage = () => {
      currentPosition += 75; // Изменить скорость анимации, изменяя значение здесь
      image.style.top = currentPosition + 'px';

      if (currentPosition < targetPosition) {
        requestAnimationFrame(moveImage);
      } else {
        this.back(event_target, onclick_event);
      }
    }

    moveImage();
  }

  animateImageFromUp() {
    var image = document.querySelector('.rate-carousel-item-3');
    image.style.top = this.startPosition + 'px';
    image.style.top = image.getBoundingClientRect().top - image.parentNode.offsetHeight + 'px';
    var currentPosition = image.getBoundingClientRect().top;
    var targetPosition = 0;

    function moveImage() {
      currentPosition += 75; // Изменить скорость анимации, изменяя значение здесь
      image.style.top = currentPosition + 'px';

      if (currentPosition < targetPosition) {
        requestAnimationFrame(moveImage);
      } else {
        image.style.top = 0 + 'px';
      }
    }

    moveImage();
  }

  handleSwipeStart(event) {
    //this.startX = event.clientX || event.touches[0].clientX;
    this.startY = event.clientY || event.touches[0].clientY;
  }

  handleSwipeMove(event) {
    event.preventDefault();

    // Определяем текущую позицию курсора
    //this.endX = event.clientX || event.touches[0].clientX;
    this.endY = event.clientY || event.touches[0].clientY;
  }

  handleSwipeEnd(event) {
    // const diffX = this.endX - this.startX;
    // if (diffX > 50) {
    //   $(".rate-carousel-control-back").triggerHandler("click");
    // } else if (diffX < -50) {
    //   $(".rate-carousel-control-next").triggerHandler("click");
    // } else {
    // }
    const diffY = this.endY - this.startY;
    if (diffY > 50) {
      $(".rate-carousel-control-back").triggerHandler("click");
    } else if (diffY < -50) {
      $(".rate-carousel-control-next").triggerHandler("click");
    } else {
    }
  }

  controlManager(control, event_target = null, onclick_event = null) {
    if (isMobile()) {
      if (control === 'like') return this.animateImageToUp(event_target, onclick_event, 'like');
      if (control === 'dislike') return this.animateImageToUp(event_target, onclick_event, 'dislike');
      if (control === 'next') return this.animateImageToUp(event_target, onclick_event, 'next_image');
      if (control === 'back') return this.animateImageToDown(event_target, onclick_event);
    } else {
      if (control === 'dislike') return this.dislike(event_target, onclick_event);
      if (control === 'next') return this.next_image(event_target, onclick_event);
      if (control === 'like') return this.like(event_target, onclick_event);
      if (control === 'back') return this.back(event_target, onclick_event);
    }

    return;
  }

  back(event_target = null, onclick_event = null) {
    let form_data = new FormData();
    form_data.append('action', 'back_image');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/rate-ajax.php", true);
    xhr.onreadystatechange = () => {
      if (xhr.readyState === 4 && xhr.status === 200) {
        let json = xhr.responseText;
        let answer = JSON.parse(json);
        if (Boolean(answer['status'])) {
          // Update order of items in data array to be shown in carousel
          this.carouselData.unshift(this.carouselData.pop());

          // Push the first item to the end of the array so that the previous item is front and center
          this.carouselInView.push(this.carouselInView.shift());

          // Update the css class for each carousel item in view
          this.carouselInView.forEach((item, index) => {
            this.carouselContainer.children[index].className = `rate-carousel-item rate-carousel-item-${item}`;
          });

          // Using the first 5 items in data array update content of carousel items in view
          this.carouselData.slice(0, 5).forEach((data, index) => {
            document.querySelector(`.rate-carousel-item-${index + 1}`).src = './inc/assets/img/' + data.name;
            document.querySelector(`.rate-carousel-item-${index + 1}`).setAttribute('data-id', data.id);
          });
          if(isMobile()) {
            this.animateImageFromUp();
          }
        }
        if (isMobile()) {
          hideSlidersForMobile();
        }
        focusOnCurrentSlider();
        htmlSliderMove('back', this.carouselData.length);
        event_target.onclick = onclick_event;
        // let liked_image = document.querySelector('.rate-carousel-item-3');
        // liked_image.parentNode.style.transform = '';
      } else if (xhr.readyState === 4 && xhr.status === 400) {
        event_target.onclick = onclick_event;
        redirectLogin();
      }
    }
    xhr.send(form_data);
  }

  next(event_target = null, onclick_event = null) {
    this.carouselData.push(this.carouselData.shift());

    // Take the last item and add it to the beginning of the array so that the next item is front and center
    this.carouselInView.unshift(this.carouselInView.pop());

    // Update the css class for each carousel item in view
    this.carouselInView.forEach((item, index) => {
      this.carouselContainer.children[index].className = `rate-carousel-item rate-carousel-item-${item}`;
    });

    // Using the first 5 items in data array update content of carousel items in view
    this.carouselData.slice(0, 5).forEach((data, index) => {
      document.querySelector(`.rate-carousel-item-${index + 1}`).src = './inc/assets/img/' + data.name;
      document.querySelector(`.rate-carousel-item-${index + 1}`).setAttribute('data-id', data.id);
    });
    if (isMobile()) {
      hideSlidersForMobile();
    }
    focusOnCurrentSlider();
    event_target.onclick = onclick_event;
    // let liked_image = document.querySelector('.rate-carousel-item-3');
    // liked_image.parentNode.style.transform = '';
    if(isMobile()) {
      this.animateImageFromDown();
    }
  }

  like(event_target = null, onclick_event = null) {
    let liked_image = document.querySelector('.rate-carousel-item-3');
    let image_id = liked_image.getAttribute('data-id');
    let form_data = new FormData();
    form_data.append('image_id', image_id);
    form_data.append('dislike', 0);
    form_data.append('rate', 1);
    form_data.append('next', 0);
    form_data.append('search', document.getElementById('rate-search').value);
    form_data.append('action', 'load_image');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/rate-ajax.php", true);
    xhr.onreadystatechange = () => {
      if (xhr.readyState === 4 && xhr.status === 200) {
        let json = xhr.responseText;
        console.log(json);
        let images = JSON.parse(json);
        if (images.length > 0) {
          // Then add it to the "last" item in our carouselData
          this.carouselData.splice(this.lastPushElementIndex + 1, 0, images[0]);
          // Shift carousel to display new item
          this.next(event_target, onclick_event);
          this.lastPushElementIndex = this.carouselData.findIndex(item => item.id == images[0]['id']);
        } else {
          this.next(event_target, onclick_event);
        }
        htmlSliderMove('next', this.carouselData.length);
      } else if (xhr.readyState === 4 && xhr.status === 400) {
        redirectLogin();
      }
    };
    xhr.send(form_data);
  }

  dislike(event_target = null, onclick_event = null) {
    let liked_image = document.querySelector('.rate-carousel-item-3');
    let image_id = liked_image.getAttribute('data-id');
    let form_data = new FormData();
    form_data.append('image_id', image_id);
    form_data.append('dislike', 1);
    form_data.append('rate', 0);
    form_data.append('next', 0);
    form_data.append('search', document.getElementById('rate-search').value);
    form_data.append('action', 'load_image');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/rate-ajax.php", true);
    xhr.onreadystatechange = () => {
      if (xhr.readyState === 4 && xhr.status === 200) {
        let json = xhr.responseText;
        let images = JSON.parse(json);
        if (images.length > 0) {
          // Then add it to the "last" item in our carouselData
          this.carouselData.splice(this.lastPushElementIndex + 1, 0, images[0]);
          // Shift carousel to display new item
          this.next(event_target, onclick_event);
          this.lastPushElementIndex = this.carouselData.findIndex(item => item.id == images[0]['id']);
        } else {
          this.next(event_target, onclick_event);
        }
        htmlSliderMove('next', this.carouselData.length);
      } else if (xhr.readyState === 4 && xhr.status === 400) {
        redirectLogin();
      }
    };
    xhr.send(form_data);
  }

  next_image(event_target = null, onclick_event = null) {
    let liked_image = document.querySelector('.rate-carousel-item-3');
    let image_id = liked_image.getAttribute('data-id');
    let form_data = new FormData();
    form_data.append('image_id', image_id);
    form_data.append('next', 1);
    form_data.append('search', document.getElementById('rate-search').value);
    form_data.append('action', 'load_image');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/rate-ajax.php", true);
    xhr.onreadystatechange = () => {
      if (xhr.readyState === 4 && xhr.status === 200) {
        let json = xhr.responseText;
        let images = JSON.parse(json);
        if (images.length > 0) {
          // Then add it to the "last" item in our carouselData
          this.carouselData.splice(this.lastPushElementIndex + 1, 0, images[0]);
          // Shift carousel to display new item
          this.next(event_target, onclick_event);
          this.lastPushElementIndex = this.carouselData.findIndex(item => item.id == images[0]['id']);
        } else {
          this.next(event_target, onclick_event);
        }
        htmlSliderMove('next', this.carouselData.length);
      } else if (xhr.readyState === 4 && xhr.status === 400) {
        redirectLogin();
      }
    };
    xhr.send(form_data);
  }

  play() {
    const playBtn = document.querySelector('.rate-carousel-control-play');
    const startPlaying = () => this.next();

    if (playBtn.classList.contains('playing')) {
      // Remove class to return to play button state/appearance
      playBtn.classList.remove('playing');

      // Remove setInterval
      clearInterval(this.carouselPlayState);
      this.carouselPlayState = null;
    } else {
      // Add class to change to pause button state/appearance
      playBtn.classList.add('playing');

      // First run initial next method
      this.next();

      // Use play state prop to store interval ID and run next method on a 1.5 second interval
      this.carouselPlayState = setInterval(startPlaying, 1500);
    };
  }

}

// Refers to the carousel root element you want to target, use specific class selectors if using multiple carousels
const el = document.querySelector('.rate-carousel');
if (el) {
  // Create a new carousel object
  const exampleCarousel = new Carousel(el);
  // Setup carousel and methods
  exampleCarousel.mounted();
}
