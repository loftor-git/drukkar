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

/** @file sidebar.php
 *  @brief Displays blog's sidebar.
 */

/** @brief Outputs the HTML code for the sidebar.
 */
function blog_sidebar() {
    if ($GLOBALS['blog_sidebar_enabled']) {
        echo <<<SIDEBAR
<div id="sidebar">
<h2>This is your sidebar</h2>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean convallis
nibh id ligula placerat, ut rutrum nibh adipiscing.</p>
</div><!-- #sidebar -->
SIDEBAR;
    }
}

?>
