<?php

require __DIR__ . '/vendor/autoload.php';

$app = new Leaf\App();

$app->get('/webhook', function () use($app) {
	$challenge = request()->get('challenge');
	echo "$challenge";
});

$app->run();
