<?php

class BrainFuck {

	private $input;

	private $chars = array();

	private $cells = array();

	private $cell_pointer = 0;

	private $output = '';

	private $loop_starts = true;

	private $optional_input_pointer = -1;

	public function __construct( $input ) {

		// Do sanitize

		$this->input = $input;
	}

	public function compile() {

		// Check if there is optional input to play with
		$input = explode( '!', $this->input );

		if( isset( $input[1] ) ) {

			$optional_input = $input[1];
		}

		$this->chars = preg_split( '//', $input[0] );
		array_pop( $this->chars );
		array_shift( $this->chars );

		for( $i = 0, $n = count( $this->chars ); $i < $n; $i++ ) {

			$char = $this->chars[ $i ];

			switch( $char ) {

				// Increment byte
				case '+':

					$this->increment_byte();

				break;

				// Decrement byte
				case '-':

					$this->decrement_byte();

				break;

				// Increment data pointer
				case '>':

					$this->increment_pointer();

				break;

				// Decrement data pointer
				case '<':

					$this->decrement_pointer();

				break;

				// Get current data pointer cell and at it to output
				case '.':

					$this->output .= $this->byte_to_ascii();

				break;

				// Begin loop
				case '[':

					$byte = $this->get_byte();

					if( $byte === 0 ) {

						$i = $this->loop_end_position($i);
					}
				break;

				// End loop
				case ']';

					$byte = $this->get_byte();

					if( $byte != 0 ) {

						$i = $this->loop_start_position($i);
					} else {

					}

				break;

				// Input
				case ',':

					$this->cells[ $this->cell_pointer ] = isset( $optional_input[ ++$this->optional_input_pointer] ) ? ord( $optional_input[$this->optional_input_pointer] ) : 0;

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

	private function create_cell() {

		if( ! isset( $this->cells[ $this->cell_pointer ] ) ) {

			$this->cells[ $this->cell_pointer ] = 0;
		}
	}

	private function increment_pointer() {

		++$this->cell_pointer;

		$this->create_cell();
	}

	private function decrement_pointer() {

		--$this->cell_pointer;

		$this->create_cell();
	}

	private function increment_byte() {

		$this->create_cell();

		$this->cells[ $this->cell_pointer ]++;
	}

	private function decrement_byte() {

		$this->create_cell();

		$this->cells[ $this->cell_pointer ]--;
	}
}

// List of examples: http://codegolf.com/brainfuck

// Hello world!
$input = "++++++++++[>+++++++>++++++++++>+++>+<<<<-]>++.>+.+++++++..+++.>++.<<+++++++++++++++.>.+++.------.--------.>+.>.";

// Cat
$input = '>>[-]<<[->>+<<]';

// Just another brainfuck hacker.
$input = "+++[>+++++<-]>[>+>+++>+>++>+++++>++<[++<]>---]>->-.[>++>+<<--]>--.--.+.>>>++.<<.<------.+.+++++.>>-.<++++.<--.>>>.<<---.<.-->-.>+.[+++++.---<]>>[.--->]<<.<+.++.++>+++[.<][.]<++.";

// Square numbers
$input = "++++[>+++++<-]>[<+++++>-]+<+[>[>+>+<<-]++>>[<<+>>-]>>>[-]++>[-]+>>>+[[-]++++++>>>]<<<[[<++++++++<++>>-]+<.<[>----<-]<]<<[>>>>>[>>>[-]+++++++++<[>-<-]+++++++++>[-[<->-]+[<<<]]<[>+<-]>]<<-]<<-]";

// ROT-13 (unique -> havdhr)
$input = "+[,+[-[>+>+<<-]>[<+>-]+>>++++++++[<-------->-]<-[<[-]>>>+[<+<+>>-]<[>+<-]<[<++>>>+[<+<->>-]<[>+<-]]>[<]<]>>[-]<<<[[-]<[>>+>+<<<-]>>[<<+>>-]>>++++++++[<-------->-]<->>++++[<++++++++>-]<-<[>>>+<<[>+>[-]<<-]>[<+>-]>[<<<<<+>>>>++++[<++++++++>-]>-]<<-<-]>[<<<<[-]>>>>[<<<<->>>>-]]<<++++[<<++++++++>>-]<<-[>>+>+<<<-]>>[<<+>>-]+>>+++++[<----->-]<-[<[-]>>>+[<+<->>-]<[>+<-]<[<++>>>+[<+<+>>-]<[>+<-]]>[<]<]>>[-]<<<[[-]<<[>>+>+<<<-]>>[<<+>>-]+>------------[<[-]>>>+[<+<->>-]<[>+<-]<[<++>>>+[<+<+>>-]<[>+<-]]>[<]<]>>[-]<<<<<------------->>[[-]+++++[<<+++++>>-]<<+>>]<[>++++[<<++++++++>>-]<-]>]<[-]++++++++[<++++++++>-]<+>]<.[-]+>>+<]>[[-]<]<]!unique";

// Brainfuck self
#$input = ",[>>++++++[-<+++++++>]<+<[->.<]>+++.<++++[->++++<]>.>,]!brainfuck";

// Output same
#$input = ",[.,]!lenny";

$start = microtime(true);
$brainfuck = new BrainFuck( $input );
$output = $brainfuck->compile();
$end = microtime(true);

echo $output;

echo '<br><br>Compilation time: ' . ($end - $start);

?>