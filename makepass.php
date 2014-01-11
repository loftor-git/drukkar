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

/** @file makepass.php
 *  @brief Generates salted passwords for use in config.xml.
 *
 *  Remove this from your server one you've set the password.
 */

//! What this file is called.
$me = "makepass.php";

header('Content-type: text/html; charset=utf-8');

include("inc/config.php");
include("inc/lib.php");

include("inc/header.php");
include("inc/footer.php");

// A mostly-HTML header
blog_header();

$form_post = array("password", "salt");

process_form($form_post, $_POST);

echo "<div id=\"content\">
<h2>$loc_makepass_warning</h2>
<div><form action=\"${blog_base_location}$me\" method=\"POST\">
<table>
<tr><td>$loc_password:</td>
<td><input type=password name=password></td></tr>
<tr><td>$loc_salt:</td>
<td><input type=text name=salt></td></tr>
<tr><td><input type=submit value=\"$loc_submit\"></td></tr>
</table></form></div>";

if ($form_post['password']) {
        echo "<hr><p>$loc_put_this_in_config_file</p><pre>&lt;password&gt;",
             hash_with_salt($form_post['password'], $form_post['salt']),
             "&lt;/password&gt;\n&lt;salt&gt;", $form_post['salt'],
             "&lt;/salt&gt;</pre>";
}

echo "</div><!-- #content -->\n";

blog_footer();


?>
