<?php
/*

Drukkar, a small blogging platform
Copyright (C) 2011-2014 Danyil Bohdan

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

*/

/** @file rss.php
 *  @brief Provides an RSS feed.
 */

//! What this file is called.
$me = "rss.php";

header('Content-type: application/rss+xml');

include("inc/config.php");
include("inc/lib.php");

//! Standard date format for RSS feeds.
$rfc822dt = "D, d M Y H:i:s O";
//! For <copyright>.
$year = date("Y", time());

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

$cache_file_name = "cache/feed.rss";

if ($blog_caching_enabled && cache_is_current($cache_file_name)) {
    $cache_file_in = fopen($cache_file_name, 'r');
    fgets($cache_file_in); // Skip the first line.
    fpassthru($cache_file_in);
} else {
    include("inc/markdown/markdown.php");

    if ($blog_caching_enabled) {
        ob_start();
    }

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

    $entries = sorted_entry_file_names();

    foreach (array_slice($entries, 0, $blog_entries_per_page) as $file) {
        $entry = entry_load($file);

        $id = "${blog_base_url}index.php?post=" . basename($file, ".xml") .
              ($GLOBALS['blog_entry_links_with_titles'] ? "-" .
               sanitize_file_name(strip_tags($entry->title)) : "");

            echo "<item>\n<title><![CDATA[",
                 ((string) $entry->format === "html" ? $entry->title :
                  htmlspecialchars($entry->title)), "]]></title>\n";
            echo "<link>$id</link>\n";
            echo "<guid>$id</guid>\n";
            echo "<description><![CDATA[",
                 ((string) $entry->format === "html" ? $entry->text :
                 ((string) $entry->format === "markdown" ?
                 Markdown($entry->text) : htmlspecialchars($entry->text))),
                 "]]></description>\n";
            echo "<pubDate>", date($rfc822dt, (int) $entry->date),
                 "</pubDate>\n";
            echo "</item>\n\n";
    }

    echo <<<ENDFOOTER
</channel>
</rss>

ENDFOOTER;

    if ($blog_caching_enabled) {
        $cache_file_out = fopen($cache_file_name, 'w');
        fwrite($cache_file_out, "<!-- RSS feed -->\n");
        fwrite($cache_file_out, ob_get_contents());
        fclose($cache_file_out);
        ob_end_flush();
    }

}

?>
