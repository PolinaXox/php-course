/**
 * Changes form`s method
 * 
 * @param {*} elementID 
 */
function clickOnCheckBoxEsRequestMethodIsNotGET(elementID) {
	let form = document.getElementById('myForm');
	let checkBox = document.getElementById(elementID);
	
	form.method = checkBox.checked ? 'POST' : 'GET'; 	
}

/**
 * Changes form's attribute 'action' (request uri)
 * 
 * @param {*} elementID 
 */
function clickOnCheckBoxEsRequestWrongUriStart(elementID) {
	let form = document.getElementById('myForm');
	let checkBox = document.getElementById(elementID);
	
	form.action = checkBox.checked ? '/notsum' : '/sum';
}

/**
 * Changes form's field name (nums <-> letters)
 * 
 * @param {*} elementID 
 */
function clickOnCheckBoxEsRequestWithoutNums(elementID) {
	let input = document.getElementById('nums');
	let checkBox = document.getElementById(elementID);
	let label = document.getElementById('numsLabel');

	input.name = checkBox.checked ? 'letters' : 'nums';
	label.innerHTML = input.name + ': ';
}

