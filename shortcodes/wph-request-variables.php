<?php
/**
 * All shortcodes
 *
 * @package    WPH_Request
 * @since      1.0.0
 * @author     Hashtag Errors <hashtagerrors@gmail.com>
 */
class WPH_Variable {
	/**
	 * Returns whether this is a GET request.
	 *
	 */

	public static function wphr_shortcode_isGet( $atts, $content = null ) {
		return ((new WPH_Request_Service)->getRequestType() == 'GET');
	}

	/**
	 * Returns whether this is a POST request.
	 *
	 */
	public static function wphr_shortcode_isPost( $atts, $content = null ) {
		return (new WPH_Request_Service)->getIsPostRequest();
	}

	/**
	 * Returns whether this is a DELETE request.
	 *
	 */
	public static function wphr_shortcode_isDelete( $atts, $content = null ) {
		return (new WPH_Request_Service)->getIsDeleteRequest();
	}

	/**
	 * Returns whether this is a PUT request.
	 *
	 */
	public static function wphr_shortcode_isPut( $atts, $content = null ) {
		return (new WPH_Request_Service)->getIsPutRequest();
	}

	/**
	 * Returns whether this is an Ajax request.
	 *
	 */
	public static function wphr_shortcode_isAjax( $atts, $content = null ) {
		return (new WPH_Request_Service)->getIsAjaxRequest();
	}

	/**
	 * Returns whether this is a secure connection.
	 *
	 */
	public static function wphr_shortcode_isSecure( $atts, $content = null ) {
		return (new WPH_Request_Service)->getIsSecureConnection();
	}

	/**
	 * Returns the script name used to access Wordpres.
	 *
	 */
	public static function wphr_shortcode_getScriptName( $atts, $content = null ) {
		$scriptUrl = (new WPH_Request_Service)->getScriptUrl();
		return mb_substr($scriptUrl, mb_strrpos($scriptUrl, '/')+1);
	}

	/**
	 * Returns the request's URI.
	 *
	 */
	public static function wphr_shortcode_getPath( $atts, $content = null ) {
		return (new WPH_Request_Service)->getPath();
	}

