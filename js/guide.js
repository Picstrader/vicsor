const tour = new Shepherd.Tour({
  useModalOverlay: isMobile() ? false : true,
  defaultStepOptions: {
    cancelIcon: {
      enabled: false
    },
    classes: 'shepherd-custom',
    scrollTo: { behavior: 'smooth', block: 'center' },
    popperOptions: { modifiers: [{ name: "focusAfterRender", enabled: false }] }
  }
});

tour.addStep({
  id: 1,
  title: 'Step 1',
  text: `Upload a photo, wait for the photo to pass moderation, then select the photo by clicking on it.`,
  attachTo: {
    element: '#trade_gallery',
    on: 'bottom'
  },
  buttons: [
    // {
    //   action() {
    //     if (!modal_data.image.id) {
    //       return false;
    //     }
    //     return this.next();
    //   },
    //   text: 'Next'
    // }
  ],
});

tour.addStep({
  id: 2,
  title: 'Step 2',
  text: `Enter a Invested amount, min 1 ` + fs['main_currency'] + '.',
  attachTo: {
    element: '#trade_filter__cost',
    on: 'bottom'
  },
  buttons: [
    {
      action() {
        let f = document.getElementById('trade_filter__cost');
        if (f.value == '') {
          return false;
        }
        return this.next();
      },
      text: 'Next'
    }
  ],
});

tour.addStep({
  id: 3,
  title: 'Step 3',
  text: `Enter the number of photos in the set, at least 2.`,
  attachTo: {
    element: '#trade_filter__photos',
    on: 'bottom'
  },
  buttons: [
    {
      action() {
        let f = document.getElementById('trade_filter__photos');
        if (f.value == '') {
          return false;
        }
        return this.next();
      },
      text: 'Next'
    }
  ],
});

tour.addStep({
  id: 4,
  title: 'Step 4',
  text: `Enter the number of purchased photos, must be greater than 0 and less than the total number of photos.`,
  attachTo: {
    element: '#trade_filter__purchasable',
    on: 'bottom'
  },
  buttons: [
    {
      action() {
        let f = document.getElementById('trade_filter__purchasable');
        if (f.value == '') {
          return false;
        }
        return this.next();
      },
      text: 'Next'
    }
  ],
});

tour.addStep({
  id: 5,
  title: 'Step 5',
  text: `Click to create your set.`,
  attachTo: {
    element: '#create-set__button',
    on: 'bottom'
  },
  buttons: [
  ],
});

tour.addStep({
  id: 6,
  title: 'Step 6',
  text: `Check again your entered parameters.`,
  attachTo: {
    element: '.active-sets__filter-sets',
    on: 'bottom'
  },
  buttons: [
    {
      action() {
        return this.next();
      },
      text: 'Next'
    }
  ],
});

tour.addStep({
  id: 7,
  title: 'Step 7',
  text: `Enter hashtags of your choice.`,
  attachTo: {
    element: '.trade_modal__sect_wallet',
    on: 'bottom'
  },
  buttons: [
    {
      action() {
        return this.next();
      },
      text: 'Next'
    }
  ],
});

tour.addStep({
  id: 8,
  title: 'Step 8',
  text: `Click to create a set.`,
  attachTo: {
    element: '#trade_modal__confirm',
    on: 'bottom'
  },
  buttons: [
  ],
});

tour.addStep({
  id: 9,
  title: 'Step 9',
  text: `Tutorial finished.`,
  buttons: [
    {
      action() {
        return this.hide();
      },
      text: 'Close'
    }
  ],
});

try {
  if (go_tutorial) {
    tour.start();
  }
} catch (error) {

}

function makeImagesReady() {
  let slot_nodes = document.getElementsByClassName('trade_gallery__section_foto');
  for (let i = 0; i < slot_nodes.length; i++) {
    let image_data = $(slot_nodes[i]).data('image');
    if (image_data.status != 'trading') {
      image_data.status = 'ready';
      $(slot_nodes[i]).data('image', image_data);
      let status_element = slot_nodes[i].parentNode.querySelector('.trade_gallery__section-img_description');
      status_element.innerText = fs['ready'];
    }
  }
}