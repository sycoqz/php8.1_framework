(() => {
	parasitic_unit = document.querySelector("div");	
	if (parasitic_unit.style.cssText === "margin: 0px 20% !important; text-align: center !important; font-size: 16px !important; font-family: arial !important; background-color: black !important; color: white !important;") {
		console.log(true);
		parasitic_unit.style.cssText = "display: none !important"

		}
})()