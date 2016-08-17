<?php
 // Created by PhpStorm.
 //User: juanito
 //Date: 8/3/16
 //Time: 1:19 PM
session_start();
ini_set('display_errors', 'On');   // error checking
error_reporting(E_ALL);    // error checking
$localtime_assoc = localtime(time(), true);
$localmonth = $localtime_assoc["tm_mon"];
$localday = $localtime_assoc["tm_mday"];
$localyear = $localtime_assoc["tm_year"] + 1900;
if (!isset($_COOKIE["year"])) {
	setcookie("year", $localyear, time() + (86400 * 30), "/"); // set to direct
}
$year_in = $_COOKIE["year"];
// connect to DB
$file = fopen("../../db.txt", "r") or die("Error opening file.");
$dbinfo = [];
while (!feof($file)) {
	$info = trim(fgets($file));
	array_push($dbinfo, $info);
}
$connect = mysqli_connect($dbinfo[0], $dbinfo[1], $dbinfo[2], $dbinfo[3], $dbinfo[4]);
if (empty($connect)) {
	die ("mysqli_connect failed " . mysqli_connect_error());
}
$sql = "SELECT * FROM doctor_logger WHERE YEAR = " . $year_in . " ORDER BY MONTH, DAY";
$result = $connect->query($sql);
$days = [];
$weekdays = [];
$docs1 = [];
$docs2 = [];
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$cur_day = $row["DAY"];
		if ($cur_day == "1") {
			if (isset($days_list) && isset($weekdays_list)) {
				array_push($days, $days_list);
				array_push($weekdays, $weekdays_list);
				array_push($docs1, $doc1_list);
				array_push($docs2, $doc2_list);

			}
			$days_list = [];
			$weekdays_list = [];
			$doc1_list = [];
			$doc2_list = [];
			array_push($days_list, $cur_day);
			array_push($weekdays_list, $row["WEEKDAY"]);
			array_push($doc1_list, $row["DOC1"]);
			array_push($doc2_list, $row["DOC2"]);
		}
		else {
			array_push($days_list, $cur_day);
			array_push($weekdays_list, $row["WEEKDAY"]);
			array_push($doc1_list, $row["DOC1"]);
			array_push($doc2_list, $row["DOC2"]);
		}
	}
	array_push($days, $days_list);
	array_push($weekdays, $weekdays_list);
	array_push($docs1, $doc1_list);
	array_push($docs2, $doc2_list);
}

//fetches docs

$sql = "SELECT * FROM names ORDER BY ID ASC;";
$result = $connect->query($sql);
$docs = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $basket = [];
        array_push($basket, $row["ID"]);
        array_push($basket, $row["FIRST_NAME"]);
        array_push($basket, $row["LAST_NAME"]);
        array_push($basket, $row["CLASS"]);
        array_push($docs, $basket);
    }
}
include("./month_generator_function.php");
$calendar = "";
$p = 0;
for ($i = 0; $i < 3; $i++) {
	$calendar .= '<div class="row">';
	for ($j = 0; $j < 4; $j++) {
		$calendar .= month_generator($p, $days, $weekdays, $year_in, $docs1, $docs2, $docs);
		$p++;
	}
	$calendar .= '</div>';
}
?>

