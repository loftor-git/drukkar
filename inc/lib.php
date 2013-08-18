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

/** @file lib.php
 *  @brief A library of functions shared accross many components of Drukkar.
 */

/** @brief Puts each item of the of $list in $tag and concatenates the result.
 *  @param array $list a list of items to process
 *  @return string
 */
function list_to_xml($list, $tag) {
    return list_to_string($list, "<$tag>", "</$tag>");
}

/** @brief Concatenate $list, placing each item between $left and $right
 *
 *  This function returns a string that contains of every item on $list,
 *  each enclosed between $left and $right, which are presumed to contain an
 *  opening and a closing XML tag respectively.
 *
 *  @param array $list a list of items to process
 *  @param string $left the opening tag to put on the left of each item
 *  @param string $right the closing tag to put on the right of each item
 *  @return string
 */
function list_to_string($list, $left, $right) {
    $result = "";
    
    foreach (explode("\r\n", rtrim($list)) as $i) {
        if (strlen($i) > 0)
            $result = $result . $left . htmlspecialchars($i) . $right;
    }
    
    return $result;
}

/** @brief Returns all the files linked to by an entry
 *  @param object $entry entry as a SimpleXMLElement object
 *  @return array
 */
function entry_files($entry) {
    $files = array();
    
    foreach ($entry as $key => $value)
        if (stripos(strtolower($key), "file") === 0)
            $files[] = $value;

    return $files;
}

/** @brief Check whether an entry has a specific tag
 *  @param string $tag the tag to check for
 *  @param array $entry entry as a SimpleXMLElement object
 *  @return boolean
 */
function entry_check_tag($tag, $entry) {
    $found = false;
    foreach ($entry->tag as $key => $value)
        if ((string) $value === (string) $tag)
            $found = true;

    return $found;
}

/** @brief Formats an entry for output
 *  @param object $entry entry as a SimpleXMLElement object
 *  @param string $entry_id the given entry's id that's used for linking to it
 *  @param string $link_target target for the link to view this particular
 *                             entry
 *  @return string
 */
function entry_format($entry, $entry_id, $link_target = "index.php",
                       $base_dir = "/", $files_dir = "files/") {
    $files = "";
    $tags = "";
    
    foreach ($entry->file as $file) {
        $files = $files . "<a href=\"" . $base_dir . $files_dir .
                 urlencode($file) . "\">" . htmlspecialchars($file) .
                 "</a><br />";
    }
    
    foreach ($entry->tag as $tag) {
        if (!((string) $tag === "_excluded" || (string) $tag === "_hidden"))
            $tags = $tags . "<a href=\"index.php?tag=" . urlencode($tag) .
                    "\">" . htmlspecialchars($tag) . "</a>, ";
    }
    $tags = substr($tags, 0, -2);
    
    return "<div class=\"blogentry\">" .
            "<h2 class=\"entrytitle\"><a class=\"titlelink\" href=\"" .
            "$link_target?post=" . htmlspecialchars($entry_id) .
             ($GLOBALS['blog_entry_links_with_titles'] ? "-" .
              sanitize_file_name(strip_tags($entry->title)) : "") . "\">" .
            ((string) $entry->format === "html" ? $entry->title :
            htmlspecialchars($entry->title)) .
            "</a></h2>\n<div class=\"text\">" . 
            ((string) $entry->format === "html" ? $entry->text :
            ((string) $entry->format === "markdown" ?
            Markdown($entry->text) : htmlspecialchars($entry->text))) . 
            "</div><!-- .text -->\n<p class=\"files\">$files</p>" .
            ($GLOBALS['blog_show_dates'] ?
            "<p class=\"date\">" . date($GLOBALS['blog_date_format'],
            (int) $entry->date) . "</p>" : "") . (strlen($tags) != 0 ?
            "<p class=\"tags\">Tags: $tags</p>" : "") .
            "</div><!-- .blogentry -->";
}

/** @brief Sanitize a file name replacing special symbols with dashes
 *  @param string $string the string to be sanitized
 *  @return string
 */
function sanitize_file_name($string, $language = "ukrainian") {
    $string = preg_replace('/[^\w\-~_\.]+/u', '-',
                           transliterate(mb_strtolower($string, 'UTF-8'),
                                         $language));

    return preg_replace('/--+/u', '-', $string); # Compress repeating dashes.
}

/** @brief Transliterate Cyrillic text into English Latin script
 *  @param string $string the string to be transliterated
 *  @param string $language chooses between transliteration from Ukrainian
 *  and from Russian
 *  @return string
 */
