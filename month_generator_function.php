<?php
/*
 * Created by PhpStorm.
 * User: juanito
 * Date: 8/8/16
 * Time: 3:21 PM
 */
ini_set('display_errors', 'On');   // error checking
error_reporting(E_ALL);    // error checking

function month_generator($cur_mon, $days, $weekdays, $year, $doc1_list, $doc2_list, $docs) {
    $week = ['Sun', 'Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat'];
    $mon_list = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

$str = '
  <div class="row month_section block-'. strval($cur_mon) . '">
    <div class="col-xs-5"></div>
    <div class="col-xs-2" id="month-head-' . strval($cur_mon) . '">
      <h1 id="mon-name-' . strval($cur_mon) . '">' . $mon_list[$cur_mon] .
    '</h1>
    </div>
    <div class="col-xs-1">
      <button type="button" onClick="autopop(' . strval($cur_mon) . ')" class="btn btn-warning autopopbtn admin-only opts"> Auto-populate </button>
    </div>
    <div class="col-xs-1">
      <button type="button" onClick="clear(' . strval($cur_mon) . ')" class="btn btn-danger clearbtn admin-only opts"> Clear </button>
    </div>
    <div class="col-xs-1">
      <button type="button" onClick="print_month(' . strval($cur_mon) . ')" class="btn btn-primary printbtn opts"> Print </button>
    </div>
        <div class="col-xs-2"></div>
  </div> <!-- end row with month name -->
  <div class="row block-' . strval($cur_mon) . '">
    <div class="col-xs-1 left_edge">
    </div>';

    for ($x = 0; $x < count($week); $x++) {
        $str .= '<div class="col-xs-1 weekbox"><h3>' . $week[$x] . '</h3></div>';
    }
    $str .= '<div class="col-xs-3"></div></div> <!-- end row of week days header -->';
    $head = 0;
    $end = 1;
    for ($i = 0; $i < 6; $i++) {
        $str .= '
  		<div class="row block-' . strval($cur_mon) . '">
  		<div class="col-xs-1 left_edge"></div>';
	$attr = 'onmouseover="colorin(this)" onmouseout="colorout(this)"';
        for ($j = 0; $j < 7; $j++) {
            $start = $weekdays[$cur_mon][0];
            if ($i == 0) {
                if ($j < $start) {
                    $tmp = $start - $j;
                    // get days from previous month
                    if ($cur_mon > 0) {
                        $daynum = $days[$cur_mon - 1][count($days[$cur_mon - 1]) - $tmp];
                    }
                    else {
                        $lastdec = ['25', '26', '27', '28', '29', '30', '31'];
                        $daynum = $lastdec[count($lastdec) - $tmp];
                    }
		            $str .= '<div class="col-xs-12 col-md-1 before">' . strval($daynum) . '</div>';
                }
                else {
                      $daynum = $days[$cur_mon][$head];
                      $head ++;
                      $class = (strval($cur_mon) . '-' . strval($daynum) . '-'. strval($year) . '-'. 'add add');
                      $docid = (strval($cur_mon) . '-' . strval($daynum) . '-'. strval($year) . '-'. 'docname');
                      $plus = '<span style="margin:3px" class="glyphicon glyphicon-plus ' . $class . '" id="add-icon"></span>';
                      $remove_doc = '<span class="glyphicon glyphicon-remove deletedoc"></span>';
                      if (isset($doc1_list[$cur_mon][$daynum - 1])) {
                          $dox = '<span class="docname n1" id="' . $docid . '-1">' . $docs[($doc1_list[$cur_mon][$daynum - 1])][1] . " " . $docs[($doc1_list[$cur_mon][$daynum - 1])][2] . $remove_doc . '</span>';
                      }
                      else {
                          $dox = '';
                      }
                      if (isset($doc2_list[$cur_mon][$daynum - 1])) {
                          $dox .=  '<span class="docname n2" id="' . $docid . '-2">' . $docs[($doc2_list[$cur_mon][$daynum - 1])][1] . " " . $docs[($doc2_list[$cur_mon][$daynum - 1])][2] . $remove_doc . '</span>';
                      }
                      $str .= '<div class="col-xs-12 col-md-1 norm day" ' . $attr . 'id="' . (strval($cur_mon) . "-" . strval($daynum) . "-" . strval($year) . "-box") . '">' . strval($daynum) . $plus . $dox . '</div>';
                }
            }
            else if (($i > 3) && (count($days[$cur_mon]) <= $head)) {
		        $str .= '<div class="col-xs-12 col-md-1 after">' . strval($end) . '</div>';
                $end++;
            }
            else {
                $daynum = $days[$cur_mon][$head];
                $head ++;
                $class = (strval($cur_mon) . '-' . strval($daynum) . '-'. strval($year) . '-'. 'add add');
                $docid = (strval($cur_mon) . '-' . strval($daynum) . '-'. strval($year) . '-'. 'docname');
                $plus = '<span style="margin:3px" class="glyphicon glyphicon-plus ' . $class . '" id="add-icon"></span>';
                $remove_doc = '<span class="glyphicon glyphicon-remove deletedoc"></span>';
                if (isset($doc1_list[$cur_mon][$daynum - 1])) {
                    $dox = '<span class="docname n1" id="' . $docid . '-1">' . $docs[($doc1_list[$cur_mon][$daynum - 1])][1] . " " . $docs[($doc1_list[$cur_mon][$daynum - 1])][2] . $remove_doc . '</span>';
                }
                else {
                    $dox = '';
                }
                if (isset($doc2_list[$cur_mon][$daynum - 1])) {
                    $dox .=  '<span class="docname n2" id="' . $docid . '-2">' . $docs[($doc2_list[$cur_mon][$daynum - 1])][1] . " " . $docs[($doc2_list[$cur_mon][$daynum - 1])][2] . $remove_doc . '</span>';
                }
                $str .= '<div class="col-xs-12 col-md-1 norm day" ' . $attr . 'id="' . (strval($cur_mon) . "-" . strval($daynum) . "-" . strval($year) . "-box") . '">' . strval($daynum) . $plus . $dox . '</div>';
            }
        }
        $str .= '<div class="col-xs-3"></div></div>';
    }
    return $str;
}
// end month generator function

?>