<!DOCTYPE html>
<html lang="en" id="kahuna">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title> Doctor Logger </title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
  <link rel="stylesheet" href="./index.css">



  <!-- JQuery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <!-- Latest compiled and minified JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <script src="//cdn.jsdelivr.net/jquery.scrollto/2.1.2/jquery.scrollTo.min.js"></script> <!-- scroll to function -->
  <!-- scrollto plugin -->
  <script>
      <?php
          echo 'var cur_year =' . $_COOKIE["year"] . ';';
      ?>
      var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
      function clear_month(mon) {
          // first ask user if they are sure
          if (window.confirm("Are you sure you want to delete all entrees for " + months[mon] + " " + cur_year + "?")) {
              $.post("./clear_month.php", { //changes year cookie
                      month: mon
                  },
                  function (response) {
                      var b = 0;
                      do  {
                          if (dox1[mon][b] != -1) {
                              var c = b + 1;
                              var st =  "#" + mon + "-" + b + "-" + cur_year + "-" + "docname-1";
                              var st2 =  "#" + mon + "-" + c + "-" + cur_year + "-" + "docname-1";
                              $(st).remove();
                              $(st2).remove();
                              // update month table
                              var ident = "#doc-" + dox1[mon][b] + "-total-days";
                              var counter = parseInt($(ident).text());
                              counter --;
                              $(ident).text(counter);
                              var ident = "#total-month-doc-" + dox1[mon][b];
                              var counter = parseInt($(ident).text());
                              counter --;
                              $(ident).text(counter);
                              dox1[mon][b] = -1;
                          }
                          if (dox2[mon][b] != -1) {
                              var c = b + 1;
                              var st =  "#" + mon + "-" + b + "-" + cur_year + "-" + "docname-2";
                              var st2 =  "#" + mon + "-" + c + "-" + cur_year + "-" + "docname-2";
                              $(st).remove();
                              $(st2).remove();
                              dox2[mon][b] = -1;
                          }
                          b++;
                      } while (b < dox1[mon].length);
                      var st =  "#" + mon + "-" + dox1[mon].length + "-" + cur_year + "-" + "docname-";
                      $(st + "2").remove();
                      $(st + "1").remove();
                  }); // ends post
          }
        // physically update
      }
      function print_month(mon) {
          alert("When you print, make sure to set orientation to \"landscape\".");
          $.post("./print_month.php", {
                  mon: mon
              },
              function (response) {
                  var win = window.open('./printhere.php', '_blank');
                  if (win) {
                      //Browser has allowed it to be opened
                      win.focus();
                  } else {
                      //Browser has blocked it
                      alert('Please allow popups so you can print this month.');
                  }
              }
          ); // ends post
      }
      // make local month count for docs
      var dox1 = [];
      var dox2 = [];
      var doxnames = [];
      var weekdays = [];
      <?php
      foreach ($docs1 as $bin) {
          echo 'var bin = [];';
          foreach ($bin as $elem) {
              if (isset($elem)) {
                  echo 'bin.push(' . $elem . ');';
              }
              else {
                  echo 'bin.push(-1);';
              }
          }
          echo 'dox1.push(bin);';
      }
      foreach ($docs2 as $bin) {
          echo 'var bin = [];';
          foreach ($bin as $elem) {
              if (isset($elem)) {
                  echo 'bin.push(' . $elem . ');';
              }
              else {
                  echo 'bin.push(-1);';
              }
          }
          echo 'dox2.push(bin);';
      }
      foreach ($docs as $bin) {
          echo 'var bin = [];';
          foreach ($bin as $elem) {
              echo 'bin.push("' . $elem . '");';
          }
          echo 'doxnames.push(bin);';
      }
      foreach ($weekdays as $bin) {
          echo 'var bin = [];';
          foreach ($bin as $elem) {
              echo 'bin.push("' . $elem . '");';
          }
          echo 'weekdays.push(bin);';
      }
      ?>
      function add_doctor() {
          $.post("./add_doctor.php", {
                   first: $("#fname").val(),
                   last: $("#lname").val()
              },
              function (response) {
                  response.trim();
                  window.location.reload(true);
              }
          ); // ends post
      }
      function remove_doctor() {
          if (window.confirm("Are you sure you want to remove " + doxnames[$("#r_drop").val()][1] + " " + doxnames[$("#r_drop").val()][2] + "?"))
          {
              $.post("./remove_doctor.php", {
                      id: $("#r_drop").val()
                  },
                  function (response) {
                      response.trim();
                      window.location.reload(true);
                  }
              ); // ends post
          }
      }
	  var prevcolor;
	  function colorin(id) {
		  prevcolor = $(id).css("background-color");
		  $(id).css("background-color", "lightgreen");
		  if (!plusoff) {
<?php
		  if (isset($_COOKIE["logged_in"])) {
			  if ($_COOKIE["logged_in"] == "true") {
				  // make plus available when hovering

				  echo '
				  	 var txt = $(id).attr("id");
				  	 txt = txt.slice(0, -4);
				  	 txt = "." + txt + "-add";
				     $(txt).show();
				  		';

			  }
		  }
?>	} // ends plusoff
	  }
	  function colorout(id) {
		  // if you are not having over plus_link
		  $(id).css("background-color", prevcolor);

<?php
		  if (isset($_COOKIE["logged_in"])) {
			  if ($_COOKIE["logged_in"] == "true") {
				  // remove plus when hover out
				  echo '
				  	 var txt = $(id).attr("id");
				  	 txt = txt.slice(0, -4);
				  	 txt = "." + txt + "-add";
				     $(txt).hide();
				  ';
			  }
		  }
?>       // } // ends if hover
	  }
      function autopop() {
          $("#autopop-modal").modal('show');
          /*
          if (window.confirm("Are you certain you want auto-populate " + cur_year + "? \n This will erase all current entrees.")) {
              $.post("./autopopyear.php", { //changes year cookie
                      year : cur_year
                  },
                  function(response) {
                      window.location.reload(true);
                     // window.location.reload(true);
                  }); // ends post
          } */
      }
      function autopop_submit() {
          var poporder = [];
          var check = true;
          for (var i = 0; i < doxnames.length; i++) {
              var v = $('#pop-order-' + i).val();
              poporder.push(v);
          }
          // check if we have duplicates
          var lyst = poporder.slice(0);
          for (var j = 0; j < lyst.length; j++) {
              lyst.sort();
              if (lyst[j] == lyst[j + 1]) {
                  check = false;
              }
          }
          if (!check) {
              alert("You may not have duplicate values.");
          }
          else {
              var ord = '';
              ord += poporder[0];
              for (var x = 1; x < poporder.length; x++) {
                  ord += '-' + poporder[x];
              }
              // now get all our months
              var mon_array = document.getElementsByName("month_select");
              var selected_months = [];
              for (var i = 0; i < mon_array.length; i++) {
                  if (mon_array[i].checked) {
                      selected_months.push(mon_array[i].value);
                  }
              }
              if (selected_months.length == 0) {
                  alert("You must select at least one month.");
              }
              else {
                  console.log(selected_months);
                  var mselect = '';
                  mselect += selected_months[0];
                  if (selected_months.length > 1) {
                      for (var x = 1; x < selected_months.length; x++) {
                          mselect += '-' + selected_months[x];
                      }
                  }

                  $.post("./autopopyear.php", { //changes year cookie
                       order : ord,
                       months : mselect,
                       year : cur_year
                   },
                   function(response) {
                       window.location.reload(true);
                   }); // ends post */
              }
          }
      }
      function clear_all() {
          if (window.confirm("Are you certain you want to clear all days in the year " + cur_year + "?")) {
              $.post("./clearyear.php", {
                      year : cur_year
                  },
                  function(response) {
                      window.location.reload(true);
                  }); // ends post
          }
      }

	function yearswitcher(val) { // if user selects a different year
  		$.post("./changeyear.php", { //changes year cookie
                	year : val
            	},
            	function(response) {
                	window.location.reload(true);
            	}); // ends post

	}

	  function update() { // AJAX call that updates right table when there's a change
		  // must generate doctor stats
		  $.post("./docstats.php", {
				  year: val
			  },
			  function (response) {
				  response.trim();
			  }
		  ); // ends post
	  }

	  function doc_add(id) {
		  var strr = ($(id).attr("id"));
		  strr = strr.split(" ");
		  strr = strr[2];
		  strr = (strr.slice(0, -3));
		  $(".remove-drop, .remove_lyst, #add_menu").remove();
		  $("#" + strr + "box").css("background-color", "white");
		  // must generate doctor stats
		  plusoff = false;
          droppy = false;
		  $.post("./doc_add.php", {
				  date: strr,
			  	  doc: id.value
			  },
			  function (response) {
				  response.trim();
                  response = response.split(" ");
                  if (response[0] == "doc_1_added") {
                    // add doc 1 span and update year and month js bins
                      var ident = response[1];
                      // insert first and last name of doc
                      var docid = response[2];
                      var counter = parseInt($("#doc-" + docid + "-total-days").text());
                      counter ++;
                      $("#doc-" + docid + "-total-days").text(counter);
                      var monup = ident.split("-");
                      dox1[monup[0]][monup[1]] = docid; // place4
                      // now physically append to current month counter
                      var counter = parseInt($("#total-month-doc-" + docid).text());
                      counter ++;
                      $("#total-month-doc-" + docid).text(counter);
                      var elem = '<span class="docname n1" id="' + ident + '" >' + doxnames[docid][1] + " " + doxnames[docid][2]   + '<span class="glyphicon glyphicon-remove deletedoc"></span></span>';
                      var boxid = "#" + ident.slice(0, -9) + "box";
                      $(boxid).append(elem);
                      $("#" + ident).hover(function() {
                        //  var id = $(this).attr("id");
                          $("#" + ident).css("background-color", "red");
                          // show x
                          $("#" + ident + " span").show();
                      }, function() {
                          $("#" + ident).css("background-color", "transparent");
                          // hide x
                          $("#" + ident + " span").hide();
                      });
                      var id = ident;
                      $("#" + id).click(function() {
                      $.post("./deletedoc.php", {
                              box : id
                          },
                          function(response) {
                              var counter = parseInt($("#doc-" + response + "-total-days").text());
                              counter --;
                              $("#doc-" + response + "-total-days").text(counter);
                              // update month list and refresh
                              var monup = id.split("-");
                              if (dox1[monup[0]][monup[1]] != -1) {
                                  dox1[monup[0]][monup[1]] = -1; // place4
                              }
                              // now physically append to current month counter
                              var counter = parseInt($("#total-month-doc-" + response).text());
                              counter --;
                              $("#total-month-doc-" + response).text(counter);

                              /*
                              // check if weekend value deleted
                              var enddays = [5, 6, 0];
                              var temp = weekdays[monup[0]][monup[1]]; // what day of the week are we deleting?
                              var todelete = false; // turns true if we want to decrease weekend count
                              for (var i in enddays) {
                                  if (temp == i) {

                                  }
                              }
                              if ($.inArray(weekdays[monup[0]][monup[1]], enddays)) {
                                  var counter = parseInt($("#doc-" + response + "-total-weekends").text());
                                  counter--;
                                  $("#doc-" + response + "-total-weekends").text(counter);
                              }  */
                              var tmp = "#" + id;
                              $(tmp).remove();
                              // now implement colorout to change color back
                              tmp = tmp.slice(0, -9) + "box";
                              colorout(tmp);
                              // update table on right
                          }); // ends post

                      })

                  }
                  if (response[0] == "doc_2_added") {
                      // add doc 1 span and update year and month js bins
                      var ident = response[1];
                      // insert first and last name of doc
                      var docid = response[2];
                      var elem = '<span class="docname n2" id="' + ident + '" >' + doxnames[docid][1] + " " + doxnames[docid][2]   + '<span class="glyphicon glyphicon-remove deletedoc"></span></span>';
                      var boxid = "#" + ident.slice(0, -9) + "box";
                      $(boxid).append(elem);
                      var monup = ident.split("-");
                      dox2[monup[0]][monup[1]] = docid;
                      $("#" + ident).hover(function() {
                          $("#" + ident).css("background-color", "red");
                          // show x
                          $("#" + ident + " span").show();
                      }, function() {
                          //var id = $(ident).attr("id");
                          $("#" + ident).css("background-color", "transparent");
                          // hide x
                          $("#" + ident + " span").hide();
                      });
                      var id = ident;
                      $("#" + id).click(function() {
                          $.post("./deletedoc.php", {
                                  box : id
                              },
                              function(response) {
                                  var tmp = "#" + id;
                                  $(tmp).remove();
                                  // now implement colorout to change color back
                                  tmp = tmp.slice(0, -9) + "box";
                                  colorout(tmp);
                                  // update table on right
                              }); // ends post

                      })
                  }
                  if (response[0] == "full") {
                      alert("This day is full. Delete a doctor.");
                  }
                  if (response[0] == "already_added") {
                      alert("Doctor already assigned to this day.");
                  }
			  }
		  ); // ends post
		  // add save button
	  }
	  function removedrop(dis) {
		  $(dis).remove();
	  }
	  function remove_lyst(dis) {
		  var strr = $(dis).attr("id");
		  strr = strr.split(" ");
		  strr = strr[2];
		  $("#" + strr.slice(0, -3) + "box").css("background-color", "white");
		  $(".remove-drop, .remove_lyst").remove()
		  $(".add").hide(); // check this later
		  droppy = false;
		  plusoff = false;
	  }
	  var droppy = false;
	  var plusoff = false;
      function log_out() {
          $.post("./log_out.php", {

              },
              function(response) {
                  // maybe have some confirmation
                  window.location.reload(true);
              }); // ends post
      } // ends log_out function
  $(document).ready(function() {


      $("#add-remove-btn").click(function() {
        $('#add-remove-modal').modal('show');
      });

      $("#log_in_button").click(function() {
          log_me_in();
      });
      $("#u_input, #p_input").keypress(function(e) {
          if(e.which == 13) {
              log_me_in();
          }
      });
      function log_me_in() {
          $.post("./log_in.php", { //changes year cookie
                  uname : $("#u_input").val(),
                  pass : $("#p_input").val()
              },
              function(response) {
                  if (response == "PASS") {
                      window.location.reload(true);
                  }
                  else {
                      alert("Incorrect login info");
                  }
              }); // ends post
      }

	  <?php
	  if ($_COOKIE["year"] == $localyear) {
		  echo '
			$.scrollTo( $("#month-head-' . $localmonth . '"), 500);
			// also color our current current day cell yellow
			var cur_day_box = "#' . $localmonth . '-' . $localday . '-' . $localyear . '-box";
			$(cur_day_box).delay("fast").css("background-color", "yellow");
		';
	  }
	  else {
		  echo '
			$.scrollTo( $("#month-head-0"), 500);';
	  }
	  if (isset($_COOKIE["logged_in"])) {
		  if ($_COOKIE["logged_in"] == "false") {
			  // make plus available when hovering
             echo ' $(".admin-only").hide();
                    plusoff=true;
             ';
		  }
          else {
              // if user logged in
              echo '
              $(".docname").hover(function() {
                  var id = $(this).attr("id");
                  $("#" + id).css("background-color", "red");
                  // show x
                  $("#" + id + " span").show();
              }, function() {
                  var id = $(this).attr("id");
                  $("#" + id).css("background-color", "transparent");
                  // hide x
                  $("#" + id + " span").hide();
              });
              $(".docname").click(function() {
                  var id = $(this).attr("id");
              $.post("./deletedoc.php", {
                  box : id
              },
              function(response) {
                  var tmp = "#" + id;
                  var monup = id.split("-");
                  var docnum = monup[4];
                  if (docnum == 1) {
                      var counter = parseInt($("#doc-" + response + "-total-days").text());
                      counter --;
                      $("#doc-" + response +  "-total-days").text(counter);
                      // now physically append to current month counter
                      var counter = parseInt($("#total-month-doc-" + response).text());
                      counter --;
                      $("#total-month-doc-" + response).text(counter);
                  }
                  if (dox1[monup[0]][monup[1] - 1] != -1) {
                      dox1[monup[0]][monup[1] - 1] = -1; // place4
                  }
                  else if (dox2[monup[0]][monup[1] - 1] != -1) {
                      dox2[monup[0]][monup[1] - 1] = -1; // place4
                  }
                  $(tmp).remove();
                  // now implement colorout to change color back
                  tmp = tmp.slice(0, -9) + "box";
                  colorout(tmp);
                  // update table on right
              }); // ends post
      });

              ';
          }
	  }



	  ?>
	  $('#monthscroll').on('activate.bs.scrollspy', function () {
		  var activeSection = $(this).find("li.active a").attr("href");
			// fetch month and year stats
          var doc = activeSection.split("-");
          var monc = doc[2]; // gets what month we are currently on
          // fetch doc stats for this month and update table
          for (var docid = 0; docid < doxnames.length; docid++) {
              var doccount = 0;
              for (var i = 0; i < (dox1[monc]).length; i++) {  // we're only checking current month
                  var tmp = dox1[monc][i];
                  if (tmp == docid) {
                      doccount ++;
                  }
              }
              $('#total-month-doc-' + docid).text(doccount);
          }
	  });
	  $(".add").click(function() {
		  var boxid = $(this).attr("class");
		  var tempid = boxid;
		  boxid = boxid.slice(25, -7) + 'box';
		  if (!droppy) {
			  var str = '<div id="add_menu"><select id="' + tempid + 'drop" class="remove-drop" onchange="doc_add(this)" onclick="alert("test");"><option></option>' +
				  <?php
                  foreach ($docs as $bin) {
                      echo '\'<option value="' . $bin[0] . '">' . $bin[1] . ' ' . $bin[2] . '</option>\' +';
                  }
				  ?>
				  '</select><span class="glyphicon glyphicon-remove remove_lyst" id="' + tempid + '-rl" onClick="remove_lyst(this)"></div><!--end add menu-->';

			  $("#" + boxid).append(str);
			  plusoff = true;
			  $(".add").hide();
			  droppy = true;
		  }
		  else {
			  alert("Close current dropdown or refresh page.");
		  }
	  })

  }); // ends document.ready
  </script>
    
