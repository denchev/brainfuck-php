<?php

function debug( $msg, $emphasis = false ) {

	//echo $msg . '<br>';
}

class BrainFuck {

	private $input;

	private $chars = array();

	private $cells = array();

	private $cell_pointer = 0;

	private $output = '';

	private $loop_starts = true;

	public function __construct( $input ) {

		// Do sanitize

		$this->input = $input;
	}

	public function compile() {

		$this->chars = preg_split( '//', $this->input );

		for( $i = 0, $n = count( $this->chars ); $i < $n; $i++ ) {

			$char = $this->chars[ $i ];

			switch( $char ) {

				// Increment byte
				case '+':
					debug( 'Increment byte' );
					$this->increment_byte();
				break;

				// Decrement byte
				case '-':
					debug( 'Decrement byte' );
					$this->decrement_byte();
				break;

				// Increment data pointer
				case '>':
					debug( 'Increment data pointer' );
					$this->increment_pointer();
				break;

				// Decrement data pointer
				case '<':
					debug( 'Decrement data pointer' );
					$this->decrement_pointer();
				break;

				// Get current data pointer cell and at it to output
				case '.':
					$this->output .= $this->byte_to_ascii();
				break;

				// Begin loop
				case '[':

					$byte = $this->get_byte();

					if( $byte == 0 ) {

						$i = $this->loop_end_position($i);
					}
				break;

				// End loop
				case ']';

					$byte = $this->get_byte();

					if( $byte != 0 ) {

						$i = $this->loop_start_position($i);
					}

				break;

				case ',':
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

		if( ! isset( $this->cells[ $this->cell_pointer ] ) ) {

			$this->cells[ $this->cell_pointer ] = 0;
		}
	}

	private function decrement_pointer() {

		--$this->cell_pointer;

		if( ! isset( $this->cells[ $this->cell_pointer ] ) ) {

			$this->cells[ $this->cell_pointer ] = 0;
		}
	}

	private function increment_byte() {

		if( ! isset( $this->cells[ $this->cell_pointer ] ) ) {

			$this->cells[ $this->cell_pointer ] = 0;
		}

		$this->cells[ $this->cell_pointer ]++;
	}

	private function decrement_byte() {

		if( ! isset( $this->cells[ $this->cell_pointer ] ) ) {

			$this->cells[ $this->cell_pointer ] = 0;
		}

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

$start = microtime(true);
$brainfuck = new BrainFuck( $input );
$output = $brainfuck->compile();
$end = microtime(true);

echo $output;

echo '<br><br>Compilation time: ' . ($end - $start);

?>