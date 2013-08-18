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
 *  @brief File manager for uploaded files and entries' XML files.
 */

header('Content-type: text/html; charset=utf-8');

session_start();

// This helps prevent session fixation attacks
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

//! What this file is called.
$me = "files.php";

include("inc/config.php");
include("inc/lib.php");

include("inc/header.php");
include("inc/footer.php");

// The header. It's mostly HTML with little logic.
blog_header();

echo "<div id=\"content\">\n";

$form_post = array('password', 'action', 'argument', 'translit', 'file');
$form_get = array('dir');

process_form($form_post, $_POST);
process_form($form_get, $_GET);

$form_post['password'] = htmlspecialchars($form_post['password']);

/* Here we define the directories that will be accessable via web interface.
The format is "displayed_name" => "actual_location". */
$dirs = array("$blog_files_dir" => $blog_files_dir,
               "$blog_entries_dir" => $blog_entries_dir,
               "$blog_cache_dir" => $blog_cache_dir); 

if (in_array($form_get['dir'], $dirs))
    $directory = $dirs[$form_get['dir']];
else
    $directory = $blog_files_dir;

if ((hash_with_salt($form_post['password'], $blog_salt) === $blog_password) 
&& !isset($_SESSION['is_logged_in'])) {
    $_SESSION['is_logged_in'] = true;
    $_SESSION['created'] = time();
}

if (array_key_exists('logout', $_GET)) {
    session_unset(); 
    session_destroy(); 
}

// Expire user's session after a period of inactivity
if (isset($_SESSION['last_activity']) &&
time() - $_SESSION['last_activity'] > $blog_session_length) { 
    session_unset(); 
    session_destroy(); 
    echo "$loc_session_expired";
}

echo <<< END
<script type="text/javascript">
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

function fview(file) {
    document.form.action.value = "view";
    document.form.file.value = file;
    document.form.submit();
}

function fmtcode() {
    var preElements = document.getElementsByTagName("pre");
    for (p in preElements) {
        if (p >= 0) {
            var pre = preElements[p];
            var lines = pre.innerHTML.split('\\n');
            var newList = document.createElement('ol');
            newList.className = "source";
            for (line in lines) {
                listEl = document.createElement('li');
                listEl.className = (line % 2 == 0 ? 'even' : 'odd' ) + 'line';
                listEl.innerHTML = (lines[line] == "" ? "<br>" : lines[line]);
                newList.appendChild(listEl);
            }
            pre.parentNode.replaceChild(newList, pre);
        }
    }
}

window.addEventListener('load', fmtcode, false);
</script>

END;

if (isset($_SESSION['is_logged_in'])) {
    $_SESSION['last_activity'] = time();
    // Change session ID every 5 minutes
    if (time() - $_SESSION['created'] > 300) {
         session_regenerate_id(true);
         $_SESSION['created'] = time();
    }
  
    echo "<p><a id=\"logout\" href=\"$me?logout\">$loc_log_out</a>\n";    
    echo "<p>$loc_directories ";
    
    foreach ($dirs as $dir) {
        echo "<a href=\"$me?dir=$dir\">[ $dir ]</a> ";
    }
    echo "</p><br>\n";

    // Process the action that the user selected
    if ($form_post['action']) {
        $file_name = htmlspecialchars(basename($form_post['file']));
        if (file_exists($directory . $file_name)) {
            switch ($form_post['action']) {
                case "delete":
                    if(unlink($directory . $file_name)) {
                        printf($loc_file_deleted, $file_name);
                    }
                    break;
                case "rename":
                    $new_name = htmlspecialchars(basename(
                                $form_post['argument']));
                    if (rename($directory . $file_name,
                    $directory . $new_name)) {
                        printf($loc_file_renamed, $file_name, $new_name);
                    }
                    break;
                case "view":
                    echo "<h2>$file_name</h2><pre class=\"source\">\n",
                         htmlspecialchars(file_get_contents($directory .
                                                             $file_name)),
                         "</pre><hr>\n";
                    break;
            }
        } else {
            echo "<span class=\"error\">$loc_file_not_found ",
                 "$file_name.</span>";
        }
        echo "<br>\n";
    } else {
        // If there's no action we should process the uploaded files
        if (process_uploaded_files($_FILES, $form_post['translit'],
                                   $directory)) {
            echo "<br>\n"; // Line break after process_uploaded_files's message.
        }
        
    }
    
    echo "<form name=\"form\" action=\"$me",
         (htmlspecialchars($form_get['dir']) ?
          "?dir=" . htmlspecialchars($form_get['dir']) : ""),
         "\" method=\"post\" enctype=\"multipart/form-data\">\n";
    
    foreach (glob($directory . "*") as $file) {
        $file_stat = stat($file);
        $user_and_group = 
        $file_info = "<br>" . decoct($file_stat['mode']) . "&emsp;" .
                     user_name_from_uid_safe($file_stat['uid']) . "&emsp;" .
                     group_name_from_gid_safe($file_stat['gid']) . "&emsp;" .
                     human_readable_file_size($file_stat['size']) . "&emsp;" .
                     date($blog_date_format, $file_stat['mtime']);
        $file = basename($file);
        echo "<p><span class=\"actionbuttons\"><input type=\"button\" ",
             "onClick=\"javascript:fview('$file');\" value=\"$loc_view\">",
             "&nbsp;<input type=\"button\" ",
             "onClick=\"javascript:frename('$file');\" ",
             "value=\"$loc_rename\">&nbsp;<input type=\"button\" ",
             "onClick=\"javascript:fdelete('$file');\" value=\"$loc_delete\">",
             "</span><a href=\"$directory$file\">$file</a> ", $file_info,
             "</p>\n";
    }
    echo "<hr>\n<p>$loc_upload<br>\n",
         "<input type=\"file\" name=\"file1\"><br>\n",
         "<input type=\"file\" name=\"file2\"><br>\n",
         "<input type=\"file\" name=\"file3\"></p>\n",
         "<p>$loc_translit<br>",
         "<input type=\"radio\" name=\"translit\" value=\"russian\">",
         "&nbsp;$loc_russian <input type=\"radio\" name=\"translit\" ",
         "value=\"ukrainian\" checked>&nbsp;$loc_ukrainian</p>\n",
         "<p><input type=\"hidden\" name=\"file\">",
         "<input type=\"hidden\" name=\"action\">",
         "<input type=\"hidden\" name=\"argument\">",
         "<input type=\"submit\" name=\"submitbutton\" ",
         "value=\"$loc_submit\"></p>\n</form>";
} else { // The user isn't logged in.
    echo "<form name=\"form\" action=\"$me\" method=\"post\" ",
          "enctype=\"multipart/form-data\">\n",
          "<p>$loc_password<br><input type=password name=password ",
          "value=\"${form_post['password']}\"></p>\n",
          "<p><input type=\"submit\" name=\"submitbutton\" ",
          "value=\"$loc_log_in\"></p>\n</form>";
}

echo "</div><!-- #content -->\n";

// A mostly-HTML footer
blog_footer();

?>
