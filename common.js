//トップに戻るボタン
$(function () {
  $("#topbtn").hide();
  $(window).scroll(function () {

//トップから801px下にスクロールした時にボタンが現れる
      if ($(window).scrollTop() > 800) {
          $("#topbtn").fadeIn(1000);
      } else {
          $("#topbtn").fadeOut();
      }
  });
  
//ボタンを押したときトップに戻る
  $("#topbtn").click(function () {
      $("body,html").animate({scrollTop: 0}, 500);
      return false;
  });

});
  