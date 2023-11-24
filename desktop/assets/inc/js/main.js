// ******************  TOGGLE OFF OR ON SIDEBAR ******************************************

const body = document.querySelector("body"),
	sidebar = body.querySelector("nav"),
	toggle = body.querySelector(".toggle"),
	modeSwitch = body.querySelector(".toggle-switch"),
	modeText = body.querySelector(".mode-text");

toggle.addEventListener("click", () => {
	sidebar.classList.toggle("close");
});

// ******************  DARK OR LIGHT MODE ***********************************************

modeSwitch.addEventListener("click", () => {
	body.classList.toggle("dark");

	if (body.classList.contains("dark")) {
		modeText.innerText = "Light mode";
	} else {
		modeText.innerText = "Dark mode";
	}
});

// ******************  TOGGLE OFF OR ON SEARCH CALENDAR MAIN PAGE ***************************

toggleButtonVisibility();

function modalVisible() {
	let modals = document.querySelectorAll(".modal");
	return $(".modal").hasClass("show");
}

function toggleButtonVisibility() {
	let bouton = document.getElementById("calendarBtn");
	if (modalVisible()) {
		bouton.style.display = "none"; // Cacher le bouton
	} else {
		bouton.style.display = "block"; // Afficher le bouton
	}
}

$("#calendarModal").on("shown.bs.modal", toggleButtonVisibility);
$("#calendarModal").on("hidden.bs.modal", toggleButtonVisibility);
$("#SPWModal").on("shown.bs.modal", toggleButtonVisibility);
$("#SPWModal").on("hidden.bs.modal", toggleButtonVisibility);
$("#emailContact").on("shown.bs.modal", toggleButtonVisibility);
$("#emailContact").on("hidden.bs.modal", toggleButtonVisibility);

// ************************ EMAIL FORM VALIDATION *******************************************

(function () {
	"use strict";

	// Fetch all the forms we want to apply custom Bootstrap validation styles to
	let forms = document.querySelectorAll(".needs-validation");

	// Loop over them and prevent submission
	Array.prototype.slice.call(forms).forEach(function (form) {
		form.addEventListener(
			"submit",
			function (event) {
				if (!form.checkValidity()) {
					event.preventDefault();
					event.stopPropagation();
				}
				form.classList.add("was-validated");
			},
			false
		);
	});
})();
