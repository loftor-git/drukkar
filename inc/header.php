<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?php echo $blog_title; ?></title>
    <style type="text/css" media="all">
        @import "blog.css";
    </style>
</head>
<body>
<div id="container"><table id="main"><tr><td id="header"><h1 id="title"><a href="<?php echo $me; ?>"><?php echo $blog_title; ?></a></h1><?php if (strlen($blog_subtitle) > 0) echo "<p id=\"subtitle\">$blog_subtitle</p>"; ?></td></tr>