function transliterate($string = '', $language = "ukrainian") {
    $ukrainian = (string) $language === "ukrainian";
    $string=strtr($string, array(" є" => " ye",
                                  " ї" => " yi",
                                  " ю" => " yu", 
                                  " я" => " ya",
                                  " й" => " y",
                                  "є" => "ie",
                                  "ї" => "i",
                                  "ю" => ($ukrainian ? "iu" : "yu"), 
                                  "я" => ($ukrainian ? "ia" : "ya"),
                                  "й" => ($ukrainian ? "i" : "y"), 
                                  "ё" => "io",
                                  "ье" => "ye",
                                  "ж" => "zh",
                                  "х" => "kh",
                                  "ц" => "ts",
                                  "ч" => "ch",
                                  "ш" => "sh",
                                  "щ" => "shch",
                                  "ь" => "",
                                  "ъ" => "", 
                                 ));
    
    $string=strtr($string, array("а" => "a",
                                  "б" => "b",
                                  "в" => "v",
                                  "г" => ($ukrainian ? "h" : "g"),
                                  "ґ" => "g",
                                  "д" => "d",
                                  "е" => "e",
                                  "з" => "z",
                                  "и" => ($ukrainian ? "y" : "i"),
                                  "і" => "i",
                                  "к" => "k",
                                  "л" => "l",
                                  "м" => "m",
                                  "н" => "n",
                                  "о" => "o",
                                  "п" => "p",
                                  "р" => "r",
                                  "с" => "s",
                                  "т" => "t",
                                  "у" => "u",
                                  "ф" => "f",
                                  "ы" => "y",
                                  "э" => "e",
                                 ));
    
    return $string;
}

/** @brief Handle newly-uploaded files in a uniform way
 *  @param array $uploaded_files a $_FILES construction to be processed
 *  @param string $translit_language transliteration language
 *  @param string $directory target directory
 *  @return array
 */
function process_uploaded_files($uploaded_files,
                                 $translit_language = 'ukrainian',
                                 $directory = 'files/') {
    $files = ""; // File list in XML
    foreach ($uploaded_files as $file) {
        if ($file['error'] !== 4) { /* If a file was actually uploaded... */
            /* ...strip the the file name of all special symbols and
            transliterate Cyrillic into Latin script. */
            $file_name_sanitized = htmlspecialchars(basename(
                                   sanitize_file_name($file['name'],
                                                      $translit_language)));
            if (!file_exists($directory . $file_name_sanitized)) {
                if (move_uploaded_file($file['tmp_name'],
                                       $directory . $file_name_sanitized)) {
                    printf($GLOBALS['loc_file_uploaded'] . "<br>",
                           $file['name'], $file_name_sanitized);
                    $files = $files . "<file name=\"" . $file['name'] .
                             "\">$file_name_sanitized</file>";
                } else {
                    printf("<span class=\"error\">" .
                           $GLOBALS['loc_uploading_failed'] . "</span><br>",
                           $file['name']);
                }
            } else {
                printf("<span class=\"error\">" .
                       $GLOBALS['loc_uploading_failed_file_exists'] .
                       "</span><br>",
                       htmlspecialchars($file['name']));
            }
        }
        
    }
    return $files;
}

/** @brief Convert a date represented as a formatted string into a UNIX
 *  timestamp
 *  @param string $format the format that the date is in
 *  @param string $date_to_process self-explanatory
 *  @param mixed $result_on_error the result to return should the coversion
 *  fail
 *  @return int
 */
function string_to_time($format, $date_to_process, $result_on_error = false) {
    $d = date_parse_from_format($format, $date_to_process);
    if ($d['errors']) {
        return $result_on_error;
    }
    return mktime($d['hour'],
                   $d['minute'],
                   $d['second'],
                   $d['month'],
                   $d['day'],
                   $d['year']);
}

/** @brief Creates a new, blank blog entry object
 *  @return object
 */
function entry_new() {
    $entry = new stdClass();
    $entry->title = "";
    $entry->text = "";
    $entry->tag = array();
    $entry->file = array();
    $entry->format = "plain";
    return $entry;
}

/** @brief Load the blog entry from an XML file
 *  @param string $file file name
 *  @return object
 */
function entry_load($file) {
    $entry = simplexml_load_file($file);
    
    if ($GLOBALS['blog_entry_date_from_file_name']) {
        // Create a back-up copy from the date stored within the file itself
        $entry->old_date = $entry->date;
        $entry->date = string_to_time($GLOBALS['blog_file_name_format'],
                                      basename($file, ".xml"));
    } else {
        if (!$entry->date)
            $entry->date = filemtime($file);       
    }
    $entry->date = (int) $entry->date;
   
    return $entry;
}