</head>

<body data-spy="scroll" data-target="#monthscroll" data-offset="20">
  <div id="main_container" class="container-fluid">

<?php
	echo $calendar;



$sql = "SELECT DISTINCT YEAR FROM doctor_logger;";
$result = $connect->query($sql);
$yrs_list = [];
if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		array_push($yrs_list, $row["YEAR"]);
	}
}
echo '
<div id="left_panel">
    <div class="container">
    <div class="row">
      <nav class="col-sm-1" id="monthscroll">
        <ul class="nav nav-pills nav-stacked"><li>
	<select name="year" id="yearpicker" onchange="yearswitcher(this.value)">';
for ($c = 0; $c < count($yrs_list); $c++) {
	$y = $yrs_list[$c];
	if ($_COOKIE["year"] == $y) {
		echo '<option selected="selected" value="' . $y . '">' . $y . '</option>';
	}
	else {
		echo '<option value="' . $y . '">' . $y . '</option>';
	}
}

echo '
</select></li>';

	$months_abbr = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	for ($h = 0; $h < count($months_abbr); $h++) {
		$hrefstr = 'month-head-' . strval($h);
		$idstr = 'mon-head-' . strval($h);
		echo '<li><a id="' . $idstr . '" href="#' . $hrefstr . '">' . $months_abbr[$h] . '</a></li>';
	}
        echo '</ul>
      </nav>
    </div> <!-- end row -->
  </div> <! -- end container -->

