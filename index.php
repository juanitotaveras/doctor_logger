<?php
 // Created by PhpStorm.
 //User: juanito
 //Date: 8/3/16
 //Time: 1:19 PM
session_start();
ini_set('display_errors', 'On');   // error checking
error_reporting(E_ALL);    // error checking
setcookie("logged_in", "true", time() + (30*30), "/");
// set cookie to detect if first logged in
$localtime_assoc = localtime(time(), true);
$localmonth = $localtime_assoc["tm_mon"];
$localday = $localtime_assoc["tm_mday"];
$localyear = $localtime_assoc["tm_year"] + 1900;
if (!isset($_COOKIE["year"])) {
	setcookie("year", $localyear, time() + (86400 * 30), "/"); // set to direct
}
$year_in = $_COOKIE["year"];
// connect to DB
$file = fopen("./db.txt", "r") or die("Error opening file.");
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
$months = [];
for ($x = 0; $x < 12; $x++) {
	array_push($months, $x);
}
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

$sql = "SELECT * FROM names;";
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
//print_r($doc1_list);
//echo count($doc1_list[0]);
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
      // make local month count for docs
      var dox1 = [];
      var dox2 = [];
      var doxnames = [];
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
              if (!isset($elem)) {
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
      ?>
      for (var x in doxnames) {
       //   console.log(doxnames[x]);
         // console.log(doxnames[x].length);
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
		 // if (!$("#plus_link").is(":hover") && !$("#add-icon").is(":hover")) {
		  $(id).css("background-color", prevcolor);
		 // console.log("test");
		 // $(".add").hide();
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
	  function update_month(year_req, month_req) { // implemented every time month switched
		  /*
		  $.post("./docstatsmonth.php", {
				  year: year_req,
				  month: month_req
			  },
			  function (response) {
				  response.trim();
				  // delete current span, insert new updated span
			  }
		  ); // ends post */
		  // fetch all doc stats for current month

	  }
	  function add_save_button() {

	  }
	  function doc_add(id) {
		  var strr = ($(id).attr("id"));
		  strr = strr.split(" ");
		  strr = strr[2];
		  strr = (strr.slice(0, -3));
		  $(".remove-drop, .remove_lyst, #add_menu").remove();
		  $("#" + strr + "box").css("background-color", "white");
		  // add update master list, make function that emerges save button
		//  strr = strr.split("-"); // you must do AJAX, might as well update DB (it will reload every time anyway)
		  // add to $doc1_lyst
		  // must generate doctor stats
		  plusoff = false;
          droppy = false;
          //alert(id.value);
		  $.post("./doc_add.php", {
				  date: strr,
			  	  doc: id.value
			  },
			  function (response) {
				  response.trim();
                //  alert(response);
                  console.log(response);
                  // add physical name to cal

                  // now append to our counter

			  }
		  ); // ends post
		  // add save button
	  }
      // add function that will do all this if same person is clicked

	  function removedrop(dis) {
		  $(dis).remove();
	  }
	  function remove_lyst(dis) {
		 // alert($(dis).attr("id"));

		  var strr = $(dis).attr("id");
		  strr = strr.split(" ");
		  strr = strr[2];
		//  alert(strr.slice(0, -4) + "box");
		  $("#" + strr.slice(0, -3) + "box").css("background-color", "white");
		 // console.log("#" + strr.slice(0, -4) + "box");
		 // $(dis).remove();
		  $(".remove-drop, .remove_lyst").remove()
		  $(".add").hide(); // check this later
		  droppy = false;
		  plusoff = false;
		//  $("#" + strr.slice(0, -3) + "plus").hide();

		  //$(dis).remove();

		  //alert(strr);
	  }

	  var droppy = false;
	  var plusoff = false;
  $(document).ready(function() {


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
		  if ($_COOKIE["logged_in"] == "true") {
			  // make plus available when hovering
		  }
	  }



	  ?>
	  $('#monthscroll').on('activate.bs.scrollspy', function () {
		  var activeSection = $(this).find("li.active a").attr("href");
			// fetch month and year stats
		  //console.log(activeSection);
          var doc = activeSection.split("-");
          var monc = doc[2]; // gets what month we are currently on
          // dox1 = docs1, dox2 = docs2
          // fetch doc stats for this month and update table

          for (var docid = 0; docid < doxnames.length; docid++) {
              var doccount = 0;
              for (var i = 0; i < (dox1[monc]).length; i++) {  // we're only checking current month
                  var tmp = dox1[monc][i];
                  if (tmp == docid) {
                      doccount ++;
                  }
              }
              //console.log(doxnames[docid] + doccount);
              $('#total-month-doc-' + docid).text(doccount);
          }
	  });

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
          $.post("./deletedoc.php", { //changes year cookie
                  box : id
              },
              function(response) {
                  //window.location.reload(true);
                  //alert(response);
                  console.log(response);
                  var tmp = "#" + response;
                  //alert(tmp);
                  $(tmp).remove();
                  // now implement colorout to change color back
                  tmp = tmp.slice(0, -9) + "box";
                  colorout(tmp);
                  // update table on right
              }); // ends post
      });

	  $(".add").click(function() {
		  //this.append("<div></div>");
		  //alert($(this).attr("class"));
		  var boxid = $(this).attr("class");
		  var tempid = boxid;
		  boxid = boxid.slice(25, -7) + 'box';
         // alert( (($(this).attr("class")).split(" ")) );
		  if (!droppy) {
			  var str = '<div id="add_menu"><select id="' + tempid + 'drop" class="remove-drop" onchange="doc_add(this)" onClick="doc_add(this)">' +
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

<!--
  <script src="' . $addr . '/bootstrap-modal-bs3patch.css"></script>
  <script src="' . $addr . '/bootstrap-modal.css"></script>
  <script src="' . $addr . '/bootstrap-modalmanager.js"></script>
  <script src="' . $addr . '/bootstrap-modal.js"></script> -->
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
    <!-- fetch doctors -->';

for ($h = 0; $h < count($docs); $h++) {
	$doc_id = $docs[$h][0];
	echo '<div class="row-right-pan">
          <div class="col-xs-8"> ' . $docs[$h][2] . '</div>
	      <div class="col-xs-2" id="doc-' . $doc_id . '-total-days">
	      <!-- fetch how many days for that year -->';
	$total_year = 0;
	for ($a = 0; $a < count($docs1); $a++) {
		for ($b = 0; $b < count($docs1[$a]); $b++) {
			if ($doc_id == $docs1[$a][$b] || $doc_id == $docs2[$a][$b]) {
				$total_year ++;
			}
		}
	}
	echo $total_year;
	// now get total for current month
	echo '
		  </div> <!-- end total days -->
		  <div class="col-xs-2" id="total-month-doc-' . $doc_id . '"></div>
	      </div> <!-- end unique doctor row -->
																				';
}

echo '
  </div> <!-- end right-panel-contract -->
</body>
</html>';

?>