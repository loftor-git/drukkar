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

/** @file footer.php
 *  @brief Blog footer. Contains what is displayed at the bottom of the page
 *  (underneath the blog entries) and closes the HTML document.
 */

/** @brief Closes the layout table and the HTML document.
 */
function blog_footer() {
    echo "<div id=\"footer\">Powered by <a ",
         "href=\"http://drukkar.sourceforge.net/\">Drukkar</a> ",
         $GLOBALS['version'], "&nbsp;~&nbsp;<a href=\"rss.php\">RSS</a>",
         "</div><!-- #footer -->\n",
         "</div><!-- #container -->\n</body>\n</html>";
}

?>
