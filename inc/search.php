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

/** @file search.php
 *  @brief Displays a search bar (if enabled) and custom search or menu.
 *
 *  Put the relevant code here if you want to custom search features
 *  (like Site Search) or a menu to be displayed above the enties in your blog.
 */

/** @brief Outputs the search bar and custom search/menu, if any.
 */
function blog_search_form() {
    if ($GLOBALS['blog_search_enabled']) {
        echo '<div id="search"><form action="index.php">',
             '<div id="searchform"><input type="text" name="search" ',
             'id="searchfield" size=50>&nbsp;<input type="submit" ',
             'class="button" id="searchbutton" value="',
              $GLOBALS['loc_search'], "\"></div><!-- #searchform -->",
              "</form></div><!-- #search -->\n";
    }
    /* Custom search code goes here, e.g., Google website search. This is 
       also a good place to put a menu with links to your _excluded pages
       (e.g., "About me" and "Contacts"). */
    echo <<<CUSTOMSEARCH
    <a href="/">Home</a>aaaa
CUSTOMSEARCH;

}

?>
