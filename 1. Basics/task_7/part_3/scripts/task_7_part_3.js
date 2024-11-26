/**
 * Changes form`s method
 * 
 * @param {*} elementID 
 */
function clickOnCheckBoxEsRequestMethodIsNotGET(elementID) {
	let form = document.getElementById("myForm");
	let checkBox = document.getElementById(elementID);
	
	if(checkBox.checked == true) {
		form.method = 'POST';
	} else {
		form.method = 'GET';
	} 	
}

/**
 * Changes form's attribute 'action' (request uri)
 * 
 * @param {*} elementID 
 */

function clickOnCheckBoxEsRequestWrongUriStart(elementID) {
	let form = document.getElementById("myForm");
	let checkBox = document.getElementById(elementID);
	
	if (checkBox.checked == true) {
    	myForm.action = "/notsum";
	} else {
    	myForm.action = '/sum';
  	}
}

/**
 * Changes form's field name (nums <-> letters)
 * 
 * @param {*} elementID 
 */
function clickOnCheckBoxEsRequestWithoutNums(elementID) {
	let input = document.getElementById("nums");
	let checkBox = document.getElementById(elementID);
	let label = document.getElementById("numsLabel");
	
	if (checkBox.checked == true) {
		input.name = 'letters';
		label.innerHTML = "letters:";
	} else {
    	input.name = 'nums';
		label.innerHTML = "nums:";
  	}
}

