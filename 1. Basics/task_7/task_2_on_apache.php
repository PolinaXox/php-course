<!DOCTYPE html>

<?php
$bookId = isset($_GET['bookId']) ? $_GET['bookId'] : '12345';
$author = isset($_GET['author']) ? $_GET['author'] : 'Tan Ah Teck';
$checkBoxWithoutHeaders = isset($_GET['withoutHeaders']) ? 'checked' : '';
$checkBoxWithoutBody = isset($_GET['withoutBody']) ? 'checked' : '';
?>

<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Task 2</title>
		<style>
		.formItem {
			padding : 10px 10px;
		}
		.edgeCases {
			color : red;
			padding : 10px 10px;
			border : none;
		}
		.edgeCases legend {
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
		</style>
	</head>

	<body>
		<h1>Task 2</h1>
		<form method="GET" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
			<div class="formItem">
				<label for="bookId">Book ID: </label>
				<input type="text" id="bookId" name="bookId" value="<?php echo $bookId; ?>">
			</div>
			<div class="formItem">
				<label for="author">Author: </label>
				<input type="text" id="author" name="author" value="<?php echo $author; ?>">
			</div>
			<div class="formItem">
				<input type="submit" value="Submit request">
			</div>
			<hr>
			<fieldset class="edgeCases">   
				<legend>Edge-cases simulation:</legend>
					<div class="chBox">
						<label for="withoutHeaders">
							<input type="checkbox" name="withoutHeaders" id="withoutHeaders" <?php echo $checkBoxWithoutHeaders?>>
							Request without headers
						</label>
					</div>
					<div class="chBox">
					<label for="withoutBody">
						<input type="checkbox" class="chBox" name="withoutBody" id="withoutBody" <?php echo $checkBoxWithoutBody?>>
						Request without body
					</label>
					</div>
			</fieldset>
		</form>
		<hr>
	</body>
</html>

<?php
simulateEdgeCases();
$http = makeRequestAsExample();
outputRequestExample($http);

/**
 * Makes a request exapmple: some part of the real request with some modifications corresponding to the task
 * 
 * @return array
 */
function makeRequestAsExample() : array {
	$body = isset($_SERVER['QUERY_STRING']) ? test_input($_SERVER['QUERY_STRING']) : null;
	$headers = [];
	$headers['Host'] = isset($_SERVER['HTTP_HOST']) ? test_input($_SERVER['HTTP_HOST']) : null;
	$headers['Accept'] = isset($_SERVER['HTTP_ACCEPT']) ? test_input($_SERVER['HTTP_ACCEPT']) : null;
	$headers['Accept-Language'] = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? test_input($_SERVER['HTTP_ACCEPT_LANGUAGE']) : null;
	$headers['Accept-Encoding'] = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? test_input($_SERVER['HTTP_ACCEPT_ENCODING']) : null;
	$headers['User-Agent'] = isset($_SERVER['HTTP_USER_AGENT']) ? test_input($_SERVER['HTTP_USER_AGENT']) : null;
	$headers['Content-Length'] = (empty(array_filter($headers)) || empty($body)) ? null : strlen($body);
	
	return array_filter(
		[
			'method' => test_input($_SERVER['REQUEST_METHOD']),
			'uri' => test_input($_SERVER['REQUEST_URI']),
			'serverProtocol' => test_input($_SERVER['SERVER_PROTOCOL']),
			'headers' => array_filter($headers),
			'body' => $body,
			]
		);
}

/**
 * Outputs a request exapmple: some part of the real request with some modifications corresponding to the task
 * 
 * @param array $request
 * 
 * @return void
 */
function outputRequestExample(array $request) : void {
	echo '<h2>Your request:</h2>';
	echo $request['method'] . ' ' . $request['uri'] . ' ' . $request['serverProtocol'] . '<br>';
	
	if(isset($request['headers'])) {
		foreach($request['headers'] as $key => $value) {
			echo '<strong>' . $key . '</strong>: ' . $value . '<br>';
		}
	}

	if(isset($request['body'])) {
		echo '<br>';
		echo $request['body'];
	}
}

/**
 * Check input data to prevent harm
 * @param string $data
 * 
 * @return string
 */
function test_input(string $data) : string {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
  }

/**
 * Simulates edge-cases by changing input data
 *  
 * @return void
 */
function simulateEdgeCases() : void {
	if(isset($_GET['withoutBody'])) {
		unset($_SERVER['QUERY_STRING']);
	}
	
	if(isset($_GET['withoutHeaders'])) {
		$arr = ['REQUEST_METHOD', 'REQUEST_URI', 'SERVER_PROTOCOL', 'QUERY_STRING'];
		$fun = fn($header) => in_array($header, $arr);
		$_SERVER = array_filter($_SERVER, $fun, ARRAY_FILTER_USE_KEY);
	}	
  }
?>	

