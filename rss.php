<?php

$me = "rss.php";
header('Content-type: application/rss+xml');

include("inc/config.php");
include("inc/lib.php");
include("inc/markdown/markdown.php");

$title = "nbb.ho.am";
$url = "http://nbb.ho.am/";
$rfc822dt = "D, d M Y H:i:s O";
$year = date("Y", time());

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

echo <<<ENDHEADER
<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">

<channel>
<atom:link href="$blog_base_url$me" rel="self" type="application/rss+xml" />

<title>$blog_title</title>
<link>$blog_base_url</link>
<description>$blog_subtitle</description>
<copyright>Copyright (C) $year</copyright>
<language>$blog_locale</language>

ENDHEADER;

$entries = array_reverse(glob($blog_entries_dir . "*.xml"));

foreach (array_slice($entries, 0, $blog_entries_per_page) as $file) {
    $entry = entry_load($file);
    
    $id = "${blog_base_url}index.php?post=" . basename($file, ".xml") . ($GLOBALS['blog_entry_links_with_titles'] ? "-" . sanitize_file_name(strip_tags($entry->title)) : "");

	echo "<item>\n<title><![CDATA[" . ((string) $entry->format === "html" ? $entry->title : htmlspecialchars($entry->title)) . "]]></title>\n";
	echo "<link>$id</link>\n";
	echo "<guid>$id</guid>\n";	
	echo "<description><![CDATA[" . ((string) $entry->format === "html" ? $entry->text : ((string) $entry->format === "markdown" ? Markdown($entry->text) : htmlspecialchars($entry->text))) . "]]></description>\n";
	echo "<pubDate>" . date($rfc822dt, (int) $entry->date) . "</pubDate>\n";
	echo "</item>\n\n";
}

echo <<<ENDFOOTER
</channel>
</rss>

ENDFOOTER;

?>
