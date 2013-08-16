<?php

class BrainFuck {

	private $input = array();

	private $code;

	private $chars = array();

	private $cells = array();

	private $cell_pointer = 0;

	private $output = '';

	private $loop_starts = true;

	private $input_pointer = 0;

	public function __construct( $code, $input = "" ) {

		$this->code = preg_replace( '/[^<>+\-\.,\[\]]/', '', $code );

		$this->input = $this->split( $input );
	}

	public function compile() {

		$this->chars = $this->split( $this->code );

		for( $i = 0, $n = count( $this->chars ); $i < $n; $i += 1 ) {

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

					if( $this->get_byte() == 0 )
						return $this->output;

					$ascii = $this->byte_to_ascii();

					$this->output .= $ascii;

				break;

				// Begin loop
				case '[' :

					if( $this->get_byte() == 0 ) {

						$i = $this->loop_end_position($i);
					}

				break;

				// End loop
				case ']' ;

					if( $this->get_byte() != 0 ) {

						$i = $this->loop_start_position($i);
					}

				break;

				// Input
				case ',' :

					$this->cells[ $this->cell_pointer ] = isset( $this->input[ $this->input_pointer  ] ) ? ord( $this->input[ $this->input_pointer++ ] ) : 0;

				break;

			}

		}

		return $this->output;
	}

	private function split( $input ) {

		$input = preg_split( '//', $input );
		array_pop( $input);
		array_shift( $input );

		return $input;
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

	private function init_cell() {

		if( ! isset( $this->cells[ $this->cell_pointer ] ) )
			$this->cells[ $this->cell_pointer ] = 0;
	}

	private function increment_pointer() {

		++$this->cell_pointer;

		$this->init_cell();
	}

	private function decrement_pointer() {

		--$this->cell_pointer;

		$this->init_cell();
	}

	private function increment_byte() {

		$this->init_cell();

		$this->cells[ $this->cell_pointer ]++;
	}

	private function decrement_byte() {

		$this->init_cell();

		$this->cells[ $this->cell_pointer ]--;
	}
}


?>