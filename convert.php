<?php

$GLOBALS['config']['config'] = array();
$GLOBALS['config']['tmpFolder'] = './tmp/';
$GLOBALS['config']['outputFolder'] = './output/';
$GLOBALS['config']['scale'] = 0.1;
include("src/functions.php");
include('src/preziobject.php');
include('src/objectfactory.php');

if ($argc != 2){
	print("Help");
	print("Call: ".$argv[0]." inputFile");
}
else{
	$inputFile = $argv[1];
	if(file_exists($inputFile) == false){
		print("InputFile does not exist");
		exit(1);
	}
	$GLOBALS['contentPath'] = $GLOBALS['config']['tmpFolder'].basename($inputFile,'.zip').'/content/';
	if(is_dir($GLOBALS['contentPath']) == false){
		if(unzip($inputFile,$GLOBALS['config']['tmpFolder'],false,true) == false){
			print("Unzip failed");
			exit(1);
		}
	}

	$xmlDoc = simplexml_load_file($GLOBALS['contentPath'].'/data/content.xml');
	
	$output_Handle = fopen($GLOBALS['config']['outputFolder'].'index.html','w+');
	fwrite($output_Handle, file_get_contents('src/templates/head.html'));

	// CSS

	//fwrite($output_Handle,'<style type="text/css">');
	//fwrite($output_Handle, $xmlDoc->style);
	//fwrite($output_Handle,'</style>');
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