</div> <!-- end left panel -->


  </div> <!-- end main container -->
  <div class="container right-pan" id="right-panel-contract">
  <div class="row">
    <div class="col-xs-6 doc-col-cont" id="doc_head"><b>Doctor</b></div>
    <div class="col-xs-4 tot-col-cont" id="yr_head"><b>Year</b></div>
    <div class="col-xs-1 mon-col-cont" id="mon_head"><b>Month</b></div>

</div>
    <!-- fetch doctors -->';

for ($h = 0; $h < count($docs); $h++) {
	$doc_id = $docs[$h][0];
	echo '<div class="row">
          <div class="col-xs-6 doc-col-cont" > ' . $docs[$h][2] . '</div>
	      <div class="col-xs-2 tot-col-cont" style="color:green" id="doc-' . $doc_id . '-total-days">
	      <!-- fetch how many days for that year -->';
	$total_year = 0;
	for ($a = 0; $a < count($docs1); $a++) {
		for ($b = 0; $b < count($docs1[$a]); $b++) {
			if ($doc_id == $docs1[$a][$b]) {
				$total_year ++;
			}
		}
	}
    // now fetch how many weekends per year
    $total_weekends = 0;
    $week_end = [5, 6, 0];
    $tab = 0;
    for ($a = 0; $a < count($docs1); $a++) {
        for ($b = 0; $b < count($docs1[$a]); $b++) {
            if ($doc_id == $docs1[$a][$b]) {
                if (in_array($weekdays[$a][$b], $week_end)){ // only count if it's three days in a row
                    $tab++;
                } else {
                    $tab = 0;
                }
                if ($tab > 2) {
                    $total_weekends ++;
                }
            }
        }
    }

    echo $total_year;
    echo '</div><div class="col-xs-2 tot-col-cont" style="color:darkred" id="doc-' . $doc_id . '-total-weekends">';
    echo $total_weekends;

	// now get total for current month
	echo '
		  </div> <!-- end total days -->
		  <div class="col-xs-1 mon-col-cont" style="color:blue" id="total-month-doc-' . $doc_id . '"></div>
	      </div> <!-- end unique doctor row -->
																				';
}

