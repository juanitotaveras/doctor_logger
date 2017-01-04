/**
 * Created by juanito on 1/3/17.
 */

/* Makes autopopulate modal appear when button is clicked
 (admin must be logged in */
function autopop() {
    $("#autopop-modal").modal('show');
}

/* opens new window to print out a particular month's schedule */
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
                    console.log(response);
                    window.location.reload(true);
                }); // ends post */
        }
    }
}

/* clears all days out of selected year */
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

/* changes currently selected year when dropdown is used */
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
                // now check if doc is CCC; if so, increment
                if (doxnames[docid][3] == 1) {
                    var counter = parseInt($("#ccc-year").text());
                    counter ++;
                    $("#ccc-year").text(counter);
                    var counter = parseInt($("#ccc-month").text());
                    counter ++;
                    $("#ccc-month").text(counter);

                }
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
                            // check if ccc
                            if (doxnames[response][3] == 1) {  // if doc is CCC, alter them
                                var counter = parseInt($("#ccc-year").text());
                                counter --;
                                $("#ccc-year").text(counter);
                                var counter = parseInt($("#ccc-month").text());
                                counter --;
                                $("#ccc-month").text(counter);
                            }
                            var tmp = "#" + id;
                            $(tmp).remove();
                            // now implement colorout to change color back
                            tmp = tmp.slice(0, -9) + "box";
                            colorout(tmp);
                            // update table on right
                        }); // ends post
                })
            }
            else if (response[0] == "doc_2_added") {
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
            else if (response[0] == "full") {
                alert("This day is full. Delete a doctor.");
            }
            else if (response[0] == "already_added") {
                alert("Doctor already assigned to this day.");
            }
            else {
                alert(response);
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

/* logs user out */
function log_out() {
    $.post("./log_out.php", {

        },
        function(response) {
            // maybe have some confirmation
            window.location.reload(true);
        }); // ends post
} // ends log_out function
