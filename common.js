$(function () {
  $("#topbtn").hide();
  $(window).scroll(function () {
      if ($(window).scrollTop() > 800) {
          $("#topbtn").fadeIn(1000);
      } else {
          $("#topbtn").fadeOut();
      }
  });
  $("#topbtn").click(function () {
      $("body,html").animate({scrollTop: 0}, 500);
      return false;
  });

});
  