echo '
  </div> <!-- end right-panel-contract -->';

if ($_COOKIE["logged_in"] == "true") {
    //produce admin panel
    echo '<div id="admin_panel" class="container">
      <div class="row">
        <div class="col-xs-12">
          <button type="button" class="btn btn-primary" id="add-remove-btn">Modify doctors</button>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <button type="button" class="btn btn-warning" id="autopop-all-btn" onclick="autopop()">Auto-populate</button>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">
           <button type="button" class="btn btn-danger" id="clear-all-btn" onclick="clear_all()">Clear all</button>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <button class="btn btn-default" onClick="log_out()" id="log-out-btn">Log out</button>
        </div>
      </div> <!-- end row -->

    </div><!-- end admin_panel -->
    ';
}
else {
    echo '
  <div id="login_panel" class="container">
    <form>
    <div class="row" id="u_box">
      <div class="col-xs-12">
        <input type="text" name="username" id="u_input" placeholder="Username">
      </div>
    </div>
    <div class="row" id="p_box">
      <div class="col-xs-12">
        <input type="password" name="password" id="p_input" placeholder="Password">
      </div>
    </div>
    <div class="row" id="log_box">
      <div class="col-xs-6" >

      </div>
      <div class="col-xs-6">
        <button type="button" class="btn btn-default" id="log_in_button">Log In</button>
      </div>
    </div>
    </form>
  </div> <!-- end login_panel -->';
}

