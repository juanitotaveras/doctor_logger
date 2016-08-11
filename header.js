<!--
/* provides JS functions for home page and header
 * author(s): Juanito Taveras 2/28/16
 * last modified 2/29/16 */
                                            
function drop(id) {
	$(id).dropdown("toggle");
}
function drop2(id) {  // for settings icon
//	$(id).dropdown("toggle");

}

	if ($(window).width() < 769) {
	//	alert("test");
		$("#navsearch").attr("class", "");
//		$("#navsearch").append("Search").css("font-size", "100%").css("left", "3%");
	//	$("#navsearch").css("font-size", "");
//		$("#home_button").text("");
//		$("#home_button").append("<span class=\"glyphicon glyphicon-home\" id=\"home_icon\"></span>");
//		$("#home_button").attr("class", "glyphicon glyphicon-home");	
	}
	else {
//		$("#navsearch").attr("class", "glyphicon glyphicon-search").css("font-size", "160%");
		$("#navsearch").attr("class", "glyphicon glyphicon-search");
	}
$(document).ready(function() {

	// Activates popups and tooltips
	$('[data-toggle="popover"]').popover();
	$('[data-toggle="tooltip"]').tooltip();
	$('.dropdown-toggle').dropdown();
	if ($(window).width() < 769) {
	//	alert("test");
		$("#navsearch").attr("class", "");
//		$("#navsearch").append("Search").css("font-size", "100%").css("left", "3%");
	//	$("#navsearch").css("font-size", "");
		$("#home_button").text("");
		$("#home_button").append('<span class="glyphicon glyphicon-home" id="home_icon"></span>');	
	}
	else {
//		$("#navsearch").attr("class", "glyphicon glyphicon-search").css("font-size", "160%");
		$("#navsearch").attr("class", "glyphicon glyphicon-search");
	}

	$("#navsearch").click(function() {
		var search = $("#search");
		if (search.css("display") == "none") {
			search.show();
			search.animate({width: "900%"}, "fast");
		}
		else {
			search.animate({width: "100%"}, "fast");
			search.hide();
		}
	});
		
	$("#settings_icon").click(function() {
		$(this).dropdown("toggle");
	//	console.log("test");
	});
	// SIGN UP MODAL FORM VERIFICATION
	function unameAvailable(name) {
		// checks if username is available, returns true if it is available
		$.post("./signup_checkusername.php", {
			username : name
			},
			function(response) {
				if (response.trim() == "taken") {
					return false;
				} 
				else {
					return true;
				} 	
			}
		);
	
	}
	//var message = "";
	// checks username, returns message if unsuccessful
	function unameChecker(uname) {
		var message = "";
	//	var message = "";
		if (!(uname.length >= 3 && uname.length <= 20)) {
			message = "Username must be 3 - 20 characters in length.";
			return message;
		} 
		var patt = /[^\w\d\-\_]/i;
		if (patt.test(uname)) {  // if something that is not a letter, digit, underscore, hyphen found
			message = "Username can only contain letters, digits, underscores, and hyphens.";
			return message;
		} //else {alert("cake");}
	
		return message; 
	}

	function passwordChecker(pswd) {
		var message = "";
		// checks length of password, returns message if incorrect
		if (pswd.length < 8) {
			message = "Password must be at least 8 characters in length.";
		}
		var patt = /[\w]/i;
		var patt2 = /[\d]/i;
		if (!patt.test(pswd) || !patt2.test(pswd)) {
			message = "Password must have at least one letter and one digit.";
		}

		// add checker to make sure password has uppercase letter and digit
		// message if password has weird stuff
		return message;
	}
	


	// adds glyphicon depending on whether username is correct or not
//	$("#usname").keyup(function() {  // when user focuses out of username field	
	function unameValidate() {
		var uname_correct = false;
		var uname_str = $("#usname").val();	
		var ucheck = unameChecker(uname_str);
		// if username is correct (not yet checked if available)
		if (ucheck == "") {
			// check if is available
			$.post("./signup_checkusername.php", {
			username : uname_str
			},
			function(response) {
				// if username is not available
				if (response.trim() == "taken") {
					// if first icon already added, change it to an 'x'
					if ($("#check_icon_1").length > 0) { 
							$("#check_icon_1").popover("destroy");
							$("#check_icon_1").attr("class", "glyphicon glyphicon-remove");
							$("#check_icon_1").remove();
							$("#uname_field_icon").append('<span class="glyphicon glyphicon-remove" id="check_icon_1" data-toggle="popover" data-content="Username has already been taken."title="" data-placement="auto right"></span>');
					}
					// if not, add a new icon
					else {
						$("#uname_field_icon").append('<span class="glyphicon glyphicon-remove" id="check_icon_1" data-toggle="popover" title="" data-content="Username has already been taken." data-placement="auto right"></span>');
					} 
				} 
				// if username is available
				else {
					// change icon to 'checkmark'
					if ($("#check_icon_1").length > 0) { // if icon present
						$("#check_icon_1").popover("destroy");
						$("#check_icon_1").attr("class", "glyphicon glyphicon-ok");
					}
					// if icon not there, add a new one
					else {	
						$("#check_icon_1").popover("destroy");
						$("#uname_field_icon").append('<span class="glyphicon glyphicon-ok" id="check_icon_1" data-toggle="popover" title="" data-content="" data-placement="right"></span>');
					}		
				} 	
			}
		);		// if first icon already added
			
		}
		// if username is not correct
		else {
			// if first icon already added
			if ($("#check_icon_1").length > 0) {
				$("#check_icon_1").popover("destroy");
				$("#check_icon_1").remove();
				$("#uname_field_icon").append('<span class="glyphicon glyphicon-remove" id="check_icon_1" data-toggle="popover" title="" data-content="' + ucheck + '" data-placement="auto right"></span>');
				console.log("should show diff message from ucheck");
			}
			else {
				$("#uname_field_icon").append('<span class="glyphicon glyphicon-remove" id="check_icon_1" data-toggle="popover" title="" data-content="' + ucheck + '" data-placement="auto right"></span>');
			} 

		}
		// if field is left empty
		if ($("#usname").val() == "") {
			$("#check_icon_1").popover("destroy");
			$("#check_icon_1").remove();
			// remove popover content if field left blank
		}
	} //ends unameValidate

	$("#usname").keyup(function() {
		unameValidate();
		if ($("#check_icon_1").attr("class") == "glyphicon glyphicon-ok") {
			$("#check_icon_1").popover("destroy");
		}
	});


	$("#usname").focusout(function() {
		if ($("#check_icon_1").attr("class") == "glyphicon glyphicon-ok" || $("#usname").val() == "") {
			$("#check_icon_1").popover("destroy");
		}
		else {
			$("#check_icon_1").delay("slow").popover("show");
		}
	} );

	// adds glyphicon depending on whether passowrd is correct or not
//	$("#pswd").keyup(function() {
	function validatePassword() {
		var pass = $("#pswd").val();
		var pcheck = passwordChecker(pass);
		var icon2 = $("#check_icon_2");
		if (pcheck == "") {   // if password is correct
			if (icon2.length > 0) {   // if icon has already been added
				icon2.popover("destroy");
				$("#check_icon_2").attr("class", "glyphicon glyphicon-ok");
			}
			else {
				$("#password_field_icon").append('<span class="glyphicon glyphicon-ok" id="check_icon_2"></span>');		
			}
		}
		else {   // if password is not correct
			if (icon2.length > 0) { // if icon already been added
				icon2.popover("destroy");
				icon2.remove();
				$("#password_field_icon").append('<span class="glyphicon glyphicon-remove" id="check_icon_2" data-toggle="popover" title="" data-content="' + pcheck + '" data-placement="auto right"></span>');
				//$("#check_icon_2").attr("class", "glyphicon glyphicon-remove");
			}
			else {  // second icon not added yet
				$("#password_field_icon").append('<span class="glyphicon glyphicon-remove" id="check_icon_2" data-toggle="popover" title="" data-content="' + pcheck + '" data-placement="auto right"></span>');
			}

		}
		if ($("#pswd").val() == "") {
			icon2.popover("destroy");
			icon2.remove();		
		}
	}
	$("#pswd").keyup(function() {
		validatePassword();
		if ($("#check_icon_2").attr("class") == "glyphicon glyphicon-ok") {
			$("#check_icon_2").popover("destroy");
		}
	});  // end pswd.keyup
	$("#pswd").focusout(function() {
	//	validatePassword();
		if ($("#check_icon_2").attr("class") == "glyphicon glyphicon-ok" || $("#pswd").val() == "") {
			$("#check_icon_2").popover("destroy");
		}
		else {
			$("#check_icon_2").delay("slow").popover("show");
		}
		if ($("#pswd").val() == "") {
			$("#check_icon_2").remove();
		}
	});

	// makes sure repeat password is entered correctly
	function validateRepeat() {
		var rep = $("#repeat").val();
		var pass = $("#pswd").val();
		var iconfield3 = $("#repeat_field_icon");
		var icon3 = $("#check_icon_3");
		if (rep != pass) {  // if username and password don't match
			if (icon3.length > 0) {
				// if third icon already added
				icon3.popover("destroy");
				icon3.remove();
				iconfield3.append('<span class="glyphicon glyphicon-remove" id="check_icon_3" data-toggle="popover" title="" data-content="Password and repeat password must match." data-placement="auto right"></span>');
			}
			else {
				iconfield3.append('<span class="glyphicon glyphicon-remove" id="check_icon_3" data-toggle="popover" title="" data-content="Password and repeat password must match." data-placement="auto right"></span>');
			}
		}
		else { // if repeat and password match
			if (icon3.length > 0) {
				icon3.popover("destroy");
				icon3.attr("class", "glyphicon glyphicon-ok");
			}
			else {
				iconfield3.append('<span class="glyphicon glyphicon-ok" id="check_icon_3"></span>');
			}	
		}
		if (rep == "" && icon3.length > 0) {
			icon3.popover("destroy");
			icon3.remove();
		}
	} // end validateRepeat
	$("#repeat").keyup(function() {
		validateRepeat();
		if ($("#check_icon_3").attr("class") == "glyphicon glyphicon-ok") {
			$("#check_icon_3").popover("destroy");
		}
	});  // end pswd.keyup

	$("#repeat").focusout(function() {
		validateRepeat();
		if ($("#repeat").val() == "") {
			$("#check_icon_3").remove();
		}
		if ($("#check_icon_3").attr("class") == "glyphicon glyphicon-ok" || $("#repeat").val() == "") {
			$("#check_icon_3").popover("destroy");
		}
		else {
			$("#check_icon_3").delay("slow").popover("show");
		}
		
	});
	
	var efield = $("#email");
	var email = $("#email").val();
	var iconfield4 = $("#email_field_icon");
	var icon4 = $("#check_icon_4");
	function emailValidate() {
		var pattern = /[\w@\.]/;
		var patt = /\w/i;
		var patt2 = /@/;
		var patt3 = /\./;
		var email_str = $("#email").val();
		if (!(email_str.length > 8 && patt.test(email_str) && patt2.test(email_str) && patt3.test(email_str))) { // if email doesn't pass all tests
			console.log("email didn't pass test");
			if ($("#check_icon_4").length > 0) {
				$("#check_icon_4").popover("destroy");
				$("#check_icon_4").remove();
				iconfield4.append('<span class="glyphicon glyphicon-remove" id="check_icon_4" data-toggle="popover" title="" data-content="Invalid email." data-placement="auto right"></span>');
			}
			else {
				iconfield4.append('<span class="glyphicon glyphicon-remove" id="check_icon_4" data-toggle="popover" title="" data-content="Invalid email." data-placement="auto right"></span>');
			}
			return "Please enter a valid email";
		} else {  // if email does pass tests
			console.log("email passed test");
			if ($("#check_icon_4").length > 0) {  // if icon already present
				$("#check_icon_4").popover("destroy");
				$("#check_icon_4").attr("class", "glyphicon glyphicon-ok");
			}
			else {
				iconfield4.append('<span class="glyphicon glyphicon-ok" id="check_icon_4"></span>');
			}	
			
			return "";
		}
	}

	efield.keyup(function() {
		emailValidate();
		if ($("#check_icon_4").attr("class") == "glyphicon glyphicon-ok") {
			icon4.popover("destroy");
			// add option to opt in for emails
			if ($("#email_check").length == 0) {
				$("#email_div").append('<input type="checkbox" id="email_check" name="email_check" value="opt_in"> Send me infrequent emails about Polyphasic.xyz news<br>');
				//$("#email_div").append('<fieldset id="checkArray"><input type="checkbox" name="email_check" value="opt"/> Send me infrequent emails about Polyphasic.xyz news</fieldset>');
			//	console.log("should have checkbox appear");
			}
		}
		if ($("#email").val() == "") {
			$("#check_icon_4").remove();
		}
	});

	efield.focusout(function() {
		if (icon4.attr("class") == "glyphicon glyphicon-ok" || email == "") {
			icon4.popover("destroy");
		}
		else {
			icon4.delay("slow").popover("show");
		}
		if ($("#email").val() == "") {
			$("#check_icon_4").remove();
		}
	});

// if everything has been validated
function sign_up() {		
	if ($("#check_icon_1").attr("class") == "glyphicon glyphicon-ok" && $("#check_icon_2").attr("class") == "glyphicon glyphicon-ok" && $("#check_icon_3").attr("class") == "glyphicon glyphicon-ok" && $("#check_icon_4").attr("class") != "glyphicon glyphicon-remove") {
		var timezone = jstz.determine();
		var tz = timezone.name();
		var optin = false;
		if ($("#email_check").is(":checked")) {
			optin = true;
		//	alert("checked");
		}
		
		$.post("./signup_forum.php", {
			uname : $("#usname").val(),
			passwd : $("#pswd").val(),
			repeat : $("#repeat").val(),
			email : $("#email").val(),
			opt : optin,
			timezone : tz
		},
		function(response) {
			response.trim();
			if (response == "success" ) {
				alert("Signed up successfully.");
				window.location.reload(true);
			}
			else {
				alert("Sign up not successful. Try again.");
			//	alert(response);
	//			console.log(response);
			}
		});
			
	} else {
		alert ("Check your information.");
	}
}  //ends function sign_up

function log_in() {
	$.post("./login.php", {
		uname : $("#usrname").val(),
		passwd : $("#psw").val()
	},
	function(response) {
		response.trim();
		if (response == "success") {
			alert("Logged in successfully.");
			window.location.reload(true);
		}
		else {
			alert("Please check your information.");
		}
	}); // ends post

} //ends function log_in

$("#login_button").click(function() {
	log_in()
});
$("#usrname, #psw").keypress(function(e) {
	if(e.which == 13) {
		log_in();
	}
});

// activate sign up when you click the button
$("#signup_button").click(function() {
	sign_up();
});

//have enter activate signup
$("#usname, #pswd, #repeat, #email").keypress(function(e) {
	if(e.which == 13) { // if 'enter' key pressed
		sign_up();
	}
});

$("#logoutbtn").click(function() {
	$.post("./logger.php", {
		logout : "logout",
		action : "out"
	},
	function(response) {
		response.trim();
		if (response == "success") {
			alert("You have been logged out.");
			window.location.reload(true);
		}
	});
}); // ends logoutbtn.click	

// activates sign up modal
$("#signupbtn").click(function(){
//	$("#signupModal").modal();
	
	window.location.href = "/forum/index.php?action=register";	
});

// activates log in modal
$("#loginbtn").click(function(){
	$("#loginModal").modal();
});

}); // ends document.ready
-->
