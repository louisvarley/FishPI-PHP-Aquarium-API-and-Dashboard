<?php


/* Return a Camel Cased string to a dashed string */
function camelToDashed($str) {
    return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $str));
}

/* Return a dashed string as camel case */
function dashedtoCamel($str) {
	$str = str_replace(' ', '', ucwords(str_replace('-', ' ', $str)));
    $str[0] = strtolower($str[0]);
    return $str;
}

/* replace any "view", "controller" or "model" from a string */
function removeMVC($str){
	
	$str = str_replace('view','',$str);
	$str = str_replace('controller','',$str);
	$str = str_replace('model','',$str);	
	
	return $str;
}
