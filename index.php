<?php
/*

Drukkar, a small blogging platform
Copyright (C) 2011-2013 Danyil Bohdan

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
 *  @brief Post viewer.
 */

function page_start($title = "") {
    // The header. It's mostly HTML with little logic.
    blog_header($title);

    blog_search_form();
    
    blog_sidebar();

    echo "<div id=\"content\">\n";
}

//! The current file's name.
$me = "index.php"; // basename($_SERVER['SCRIPT_FILENAME']);
header('Content-type: text/html; charset=utf-8');

include("inc/lib.php");
include("inc/config.php");

include("inc/header.php");
include("inc/search.php");
include("inc/sidebar.php");
include("inc/footer.php");

$form = array('page', 'post', 'tag', 'search');
process_form($form, $_GET);
$post = $form['post'];
if ($post) {
    $post = basename($post);
    $form['page'] = false;
    $form['tag'] = false;
    $form['search'] = false;
}

//! Uniquely identifies the blog entry we're caching.
$cache_id = str_replace("\n", "", var_export($form, true));
//! Cache file naming is something of a hack.
$cache_file_name = $blog_cache_dir . md5($cache_id) . ".html";

if ($blog_caching_enabled && (!$form['search'] || $blog_cache_searches)
&& cache_is_current($cache_file_name)) {
    $cache_file_in = fopen($cache_file_name, 'r');
    fgets($cache_file_in); // Skip the first line.
    fpassthru($cache_file_in);
} else {
    include("inc/markdown/markdown.php");

    if ($blog_caching_enabled) {
        ob_start();
    }

    if ($post) {
        $file = $blog_entries_dir . $post . ".xml";
        if (file_exists($file)) {
            $entry = entry_load($file);
            page_start(strip_tags($entry->title) . " | " . $blog_title);
            echo entry_format($entry, basename($file, ".xml"), $me,
                              $blog_base_location, $blog_files_dir);
        } else {
            page_start();
            echo $loc_entry_not_found;
        }
     } else { // !$post
        page_start();

        $entries = sorted_entry_file_names();

        if ($form['tag'] === '_excluded' || $form['tag'] === '_hidden') {
            // Don't allow searching for _hidden or _excluded entries.
            $entries = array();
        } else {
            $entries = array_filter($entries,
            function ($file) {
                    /* This function filters out the entries based on what
                       we're looking to display. */
                    global $form;
                    global $blog_search_enabled;

                    $entry = entry_load($file);

                    if (entry_check_tag('_hidden', $entry)) {
                            /* _hidden entries can only be viewed with a
                               direct link. */
                            return false;
                    }

                    // $t indicates whether to display the current entry.
                    $t = true;

                    if ($form['tag']) {
                        /* If we've been given a tag filter out entries
                           without it. */
                        $t = $t && entry_check_tag($form['tag'], $entry);
                    }

                    /* We look for a string in each entry's text, title,
                       date and file names. */
                    if ($form['search'] && $blog_search_enabled) {
                        $t = $t &&
                             (stripos($entry->title, $form['search'])
                             !== false ||
                             stripos($entry->text, $form['search'])
                             !== false ||
                             stripos(date($GLOBALS['blog_date_format'],
                                          (int) $entry->date),
                                     $form['search'])
                             !== false ||
                             stripos(join(" ", entry_files($entry)),
                                     $form['search'])
                             !== false);
                    }

                    /* Don't show excluded entries if not searching or viewing
                       posts by tag */
                    if (!$form['tag'] && !$form['search']) {
                        $t = $t && !entry_check_tag('_excluded', $entry);
                    }

                    return $t;
                }
            );
        }

        if ($form['tag'] || $form['search']) {
            $blog_entries_per_page =
            $blog_entries_per_page_for_tags_and_search;
        }

        if (count($entries) > 0) {
            // Output one pagefull of entries
            foreach (array_slice($entries,
                                  $form['page'] * $blog_entries_per_page,
                                  $blog_entries_per_page) as $file) {
                $entry = entry_load($file);
                echo entry_format($entry,
                                   basename($file, ".xml"),
                                   $me,
                                   $blog_base_location,
                                   $blog_files_dir) . "\n";
            }

            /* Used below when generating links to different pages of search or
               tag lookup results. */
            $query_string = array();

            if ($form['tag']) {
                $query_string[] = "tag=" . $form['tag'];
            }

            if ($form['search']) {
                $query_string[] = "search=" . $form['search'];
            }

            // Show page navigation links if needed
            if (($form['page'] + 1) * $blog_entries_per_page <
count($entries)) {
                $query_string[] = "page=" . ($form['page'] + 1);
                echo "<a id=\"prevpagelink\" href=\"$me?",
                     implode("&", $query_string),
                     "\">$loc_prev_page</a>";
            }

            // If there are more entries to display
            if ($form['page'] > 0) {
                $query_string[] = "page=" . ($form['page'] - 1);
                echo "<a id=\"nextpagelink\" href=\"$me?",
                implode("&", $query_string), "\">$loc_next_page</a>";
            }
        } else {
            echo $loc_no_matches;
        }
    }

    echo "</div><!-- #content -->\n";

    // A mostly-HTML footer
    blog_footer();

    if ($blog_caching_enabled) {
        $cache_file_out = fopen($cache_file_name, 'w');
        fwrite($cache_file_out, "<!-- $cache_id -->\n");
        fwrite($cache_file_out, ob_get_contents());
        fclose($cache_file_out);
        ob_end_flush();
    }
}
?>
