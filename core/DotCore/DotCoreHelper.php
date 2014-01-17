<?php

/*
 * 
 * Store global functions for global use
 * 
 */

function LineBreaksToBr($string) {
	return str_replace('\n', '<br />', $string);
}

function BrToLineBreaks($string) {
	return str_replace('<br />', '\n', $string);
}

function is_url($url){

	$regex = "/(https?:\/\/)"
		. "?(([0-9a-z_!~*'().&=+$%-]+: )?[0-9a-z_!~*'().&=+$%-]+@)?" //user@
		. "(([0-9]{1,3}\.){3}[0-9]{1,3}" // IP- 199.194.52.184
		. "|" // allows either IP or domain
		. "([0-9a-z_!~*'()-]+\.)*" // tertiary domain(s)- www.
		. "([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\." // second level domain
		. "[a-z]{2,6})" // first level domain- .com or .museum
		. "(:[0-9]{1,4})?" // port number- :80
		. "((\/?)|" // a slash isn't required if there is no file name
		. "(\/[0-9a-z_!~*'().;?:@&=+$,%#-]+)+\/?)/";

	return preg_match($regex, $url);
}

function is_email($email) {
	return preg_match( '/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/', $email);
}

function clean_request()
{
	return clean_array($_REQUEST);
}

function clean_array(array $arr)
{
	if(get_magic_quotes_gpc())
	{
		foreach($arr as $key => $value)
		{
			if(is_array($arr[$key]))
			{
				$arr[$key] = clean_array($arr[$key]);
			}
			else
			{
				$arr[$key] = trim(stripslashes($value));
			}
		}
	}
	
	return $arr;
}

function remove_dir($current_dir) {
	if($current_dir[strlen($current_dir) - 1] != '/')
	{
		$current_dir .= '/';
	}
	if($dir = @opendir($current_dir)) {
		while (($f = readdir($dir)) !== false) {
			if($f > '0' and filetype($current_dir.$f) == 'file') {
				unlink($current_dir.$f);
			} elseif($f > '0' and filetype($current_dir.$f) == 'dir') {
				remove_dir($current_dir.$f.'\\');
			}
		}
		closedir($dir);
		rmdir($current_dir);
	}
}

function str_replace_once($needle , $replace , $haystack){
	// Looks for the first occurence of $needle in $haystack
	// and replaces it with $replace.
	$pos = strpos($haystack, $needle);
	if ($pos === false) {
		// Nothing found
	return $haystack;
	}
	return substr_replace($haystack, $replace, $pos, strlen($needle));
}

function remove_empty_lines($string)
{
	return preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $string);
}

function array_push_ref(array &$arr,&$val)
{
	$arr[] = &$val;
	//If you want array_push to merge the two arrays, uncomment the next block of code:
	//
	// if(!is_array($val))
	// {
	// 	$arr[] = &$val;
	// }
	// else
	// {
	// 	foreach($val as &$v) {
	// 		$arr[] = &$v;
	// 	}
	// }
}

?>
