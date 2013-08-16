<?php

// List of examples: http://codegolf.com/brainfuck

require_once "brainfuck.php";

// Hello world!
$input = "++++++++++[>+++++++>++++++++++>+++>+<<<<-]>++.>+.+++++++..+++.>++.<<+++++++++++++++.>.+++.------.--------.>+.>.";
$bf = new BrainFuck( $input );
echo $bf->compile();
echo '<br><br>';

// Just another brainfuck hacker.
$input = "+++[>+++++<-]>[>+>+++>+>++>+++++>++<[++<]>---]>->-.[>++>+<<--]>--.--.+.>>>++.<<.<------.+.+++++.>>-.<++++.<--.>>>.<<---.<.-->-.>+.[+++++.---<]>>[.--->]<<.<+.++.++>+++[.<][.]<++.";
$bf = new BrainFuck( $input );
echo $bf->compile();
echo '<br><br>';

// Square numbers
$input = "++++[>+++++<-]>[<+++++>-]+<+[>[>+>+<<-]++>>[<<+>>-]>>>[-]++>[-]+>>>+[[-]++++++>>>]<<<[[<++++++++<++>>-]+<.<[>----<-]<]<<[>>>>>[>>>[-]+++++++++<[>-<-]+++++++++>[-[<->-]+[<<<]]<[>+<-]>]<<-]<<-]";
$bf = new BrainFuck( $input );
echo $bf->compile();
echo '<br><br>';

// ROT-13 (unique -> havdhr)
$input = "+[,+[-[>+>+<<-]>[<+>-]+>>++++++++[<-------->-]<-[<[-]>>>+[<+<+>>-]<[>+<-]<[<++>>>+[<+<->>-]<[>+<-]]>[<]<]>>[-]<<<[[-]<[>>+>+<<<-]>>[<<+>>-]>>++++++++[<-------->-]<->>++++[<++++++++>-]<-<[>>>+<<[>+>[-]<<-]>[<+>-]>[<<<<<+>>>>++++[<++++++++>-]>-]<<-<-]>[<<<<[-]>>>>[<<<<->>>>-]]<<++++[<<++++++++>>-]<<-[>>+>+<<<-]>>[<<+>>-]+>>+++++[<----->-]<-[<[-]>>>+[<+<->>-]<[>+<-]<[<++>>>+[<+<+>>-]<[>+<-]]>[<]<]>>[-]<<<[[-]<<[>>+>+<<<-]>>[<<+>>-]+>------------[<[-]>>>+[<+<->>-]<[>+<-]<[<++>>>+[<+<+>>-]<[>+<-]]>[<]<]>>[-]<<<<<------------->>[[-]+++++[<<+++++>>-]<<+>>]<[>++++[<<++++++++>>-]<-]>]<[-]++++++++[<++++++++>-]<+>]<.[-]+>>+<]>[[-]<]<]";
$bf = new BrainFuck( $input, 'unique' );
echo $bf->compile();
echo '<br><br>';

// Brainfuck self
$input = ",[>>++++++[-<+++++++>]<+<[->.<]>+++.<++++[->++++<]>.>,]";
$bf = new BrainFuck( $input, 'brainfuck' );
echo $bf->compile();
echo '<br><br>';

// Output same
$input = ",[.,]";
$bf = new BrainFuck( $input, 'lenny' );
echo $bf->compile();
echo '<br><br>';

// Reverse input
$input = ",[>,]<[.<]";
$bf = new BrainFuck( $input, 'lenny' );
echo $bf->compile();
echo '<br><br>';

?>