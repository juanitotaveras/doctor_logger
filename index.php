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
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$cur_day = $row["DAY"];
		if ($cur_day == "1") {
			if (isset($days_list) && isset($weekdays_list)) {
				array_push($days, $days_list);
				array_push($weekdays, $weekdays_list);
				array_push($doc1_list, $days_list);
				array_push($doc2_list, $weekdays_list);

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
	array_push($doc1_list, $days_list);
	array_push($doc2_list, $weekdays_list);
}
include("./month_generator_function.php");
$calendar = "";
$p = 0;
for ($i = 0; $i < 3; $i++) {
	$calendar .= '<div class="row">';
	for ($j = 0; $j < 4; $j++) {
		$calendar .= month_generator($p, $days, $weekdays, $year_in);
		$p++;
	}
	$calendar .= '</div>';
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
		  $(".remove-drop, .remove_lyst").remove();
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
		  console.log("#" + strr.slice(0, -4) + "box");
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
		  console.log(activeSection);
		  // go through all doc divs and update month count
	  });

	  $(".add").click(function() {
		  //this.append("<div></div>");
		  //alert($(this).attr("class"));
		  var boxid = $(this).attr("class");
		  var tempid = boxid;
		  boxid = boxid.slice(25, -7) + 'box';

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
	for ($a = 0; $a < count($doc1_list); $a++) {
		for ($b = 0; $b < count($doc1_list[$a]); $b++) {
			if ($doc_id == $doc1_list[$a][$b] || $doc_id == $doc2_list[$a][$b]) {
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