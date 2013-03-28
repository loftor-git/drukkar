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

/** @file files.php
*   @brief File manager for uploaded files and entries' XML files.
*/

header('Content-type: text/html; charset=utf-8');

session_start();

if (!isset($_SESSION['initiated'])) { // This helps prevent session fixation attacks
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

//! What this file is called.
$me = "files.php";

include("inc/config.php");
include("inc/lib.php");

// The header. It's mostly HTML with little logic.
include("inc/header.php");

echo '<tr><td id="content">';

$form_post = array('password', 'action', 'argument', 'translit', 'file');
$form_get = array('dir');

process_form($form_post, $_POST);
process_form($form_get, $_GET);

$form_post['password'] = htmlspecialchars($form_post['password']);

$dirs = array("$blog_files_dir" => $blog_files_dir, "$blog_entries_dir" => $blog_entries_dir, "$blog_cache_dir" => $blog_cache_dir); // Here we define the directories that will be accessable via web interface. The format is "displayed_name" => "actual_location".

if (in_array($form_get['dir'], $dirs))
    $directory = $dirs[$form_get['dir']];
else
    $directory = $blog_files_dir;

if ((hash_with_salt($form_post['password'], $blog_salt) === $blog_password) && !isset($_SESSION['is_logged_in'])) {
    session_regenerate_id(true);
    $_SESSION['is_logged_in'] = true;
    $_SESSION['created'] = time();
}

if (array_key_exists('logout', $_GET)) {
    session_unset(); 
    session_destroy(); 
}

if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $blog_session_length) { // Expire user's session after a period of inactivity
    session_unset(); 
    session_destroy(); 
    echo "$loc_session_expired";
}

echo <<< END
<script language="JavaScript" type="text/JavaScript">
function fdelete(file) {
if (confirm('$loc_delete_prompt_file'.replace('%s', file))) {
        document.form.action.value = "delete";
        document.form.file.value = file;
        document.form.submit();
    }    
}

function frename(file) {
    newname = prompt('$loc_rename_prompt', file);
    if (newname) {
        document.form.action.value = "rename";
        document.form.file.value = file;
        document.form.argument.value = newname;
        document.form.submit();
    }
}
</script>
END;

if (isset($_SESSION['is_logged_in'])) {
    $_SESSION['last_activity'] = time();
    if (time() - $_SESSION['created'] > 300) { // Change session ID every 5 minutes
         session_regenerate_id(true);
         $_SESSION['created'] = time();
    }
  
    echo "<p><a id=\"logout\" href=\"$me?logout\">$loc_log_out</a>";    
    echo "<p>$loc_directories ";
    
    foreach ($dirs as $dir) {
        echo "<a href=\"$me?dir=$dir\">[ $dir ]</a> ";
    }
    echo "</p><br>";

    if ($form_post['action']) { // Process the action that the user selected
        $file_name = htmlspecialchars(basename($form_post['file']));
        if (file_exists($directory . $file_name)) {
            switch ($form_post['action']) {
                case "delete":
                    if(unlink($directory . $file_name))
                        printf($loc_file_deleted, $file_name);
                    break;
                case "rename":
                    $new_name = htmlspecialchars(basename($form_post['argument']));
                    if (rename($directory . $file_name, $directory . $new_name))
                        printf($loc_file_renamed, $file_name, $new_name);
                    break;
            }
        } else {
            echo "<span class=\"error\">$loc_file_not_found $file_name.</span>";
        }
        echo "<br>";
    } else {
        if (process_uploaded_files($_FILES, $form_post['translit'], $directory))
            echo "<br>";// If there's no action we should process the uploaded files
        
    }
    
    echo "<form name=\"form\" action=\"$me" . (htmlspecialchars($form_get['dir']) ? "?dir=" . htmlspecialchars($form_get['dir']) : "") . "\" method=\"post\" enctype=\"multipart/form-data\">";
    
    foreach (glob($directory . "*") as $file) {
        $file = basename($file);
        echo "<p><span class=\"actionbuttons\"><input type=\"button\" onClick=\"javascript:frename('$file');\" value=\"$loc_rename\">&nbsp;<input type=\"button\" onClick=\"javascript:fdelete('$file');\" value=\"$loc_delete\"></span> <a href=\"$directory$file\">$file</a></p>";
    }
    echo "<hr><p>$loc_upload<br><input type=\"file\" name=\"file1\"><br><input type=\"file\" name=\"file2\"><br><input type=\"file\" name=\"file3\"></p>
    <p>$loc_translit<br><input type=\"radio\" name=\"translit\" value=\"russian\">&nbsp;$loc_russian <input type=\"radio\" name=\"translit\" value=\"ukrainian\" checked>&nbsp;$loc_ukrainian</p>   
    <p><input type=\"hidden\" name=\"file\"><input type=\"hidden\" name=\"action\"><input type=\"hidden\" name=\"argument\"><input type=\"submit\" name=\"submitbutton\" value=\"$loc_submit\"></p></form>";
} else { // The user isn't logged in.
    echo "<form name=\"form\" action=\"$me\" method=\"post\" enctype=\"multipart/form-data\">
    <p>$loc_password<br><input type=password name=password value=\"${form_post['password']}\"></p>
    <p><input type=\"submit\" name=\"submitbutton\" value=\"$loc_log_in\"></p></form>";
}

echo '</td></tr>';

// A mostly-HTML footer
include("inc/footer.php")

?>