?>
      <!-- add-remove-modal -->
      <div class="modal fade" id="add-remove-modal" role="dialog">
          <div class="modal-dialog">
              <!-- Modal content-->
              <div class="modal-content">
                  <div class="modal-header" style="padding:35px 50px;">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h2 id="mod-doc-head"><span class="glyphicon glyphicon-user" id="user-icon"></span>Modify Doctors</h2>
                  </div>
                  <div class="modal-body" style="padding:40px 50px;">
                    <div class="container" id="container-in-modal">
                        <div clas="row">
                            <div class="col-xs-3 offset-xs-3">
                                <input type="text" placeholder="First name" id="fname" name="fname">
                            </div>
                            <div class="col-xs-3">
                                <input type="text" placeholder="Last name" id="lname" name="lname">
                            </div>
                            <div class="col-xs-3">
                                <button type="button" onclick="add_doctor()" class="btn btn-primary" id="add_button">Add</button>
                            </div>
                        </div>     <!-- end row -->
                        <div class="row">
                            <div class="col-xs-12">
                              <hr style="padding:0; height: 2px;">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-4">
                                <select id="r_drop">
                                <?php
                                foreach ($docs as $bin) {
                                    echo '\'<option value="' . $bin[0] . '">' . $bin[1] . ' ' . $bin[2] . '</option>\' +';
                                }
                                ?>
                                </select>
                            </div>
                            <div class="col-xs-4">
                                <button type="button" id="rmv-btn" onclick="remove_doctor()" class="btn btn-danger">Remove</button>
                            </div>
                        </div> <!-- end row -->
                     </div> <!-- end container -->

                  </div> <!-- end modal body -->
              </div> <!-- end modal header -->
          </div> <!-- end modal content -->
      </div>
      <!-- end #add-remove-modal -->

      <!-- add-remove-modal -->
      <div class="modal fade" id="autopop-modal" role="dialog">
          <div class="modal-dialog">
              <!-- Modal content-->
              <div class="modal-content">
                  <div class="modal-header" style="padding:35px 50px;">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h2 id="mod-doc-head"><span class="glyphicon glyphicon-repeat" id="user-icon"></span>Auto-populate</h2>
                  </div>
                  <div class="modal-body" style="padding:40px 50px;">
                      <div class="container" id="container-in-modal">
