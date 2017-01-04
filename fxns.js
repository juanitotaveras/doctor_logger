/**
 * Created by juanito on 1/3/17.
 */
function autopop() {
    $("#autopop-modal").modal('show');
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