function checkRegTerms() {
  let privacy_policy = document.getElementById('privacy-policy');
  let full_age = document.getElementById('full-age');
  let confirm = document.getElementById('registration-confirm');
  if (privacy_policy.checked && full_age.checked) {
    confirm.disabled = false;
  } else {
    confirm.disabled = true;
  }
}

var daysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],
  today = new Date(),
  // default targetDate is christmas
  targetDate = new Date(today.getFullYear(), 11, 25);
if (typeof user_birth_data !== 'undefined') {
  console.log(user_birth_data);
  setYears(user_birth_data);
  setDate(targetDate, user_birth_data);
} else {
  setYears();
  setDate(targetDate);
}

$("#select-month").change(function () {
  var monthIndex = $("#select-month").val();
  setDays(monthIndex);
});

function setDate(date, user_data = null) {
  setDays(date.getMonth());
  if (user_data) {
    Number(user_data.day) > 0 ? $("#select-day").val(Number(user_data.day)) : '';
    Number(user_data.month) > 0 ? $("#select-month").val(Number(user_data.month) - 1) : '';
    Number(user_data.year) > 0 ? $("#select-year").val(Number(user_data.year)) : '';
  }
}

// make sure the number of days correspond with the selected month
function setDays(monthIndex) {
  var optionCount = $('#select-day option').length - 1,
    daysCount = daysInMonth[monthIndex];

  if (optionCount < daysCount) {
    for (var i = optionCount; i < daysCount; i++) {
      $('#select-day')
        .append($("<option></option>")
          .attr("value", i + 1)
          .text(i + 1));
    }
  }
  else {
    for (var i = daysCount; i < optionCount; i++) {
      var optionItem = '#select-day option[value=' + (i + 1) + ']';
      $(optionItem).remove();
    }
  }
}

function setYears(user_data = null) {
  var year = today.getFullYear();
  var year_min = new Date(1960, 1, 1);
  year_min = year_min.getFullYear();
  console.log(year_min);
  console.log(year);
  for (var i = year_min; i <= year; i++) {
    $('#select-year')
      .append($("<option></option>")
        .attr("value", i)
        .text(i));
  }
}
