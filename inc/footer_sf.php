<?php

function blog_footer() {
    echo "<tr><td id=\"footer\">Powered by <a ",
         "href=\"http://drukkar.sourceforge.net/\">Drukkar</a> ",
         $GLOBALS['version'], "<br><p><a href=\"http://sourceforge.net/" .
         "projects/drukkar\"><img src=\"/sflogo.png\" alt=\"Get Drukkar at " .
         "SourceForge.net. Fast, secure and Free Open Source software " .
         "downloads\"></a>&nbsp;&nbsp;&nbsp;<a " .
         "href=\"http://validator.w3.org/check?uri=referer\">" .
         "<img src=\"http://www.w3.org/Icons/valid-html401\" " .
         "alt=\"Valid HTML 4.01 Strict\" height=\"31\" width=\"88\"></a></p>",
         "</td></tr>\n", "</table>\n</div>\n</body>\n</html>";
}

?>
