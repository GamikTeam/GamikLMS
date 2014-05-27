<?php
/*
 * Klasa wyjątku aplikacji
 */
class ExceptionDb extends Exception {
	public function __construct($Message, $log = '' , $code=0, $previous = NULL) {
		parent::__construct( $Message, $code, $previous);
	}
}
