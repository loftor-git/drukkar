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

/** @file search.php
*   @brief Displays a search bar (if enabled) and custom search or menu.
*
*   Put the relevant code here if you want to custom search features (like Site Search) or a menu to be displayed above the enties in your blog.
*/

if ($blog_search_enabled == 1) {
    echo '<tr><td id="search"><form action="index.php"><div id="searchform"><input type="text" name="search" size=50><input type="submit" value="' . $loc_search . '"></div></form></td></tr>';
} else {
    // Custom search code goes here, e.g., Google website search. Either that or nothing to only have built-in search.
    echo <<<CUSTOMSEARCH

CUSTOMSEARCH;
}
?>
