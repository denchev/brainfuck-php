<?php

set_time_limit( 5 );

class BrainFuck {

	private $input = array();

	private $code;

	private $chars = array();

	private $cells = array();

	private $cell_pointer = 0;

	private $output = '';

	private $loop_starts = true;

	private $optional_input_pointer = 0;

	public function __construct( $code, $input = "" ) {

		// Do sanitize

		$this->code = preg_replace( '/[^<>+\-\.,\[\]]/', '', $code );

		$this->input = preg_split( '//', $input );
		array_pop( $this->input );
		array_shift( $this->input );
	}

	public function compile() {

		// Check if there is optional input to play with
		$code = explode( '!', $this->code );

		$this->chars = preg_split( '//', $this->code );
		array_pop( $this->chars );
		array_shift( $this->chars );

		for( $i = 0, $n = count( $this->chars ); $i < $n; $i++ ) {

			$char = $this->chars[ $i ];

			switch( $char ) {

				// Increment byte
				case '+' :

					$this->increment_byte();

				break;

				// Decrement byte
				case '-' :

					$this->decrement_byte();

				break;

				// Increment data pointer
				case '>' :

					$this->increment_pointer();

				break;

				// Decrement data pointer
				case '<' :

					$this->decrement_pointer();

				break;

				// Get current data pointer cell and at it to output
				case '.' :

					$ascii = $this->byte_to_ascii();

					if( $this->get_byte() == 0 )
						return $this->output;

					$this->output .= $ascii;

				break;

				// Begin loop
				case '[' :

					$byte = $this->get_byte();

					if( $byte == 0 ) {

						$i = $this->loop_end_position($i);
					}
				break;

				// End loop
				case ']' ;

					$byte = $this->get_byte();

					if( $byte != 0 ) {

						$i = $this->loop_start_position($i);
					}

				break;

				// Input
				case ',' :

					if( isset( $this->input[ $this->optional_input_pointer  ] ) ) {

						$this->cells[ $this->cell_pointer ] = ord( $this->input[ $this->optional_input_pointer ] );

						$this->optional_input_pointer++;
					} else {

						$this->cells[ $this->cell_pointer ] = 0;
					}
				break;

			}


		}

		return $this->output;
	}

	private function loop_start_position( $position ) {

		$has_prev_loops = 0;

		for( $i = $position; $i >= 0; $i-- ) {

			$char = $this->chars[ $i ];

			if( $char == ']' ) {

				$has_prev_loops++;

			} else if ( $char == '[' ) {

				if( --$has_prev_loops == 0 ) {

					return $i;
				}
			}
		}

		throw new Exception('Cannot find loop start.');
	}

	private function loop_end_position( $position ) {

		$has_next_loops = 0;

		for( $i = $position; $i < count( $this->chars ); $i++ ) {

			$char = $this->chars[ $i ];

			if( $char == ']' ) {

				if( --$has_next_loops == 0 ) {

					return $i;
				}

			} else if ( $char == '[' ) {

				$has_next_loops++;
			}
		}

		throw new Exception('Cannot find loop end.');
	}

	private function get_byte() {

		return $this->cells[ $this->cell_pointer ];
	}

	private function byte_to_ascii() {

		return chr( $this->get_byte() );
	}

	private function increment_pointer() {

		++$this->cell_pointer;

		if( ! isset( $this->cells[ $this->cell_pointer ] ) )
			$this->cells[ $this->cell_pointer ] = 0;
	}

	private function decrement_pointer() {

		--$this->cell_pointer;

		if( ! isset( $this->cells[ $this->cell_pointer ] ) )
			$this->cells[ $this->cell_pointer ] = 0;
	}

	private function increment_byte() {

		if( ! isset( $this->cells[ $this->cell_pointer ] ) )
			$this->cells[ $this->cell_pointer ] = 0;

		$this->cells[ $this->cell_pointer ]++;
	}

	private function decrement_byte() {

		if( ! isset( $this->cells[ $this->cell_pointer ] ) )
			$this->cells[ $this->cell_pointer ] = 0;

		$this->cells[ $this->cell_pointer ]--;
	}
}

/*
 * TESTS
 */

// List of examples: http://codegolf.com/brainfuck

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