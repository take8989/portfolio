var member = [];
var members = '';
//利用者が変わった時はこの部分を変更する
var memberA = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
var memberB = ['K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S'];
//ここまで変更可
var role = [1, 1, 1, 1, 1, 1, 2, 2, 3, 4];
var rand = 0;
var randnum = 0;

function set_member() {
   members = $('[name=team]').val();
   if (members === "A") {
      //利用者が変わった時はこの部分を変更する
      member = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
      //ここまで変更可
   } else if (members === "B") {
      //利用者が変わった時はこの部分を変更する
      member = ['K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S'];
      //ここまで変更可
   }
};

$(function first_checkbox() {
   set_member();
   for (var i = 0; i < member.length; i++) {
      $('#checkboxs').append('<label><input type="checkbox" class="check" value=' + member[i] + '>' + member[i] + '</label>');
   }
});

$(function member_table() {
   var strA = "";
   for (var i = 0; i < memberA.length; i++) {
      strA = strA + memberA[i] + '、';
   }
   strA = strA.slice(0, -1);
   $("#memberA").append(strA);

   var strB = "";
   for (var i = 0; i < memberB.length; i++) {
      strB = strB + memberB[i] + '、';
   }
   strB = strB.slice(0, -1);
   $("#memberB").append(strB);


});

function change_absentee() {
   $('#checkboxs').html('');
   for (var i = 0; i < member.length; i++) {
      $('#checkboxs').append('<label><input type="checkbox" class="check" value=' + member[i] + '>' + member[i] + '</label>');
   }
}

function set_role() {
   if (member.length <= 4) {
      role = [1, 2, 3, 4];
   } else if (member.length === 5) {
      role = [1, 1, 2, 3, 4];
   } else if (member.length === 6) {
      role = [1, 1, 2, 2, 3, 4];
   } else if (member.length === 7) {
      role = [1, 1, 1, 2, 2, 3, 4];
   } else if (member.length === 8) {
      role = [1, 1, 1, 1, 2, 2, 3, 4];
   } else if (member.length === 9) {
      role = [1, 1, 1, 1, 1, 2, 2, 3, 4];
   } else if (member.length === 10) {
      role = [1, 1, 1, 1, 1, 1, 2, 2, 3, 4];
   }
}

function allocate() {
   $('#role1').html('');
   $('#role2').html('');
   $('#role3').html('');
   $('#role4').html('');

   if (member.length <= 4) {
      for (var i = 0; i < member.length; i) {
         rand = Math.floor(Math.random() * member.length);
         random = member[rand];
         role_key = Math.floor(Math.random() * role.length);
         role_num = role[role_key];
         $('#role' + role_num).append('<p>' + random + '</p>');
         member.splice(rand, 1);
         role.splice(role_key, 1);
      }
   } else if (member.length === 5) {
      for (var i = 0; i < member.length; i) {
         rand = Math.floor(Math.random() * member.length);
         random = member[rand];
         role_key = Math.floor(Math.random() * role.length);
         role_num = role[role_key];
         $('#role' + role_num).append('<p>' + random + '</p>');
         member.splice(rand, 1);
         role.splice(role_key, 1);
      }
      $('#role1 p').addClass('left');
   } else {
      for (var i = 0; i < member.length; i) {
         rand = Math.floor(Math.random() * member.length);
         random = member[rand];
         role_key = Math.floor(Math.random() * role.length);
         role_num = role[role_key];
         $('#role' + role_num).append('<p>' + random + '</p>');
         member.splice(rand, 1);
         role.splice(role_key, 1);
      }
      $('#role1 p').addClass('left');
      $('#role2 p').addClass('left');
   }
   set_member();
}

function exclude_absentee() {
   var absentee = $('.check:checked').map(function () {
      return $(this).val();
   }).get();

   for (var i = 0; i < absentee.length; i++) {
      member.splice(member.indexOf(absentee[i]), 1);
   }
   //$('#test1').html(absentee);
   //$('#test2').html(member);
}

$(function () {
   $('#team').change(set_member);
   $('#team').change(change_absentee);
   $('#allocation').click(exclude_absentee);
   $('#allocation').click(set_role);
   $('#allocation').click(allocate);
});