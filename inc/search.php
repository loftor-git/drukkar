<?php
if ($blog_search_enabled == 1) {
    echo '<tr><td id="search"><form action="index.php"><div id="searchform"><input type="text" name="search" size=50><input type="submit" value="' . $loc_search . '"></div></form></td></tr>';
} else {
    // Custom search code goes here, e.g., Google website search. Either that or nothing to only have built-in search.
    echo <<<CUSTOMSEARCH

CUSTOMSEARCH;
}
?>