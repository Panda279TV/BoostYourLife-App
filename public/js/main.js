$(document).ready(function() {
	// Index.php Slider
	var swiper = new Swiper(".swiper-index-container", {
		cssMode: true,
		pagination: {
		el: ".swiper-pagination",
		clickable: true
		},
		mousewheel: true,
		keyboard: true,
		autoplay: false,
		loop: false,
		allowTouchMove: true,
		simulateTouch: true
	});
	// Home.php Slider
	var swiper = new Swiper(".swiper-home-newNLP", {
		slidesPerView: 3,
		spaceBetween: 30,
		navigation: {
		nextEl: ".swiper-button-next",
		prevEl: ".swiper-button-prev"
		},
		cssMode: true,
		mousewheel: true,
		keyboard: true,
		autoplay: false,
		loop: true,
		allowTouchMove: true,
		simulateTouch: true,
		breakpoints: {
		// when window width is >= 320px
		320: {
			slidesPerView: 1,
			spaceBetween: 40
		},
		// when window width is >= 480px
		480: {
			slidesPerView: 1,
			spaceBetween: 40
		},
		// when window width is >= 640px
		640: {
			slidesPerView: 2,
			spaceBetween: 40
		},
		// when window width is >= 800px
		800: {
			slidesPerView: 3,
			spaceBetween: 40
		}
		}
	});
	// Framework Tablesaw initialisieren
	Tablesaw.init();
	// Der btnInputEmpty wird nach dem ausfüllen zu btnInputFull. Beim Login
	const $btnSecondary = $(".btnSecondary");
	const $emailInputLogin = $("#emailLogin");
	const $passwordInputLogin = $("#passwordLogin");
	$passwordInputLogin.on("change", function() {
		if ($emailInputLogin.val() !== "" && $passwordInputLogin.val() !== "") {
		$btnSecondary.removeClass("btnSecondary").addClass("btnPrimary");
		}
	});
	// Der btnInputEmpty wird nach dem ausfüllen zu btnInputFull. Beim Registrieren
	const $firstnameInputRegister = $("#firstnameRegister");
	const $lastnameInputRegister = $("#lastnameRegister");
	const $emailInputRegister = $("#emailRegister");
	const $passwordInputRegister = $("#passwordRegister");
	const $passwordRepeatInputRegister = $("#passwordRepeatRegister");
	$passwordRepeatInputRegister.on("change", function() {
		if (
		$firstnameInputRegister.val() !== "" &&
		$lastnameInputRegister.val() !== "" &&
		$emailInputRegister.val() !== "" &&
		$passwordInputRegister.val() !== "" &&
		$passwordRepeatInputRegister.val() !== ""
		) {
		$btnSecondary.removeClass("btnSecondary").addClass("btnPrimary");
		}
	});
	// Der btnInputEmpty wird nach dem ausfüllen zu btnInputFull. Beim Normalen Übungen Suchen
	const $searchNLPInput = $("#searchNLPInput");
	$searchNLPInput.on("change", function() {
		if ($searchNLPInput.val() !== "") {
		$btnSecondary.removeClass("btnSecondary").addClass("btnPrimary");
		}
	});
	// Der btnInputEmpty wird nach dem ausfüllen zu btnInputFull. Beim Admin Nlp Suchen und Admin User Suchen
	const $searchAdminNLPInput = $("#search");
	$searchAdminNLPInput.on("change", function() {
		if ($searchAdminNLPInput.val() !== "") {
		$btnSecondary.removeClass("btnSecondary").addClass("btnPrimary");
		}
	});
	// Der btnInputEmpty wird nach dem ausfüllen zu btnInputFull. Beim Passwort Ändern
	const $passwordChange = $("#passwordChange");
	const $passwordRepeatChange = $("#passwordRepeatChange");
	$passwordRepeatChange.on("change", function() {
		if ($passwordChange.val() !== "" && $passwordRepeatChange.val() !== "") {
		$btnSecondary.removeClass("btnSecondary").addClass("btnPrimary");
		}
	});
	// Der btnInputEmpty wird nach dem ausfüllen zu btnInputFull. Beim Passwort Vergessen
	const $firstnameInputForgotPassword = $("#firstnameForgotPassword");
	const $lastnameInputForgotPassword = $("#lastnameForgotPassword");
	const $emailInputForgotPassword = $("#emailForgotPassword");
	$emailInputForgotPassword.on("change", function() {
		if (
		$firstnameInputForgotPassword.val() !== "" &&
		$lastnameInputForgotPassword.val() !== "" &&
		$emailInputForgotPassword.val() !== ""
		) {
		$btnSecondary.removeClass("btnSecondary").addClass("btnPrimary");
		}
	});
	// Der btnInputEmpty wird nach dem ausfüllen zu btnInputFull. Beim User anlegen
	const $firstnameInputRegisterAdmin = $("#firstnameRegisterAdmin");
	const $lastnameInputRegisterAdmin = $("#lastnameRegisterAdmin");
	const $emailInputRegisterAdmin = $("#emailRegisterAdmin");
	const $passwordInputRegisterAdmin = $("#passwordRegisterAdmin");
	const $passwordRepeatInputRegisterAdmin = $("#passwordRepeatRegisterAdmin");
	$passwordRepeatInputRegisterAdmin.on("change", function() {
		if (
		$firstnameInputRegisterAdmin.val() !== "" &&
		$lastnameInputRegisterAdmin.val() !== "" &&
		$emailInputRegisterAdmin.val() !== "" &&
		$passwordInputRegisterAdmin.val() !== "" &&
		$passwordRepeatInputRegisterAdmin.val() !== ""
		) {
		$btnSecondary.removeClass("btnSecondary").addClass("btnPrimary");
		}
	});
	// Der btnInputEmpty wird nach dem ausfüllen zu btnInputFull. Beim NLP Übung anlegen
	const $titleInput = $("#title");
	const $descriptionInput = $("#description");
	const $textInput = $("#text");
	$textInput.on("change", function() {
		if (
		$titleInput.val() !== "" &&
		$descriptionInput.val() !== "" &&
		$textInput.val() !== ""
		) {
		$btnSecondary.removeClass("btnSecondary").addClass("btnPrimary");
		}
	});
	// Hamburger und Dropdown
	const $hamburger = $(".hamburger");
	const $lightbox = $(".lightbox");
	const $hamburgerDropdown = $(".hamburgerDropdown");
	$hamburger.on("click", function() {
		$hamburger.toggleClass("is-active");
		$lightbox.fadeToggle();
		$hamburgerDropdown.fadeToggle();
	});
	$lightbox.on("click", function() {
		$hamburger.toggleClass("is-active");
		$lightbox.fadeToggle();
		$hamburgerDropdown.fadeToggle();
	});
	// Gibt in der Console aus, dass das Dokument geladen wurde
	console.log("Document Ready");
});
