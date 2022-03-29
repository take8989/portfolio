var interval_id;
var interval_id3;
var timeout_id;
var timeout_id2;
var timeout_id3;
var start_flag = false; // スタート処理中はtrue
var start_flag3 = false;
var start_flag4 = false;
var min = 0;
var sec = 0;
var subWin;
var subWin2;
var subWin3;
var f_hour;
var f_mi;

//起動したときのタイマー表示
$(function first_timer() {
   set_timer();
});

function set_timer() {
   min = $('#working_set .min').val();
   sec = $('#working_set .sec').val();
   if (min < 10 && sec < 10) {
      $('#time').html('0' + min + ':' + '0' + sec);
   } else if (min < 10) {
      $('#time').html('0' + min + ':' + sec);
   } else if (sec < 10) {
      $('#time').html(min + ':' + '0' + sec);
   } else {
      $('#time').html(min + ':' + sec);
   }

   if (min == 0) {
      min = 0;
   }

   if (sec == 0) {
      sec = 0;
   }

   if (min !== 0 || sec !== 0) {
      $('#start').prop('disabled', false);
   } else {
      $('#start').prop('disabled', true);
   }
   $('#reset').prop('disabled', true);
}

function open_sub() {
   subWin = window.open('sub-index.html' + '?rf_min=' + $('#refresh_set .min').val() + '&rf_sec=' + $('#refresh_set .sec').val(), '_blank', 'width=500,height=500,scrollbars=0,location=0,menubar=0,toolbar=0,status=1,directories=0,resizable=1,left=' + (window.screen.width - 500) / 2 + ',top=' + (window.screen.height - 500) / 2);
   subWin.focus();
}

function open_sub2() {
   subWin2 = window.open('sub-index2.html' + '?rf_min=' + $('#refresh_set .min').val() + '&rf_sec=' + $('#refresh_set .sec').val(), '_blank', 'width=500,height=500,scrollbars=0,location=0,menubar=0,toolbar=0,status=1,directories=0,resizable=1,left=' + (window.screen.width - 500) / 2 + ',top=' + (window.screen.height - 500) / 2);
   subWin2.focus();
}

function open_sub3() {
   subWin3 = window.open('sub-index3.html' + '?rf_min=' + $('#refresh_set .min').val() + '&rf_sec=' + $('#refresh_set .sec').val(), '_blank', 'width=500,height=500,scrollbars=0,location=0,menubar=0,toolbar=0,status=1,directories=0,resizable=1,left=' + (window.screen.width - 500) / 2 + ',top=' + (window.screen.height - 500) / 2);
   subWin3.focus();
   stop_timer();
   $('#time').html('作業終了');
   $('#time').css('color', 'red');
   //$('#test').html(new Date().setHours(f_hour, f_mi, 0, 0) - new Date());
}

/*function close_win() {
   subWin.close();
}*/


function start_timer() {
   f_hour = $('#finish_set .hour').val();
   f_mi = $('#finish_set .min').val();
   // 停止しているときのみ、スタート処理を開始(二重クリック防止)
   if (start_flag === false) {
      // 1秒ごとにflash_textを実行
      interval_id = setInterval(timer, 1000);
      start_flag = true;
      if (new Date().setHours(f_hour, f_mi - 5, 0, 0) - new Date() > 0) {
         timeout_id = setTimeout(() => {
            open_sub2();
         }, new Date().setHours(f_hour, f_mi - 5, 0, 0) - new Date()) // 設定した終了時刻の5分前に実行;
      } else {
         //$('#test').append('open_sub2:error');
      }

      if (new Date().setHours(f_hour, f_mi, 0, 0) - new Date() > 0) {
         timeout_id2 = setTimeout(() => {
            open_sub3();
         }, new Date().setHours(f_hour, f_mi, 0, 0) - new Date()) // 設定した終了時刻に実行;
      } else {
         //$('#test').append('open_sub3:error');
      }

      $('#start').prop('disabled', true);
      $('#stop').prop('disabled', false);
      $('#reset').prop('disabled', false);
      $('#working_set .min').prop('disabled', true);
      $('#working_set .sec').prop('disabled', true);
   }
}

function timer() {
   //$('#test').html(new Date().setHours(f_hour, f_mi, 0, 0) - new Date());
   if (min === 0 && sec === 0) {
      clearInterval(interval_id);
      start_flag = false;
      $('#stop').prop('disabled', true);
      open_sub();
      timeout_id3 = setTimeout(() => {
         interval_id3 = setInterval(subWin_check, 1000);
      }, 60000) //1分後に実行;

      start_flag3 = true;
   } else if (sec === 0) {
      min--;
      sec = 59;
   } else {
      sec--;
   }

   if (min === 0 && sec === 0) {
      $('#time').html('Time UP!');
      $('#time').css({
         color: "red"
      });
   } else if (min < 10 && sec < 10) {
      $('#time').html('0' + min + ':' + '0' + sec);
   } else if (min < 10) {
      $('#time').html('0' + min + ':' + sec);
   } else if (sec < 10) {
      $('#time').html(min + ':' + '0' + sec);
   } else {
      $('#time').html(min + ':' + sec);
   }
}

