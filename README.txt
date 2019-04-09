=== WP Request ===
Contributors:hashtagerrors
Tags: isGet, isPost, isSecure, getPath, getUrl, getSegments, getSegment, getFirstSegment, getLastSegment, getParam, getQuery, getPost, getServerName, isMobileBrowser, getHostInfo, getScriptUrl, getPathInfo, getRequestUri, getServerPort, getUrlReferrer, getUserAgent, getUserHostAddress, getUserHost, getPort, getQueryString, getQueryStringWithoutPath, getIpAddress, getClientOS
Requires at least: 4.6
Tested up to: 5.1.1
Stable tag: 1.0.0
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Get all sorts of info about the current request from WP Request.

== Description ==

The plugin has following shortcodes available :

* [wph_request_isGet]

Returns whether this is a GET request.

* [wph_request_isPost]

Returns whether this is a POST request.

* [wph_request_isDelete]

Returns whether this is a DELETE request.

* [wph_request_isPut]

Returns whether this is a PUT request.

* [wph_request_isAjax]

Returns whether this is an Ajax request.

* [wph_request_isSecure]

Returns whether this is a secure connection.

* [wph_request_getScriptName]

Returns the script name used to access Index page.

* [wph_request_getPath]

Returns the request's URI.

* [wph_request_getUrl]

Returns the request's full URL.

* [wph_request_getSegments]

Returns all URI segments.

* [wph_request_getSegment num="segment_number"]

Returns a specific URI segment, or null if the segment doesn't exist.

* [wph_request_getFirstSegment]

Returns the first URI segment.

* [wph_request_getLastSegment]

Returns the last URL segment.

* [wph_request_getParam name="parameter_name"]

Returns a variable from either the query string or the post data.

* [wph_request_getQuery name="parameter_name"]

Returns a variable from the query string.

* [wph_request_getPost name="parameter_name"]

Returns a value from post data.

* [wph_request_getServerName]

Returns the server name.

* [wph_request_isMobileBrowser]

Returns whether the request is coming from a mobile browser.

* [wph_request_getHostInfo]

Returns the schema and host part of the application URL.

* [wph_request_getScriptUrl]

Returns the relative URL of the entry script.

* [wph_request_getPathInfo]

eturns the path info of the currently requested URL.

* [wph_request_getRequestUri]

Returns the request URI portion for the currently requested URL.

* [wph_request_getServerPort]

Returns the server port number.

* [wph_request_getUrlReferrer]

Returns the URL referrer or null if not present.

* [wph_request_getUserAgent]

Returns the user agent or null if not present.

* [wph_request_getUserHostAddress]

Returns the user IP address.

* [wph_request_getUserHost]

Returns the user host name or null if it cannot be determined.

* [wph_request_getPort]

Returns the port to use for insecure requests.

* [wph_request_getQueryString]

Returns part of the request URL that is after the question mark.

* [wph_request_getQueryStringWithoutPath]

Returns the request’s query string, without the p= parameter.

* [wph_request_getIpAddress]

Returns the best guess of the client’s actual IP address

* [wph_request_getClientOs]

Returns whether the client is running "Windows", "Mac", "Linux" or "Other".

== Installation ==

**Through Dashboard**

1. Log in to your WordPress admin panel and go to Plugins -> Add New
2. Type **WP Request** in the search box and click on search button.
3. Find WP Request plugin.
4. Then click on Install Now after that activate the plugin.
5. Place any of the available shortcode through `<?php do_shortcode('[wph_request_getIpAddress]'); ?>` in your templates

**Installing Via FTP**

1. Download **WP Request** plugin from https://wordpress.org/plugins/.
2. Unzip.
3. Upload the **wp-request** folder into your plugins directory.
4. Log in to your WordPress admin panel and click the Plugins menu.
5. Then activate the plugin.
5. Place any of the available shortcode through `<?php do_shortcode('[wph_request_getIpAddress]'); ?>` in your templates

== Changelog ==

= 0.1.0 - 2/29/2019 =
* Initial Release