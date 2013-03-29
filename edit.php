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

/** @file edit.php
 *   @brief Blog entry editor.
 */

header('Content-type: text/html; charset=utf-8');

session_start();

// This helps prevent session fixation attacks
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

include("inc/config.php");
include("inc/lib.php");

include("inc/header.php");
include("inc/footer.php");

//! How this file is named.
$me = "edit.php";

// Blog header. It's mostly HTML with little logic.
blog_header();

echo "<tr><td id=\"content\">\n";

$form_post = array('password', 'title', 'date', 'date_backup', 'tags',
                    'format', 'files', 'translit', 'submit', 'text');
$form_get = array('file');

process_form($form_post, $_POST);
process_form($form_get, $_GET);

$form_post['date_backup'] = htmlspecialchars($form_post['date_backup']);
$file_to_edit = basename(htmlspecialchars($form_get['file']));

if ((hash_with_salt($form_post['password'], $blog_salt) === $blog_password)
&& !isset($_SESSION['is_logged_in'])) {
    session_regenerate_id(true);
    $_SESSION['is_logged_in'] = true;
    $_SESSION['created'] = time();
}

if (array_key_exists('logout', $_GET)) {
    session_unset(); 
    session_destroy(); 
}

if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] 
> $blog_session_length) { // Expire user's session after a period of inactivity
    session_unset(); 
    session_destroy(); 
    echo "$loc_session_expired";
}

if ($form_post['submit'] === $loc_delete) {
    unlink($blog_entries_dir . $file_to_edit);
    $file_to_edit = ""; // Go back to the entry list after deletion
}

if (isset($_SESSION['is_logged_in'])) {
    $_SESSION['last_activity'] = time();
    if (time() - $_SESSION['created'] > 300) {
        // Change session ID every 5 minutes
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
    
    echo "<p><a id=\"logout\" href=\"$me?logout\">$loc_log_out</a></p>\n";
    if (!$file_to_edit) {
        // If no file has been specified we list all entries the user can edit
        echo "<p><a href=\"$me?file=", date($blog_file_name_format),
              ".xml\">$loc_new</a></p>\n";
        foreach (array_reverse(glob($blog_entries_dir . "*.xml")) as $file) {
            $entry = entry_load($file);
            echo "<a href=\"$me?file=", basename($file), "\">",
                 date($blog_date_format, (int) $entry->date), "&emsp;" ,
                 ((string) $entry->format === "html" ? $entry->title :
                  htmlspecialchars($entry->title)), "</a><br>\n";
       }
    } else {
        $entry_exists = file_exists($blog_entries_dir . $file_to_edit);
        echo "<form name=\"form\" action=\"$me?file=", $file_to_edit,
             "\" method=\"post\" enctype=\"multipart/form-data\">";
        echo "<p><a href=\"$me\">$loc_back</a></p>";

        if ((string) $form_post['submit'] === $loc_save) {
            // Save the form that the user submitted to a file
            $uploaded_files = process_uploaded_files($_FILES,
                                                     $form_post['translit'], 
                                                     $blog_files_dir);
            if (entry_save($blog_entries_dir . $file_to_edit,
                           $form_post['format'],
                           $form_post['title'],
                           $form_post['text'],
                           list_to_xml($form_post['tags'], "tag"),
                           list_to_xml($form_post['files'],
                                       "file") . $uploaded_files,
                           $form_post['date'],
                           $form_post['date_backup'])) {
                echo $loc_edit_saved;
                $entry_exists = True;
            } else {
                echo "<span class=\"error\">$loc_saving_failed</span><br>";
            }
        }

        if ($entry_exists) {
            echo "<p><a href=\"", $blog_base_location, "index.php?post=",
                  basename($file_to_edit, ".xml"),
                  "\">$loc_view_entry</a></p>";
        }
        // Display the selected blog entry        
        if ($entry_exists) {
            $entry = entry_load($blog_entries_dir . $file_to_edit);
            $entry->format = htmlspecialchars($entry->format);            
        } else {
            $entry = entry_new();
        }
        $new_entry = !$entry_exists;
       
        echo "<p>$loc_format<br><input type=\"radio\" name=\"format\" ",
             "value=\"html\" ", ((string) $entry->format === "html" ?
             "checked" : ""),
             ">&nbsp;$loc_html <input type=\"radio\" name=\"format\" ",
             "value=\"markdown\" ",
             ((string) $entry->format === "markdown" ? "checked" : ""),
             ">&nbsp;$loc_markdown <input type=\"radio\" name=\"format\" ",
             "value=\"plain\" ",
             ((string) $entry->format === "plain" ? "checked" : ""),
             ">&nbsp;$loc_plain</p>",
             "<p>$loc_title<br><input type=\"text\" size=60 name=\"title\" ",
             "value=\"",
             htmlspecialchars($entry->title), "\"></p>",
             "<p>$loc_text<br><textarea rows=15 cols=60 name=\"text\">",
             htmlspecialchars($entry->text), "</textarea></p>";
        
        echo "<p>$loc_tags<br><textarea rows=10 cols=20 name=\"tags\">";
        if ($entry) { // Write out the in the appropriate text area.
            foreach ($entry->tag as $key => $value) {
                echo htmlspecialchars($value), "\n";
            }
        }
        echo "</textarea></p>";
        
        echo "<p>$loc_files<br><textarea rows=10 cols=60 name=\"files\">";            
        if ($entry)
            foreach ($entry->file as $key => $value)
                echo htmlspecialchars($value), "\n";
        echo "</textarea></p>";
        
        /* Storing UNIX time in a variable prevents different results each
        time we need current time below. */
        $time = time();
        if ($blog_entry_date_from_file_name) {
            if (isset($entry->old_date))
                echo "<input type=\"hidden\" name=\"date\" value=\"",
                     date($blog_date_format, (int) $entry->old_date), "\">";
        } else {
            echo "<p>$loc_date<br><input type=\"text\" size=20 ",
                 "name=\"date\" value=\"",
                 date($blog_date_format,
                      ($new_entry ? $time : (int) $entry->date)),
                 "\"><input type=\"hidden\" name=\"date_backup\" value=\"",
                 date($blog_date_format, 
                      ($new_entry ? $time : (int) $entry->date)), "\"></p>";
        }
        
        echo <<<ENDFORM
<p>$loc_upload<br><input type="file" name="file1"><br>
<input type="file" name="file2"><br>
<input type="file" name="file3"></p>
<p>$loc_translit<br>
<input type="radio" name="translit" value="russian">&nbsp;$loc_russian
 <input type="radio" name="translit" value="ukrainian" checked>&nbsp;
$loc_ukrainian</p>
<p><input type="submit" name="submit" value="$loc_save">
<input type="submit" name="submit" value="$loc_delete"
 onClick="javascript:return confirm('$loc_delete_prompt_entry');"></p>
</form>
ENDFORM;
  
        }
} else {
    echo <<<ENDAUTHFORM
<form name="form" action="$me" method="post"
 enctype="multipart/form-data">
<p>$loc_password<br>
<input type="password" name="password" value=""></p>
<p><input type="submit" name="submit" value="$loc_log_in"></p>
</form>
ENDAUTHFORM;

}

echo "</td></tr>\n";

// A mostly-HTML footer
blog_footer();

?>
