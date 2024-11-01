=== Sociable Zyblog Edition ===
Contributors: TimZ
Donate link: http://www.kiva.org/invitedby/tim5156
Tags: sociable, social, bookmark, bookmarks, bookmarking, social bookmarking, social bookmarks, posts
Requires at least: 2.8
Tested up to: 3.4.1
stable tag: 2.0.14
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automatically add links on your posts to popular social bookmarking sites.

== Description ==

This plugin automatically add links on your posts to popular social bookmarking sites.
It is an enhanced version of Peter Harkins Sociable plugin.

= Supported Bookmarking Services =

__*100 social bookmarking services*__ are included! Supported services are:

Ask, BarraPunto, BlinkList, Bloglines, Blogosphere News, Blogsvine, Book.mark.hu, Colivia, DZone, De.lirio.us, Design Float, Digg, DotNetKicks, Facebook, Fark, Folkd, Furl, Gamebuzz, Global Grind, Google Bookmarks, Gwar, Haohao, HealthRanker, Hemidemi, Hype, Internetmedia, Kirtsy, Klickts, LinkaGoGo, Linkarchiv, Linkarena, LinkedIn, Linkter, Live-MSN, Lufee, Meneame, MisterWong, Mixx, MyShare, MySpace, MyTagz, N4G, Netscape, Netselector, Netvouz, NewsVine, Newsrider, NuJIJ, Oneview, Print, Ratimarks, Readster, Reddit, Rojo, SEOigg, SalesMarks, Scoopeo, Segnalo, Simpy, Slashdot, Smarking, Social-Bookmarking.dk, Socializer, Socialogs, SphereIt, Squidoo, StumbleUpon, Technorati, ThisNext, Twitter, Upnews, VoteForIt, Webbrille, Weblinkr, Webnews, Webride, Wikio, Wikio DE, Wikio ES, Wikio FR, Wikio IT, Wikio UK, Wists, Wykop, XING, Xerpi, Y!GG, YahooBuzz, YahooMyWeb, blogmarks, blogtercimlap, co.mments, connotea, del.icio.us, eKudos, email, feedmelinks, newskick, scuttle, seekXL

== Installation ==

* Install via the admin interface
* If you upload manually by FTP, make sure you upload useing FTP 'binary' mode.

= Advanced Users: =

Sociable hooks `the_content()` and `the_excerpt()` to display without requiring theme editing. To heavily customize the display, use the admin panel to turn off the display on all pages, then add calls to your theme files:

`// This is optional extra customization for advanced users`
`<?php print sociable_html(); ?> // all active sites?`
`<?php print sociable_html(Array("Reddit", "del.icio.us")); ?> // only these sites if they are active`

== Changelog ==

= 2.0.14 =
* 16.07.12 Cleanup: removed 33 no longer existing bookmark sites and fixed other sites; code fixes: removed deprecated code and restructured the files; Now requires at least WordPress Version 2.8 or later

= 2.0.13 =
* 25.07.11 Added Xing; small structure change; small internal updates

= 2.0.12 =
* 11.01.09 Added MyTagz and small fix for Scuttle

= 2.0.11 =
* 11.11.08 Fixes and Updates; 3 new services, now 132 services

= 2.0.10 =
* 14.10.08 Bugfix; StumbleUpon button is working again

= 2.0.9 =
* 16.08.08 31 new services; a little code cleanup

= 2.0.8 =
* 18.05.08 initial release on the wordpress plugin directory; renamed the plugin to sociable-zyblog-edition

= 2.0.7 =
* 09.02.08 some icons caused a validation error

= 2.0.6 =
* 29.12.07 twenty new services

= 2.0.5 =
* 25.09.07 new service lufee.de, removed update function, bugfix in sociable.css

= 2.0.4 =
* 22.08.07 new service: VoteForIt and rel="nofollow" set for all generated links

= 2.0.3 =
* 24.07.07 new service: SeekXL

= 2.0.2 =
* Base version was 2.0.2 of Peter Harkins Sociable plugin.
* Changes:
 * Added much more services**
 * Added the images for the services above and added the filenames to the required files section in the sociable source code.
 * Resized old images to be 16x16 pixel. Now every combination of services will show up as a clean line of icons.
 * Compression of the larger images to reduce the filesize. Now all images are below 1 kb.
 * Added new keywords to the service descriptions `RAW_TITLE` and `RAW_PERMALINK` can be used now for services which require these strings not to be encoded. For example Bloglines, Folkd and MSN-Live.


== Upgrade Notice ==

= 2.0.14 =
Bugfix & Maintenance update; internal cleanup, no functional changes; install this update, if you want to get rid of the outdated working bookmark sites; Now requires WordPress Version 2.8 or later!