	/**
	 * Returns the request's full URL.
	 *
	 */
	public static function wphr_shortcode_getUrl( $atts, $content = null ) {
		// $uri = (new WPH_Request_Service)->getPath();
		// return UrlHelper::getUrl($uri);
		$uri =(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		return $uri;
	}

	/**
	 * Returns all URI segments.
	 *
	 */
	public static function wphr_shortcode_getSegments( $atts, $content = null ) {
		return (new WPH_Request_Service)->getSegments();
	}

	/**
	 * Returns a specific URI segment, or null if the segment doesn't exist.
	 *
	 * @param int $num
	 *
	 */
	public static function wphr_shortcode_getSegment($atts, $content = null)
	{	
		extract( shortcode_atts( array(
			'num' => ''
	    ), $atts ) );
		return (new WPH_Request_Service)->getSegment($num);
	}

	/**
	 * Returns the first URI segment.
	 *
	 */
	public static function wphr_shortcode_getFirstSegment( $atts, $content = null ) {
		return (new WPH_Request_Service)->getSegment(1);
	}

	/**
	 * Returns the last URL segment.
	 *
	 */
	public static function wphr_shortcode_getLastSegment( $atts, $content = null ) {
		return (new WPH_Request_Service)->getSegment(-1);
	}

	/**
	 * Returns a variable from either the query string or the post data.
	 *
	 */
	public static function wphr_shortcode_getParam( $atts, $content = null )
	{	
		extract( shortcode_atts( array(
			'name' => ''
	    ), $atts ) );
	    $default = '';
	    $unsan_param = (new WPH_Request_Service)->getParam($name, $default);
		return esc_html($unsan_param);
	}

	/**
	 * Returns a variable from the query string.
	 *
	 */
	public static function wphr_shortcode_getQuery($atts = null, $content = null)
	{	
		extract( shortcode_atts( array(
			'name' => ''
	    ), $atts ) );
	    $default = '';
	    $unsan_param = (new WPH_Request_Service)->getQuery($name, $default);
		return esc_html($unsan_param);
	}

	/**
	 * Returns a value from post data.
	 *
	 */
	public static function wphr_shortcode_getPost($atts = null, $content = null)
	{
		extract( shortcode_atts( array(
			'name' => ''
	    ), $atts ) );
	    $default = '';
	    $unsan_param = (new WPH_Request_Service)->getPost($name, $default);
		return esc_html($unsan_param);
	}

	/**
	 * Returns a {@link HttpCookie} if it exists, otherwise, null.
	 *
	 * @param $name
	 *
	 */
	public static function wphr_shortcode_getCookie($atts = null, $content = null)
	{
		extract( shortcode_atts( array(
			'name' => ''
	    ), $atts ) );
		return (new WPH_Request_Service)->getCookie($name);
	}

	/**
	 * Returns the server name.
	 *
	 */
	public static function wphr_shortcode_getServerName( $atts, $content = null ) {
		return (new WPH_Request_Service)->getServerName();
	}

	/**
	 * Returns whether the request is coming from a mobile browser.
	 *
	 * @param bool $detectTablets
	 *
	 */
	public static function wphr_shortcode_isMobileBrowser( $atts, $content = null, $detectTablets = false)
	{
		return (new WPH_Request_Service)->isMobileBrowser($detectTablets);
	}

	/**
	 * Returns the schema and host part of the application URL.  The returned URL does not have an ending slash. By
	 * default this is determined based on the user request information.
	 *
	 * @param string $schema
	 *
	 */
	public static function wphr_shortcode_getHostInfo( $atts, $content = null, $schema = '' )
	{
		return (new WPH_Request_Service)->getHostInfo($schema);
	}

	/**
	 * Returns the relative URL of the entry script.
	 *
	 */
	public static function wphr_shortcode_getScriptUrl( $atts, $content = null ) {
		return (new WPH_Request_Service)->getScriptUrl();
	}

	/**
	 * Returns the path info of the currently requested URL. This refers to the part that is after the entry script and
	 * before the question mark. The starting and ending slashes are stripped off.
	 *
	 */
	public static function wphr_shortcode_getPathInfo( $atts, $content = null ) {
		return (new WPH_Request_Service)->getPathInfo();
	}

	/**
	 * Returns the request URI portion for the currently requested URL. This refers to the portion that is after the
	 * host info part. It includes the query string part if any.
	 *
	 */
	public static function wphr_shortcode_getRequestUri( $atts, $content = null ) {
		return (new WPH_Request_Service)->getRequestUri();
	}

	/**
	 * Returns the server port number.
	 *
	 */
	public static function wphr_shortcode_getServerPort( $atts, $content = null ) {
		return (new WPH_Request_Service)->getServerPort();
	}

	/**
	 * Returns the URL referrer or null if not present.
	 *
	 */
	public static function wphr_shortcode_getUrlReferrer( $atts, $content = null ) {
		return (new WPH_Request_Service)->getUrlReferrer();
	}

	/**
	 * Returns the user agent or null if not present.
	 *
	 */
	public static function wphr_shortcode_getUserAgent( $atts, $content = null ) {
		return (new WPH_Request_Service)->getUserAgent();
	}

	/**
	 * Returns the user IP address.
	 *
	 */
	public static function wphr_shortcode_getUserHostAddress( $atts, $content = null ) {
		return (new WPH_Request_Service)->getUserHostAddress();
	}

	/**
	 * Returns the user host name or null if it cannot be determined.
	 *
	 */
	public static function wphr_shortcode_getUserHost( $atts, $content = null ) {
		return (new WPH_Request_Service)->getUserHost();
	}

	/**
	 * Returns the port to use for insecure requests. Defaults to 80, or the port specified by the server if the current
	 * request is insecure.
	 *
	 */
	public static function wphr_shortcode_getPort( $atts, $content = null ) {
		return (new WPH_Request_Service)->getPort();
	}

	/**
	 * Returns part of the request URL that is after the question mark.
	 *
	 */
	public static function wphr_shortcode_getQueryString( $atts, $content = null ) { 
		$unsan_param = (new WPH_Request_Service)->getQueryString();
		return esc_html($unsan_param);
	}

	/**
	 * Returns the request’s query string, without the p= parameter.
	 *
	 */
	public static function wphr_shortcode_getQueryStringWithoutPath( $atts, $content = null ) { 
		$unsan_param = (new WPH_Request_Service)->getQueryStringWithoutPath();
		return esc_html($unsan_param);
	}

	/**
	 * Retrieves the best guess of the client’s actual IP address taking into account numerous HTTP proxy headers due to
	 * variations in how different ISPs handle IP addresses in headers between hops.
	 *
	 * Considering any of these server vars besides REMOTE_ADDR can be spoofed, this method should not be used when you
	 * need a trusted source for the IP address. Use `$_SERVER['REMOTE_ADDR']` instead.
	 *
	 */
	public static function wphr_shortcode_getIpAddress( $atts, $content = null ) {
		return (new WPH_Request_Service)->getIpAddress();
	}

	/**
	 * Returns whether the client is running "Windows", "Mac", "Linux" or "Other", based on the
	 * browser's UserAgent string.
	 *
	 */
	public static function wphr_shortcode_getClientOs( $atts, $content = null ) {
		return (new WPH_Request_Service)->getClientOs();
	}
}

add_shortcode( 'wph_request_isGet', array( 'WPH_Variable', 'wphr_shortcode_isGet' ));
add_shortcode( 'wph_request_isPost', array( 'WPH_Variable', 'wphr_shortcode_isPost' ));
add_shortcode( 'wph_request_isDelete', array( 'WPH_Variable', 'wphr_shortcode_isDelete' ));
add_shortcode( 'wph_request_isPut', array( 'WPH_Variable', 'wphr_shortcode_isPut' ));
add_shortcode( 'wph_request_isAjax', array( 'WPH_Variable', 'wphr_shortcode_isAjax' ));
add_shortcode( 'wph_request_isSecure', array( 'WPH_Variable', 'wphr_shortcode_isSecure' ));
add_shortcode( 'wph_request_isLivePreview', array( 'WPH_Variable', 'wphr_shortcode_isLivePreview' ));
add_shortcode( 'wph_request_getScriptName', array( 'WPH_Variable', 'wphr_shortcode_getScriptName' ));
add_shortcode( 'wph_request_getPath', array( 'WPH_Variable', 'wphr_shortcode_getPath' ));
add_shortcode( 'wph_request_getUrl', array( 'WPH_Variable', 'wphr_shortcode_getUrl' ));
add_shortcode( 'wph_request_getSegments', array( 'WPH_Variable', 'wphr_shortcode_getSegments' ));
add_shortcode( 'wph_request_getSegment', array( 'WPH_Variable', 'wphr_shortcode_getSegment' ));
add_shortcode( 'wph_request_getFirstSegment', array( 'WPH_Variable', 'wphr_shortcode_getFirstSegment' ));
add_shortcode( 'wph_request_getLastSegment', array( 'WPH_Variable', 'wphr_shortcode_getLastSegment' ));
add_shortcode( 'wph_request_getParam', array( 'WPH_Variable', 'wphr_shortcode_getParam' ));
add_shortcode( 'wph_request_getQuery', array( 'WPH_Variable', 'wphr_shortcode_getQuery' ));
add_shortcode( 'wph_request_getPost', array( 'WPH_Variable', 'wphr_shortcode_getPost' ));
//add_shortcode( 'wph_request_getCookie', array( 'WPH_Variable', 'wphr_shortcode_getCookie' ));
add_shortcode( 'wph_request_getServerName', array( 'WPH_Variable', 'wphr_shortcode_getServerName' ));
add_shortcode( 'wph_request_isMobileBrowser', array( 'WPH_Variable', 'wphr_shortcode_isMobileBrowser' ));
add_shortcode( 'wph_request_getHostInfo', array( 'WPH_Variable', 'wphr_shortcode_getHostInfo' ));
add_shortcode( 'wph_request_getScriptUrl', array( 'WPH_Variable', 'wphr_shortcode_getScriptUrl' ));
add_shortcode( 'wph_request_getPathInfo', array( 'WPH_Variable', 'wphr_shortcode_getPathInfo' ));
add_shortcode( 'wph_request_getRequestUri', array( 'WPH_Variable', 'wphr_shortcode_getRequestUri' ));
add_shortcode( 'wph_request_getServerPort', array( 'WPH_Variable', 'wphr_shortcode_getServerPort' ));
add_shortcode( 'wph_request_getUrlReferrer', array( 'WPH_Variable', 'wphr_shortcode_getUrlReferrer' ));
add_shortcode( 'wph_request_getUserAgent', array( 'WPH_Variable', 'wphr_shortcode_getUserAgent' ));
add_shortcode( 'wph_request_getUserHostAddress', array( 'WPH_Variable', 'wphr_shortcode_getUserHostAddress' ));
add_shortcode( 'wph_request_getUserHost', array( 'WPH_Variable', 'wphr_shortcode_getUserHost' ));
add_shortcode( 'wph_request_getPort', array( 'WPH_Variable', 'wphr_shortcode_getPort' ));
add_shortcode( 'wph_request_getQueryString', array( 'WPH_Variable', 'wphr_shortcode_getQueryString' ));
add_shortcode( 'wph_request_getQueryStringWithoutPath', array( 'WPH_Variable', 'wphr_shortcode_getQueryStringWithoutPath' ));
add_shortcode( 'wph_request_getIpAddress', array( 'WPH_Variable', 'wphr_shortcode_getIpAddress' ));
add_shortcode( 'wph_request_getClientOs', array( 'WPH_Variable', 'wphr_shortcode_getClientOs' ));