<?php

function blog_footer() {
    echo "<div id=\"footer\">Powered by <a ",
         "href=\"http://drukkar.sourceforge.net/\">Drukkar</a> ",
         $GLOBALS['version'], "&nbsp;~&nbsp;<a href=\"rss.php\">RSS</a>",
         "<br><div style=\"margin-top: 0.75em;margin-right: -5px;\">",
         "<a href=\"http://sourceforge.net/projects/drukkar\">",
         "<img src=\"/sflogo.png\" alt=\"Get Drukkar at ",
         "SourceForge.net. Fast, secure and Free Open Source software " ,
         "downloads\"></a>&nbsp;&nbsp;&nbsp;<a " ,
         "href=\"http://validator.w3.org/check?uri=referer\">" ,
         "<img src=\"http://www.w3.org/Icons/valid-html401\" " ,
         "alt=\"Valid HTML 4.01 Strict\" height=\"31\" width=\"88\"></a>",
         "&nbsp;&nbsp;&nbsp;",
         "<a href=\"http://jigsaw.w3.org/css-validator/check/referer\">",
         "<img style=\"border:0;width:88px;height:31px\" ",
         "src=\"http://jigsaw.w3.org/css-validator/images/vcss\"",
         "alt=\"Valid CSS!\"></a>",
         "</div>",
         "</div><!-- #footer -->\n",
         "</div><!-- #container -->\n</body>\n</html>";
}

?>
