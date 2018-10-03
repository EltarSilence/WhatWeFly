function validity() {
	//debugger;
	//if (document.form1.states.value == "si") return true;

	//else {
		var robin = document.form1.robin.value;
		var abroad = document.form1.abroad.value;
		
		if (robin == "" || abroad == "") {
			alert("Errore: devi inserire tutte le preferenze");
			return false;
		}
		else return true;
	//}


}

function showStates() {
	$("#robin").prop('checked', false);
	$("#abroad").prop('checked', false);

	if (document.form1.state.hasAttribute("hidden")){
			document.form1.state.removeAttribute("hidden");
		//document.form1.robin.attr("checked", false);
		//document.form1.abroad.attr("checked", false);
	}
	else {
		document.form1.state.setAttribute("hidden", 1);
	}
}