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

					debug( 'Start loop at byte ' . $byte );

					$this->loop_starts = $i;

					if( $byte == 0 ) {

						debug( 'End loop' );

						// Jump $i to after this loop ends
						$i = $this->loop_end_position($i);

						$this->loop_starts = false;
					}
				break;

				// End loop
				case ']';

					debug( 'End loop get byte' );
					$byte = $this->get_byte();

					if( $byte == 0 ) {

						$this->loop_starts = false;
					} else {

						$i = $this->loop_starts;
					}

				break;

				case ',':
				break;

			}
		}

		return $this->output;
	}

	private function loop_end_position( $position ) {

		return strpos( $this->input, ']', $position );
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

// Hello world!
$input = "++++++++++[>+++++++>++++++++++>+++>+<<<<-]>++.>+.+++++++..+++.>++.<<+++++++++++++++.>.+++.------.--------.>+.>.";

// Cat
//$input = '>>[-]<<[->>+<<]';

$brainfuck = new BrainFuck( $input );
$output = $brainfuck->compile();

echo $output;

?>