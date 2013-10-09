<?php

require __dir__ . '/../include/classes/php-push.php';

try {

	$Push = new Push('API_KEY', 'API_SECRET');

	$Push->set_message('Test message.');
	$Push->set_view_type(1);
	$Push->set_url('http://push.co/');

	$result = $Push->push();

	print_r($result);

} catch (PushException $e) {
	echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
}

?>
