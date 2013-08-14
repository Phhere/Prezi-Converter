<?php

$GLOBALS['config']['config'] = array();
$GLOBALS['config']['inputFolder'] = './input/';
$GLOBALS['config']['outputFolder'] = './output/';
$GLOBALS['config']['scale'] = 0.1;
include('src/preziobject.php');
include('src/objectfactory.php');

if ($argc != 3){
	print("Help");
	print("Call: ".$argv[0]." inputFile outputName");
}
else{
	$inputFile = $argv[1];
	$outputName = $argv[2];
	if(file_exists($GLOBALS['config']['inputFolder'].$inputFile) == false){
		print("InputFile does not exist");
	}

	$xmlDoc = simplexml_load_file($GLOBALS['config']['inputFolder'].$inputFile);
	
	$output_Handle = fopen($GLOBALS['config']['outputFolder'].$outputName.'.html','w+');
	fwrite($output_Handle, file_get_contents('src/templates/head.html'));

	// CSS

	//fwrite($output_Handle,'<style type="text/css">');
	//fwrite($output_Handle, $xmlDoc->style);
	//fwrite($output_Handle,'</style>');

	fwrite($output_Handle,'</head><body>');
	//fwrite($output_Handle,'<div style="position:absolute;">');
	$objects = $xmlDoc->{'zui-table'}->object;
	$collecedObjects = array();
	$minX = 0;
	$minY = 0;
	foreach($objects as $obj){
		$o = ObjectFactory::get($obj);
		$collecedObjects[] = $o;
		$minX = min($minX,$o->x);
		$minY = min($minY,$o->y);
		//print_r($o);
	}

	foreach($collecedObjects as $o){
		$o->x = $o->x + abs($minX);
		$o->y = $o->y + abs($minY);
		fwrite($output_Handle, $o);
	}

	//fwrite($output_Handle,'</div>');
	fwrite($output_Handle, file_get_contents('src/templates/foot.html'));
}
?> 