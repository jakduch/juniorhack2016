<?php
function rglob($pattern, $flags = 0) {
	$return = glob($pattern, $flags);
	$dirs = "*";
	$separator = DIRECTORY_SEPARATOR;
	$files = $pattern;
	if (preg_match('~(.*)([/\\\\])(.+)~', $pattern, $match)) {
		$dirs = "$match[1]$match[2]*";
		$separator = $match[2];
		$files = $match[3];
	}

	foreach (glob($dirs, ($flags & ~GLOB_MARK) | GLOB_ONLYDIR) as $subdir) {
		$return = array_merge($return, rglob("$subdir$separator$files", $flags));
	}
	return $return;
}

$hovno = (rglob("*"));
foreach($hovno as $h) {
	echo($h);
 echo is_file($h) ? " - OK" : "- CHYBA";
	echo("<br />");

}