function stop_timer() {
   // setIntervalによる繰り返し動作を停止
   clearInterval(interval_id);
   clearTimeout(timeout_id);
   clearTimeout(timeout_id2);
   start_flag = false;
   $('#start').prop('disabled', false);
   $('#stop').prop('disabled', true);
   $('#working_set .min').prop('disabled', false);
   $('#working_set .sec').prop('disabled', false);
}

function reset_timer() {
   /*if(subWin.closed) {
      $("#test").html("閉じている");
   } else {
      $("#test").html("閉じていない");
   }*/
   min = $('#working_set .min').val();
   sec = $('#working_set .sec').val();

   if (min == 0) {
      min = 0;
   }

   if (sec == 0) {
      sec = 0;
   }

   if (min < 10 && sec < 10) {
      $('#time').html('0' + min + ':' + '0' + sec);
   } else if (min < 10) {
      $('#time').html('0' + min + ':' + sec);
   } else if (sec < 10) {
      $('#time').html(min + ':' + '0' + sec);
   } else {
      $('#time').html(min + ':' + sec);
   }
   clearInterval(interval_id);
   clearTimeout(timeout_id);
   clearTimeout(timeout_id2);
   clearInterval(interval_id3);
   clearTimeout(timeout_id3);
   start_flag = false;
   $('#time').css({
      color: "#000000"
   });
   $('#start').prop('disabled', false);
   $('#stop').prop('disabled', true);
   $('#reset').prop('disabled', true);
   $('#working_set .min').prop('disabled', false);
   $('#working_set .sec').prop('disabled', false);
}

function subWin_check() {
   if (!subWin || subWin.closed) {
      clearInterval(interval_id3);
      clearTimeout(timeout_id3);
      start_flag3 = false;
      reset_timer();
      start_timer();
   }
}

$(function () {
   $('#start').click(start_timer);
   $('#stop').click(stop_timer);
   $('#reset').click(reset_timer);
   $('#working_set .set_timer').change(set_timer);
   if (min === 0 && sec === 0) {
      $('#start').prop('disabled', true);
   }
   $('#stop').prop('disabled', true);
   $('#reset').prop('disabled', true);
});

//休憩時間の設定//

var interval_id2;
var start_flag2 = false; // スタート処理中はtrue
var para = location.search;
var value = para.split('=');
var value2 = value[1].split('&');
var rf_min = value2[0];
var rf_sec = value[2];

$(function () {
   $('#rf_start').click(start_timer2);
   $('#rf_finish').click(finish_refresh);
   $('#rf_finish').prop('disabled', true);
});



if (rf_min == 0) {
   rf_min = 0;
}

if (rf_sec == 0) {
   rf_sec = 0;
}


$(function refresh_set_timer() {
   if (rf_min < 10 && rf_sec < 10) {
      $('#rf_time').html('0' + rf_min + ':' + '0' + rf_sec);
   } else if (rf_min < 10) {
      $('#rf_time').html('0' + rf_min + ':' + rf_sec);
   } else if (rf_sec < 10) {
      $('#rf_time').html(rf_min + ':' + '0' + rf_sec);
   } else {
      $('#rf_time').html(rf_min + ':' + rf_sec);
   }

});

function start_timer2() {
   if (start_flag2 === false) {
      $('#rf_start').prop('disabled', true);
      $('#rf_finish').prop('disabled', false);
      // 1秒ごとにflash_textを実行
      interval_id2 = setInterval(refresh_start, 1000);
      start_flag2 = true;
   }
}

function refresh_start() {
   if (rf_min === 0 && rf_sec === 0) {
      clearInterval(interval_id2);
      start_flag2 = false;
   } else if (rf_sec === 0) {
      rf_min--;
      rf_sec = 59;
   } else {
      rf_sec--;
   }

   if (rf_min === 0 && rf_sec === 0) {
      $('#rf_time').html('Time UP!');
      $('#rf_time').css({
         color: "red"
      });
      $('#message').html('作業を再開しましょう（休憩終了を押してください）');
      $('#message').css({
         color: "red"
      });
   } else if (rf_min < 10 && rf_sec < 10) {
      $('#rf_time').html('0' + rf_min + ':' + '0' + rf_sec);
   } else if (rf_min < 10) {
      $('#rf_time').html('0' + rf_min + ':' + rf_sec);
   } else if (rf_sec < 10) {
      $('#rf_time').html(rf_min + ':' + '0' + rf_sec);
   } else {
      $('#rf_time').html(rf_min + ':' + rf_sec);
   }
}

function finish_refresh() {
   window.close();
}