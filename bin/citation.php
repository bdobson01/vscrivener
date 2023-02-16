<?php
function read_tags_as_json($url){


    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

    $HTML_DOCUMENT = curl_exec($ch);
    curl_close($ch);

    $doc = new DOMDocument();
    $doc->loadHTML($HTML_DOCUMENT);

    // fecth <title>
    $res['title'] = $doc->getElementsByTagName('title')->item(0)->nodeValue;

    // fetch og:tags
    foreach( $doc->getElementsByTagName('meta') as $m ){

          if( $m->getAttribute('property') ){
              $prop = $m->getAttribute('property');
              $res[$prop] = $m->getAttribute('content');
          }
          if( $m->getAttribute('name') ){

              $name = $m->getAttribute('name');
              $res[$name] = $m->getAttribute('content');

          }
    }
    return $res;
}

function getMeta($tags, $field, &$ret)
{
	if (isset($tags[$field]))
	{
		$ret = $tags[$field];
	}
	else if (isset($tags["og:" . $field]))
	{
		$ret = $tags["og:" . $field];
	}
	else if (isset($tags["twitter:" . $field]))
	{
		$ret = $tags["twitter:" . $field];
	}
	return $ret;
}

libxml_use_internal_errors(true);
$ret = read_tags_as_json($argv[1]);
//print_r($res);

// MLA
// Last name, First name of author. “Title of Web Page.” Title of Website, Publisher, Date published, URL.

$parse = parse_url($argv[1]);
$author = "--";
$title = "--";
$site_name = $parse['host'];
$publisher = $parse['host'];
$date = "--";
$author=getMeta($ret, "author", $author);
if ($author != '--')
{
$parts = explode(' ', $author); 
$name_first = array_shift($parts);
$name_last = array_pop($parts);
$name_middle = trim(implode(' ', $parts));
$author = $name_last . ", " . $name_first;
if (strlen($name_middle) != 0)
{
	$author .= " " . $name_middle;
}
}
$title=getMeta($ret, "title", $title);
$site_name=getMeta($ret, "site_name", $site_name);
$publisher=getMeta($ret, "article:publisher", $publisher);
$date=getMeta($ret, "article:published_time", $date);

echo $author . '. "' . $title . '." ' . $site_name . ", " . $publisher . ", " . $date . ", " . $argv[1] . "\n";