/** @brief Save a blog entry to an XML file
*
 *  Returns the result of calling file_put_contents.
*
 *  @param string $file_name file name
 *  @param string $format "html", "markdown" or "plain" for plain text
 *  @param string $title
 *  @param string $text
 *  @param string $tags
 *  @param string $date
 *  @param string $date_backup used when $date is malformatted or absent
 *  @return int
 */
function entry_save($file_name, $format, $title, $text, $tags, $files, $date,
                     $date_backup) {
    $processed_date = string_to_time($GLOBALS['blog_date_format'], $date);
    if ($processed_date === false &&
    !$GLOBALS['blog_entry_date_from_file_name']) {
        $processed_date = string_to_time($GLOBALS['blog_date_format'],
                                         $date_backup);
        printf("<span class=\"error\">" . $GLOBALS['loc_invalid_date'] . 
               "</span><br>",
               htmlspecialchars($date),
               htmlspecialchars($date_backup));
    }

    return file_put_contents($file_name,
"<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<entry>
<format>$format</format>
<title>" . htmlspecialchars($title) . "</title>
<text>" . htmlspecialchars($text) . "</text>
<date>" . $processed_date . "</date>
" . $files . "
" . $tags . "
</entry>");
}

/** @brief Put _GET or _POST data into the appropriate fields of $form.
*
 *  Fields in $form for which there is no data in $get_or_post are filled with
 *  (bool) false.
*
 *  @param array &$form The form to fill
 *  @param array $get_or_post _GET or _POST data
 *  @return array
 */
function process_form(&$form, $get_or_post) {
    $form_new = array();
    foreach ($form as $var) {
        $form_new[$var] = ((isset($get_or_post) &&
                            array_key_exists($var, $get_or_post)) ?
                            $get_or_post[$var] : false);
    }
    $form = $form_new;
}


/** @brief Returns a salted cryptographic hash of a given password
 *  @param string $pass the password to hash
 *  @param string $salt the cryptographic salt
 *  @return string
 */
function hash_with_salt($pass, $salt = "") {
    return md5(md5($pass) . $salt);
}

/** @brief Check if a cache file is newer than files used to generate output.
*
 *  This returns whether the file is newer than *.php, inc/*.php, entries/*.xml
 *  and config.xml.
*
 *  @param string $cache_file_name cache file name
 *  @return boolean
 */
function cache_is_current($cache_file_name) {
    if (!file_exists($cache_file_name)) {
        return false;
    }

    $cache_mtime = filemtime($cache_file_name);
    foreach (array_merge(glob("*.php"),
                          glob("inc/*.php"),
                          glob($GLOBALS['blog_entries_dir'] . "*.xml"),
                          array("config.xml")) as $file) {
        if (filemtime($file) > $cache_mtime) {
            return false;
        }
    }

    return true;
}

/** @brief Convert file size to human-readable format.
 *
 *  The result is a string with the size in bytes or KiB to PiB and unit name.
 *
 *  @param int $size
 *  @param int $decimals
 *  @return string
 */
function human_readable_file_size($size, $decimals = 2) {
    $units = array($GLOBALS['loc_bytes'],
                    $GLOBALS['loc_kib'],
                    $GLOBALS['loc_mib'],
                    $GLOBALS['loc_gib'],
                    $GLOBALS['loc_tib'],
                    $GLOBALS['loc_pib']); // Future proofing!
    if ($size < 1024) {
        return "$size ${units[0]}";
    } else {
        $factor = min(floor(log((double) $size, 1024)), count($units));
        return sprintf("%.{$decimals}f", $size / pow(1024, $factor)) . " " .
                $units[$factor];
    }
}

/** @brief Try to get the *nix user name by UID.
 *  @param int $uid
 *  @return string
 */
function user_name_from_uid_safe($uid) {
    if (function_exists('posix_getpwuid')) {
        $u = posix_getpwuid($uid);
        return $u['name'];
    }  elseif (is_readable('/etc/passwd')) {
        exec(sprintf('grep :%d: /etc/passwd | cut -d: -f1', (int) $uid),
             $output, $retcode);
        if ($retcode == 0) {
            return trim($output[0]);
        }
    }
    
    return $uid;
}

/** @brief Try to get the *nix group name by GID.
 *  @param int $gid
 *  @return string
 */
function group_name_from_gid_safe($gid) {
    if (function_exists('posix_getgrgid')) {
        $g = posix_getgrgid($gid);
        return $g['name'];
    }  elseif (is_readable('/etc/group')) {
        exec(sprintf('grep :%d: /etc/group | cut -d: -f1', (int) $gid),
             $output, $retcode);
        if ($retcode == 0) {
            return trim($output[0]);
        }
    }
    
    return $gid;
}
?>
