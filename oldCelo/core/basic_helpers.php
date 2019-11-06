<?php

function err_log($mixed) {
	if (is_string($mixed)) {
		error_log($mixed);
	} elseif (is_array($mixed) || is_object($mixed)) {
		error_log(print_r($mixed, true));
	}
}
