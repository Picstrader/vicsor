$(function () {


  /*
   * International Telephone Input v16.0.0
   * https://github.com/jackocnr/intl-tel-input.git
   * Licensed under the MIT license
  */
  var input = document.querySelectorAll(".form-control-registration-phone");
  var iti_el = $('.iti.iti--allow-dropdown.iti--separate-dial-code');
  if (iti_el.length) {
    iti.destroy();
  }
  for (var i = 0; i < input.length; i++) {
    iti = intlTelInput(input[i], {
      autoHideDialCode: false,
      autoPlaceholder: "aggressive",
      initialCountry: "auto",
      separateDialCode: true,
      preferredCountries: [],
      customPlaceholder: function (selectedCountryPlaceholder, selectedCountryData) {
        return '' + selectedCountryPlaceholder.replace(/[0-9]/g, 'X');
      },
      geoIpLookup: function (callback) {
        $.get('https://ipinfo.io', function () { }, "jsonp").always(function (resp) {
          var countryCode = (resp && resp.country) ? resp.country : "";
          callback(countryCode);
        });
      },
      onlyCountries: ['au','at','az','ar','by','be','br','hu','de','hk','gr','dk','in','id','ie','es','it','ca','cy','cn','lv','lt','lu',
        'my','mx','nl','no','nz','pk','pl','pt','kr','ru','sa','sg','si','us','th','tr','uz','ua','fi','fr','cz','ch','se','za',
        'jp','bh','bg','vn','ge','eg','il','is','kz','ke','cr','kw','mt','ma','om','pe','sk','tn','uy','ph','hr','ee'],
      utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.0/js/utils.js" // just for 
    });


    $('.form-control-registration-phone').on("focus click countrychange", function (e, countryData) {

      var pl = $(this).attr('placeholder') + '';
      var res = pl.replace(/X/g, '9');
      if (res != 'undefined') {
        $(this).inputmask(res, { placeholder: "X", clearMaskOnLostFocus: true });
      }

    });

    $('.form-control-registration-phone').on("focusout", function (e, countryData) {
      var intlNumber = iti.getNumber();
      var full_phone = document.getElementById('full-phone');
      full_phone.value = intlNumber;
    });

  }


})