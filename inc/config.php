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

/** @file config.php
*   @brief Reads the configuration from config.xml into $blog_option_name variables.
*/

//! The current version of Drukkar version
$version = "1.09";

$blog_settings = simplexml_load_file("config.xml");
#var_dump($blog_settings);

$blog_entries_dir = (string) $blog_settings->entries_dir;
$blog_files_dir = (string) $blog_settings->files_dir;
$blog_title = (string) $blog_settings->title;
$blog_subtitle = (string) $blog_settings->subtitle;
$blog_password = (string) $blog_settings->password;
$blog_salt = (string) $blog_settings->salt;
$blog_session_length = (int) $blog_settings->session_length;
$blog_entries_per_page = (int) $blog_settings->entries_per_page;
$blog_entries_per_page_for_tags_and_search = (int) $blog_settings->entries_per_page_for_tags_and_search;
$blog_locale = (string) $blog_settings->locale;
$blog_entry_links_with_titles = (bool) (int) $blog_settings->entry_links_with_titles;
$blog_date_format = (string) $blog_settings->date_format;
$blog_entry_date_from_file_name = (bool) (int) $blog_settings->entry_date_from_file_name;
$blog_file_name_format = (string) "YmdHis";
$blog_search_enabled = (bool) (int) $blog_settings->search_enabled;
$blog_show_dates = (bool) (int) $blog_settings->show_dates;
$blog_base_location = (string) $blog_settings->base_location;
$blog_base_url = (string) $blog_settings->base_url;
$blog_time_zone = (string) $blog_settings->time_zone;

date_default_timezone_set($blog_time_zone);

include("loc_$blog_locale.php");

?>
