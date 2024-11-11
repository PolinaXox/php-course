<!DOCTYPE html>

<?php
// This php block code "saves" (or sets) states of the html-page elements
// The page saves its state for users  

// text fields content which are processing by server or their default values
$bookId = isset($_REQUEST['bookId']) ? $_REQUEST['bookId'] : '12345';
$author = isset($_REQUEST['author']) ? $_REQUEST['author'] : 'Tan Ah Teck';

// text fields states: disabled and hidden or not
$fieldDisabled = isset($_REQUEST['esRequestWithoutBody']) ? 'disabled' : '';
$fieldHidden = isset($_REQUEST['esRequestWithoutBody']) ? 'hidden' : '';

// check box and radio button states which are processing by server
$checkBoxRequestWithoutBody = isset($_REQUEST['esRequestWithoutBody']) ? 'checked' : '';
$rbMethodOptionGET = isset($_REQUEST['methodOptions']) && ($_REQUEST['methodOptions'] === 'GET') ? 'checked' : '';
$rbMethodOptionPOST = isset($_REQUEST['methodOptions']) && ($_REQUEST['methodOptions'] === 'POST') ? 'checked' : '';
?>

<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Task 2</title>
		<!-- css -->
		<style>
		.formItem {
			padding : 10px 10px;
		}
		.edgeCase {
			color : red;
			padding : 10px 10px;
			border : none;
		}
		.edgeCase legend {
			font-weight: bold;	 
		}
		.chBox input::before {
  		content: '';
  		background: #fff;
  		display: block;
  		width: 10px;
  		height: 10px;
  		border: 1px solid black;
		}
		.chBox input:checked::before {
		background-color: red;
		}
		.additionalOptions {
			color : navy;
			padding : 10px 10px;
			border : none;
		}
		.additionalOptions legend {
			font-weight: bold;	 
		}
		</style>
	</head>

	<body>
		<h1>Task 2</h1>
		<form id="myForm" method = <?php echo htmlspecialchars($_SERVER['REQUEST_METHOD']);?> 
			action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
			<div class="formItem">
				<label for="bookId">Book ID: </label>
				<input type="text" id="bookId" name="bookId" value="<?php echo $bookId; ?>" 
				<?php echo $fieldDisabled; ?> <?php echo $fieldHidden; ?>>
			</div>
			<div class="formItem">
				<label for="author">Author: </label>
				<input type="text" id="author" name="author" value="<?php echo $author; ?>"
				 <?php echo $fieldDisabled; ?> <?php echo $fieldHidden; ?>>
			</div>
			<div class="formItem">
				<input type="submit" value="Submit request">
			</div>
			<hr>
			<fieldset class="edgeCase">   
				<legend>Edge-case simulation:</legend>
					<div class="chBox">
					<label for="esRequestWithoutBody">
						<input type="checkbox" name="esRequestWithoutBody" id="esRequestWithoutBody"
						onclick = 'clickOnCheckBoxEsRequestWithoutBody(id)' <?php echo $checkBoxRequestWithoutBody?>>
						Request without body
					</label>
					</div>
			</fieldset>
			<hr>
			<fieldset class="additionalOptions">   
				<legend>Method options:</legend>
					<div>
					<label for="methodOptionGET">
						<input type="radio" name="methodOptions" id="methodOptionGET" value='GET'
						onclick = 'chooseFormMethodByRadioButton(id)' <?php echo $rbMethodOptionGET?>>GET
					</label>
					</div>
					<div>
					<label for="methodOptionPOST">
						<input type="radio" name="methodOptions" id="methodOptionPOST" value="POST"
						onclick = 'chooseFormMethodByRadioButton(id)' <?php echo $rbMethodOptionPOST?>>POST
					</label>
					</div>
			</fieldset>
		</form>
	</body>
</html>

<!-- java script -->
<script>
/**
 * If the check box is checked disables and hides input fields.
 * If the checkbox is unchecked makes text fields visible and able.
 * 
 * @param	elementID	onclicked check box id 
 */
function clickOnCheckBoxEsRequestWithoutBody(elementID) {
	let checkBox = document.getElementById(elementID);
  	let fields = ["bookId", "author"]; 

	fields.forEach(element => {
		let field = document.getElementById(element);
		field.disabled = field.hidden = checkBox.checked;
	});
}

/**
 * Sets the form method according to the selected radio button
 * 
 * @param	elementID	selected radio button id 
 */
function chooseFormMethodByRadioButton(elementId) {
	let form = document.getElementById("myForm");
	let radioButton = document.getElementById(elementId);
	form.method = radioButton.value;
}
</script>

<?php
$http = getRequest();
outputRequest($http);

/**
 * Gets, checks and return the real parts of user`s request
 * 
 * @return array 
 */
function getRequest() : array {
	$requestAsArray = array_filter ( 
		[
			'requestLine' => apache_lookup_uri($_SERVER['REQUEST_URI'])->the_request,
			'requestHeaders' => getallheaders(),
			'requestBody' => file_get_contents('php://input'),
			]
		);
	array_walk_recursive($requestAsArray, 'test_input');
	
	return $requestAsArray;
}

/**
 * Output the user`s request uncluding some superglobals with some decorative elements
 *  
 * @param array $userRequest
 * 
 * @return void
 */
function outputRequest(array $userRequest) : void {
	echo '<hr style="border-top: 5px double #777">';
	echo '<h2>Request:</h2>';
	
	// request line
	echo $userRequest['requestLine'] . '<br>';
	
	// headers
	array_walk($userRequest['requestHeaders'], 'printKeyAndValue');

	// body
	if(isset($userRequest['requestBody'])) echo '<br>' . $userRequest['requestBody'];

	// Superglobal`s content
	if(!empty($_GET)) printSuperglobal($_GET, '$_GET');

	if(!empty($_POST)) printSuperglobal($_POST, '$_POST');

	echo '<hr>';
}

/**
 * Changes input data to prevent harm, injections ect.
 * (Uses as callback function for array_walk_recursive() for the input data)
 * 
 * @param string $data
 * 
 * @return void
 */
function test_input(string &$data) : void { //
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
  }

/**
 * Outputs key and its value in a nice separate line 
 * (Uses as callback function for array_walk() for print header's/variable's names and values)
 * 
 * @param string $value
 * @param string $key
 * 
 * @return void
 */
function printKeyAndValue(string &$value, string $key) : void {
	echo '<strong>' . $key . '</strong>: ' . $value . '<br>';
};

/**
 *  Output the superglobal with some decorative elements
 * 
 * @param array $array
 * @param string $label
 * 
 * @return void
 */
function printSuperglobal(array &$array, string $label) : void {
	// prevent harm before output
	$arr = $array;
	array_walk_recursive($arr, 'test_input');

	// output
	echo '<hr style="border-top: 1px dashed #333">';
	echo '<h3>Superglobal ' . $label . ' contains:</h3>';
	array_walk($arr, 'printKeyAndValue');
}