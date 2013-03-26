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

/** @file footer.php
*   @brief Blog footer. Contains what is displayed at the bottom of the page beneath the blog entries and closes the HTML document.
*/

echo <<<ENDFOOTER
<tr><td id="footer">Powered by <a href="http://drukkar.sourceforge.net/">Drukkar</a> $version&nbsp;~&nbsp;<a href="rss.php">RSS</a></td></tr></table></div>
</body>
</html>
ENDFOOTER

?>
