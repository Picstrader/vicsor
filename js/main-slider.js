$(document).ready(function() {
  
    var $slider = $(".main-page-slider"),
        $slideBGs = $(".main-page-slide__bg"),
        diff = 0,
        curSlide = 0,
        numOfSlides = $(".main-page-slide").length-1,
        animating = false,
        animTime = 500,
        autoSlideTimeout,
        autoSlideDelay = 3000,
        $pagination = $(".main-page-slider-pagi");
    
    function createBullets() {
      for (var i = 0; i < numOfSlides+1; i++) {
        var $li = $("<li class='main-page-slider-pagi__elem'></li>");
        $li.addClass("main-page-slider-pagi__elem-"+i).data("page", i);
        if (!i) $li.addClass("main-page-active");
        $pagination.append($li);
      }
    };
    
    createBullets();
    
    function manageControls() {
      $(".main-page-slider-control").removeClass("main-page-inactive");
      if (!curSlide) $(".main-page-slider-control.main-page-left").addClass("main-page-inactive");
      if (curSlide === numOfSlides) $(".main-page-slider-control.main-page-right").addClass("main-page-inactive");
    };
    
    function autoSlide() {
      autoSlideTimeout = setTimeout(function() {
        curSlide++;
        if (curSlide > numOfSlides) curSlide = 0;
        changeSlides();
      }, autoSlideDelay);
    };
    
    autoSlide();
    
    function changeSlides(instant) {
      if (!instant) {
        animating = true;
        manageControls();
        $slider.addClass("main-page-animating");
        $slider.css("top");
        $(".main-page-slide").removeClass("main-page-active");
        $(".main-page-slide-"+curSlide).addClass("main-page-active");
        setTimeout(function() {
          $slider.removeClass("main-page-animating");
          animating = false;
        }, animTime);
      }
      window.clearTimeout(autoSlideTimeout);
      $(".main-page-slider-pagi__elem").removeClass("main-page-active");
      $(".main-page-slider-pagi__elem-"+curSlide).addClass("main-page-active");
      $slider.css("transform", "translate3d("+ -curSlide*100 +"%,0,0)");
      $slideBGs.css("transform", "translate3d("+ curSlide*50 +"%,0,0)");
      diff = 0;
      autoSlide();
    }
  
    function navigateLeft() {
      if (animating) return;
      if (curSlide > 0) curSlide--;
      changeSlides();
    }
  
    function navigateRight() {
      if (animating) return;
      if (curSlide < numOfSlides) curSlide++;
      changeSlides();
    }
  
    $(document).on("mousedown touchstart", ".main-page-slider", function(e) {
      if (animating) return;
      window.clearTimeout(autoSlideTimeout);
      var startX = e.pageX || e.originalEvent.touches[0].pageX,
          winW = $(window).width();
      diff = 0;
      
      $(document).on("mousemove touchmove", function(e) {
        var x = e.pageX || e.originalEvent.touches[0].pageX;
        diff = (startX - x) / winW * 70;
        if ((!curSlide && diff < 0) || (curSlide === numOfSlides && diff > 0)) diff /= 2;
        $slider.css("transform", "translate3d("+ (-curSlide*100 - diff) +"%,0,0)");
        $slideBGs.css("transform", "translate3d("+ (curSlide*50 + diff/2) +"%,0,0)");
      });
    });
    
    $(document).on("mouseup touchend", function(e) {
      $(document).off("mousemove touchmove");
      if (animating) return;
      if (!diff) {
        changeSlides(true);
        return;
      }
      if (diff > -8 && diff < 8) {
        changeSlides();
        return;
      }
      if (diff <= -8) {
        navigateLeft();
      }
      if (diff >= 8) {
        navigateRight();
      }
    });
    
    $(document).on("click", ".main-page-slider-control", function() {
      if ($(this).hasClass("left")) {
        navigateLeft();
      } else {
        navigateRight();
      }
    });
    
    $(document).on("click", ".main-page-slider-pagi__elem", function() {
      curSlide = $(this).data("page");
      changeSlides();
    });
    
  });