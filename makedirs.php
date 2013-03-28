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

/** @file makedirs.php
*   @brief Creates directories for files, entries and cache.
*
*   This is useful when you can't chmod/chown the directries to make them writable for your web server's *nix user.
*/

//! What this file is called.
$me = "makedirs.php";

header('Content-type: text/html; charset=utf-8');

include("inc/config.php");
include("inc/lib.php");

// A mostly-HTML header
include("inc/header.php");

$form_post = array("password", "salt");

process_form($form_post, $_POST);

echo "<tr><td id=\"content\">";

echo "<h1>Creating directories...</h1><p>";

$dirs = array($blog_files_dir, $blog_entries_dir, $blog_cache_dir);

$t = true;

foreach ($dirs as $dir) {
    echo "<br>Creating <tt>$dir</tt>...<br>";
    $t &= mkdir($dir, 0755);
}

if ($t) {
    echo "Success! Once you've placed a correct <tt>.htaccess</tt> in each directory and made sure that everything works don't forget to remove <tt>$me</tt> from your server.";
}

echo "</td></tr>";

include("inc/footer.php");


?>
