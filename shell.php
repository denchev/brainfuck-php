<?php

require "brainfuck.php";

if( $argv ) {

	$bf = new BrainFuck( $argv[1], isset( $argv[2] ) ? $argv[2] : null );
	echo $bf->compile();
}

exit;

?>