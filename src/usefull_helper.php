<?php
  
function arrnize(...$data)
{
	switch (true) {
		case count($data) == 0: return [];
		case is_array($data[0]): return $data[0];
		case array_key_exists(1, $data): return [$data[0] => $data[1]];
		default: return [$data[0]];
	}
}

function buildHtmlTag($type, $attr = [], $text = "", $escape_text = true)
{
  $text = $escape_text ? htmlentities($text) : $text;
  $_attr = "";

	foreach ($attr as $name => $value) {
		$_attr .= sprintf(' %s="%s"', $name, $value);
	}

	if (in_array($type, ['meta', 'img'])) {
		return sprintf('<%s%s>', $type, $_attr);
	} else {
		return sprintf('<%s%s>%s</%s>', $type, $_attr, $text, $type);
	}
}