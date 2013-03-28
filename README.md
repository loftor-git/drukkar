Drukkar
=======

Drukkar is a small blogging software program and CMS made to work with
limited server resources and limited bandwidth. It was developed with the
following in mind:

*   minimum page overhead; the content should account for most of your web
traffic (this saves you bandwidth and enables access to your blog over slow
connections like GPRS and Tor)
*   working without a database (with a little fiddling it can be installed in
`public_html` on that university server account you have)
*   ease of releasing files with your posts (useful for government
organizations with their PDF forms [1] or game dev blogs)

The posts can be written in HTML,
[Markdown](https://en.wikipedia.org/wiki/Markdown) or plain text and are stored
as XML files one post per file. caching. Drukkar is self-contained and only depends on
PHP&nbsp;5 on your server. It has built-in caching. Pick up the documentation
and see it in action at <http://drukkar.sourceforge.net/>.

The project is hosted at <https://sourceforge.net/projects/drukkar/>
([code](https://sourceforge.net/p/drukkar/code/)) and
<https://github.com/dbohdan/drukkar>.

[1] It was developed for one.
