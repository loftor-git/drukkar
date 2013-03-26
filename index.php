<?php
/* 

Drukkar, a small blogging platform
Copyright (C) 2011-2012 Danyil Bohdan

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

/** @file index.php
    @brief Post viewer
*/

$me = "index.php";
header('Content-type: text/html; charset=utf-8');

include("inc/config.php");
include("inc/lib.php");
include("inc/markdown/markdown.php");


// A mostly-HTML header
include("inc/header.php");

// Search form
include("inc/search.php");

echo '<tr><td id="content">';

$entries = array_reverse(glob($blog_entries_dir . "*.xml"));

$form = array('page', 'post', 'tag', 'search');

process_form($form, $_GET);

$display_excluded = false;

if ($form['post']) {    
    $file = $blog_entries_dir . basename(strtok($form['post'] . "-", "-")) . ".xml";
    if (file_exists($file)) {
        $entry = entry_load($file);
        echo entry_format($entry, basename($file, ".xml"), $me, $blog_base_location, $blog_files_dir);
    } else {
        echo $loc_entry_not_found;
    }
 }
 else {
        $entries = array_filter($entries,
        function ($file) { // Here we filter out the entries based on what we need to display
                global $form, $blog_search_enabled;
                
                $entry = entry_load($file);
                
                if ($form['tag'] === '_excluded' || entry_check_tag('_hidden', $entry)) {
                        return false;
                }                
                
                $t = True; // $t indicates whether to display the current entry.
                
                if ($form['tag']) { // If we've been given a tag filter out entries without it.
                    $t = $t && entry_check_tag($form['tag'], $entry);
                }
                
                if ($form['search'] && $blog_search_enabled) { // We look for a string in each entry's text, title, date and file names.
                    $t = $t && (stripos($entry->title, $form['search']) !== false || stripos($entry->text, $form['search']) !== false || stripos(date($GLOBALS['blog_date_format'], (int) $entry->date), $form['search']) !== false || stripos(join(" ", entry_files($entry)), $form['search']) !== false);
                }
                
                if (!$form['tag'] && !$form['search']) { // Hide excluded entries if not searching or viewing posts by tag
                    $t = $t /*&& !entry_check_tag('_hidden', $entry)*/ && !entry_check_tag('_excluded', $entry);
                }

                return $t;
        }
    );
    
    if (!($form['tag'] || $form['search'])) {
        $blog_entries_per_page = $entries_per_page_for_tags_and_search;
    }

    if (count($entries) > 0) {
    // Output one pagefull of entries
        foreach (array_slice($entries, $form['page'] * $blog_entries_per_page, $blog_entries_per_page) as $file) {
            $entry = entry_load($file);
            echo entry_format($entry, basename($file, ".xml"), $me, $blog_base_location, $blog_files_dir) . "<hr>\n";
        }
        
        // Show page navigation links if needed
        if (($form['page'] + 1) * $blog_entries_per_page < count($entries))
            echo "<a id=\"prevpagelink\" href=\"$me?page=" . ($form['page'] + 1) . "\">$loc_prev_page</a>";
        if ($form['page'] > 0) // If there are more entries to display
            echo "<a id=\"nextpagelink\" href=\"$me?page=" . ($form['page'] - 1) . "\">$loc_next_page</a>";
    } else {
        echo $loc_no_matches;
    }
}

echo "</td></tr>";

// A mostly-HTML footer
include("inc/footer.php");

?>
