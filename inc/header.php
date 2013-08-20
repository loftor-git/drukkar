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

/** @file header.php
 *  @brief Blog header file. Opens the HTML document.
 */

/** @brief Opens the HTML document, output <head> and start page layout table.
 *  @param $page_title what to put in <title>
 */
function blog_header($page_title = "") {

    if (!isset($page_title)) {
        $page_title = $GLOBALS['blog_title'];
    }

    echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" ",
         "\"http://www.w3.org/TR/html4/strict.dtd\">\n",
         "<html>\n<head>\n",
         "<meta http-equiv=\"Content-Type\" content=\"text/html; ",
         "charset=UTF-8\">\n",
         "    <link rel=\"shortcut icon\" href=\"favicon.ico\">\n",
         "    <title>$page_title</title>\n",
         "    <style type=\"text/css\" media=\"all\">\n",
         "        @import \"blog.css\";\n",
         "    </style>\n",
         "</head>\n",
         "<body>\n",
         "<div id=\"container\">\n",
         "<div id=\"header\"><h1 id=\"title\"><a id=\"blogtitle\" href=\"",
         $GLOBALS['blog_base_location'],
         "\">${GLOBALS['blog_title']}</a></h1>";

    if (strlen($GLOBALS['blog_subtitle']) > 0) {
        echo "<p id=\"subtitle\">${GLOBALS['blog_subtitle']}</p>";
    }

    echo "</div><!-- #header -->\n";
}

?>
