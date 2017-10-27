<?php
include __DIR__.'/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

$map_json	= $_ENV['MENU'];

$map 		= json_decode($map_json, true);

print_r( handle());

function handle()
{	
	global $map;

	$pos 				= strripos($_POST['text'], '*00');

	if ($pos !== false) {
		$_POST['text'] 	= substr($_POST['text'], $pos + 3);
	}

	$_POST['text'] 		= removeStar($_POST['text']);

	if (empty($_POST['text']) || $_POST['text'] === '00') {
		return base();
	} else {
		$input_array 	= explode("*", strtoupper($_POST['text']));
		$item 			= current($input_array);

		if (array_key_exists($item, $map)) {
			return base(call($item), ['00']);
		} else {
			return "END Invalid option";
		}
	}
}

function removeStar($text)
{
	$pos 		= strpos($text, '*');

	if ($pos !== false && $pos === 0) {
		$text 	= substr($text, $pos + 1);
	}

	return $text;
}

function base($reply = "", $only = [])
{
	global $map;

	if (empty($reply))
		$reply .= 'CON ';

	$reply 		.= " \n";

	foreach ($map as $key => $value) {
		if (empty($only) || in_array($key, $only)) {
			if ($key === '00')
				$reply .= "\n";
			$reply .= $key . ". " . $value['name'] . "\n";
		}
	}

	return $reply;	
}

function call ($index)
{
	global $map;

	$set 		= $map[$index];
	$set 		= $map[$index];

	$client 	= new Client([
		'base_uri' 	=> $set['callback'],
		'headers' 	=> [
			'Content-Type' 	=> 'application/x-www-form-urlencoded',
			'Accept' 		=> 'application/json'
		]
	]);

	$_POST['text'] 	= str_replace($index, "", $_POST['text']);

	$_POST['text'] 	= removeStar($_POST['text']);

	$response 		= $client->post('', ['form_params' => $_POST]);

	return $response->getBody()->getContents();
}