<script>
    var months_abbr =  ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var head = 0;
    for (var c = 0; c < 3; c++) {
        document.writeln('<div class="row">');
        for (var m = 0; m < 4; m++) {
            document.writeln('<div class="col-xs-3"><input type="checkbox" name="month_select" value="' + head + '" checked>' + " " + months[head] + '</div>');
            head++;
        }
        document.writeln('</div>');
    }

</script>
                      <div class="row">
                         <div class="col-xs-12">
                           <hr style="padding:0; height: 2px;">
                        </div>
                      </div> <!-- end row with line -->
                      <div class="row"> <!-- start row for doc names -->
<script>
    var half = Math.ceil(doxnames.length / 2);
    var idx = 0;


    // we're gonna have two columns. Start inserting into next column when idx = half, append idx each pass

    for (var i = 0; i < doxnames.length; i++) {
        if (idx == 0 || idx == half) {
            document.write('<div class="col-xs-6">');
        }
        document.write('<div class="row"><div class="col-xs-12"><select id="pop-order-' + i + '">');
        for (var j = 0; j < doxnames.length; j++) {
            if (j != i ) {
                document.write('<option value="' + j + '">' + (j + 1) + '</option>');
            }
            else {
                document.write('<option value="' + j + '"selected>' + (j + 1) + '</option>');
            }
        }
        var str = '</select> ' + (doxnames[i][1]) + ' ' + (doxnames[i][2]) + '</div></div>';
        document.write(str);

        if (idx == (half - 1) || idx == (doxnames.length - 1)) {
            document.write('</div> <!-- end col w/ doc names -->');
        }
        idx ++;
    }



</script>
                      </div> <!-- end row for doc names -->
                      <div class="row"> <!-- row for autopop button -->
                          <div class="col-xs-2 col-xs-offset-9">
                              <button type="button" class="btn btn-warning" onclick="autopop_submit()">Auto-populate</button>
                          </div>
                      </div>
                      </div> <!-- end container -->

                  </div> <!-- end modal body -->
               </div>  <!-- end modal header -->
          </div> <!-- end modal content -->
      </div>      <!-- end autopop-modal-->
</body>
</html>
