<?php
require_once "backend/config/sessionConfig.php";
?>
<!DOCTYPE html>
<!-- saved from url=(0048)https://colorlib.com/etc/lf/Login_v11/index.html -->
<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Login V11</title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="./Login V11_files/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="./Login V11_files/icon-font.min.css">
	<link rel="stylesheet" type="text/css" href="./Login V11_files/animate.css">
	<link rel="stylesheet" type="text/css" href="./Login V11_files/hamburgers.min.css">
	<link rel="stylesheet" type="text/css" href="./Login V11_files/select2.min.css">
	<link rel="stylesheet" type="text/css" href="./Login V11_files/util.css">
	<link rel="stylesheet" type="text/css" href="./Login V11_files/main.css">
	<script src="https://kit.fontawesome.com/c275ff90f1.js" crossorigin="anonymous"></script>

	<meta name="robots" content="noindex, follow">

	<style>
		.btn-google {
			width: 90%;
			margin: 0 auto;
			display: flex;
			align-items: center;
			justify-content: center;
		}
	</style>
</head>

<body>

	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100 p-l-50 p-r-50 p-t-77 p-b-30">
				<form id="loginForm" method="POST" action="" class="login100-form validate-form">
					<span class="login100-form-title p-b-55">
						Login
					</span>

					<input type="hidden" name="action" value="loginUser">
					<div class="wrap-input100 validate-input m-b-16" data-validate="Required">
						<input class="input100" type="text" name="username" placeholder="Username">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa-regular fa-user"
								style="font-size: 22px; margin-top: 4px; margin-left: 1px"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input m-b-16" data-validate="Required">
						<input class="input100" type="password" name="pass" placeholder="Password">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<span class="lnr lnr-lock"></span>
						</span>
					</div>

					<div class="contact100-form-checkbox m-l-4">
						<input class="input-checkbox100" id="ckb1" type="checkbox" name="remember-me">
						<label class="label-checkbox100" for="ckb1">
							Remember me
						</label>
					</div>

					<div class="container-login100-form-btn p-t-25">
						<button class="login100-form-btn">
							Login
						</button>
					</div>

					<div class="text-center w-full p-t-42 p-b-22">
						<span class="txt1">
							Students need active moodle accounts
						</span>
					</div>


					<!-- <div class="text-center w-full p-t-115">
						<span class="txt1">
							Not a member?
						</span>

						<a class="txt1 bo1 hov1" href="https://colorlib.com/etc/lf/Login_v11/index.html#">
							Sign up now
						</a>
					</div> -->
				</form>
			</div>
		</div>
	</div>


	<script type="text/javascript" async="" src="./Login V11_files/analytics.js.download"></script>
	<script src="./Login V11_files/jquery-3.2.1.min.js.download"></script>

	<script src="./Login V11_files/popper.js.download"></script>
	<script src="./Login V11_files/bootstrap.min.js.download"></script>

	<script src="./Login V11_files/select2.min.js.download"></script>

	<script async="" src="./Login V11_files/js"></script>
	<script>
		$(document).ready(function () {

			//Google Analytics
			window.dataLayer = window.dataLayer || [];
			function gtag() { dataLayer.push(arguments); }
			gtag('js', new Date());
			gtag('config', 'UA-23581568-13');
			var input = $('.validate-input .input100');

			//When form is submited
			$('#loginForm').on('submit', function (e) {
				e.preventDefault();

				var check = true;
				for (var i = 0; i < input.length; i++) { //check for each input
					if (validate(input[i]) == false) { //if validation fails
						showValidate(input[i]);
						return;
					}
				}

				//Post the form if validation is successful
				$.ajax({
					type: "POST",
					url: "backend/users/authenticateLocal.php",
					data: $(this).serialize(),
					success: function (response) {
						if (response[0] === 0) {
							const field = response[1] === "user" ? 'username' : 'pass';
							const el = $('input[name="' + field + '"]');
							el.parent().attr('data-validate', response[2]);
							showValidate(el);
						} else {
							// authentication succeeded
							console.log("Success");
							window.location.href = 'index.php';
						}
					},
					error: function (xhr, status, error) {
						console.error("AJAX error:", status, error);
					}

				});
			});


			//Hide validation containers on focus
			$('.validate-form .input100').each(function () {
				$(this).focus(function () {
					hideValidate(this);
				});
			});

			//Validate function
			function validate(input) {
				if ($(input).attr('type') == 'email' || $(input).attr('name') == 'email') {
					if ($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
						return false;
					}
				}
				else {
					if ($(input).val().trim() == '') {
						return false;
					}
				}
			}

			//Show validate containers
			function showValidate(input) {
				var thisAlert = $(input).parent();

				$(thisAlert).addClass('alert-validate');
			}

			//Hide validate containers
			function hideValidate(input) {
				var thisAlert = $(input).parent();

				$(thisAlert).removeClass('alert-validate');
			}

		});
	</script>
</body>

</html>