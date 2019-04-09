<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://hashtagerrors.com
 * @since      1.0.0
 *
 * @package    WPH_Request
 * @subpackage WPH_Request/includes
 */
class WPH_Request_Service
{
	public $jsonAsArray = true;

	static $wph_requestUri;
	static $wph_pathInfo;
	static $wph_scriptFile;
	static $wph_scriptUrl;
	static $wph_hostInfo;
	static $wph_baseUrl;
	static $wph_cookies;
	static $wph_preferredAcceptTypes;
	static $wph_preferredLanguages;
	static $wph_restParams;
	static $wph_httpVersion;
	static $wph_port;
	static $wph_isMobileBrowser;
	static $wph_isMobileOrTabletBrowser;
	static $wph_ipAddress;

	/**
	 * Initializes the application component.
	 * This method overrides the parent implementation by preprocessing
	 * the user request data.
	 */
	public function init()
	{
		parent::init();
		WPH_Request_Service::normalizeRequest();
		// Get the normalized path.
	}

	/**
	 * Normalizes the request data.
	 * This method strips off slashes in request data if get_magic_quotes_gpc() returns true.
	 */
	protected function normalizeRequest()
	{
		// normalize request
		if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
		{
			if(isset($_GET))
				$_GET=WPH_Request_Service::stripSlashes($_GET);
			if(isset($_POST))
				$_POST=WPH_Request_Service::stripSlashes($_POST);
			if(isset($_REQUEST))
				$_REQUEST=WPH_Request_Service::stripSlashes($_REQUEST);
			if(isset($_COOKIE))
				$_COOKIE=WPH_Request_Service::stripSlashes($_COOKIE);
		}
	}


	/**
	 * Strips slashes from input data.
	 * This method is applied when magic quotes is enabled.
	 */
	public function stripSlashes(&$data)
	{
		if(is_array($data))
		{
			if(count($data) == 0)
				return $data;
			$keys=array_map('stripslashes',array_keys($data));
			$data=array_combine($keys,array_values($data));
			return array_map(array($this,'stripSlashes'),$data);
		}
		else
			return stripslashes($data);
	}

	/**
	 * Returns the named GET or POST parameter value.
	 * If the GET or POST parameter does not exist, the second parameter to this method will be returned.
	 * If both GET and POST contains such a named parameter, the GET parameter takes precedence.
	 */
	public function getParam($name,$defaultValue=null)
	{
		return isset($_GET[$name]) ? $_GET[$name] : (isset($_POST[$name]) ? $_POST[$name] : $defaultValue);
	}

	/**
	 * Returns the named GET parameter value.
	 * If the GET parameter does not exist, the second parameter to this method will be returned.
	 */
	public function getQuery($name,$defaultValue=null)
	{
		return isset($_GET[$name]) ? $_GET[$name] : $defaultValue;
	}

	/**
	 * Returns the named POST parameter value.
	 * If the POST parameter does not exist, the second parameter to this method will be returned.
	 */
	public function getPost($name,$defaultValue=null)
	{
		return isset($_POST[$name]) ? $_POST[$name] : $defaultValue;
	}

	/**
	 * Returns the named DELETE parameter value.
	 * If the DELETE parameter does not exist or if the current request is not a DELETE request,
	 * the second parameter to this method will be returned.
	 * If the DELETE request was tunneled through POST via _method parameter, the POST parameter
	 * will be returned instead
	 */
	public function getDelete($name,$defaultValue=null)
	{
		if(WPH_Request_Service::getIsDeleteViaPostRequest())
			return WPH_Request_Service::getPost($name, $defaultValue);

		if(WPH_Request_Service::getIsDeleteRequest())
		{
			$restParams=WPH_Request_Service::getRestParams();
			return isset($restParams[$name]) ? $restParams[$name] : $defaultValue;
		}
		else
			return $defaultValue;
	}

	/**
	 * Returns the named PUT parameter value.
	 * If the PUT parameter does not exist or if the current request is not a PUT request,
	 * the second parameter to this method will be returned.
	 * If the PUT request was tunneled through POST via _method parameter, the POST parameter
	 */
	public function getPut($name,$defaultValue=null)
	{
		if(WPH_Request_Service::getIsPutViaPostRequest())
			return WPH_Request_Service::getPost($name, $defaultValue);

		if(WPH_Request_Service::getIsPutRequest())
		{
			$restParams=WPH_Request_Service::getRestParams();
			return isset($restParams[$name]) ? $restParams[$name] : $defaultValue;
		}
		else
			return $defaultValue;
	}

	/**
	 * Returns the named PATCH parameter value.
	 * If the PATCH parameter does not exist or if the current request is not a PATCH request,
	 * the second parameter to this method will be returned.
	 * If the PATCH request was tunneled through POST via _method parameter, the POST parameter
	 * will be returned instead.
	 */
	public function getPatch($name,$defaultValue=null)
	{
		if(WPH_Request_Service::getIsPatchViaPostRequest())
			return WPH_Request_Service::getPost($name, $defaultValue);

		if(WPH_Request_Service::getIsPatchRequest())
		{
			$restParams=WPH_Request_Service::getRestParams();
			return isset($restParams[$name]) ? $restParams[$name] : $defaultValue;
		}
		else
			return $defaultValue;
	}

	/**
	 * Returns request parameters. Typically PUT, PATCH or DELETE.
	 */
	public function getRestParams()
	{
		if(self::$wph_restParams===null)
		{
			$result=array();
			if (strncmp(WPH_Request_Service::getContentType(), 'application/json', 16) === 0)
				$result = json_decode(WPH_Request_Service::getRawBody(), WPH_Request_Service::jsonAsArray);
			elseif(function_exists('mb_parse_str'))
				mb_parse_str(WPH_Request_Service::getRawBody(), $result);
			else
				parse_str(WPH_Request_Service::getRawBody(), $result);
			self::$wph_restParams=$result;
		}

		return self::$wph_restParams;
	}

	/**
	 * Returns the raw HTTP request body.
	 */
	public function getRawBody()
	{
		static $rawBody;
		if($rawBody===null)
			$rawBody=file_get_contents('php://input');
		return $rawBody;
	}

	/**
	 * Returns the currently requested URL.
	 */
	public function getUrl()
	{
		return WPH_Request_Service::getRequestUri();
	}

	/**
	 * Returns the schema and host part of the application URL.
	 * The returned URL does not have an ending slash.
	 * By default this is determined based on the user request information.
	 */
	public function getHostInfo($schema='')
	{
		if(self::$wph_hostInfo===null)
		{
			if($secure=WPH_Request_Service::getIsSecureConnection())
				$http='https';
			else
				$http='http';
			if(isset($_SERVER['HTTP_HOST']))
				self::$wph_hostInfo=$http.'://'.$_SERVER['HTTP_HOST'];
			else
			{
				self::$wph_hostInfo=$http.'://'.$_SERVER['SERVER_NAME'];
				$port=$secure ? WPH_Request_Service::getSecurePort() : WPH_Request_Service::getPort();
				if(($port!==80 && !$secure) || ($port!==443 && $secure))
					self::$wph_hostInfo.=':'.$port;
			}
		}
		if($schema!=='')
		{
			$secure=WPH_Request_Service::getIsSecureConnection();
			if($secure && $schema==='https' || !$secure && $schema==='http')
				echo "//";
				return self::$wph_hostInfo;

			$port=$schema==='https' ? WPH_Request_Service::getSecurePort() : WPH_Request_Service::getPort();
			if($port!==80 && $schema==='http' || $port!==443 && $schema==='https')
				$port=':'.$port;
			else
				$port='';

			$pos=strpos(self::$wph_hostInfo,':');
			return $schema.substr(self::$wph_hostInfo,$pos,strcspn(self::$wph_hostInfo,':',$pos+1)+1).$port;
		}
		else
			return self::$wph_hostInfo;
	}

	/**
	 * Returns the relative URL for the application.
	 */
	public function getBaseUrl($absolute=false)
	{
		if(self::$wph_baseUrl===null)
			self::$wph_baseUrl=rtrim(dirname(WPH_Request_Service::getScriptUrl()),'\\/');
		return $absolute ? WPH_Request_Service::getHostInfo() . self::$wph_baseUrl : self::$wph_baseUrl;
	}

	/**
	 * Returns the relative URL of the entry script.
	 */
	public function getScriptUrl()
	{
		if(self::$wph_scriptUrl===null)
		{
			$scriptName=basename($_SERVER['SCRIPT_FILENAME']);
			if(basename($_SERVER['SCRIPT_NAME'])===$scriptName)
				self::$wph_scriptUrl=$_SERVER['SCRIPT_NAME'];
			elseif(basename($_SERVER['PHP_SELF'])===$scriptName)
				self::$wph_scriptUrl=$_SERVER['PHP_SELF'];
			elseif(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME'])===$scriptName)
				self::$wph_scriptUrl=$_SERVER['ORIG_SCRIPT_NAME'];
			elseif(($pos=strpos($_SERVER['PHP_SELF'],'/'.$scriptName))!==false)
				self::$wph_scriptUrl=substr($_SERVER['SCRIPT_NAME'],0,$pos).'/'.$scriptName;
			elseif(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'],$_SERVER['DOCUMENT_ROOT'])===0)
				self::$wph_scriptUrl=str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));
		}
		return self::$wph_scriptUrl;
	}

	/**
	 * Returns the path info of the currently requested URL.
	 * This refers to the part that is after the entry script and before the question mark.
	 * The starting and ending slashes are stripped off.
	 */
	public function getPathInfo()
	{
		if(self::$wph_pathInfo===null)
		{
			$pathInfo=WPH_Request_Service::getRequestUri();

			if(($pos=strpos($pathInfo,'?'))!==false)
			   $pathInfo=substr($pathInfo,0,$pos);

			$pathInfo=WPH_Request_Service::decodePathInfo($pathInfo);

			$scriptUrl=WPH_Request_Service::getScriptUrl();
			$baseUrl=WPH_Request_Service::getBaseUrl();
			if(strpos($pathInfo,$scriptUrl)===0)
				$pathInfo=substr($pathInfo,strlen($scriptUrl));
			elseif($baseUrl==='' || strpos($pathInfo,$baseUrl)===0)
				$pathInfo=substr($pathInfo,strlen($baseUrl));
			elseif(strpos($_SERVER['PHP_SELF'],$scriptUrl)===0)
				$pathInfo=substr($_SERVER['PHP_SELF'],strlen($scriptUrl));

			if($pathInfo==='/' || $pathInfo===false)
				$pathInfo='';
			elseif($pathInfo!=='' && $pathInfo[0]==='/')
				$pathInfo=substr($pathInfo,1);

			if(($posEnd=strlen($pathInfo)-1)>0 && $pathInfo[$posEnd]==='/')
				$pathInfo=substr($pathInfo,0,$posEnd);

			self::$wph_pathInfo=$pathInfo;
		}
		return self::$wph_pathInfo;
	}

	/**
	 * Decodes the path info.
	 */
	protected function decodePathInfo($pathInfo)
	{
		$pathInfo = urldecode($pathInfo);

		// is it UTF-8?
		// http://w3.org/International/questions/qa-forms-utf-8.html
		if(preg_match('%^(?:
		   [\x09\x0A\x0D\x20-\x7E]            # ASCII
		 | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
		 | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
		 | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
		 | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
		 | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
		 | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
		 | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
		)*$%xs', $pathInfo))
		{
			return $pathInfo;
		}
		else
		{
			return utf8_encode($pathInfo);
		}
	}

	/**
	 * Returns the request URI portion for the currently requested URL.
	 */
	public function getRequestUri()
	{
		if(self::$wph_requestUri===null)
		{
			if(isset($_SERVER['HTTP_X_REWRITE_URL'])) // IIS
				self::$wph_requestUri=$_SERVER['HTTP_X_REWRITE_URL'];
			elseif(isset($_SERVER['REQUEST_URI']))
			{
				self::$wph_requestUri=$_SERVER['REQUEST_URI'];
				if(!empty($_SERVER['HTTP_HOST']))
				{
					if(strpos(self::$wph_requestUri,$_SERVER['HTTP_HOST'])!==false)
						self::$wph_requestUri=preg_replace('/^\w+:\/\/[^\/]+/','',self::$wph_requestUri);
				}
				else
					self::$wph_requestUri=preg_replace('/^(http|https):\/\/[^\/]+/i','',self::$wph_requestUri);
			}
			elseif(isset($_SERVER['ORIG_PATH_INFO']))  // IIS 5.0 CGI
			{
				self::$wph_requestUri=$_SERVER['ORIG_PATH_INFO'];
				if(!empty($_SERVER['QUERY_STRING']))
					self::$wph_requestUri.='?'.$_SERVER['QUERY_STRING'];
			}
		}

		return self::$wph_requestUri;
	}

	/**
	 * Returns part of the request URL that is after the question mark.
	 */
	public function getQueryString()
	{
		return isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'';
	}

	/**
	 * Return if the request is sent via secure channel (https).
	 */
	public function getIsSecureConnection()
	{
		return isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'],'on')===0 || $_SERVER['HTTPS']==1)
			|| isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'],'https')===0;
	}

	/**
	 * Returns the request type, such as GET, POST, HEAD, PUT, PATCH, DELETE.
	 */
	public function getRequestType()
	{
		if(isset($_POST['_method']))
			return strtoupper($_POST['_method']);
		elseif(isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']))
			return strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);

		return strtoupper(isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:'GET');
	}

	/**
	 * Returns whether this is a POST request.
	 */
	public function getIsPostRequest()
	{
		return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'],'POST');
	}

	/**
	 * Returns whether this is a DELETE request.
	 */
	public function getIsDeleteRequest()
	{
		return (isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'],'DELETE')) || WPH_Request_Service::getIsDeleteViaPostRequest();
	}

	/**
	 * Returns whether this is a DELETE request which was tunneled through POST.
	 */
	protected function getIsDeleteViaPostRequest()
	{
		return isset($_POST['_method']) && !strcasecmp($_POST['_method'],'DELETE');
	}

	/**
	 * Returns whether this is a PUT request.
	 */
	public function getIsPutRequest()
	{
		return (isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'],'PUT')) || WPH_Request_Service::getIsPutViaPostRequest();
	}

	/**
	 * Returns whether this is a PUT request which was tunneled through POST
	 */
	protected function getIsPutViaPostRequest()
	{
		return isset($_POST['_method']) && !strcasecmp($_POST['_method'],'PUT');
	}

	/**
	 * Returns whether this is a PATCH request.
	 */
	public function getIsPatchRequest()
	{
		return (isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'],'PATCH')) || WPH_Request_Service::getIsPatchViaPostRequest();
	}

	/**
	 * Returns whether this is a PATCH request which was tunneled through POST.
	 */
	protected function getIsPatchViaPostRequest()
	{
		return isset($_POST['_method']) && !strcasecmp($_POST['_method'],'PATCH');
	}

	/**
	 * Returns whether this is an AJAX (XMLHttpRequest) request.
	 */
	public function getIsAjaxRequest()
	{
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';
	}

	/**
	 * Returns whether this is an Adobe Flash or Adobe Flex request.
	 */
	public function getIsFlashRequest()
	{
		return isset($_SERVER['HTTP_USER_AGENT']) && (stripos($_SERVER['HTTP_USER_AGENT'],'Shockwave')!==false || stripos($_SERVER['HTTP_USER_AGENT'],'Flash')!==false);
	}

	/**
	 * Returns the server name.
	 */
	public function getServerName()
	{
		return $_SERVER['SERVER_NAME'];
	}

	/**
	 * Returns the server port number.
	 */
	public function getServerPort()
	{
		return $_SERVER['SERVER_PORT'];
	}

	/**
	 * Returns the URL referrer, null if not present
	 */
	public function getUrlReferrer()
	{
		return isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:null;
	}

	/**
	 * Returns the user agent, null if not present.
	 */
	public function getUserAgent()
	{
		return isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:null;
	}

	/**
	 * Returns the user IP address.
	 */
	public function getUserHostAddress()
	{
		return isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'127.0.0.1';
	}

	/**
	 * Returns the user host name, null if it cannot be determined.
	 */
	public function getUserHost()
	{
		return isset($_SERVER['REMOTE_HOST'])?$_SERVER['REMOTE_HOST']:null;
	}

	/**
	 * Returns entry script file path.
	 */
	public function getScriptFile()
	{
		if(self::$wph_scriptFile!==null)
			return self::$wph_scriptFile;
		else
			return self::$wph_scriptFile=realpath($_SERVER['SCRIPT_FILENAME']);
	}

	/**
	 * Returns information about the capabilities of user browser.
	 */
	public function getBrowser($userAgent=null)
	{
		return get_browser($userAgent,true);
	}

	/**
	 * Returns user browser accept types, null if not present.
	 */
	public function getAcceptTypes()
	{
		return isset($_SERVER['HTTP_ACCEPT'])?$_SERVER['HTTP_ACCEPT']:null;
	}
	
	/**
	 * Returns request content-type
	 * The Content-Type header field indicates the MIME type of the data
	 */
	public function getContentType()
	{
		if (isset($_SERVER["CONTENT_TYPE"])) {
			return $_SERVER["CONTENT_TYPE"];
		} elseif (isset($_SERVER["HTTP_CONTENT_TYPE"])) {
			//fix bug https://bugs.php.net/bug.php?id=66606
			return $_SERVER["HTTP_CONTENT_TYPE"];
		}
		return null;
	}

 	/**
	 * Returns the port to use for insecure requests.
	 * Defaults to 80, or the port specified by the server if the current
	 * request is insecure.
	 */
	public function getPort()
	{
		if(self::$wph_port===null)
			$isSecure = WPH_Request_Service::getIsSecureConnection();
			self::$wph_port=!$isSecure && isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : 80;
		return self::$wph_port;
	}

	/**
	 * Returns the port to use for secure requests.
	 * Defaults to 443, or the port specified by the server if the current
	 * request is secure.
	 */
	public function getSecurePort()
	{
		if(self::$_securePort===null)
			self::$_securePort=WPH_Request_Service::getIsSecureConnection() && isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : 443;
		return self::$_securePort;
	}

	/**
	 * Returns the cookie collection.
	 */
	public function getCookies()
	{
		if(self::$wph_cookies!==null)
			return self::$wph_cookies;
		else
			return self::$wph_cookies=new CCookieCollection($this);
	}

	/**
	 * Redirects the browser to the specified URL.
	 */
	public function redirect($url,$terminate=true,$statusCode=302)
	{
		if(strpos($url,'/')===0 && strpos($url,'//')!==0)
			$url=WPH_Request_Service::getHostInfo().$url;
		header('Location: '.$url, true, $statusCode);
		if($terminate)
			wp_die();
	}

	/**
	 * Parses an HTTP Accept header, returning an array map with all parts of each entry.
	 * Each array entry consists of a map with the type, subType, baseType and params, an array map of key-value parameters,
	 * obligatorily including a `q` value (i.e. preference ranking) as a double.
	 * For example, an Accept header value of <code>'application/xhtml+xml;q=0.9;level=1'</code> would give an array entry of
	 * <pre>
	 * array(
	 *        'type' => 'application',
	 *        'subType' => 'xhtml',
	 *        'baseType' => 'xml',
	 *        'params' => array(
	 *            'q' => 0.9,
	 *            'level' => '1',
	 *        ),
	 * )
	 * </pre>
	 *
	 * <b>Please note:</b>
	 * To avoid great complexity, there are no steps taken to ensure that quoted strings are treated properly.
	 * If the header text includes quoted strings containing space or the , or ; characters then the results may not be correct!
	 */
	public static function parseAcceptHeader($header)
	{
		$matches=array();
		$accepts=array();
		// get individual entries with their type, subtype, basetype and params
		preg_match_all('/(?:\G\s?,\s?|^)(\w+|\*)\/(\w+|\*)(?:\+(\w+))?|(?<!^)\G(?:\s?;\s?(\w+)=([\w\.]+))/',$header,$matches);
		// the regexp should (in theory) always return an array of 6 arrays
		if(count($matches)===6)
		{
			$i=0;
			$itemLen=count($matches[1]);
			while($i<$itemLen)
			{
				// fill out a content type
				$accept=array(
					'type'=>$matches[1][$i],
					'subType'=>$matches[2][$i],
					'baseType'=>null,
					'params'=>array(),
				);
				// fill in the base type if it exists
				if($matches[3][$i]!==null && $matches[3][$i]!=='')
					$accept['baseType']=$matches[3][$i];
				// continue looping while there is no new content type, to fill in all accompanying params
				for($i++;$i<$itemLen;$i++)
				{
					// if the next content type is null, then the item is a param for the current content type
					if($matches[1][$i]===null || $matches[1][$i]==='')
					{
						// if this is the quality param, convert it to a double
						if($matches[4][$i]==='q')
						{
							// sanity check on q value
							$q=(double)$matches[5][$i];
							if($q>1)
								$q=(double)1;
							elseif($q<0)
								$q=(double)0;
							$accept['params'][$matches[4][$i]]=$q;
						}
						else
							$accept['params'][$matches[4][$i]]=$matches[5][$i];
					}
					else
						break;
				}
				// q defaults to 1 if not explicitly given
				if(!isset($accept['params']['q']))
					$accept['params']['q']=(double)1;
				$accepts[] = $accept;
			}
		}
		return $accepts;
	}

	/**
	 * Compare function for determining the preference of accepted MIME type array maps
	 */
	public static function compareAcceptTypes($a,$b)
	{
		// check for equal quality first
		if($a['params']['q']===$b['params']['q'])
			if(!($a['type']==='*' xor $b['type']==='*'))
				if (!($a['subType']==='*' xor $b['subType']==='*'))
					// finally, higher number of parameters counts as greater precedence
					if(count($a['params'])===count($b['params']))
						return 0;
					else
						return count($a['params'])<count($b['params']) ? 1 : -1;
				// more specific takes precedence - whichever one doesn't have a * subType
				else
					return $a['subType']==='*' ? 1 : -1;
			// more specific takes precedence - whichever one doesn't have a * type
			else
				return $a['type']==='*' ? 1 : -1;
		else
			return ($a['params']['q']<$b['params']['q']) ? 1 : -1;
	}

	/**
	 * Returns an array of user accepted MIME types in order of preference.
	 * Each array entry consists of a map with the type, subType, baseType and params, an array map of key-value parameters.
	 */
	public function getPreferredAcceptTypes()
	{
		if(self::$wph_preferredAcceptTypes===null)
		{
			$accepts=self::parseAcceptHeader(WPH_Request_Service::getAcceptTypes());
			usort($accepts,array(get_class($this),'compareAcceptTypes'));
			self::$wph_preferredAcceptTypes=$accepts;
		}
		return self::$wph_preferredAcceptTypes;
	}

	/**
	 * Returns the user preferred accept MIME type.
	 */
	public function getPreferredAcceptType()
	{
		$preferredAcceptTypes=WPH_Request_Service::getPreferredAcceptTypes();
		return empty($preferredAcceptTypes) ? false : $preferredAcceptTypes[0];
	}

	/**
	 * Returns an array of user accepted languages in order of preference.
	 */
	public function getPreferredLanguages()
	{
		if(self::$wph_preferredLanguages===null)
		{
			$sortedLanguages=array();
			if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && $n=preg_match_all('/([\w\-_]+)(?:\s*;\s*q\s*=\s*(\d*\.?\d*))?/',$_SERVER['HTTP_ACCEPT_LANGUAGE'],$matches))
			{
				$languages=array();

				for($i=0;$i<$n;++$i)
				{
					$q=$matches[2][$i];
					if($q==='')
						$q=1;
					if($q)
						$languages[]=array((float)$q,$matches[1][$i]);
				}

				usort($languages,create_function('$a,$b','if($a[0]==$b[0]) {return 0;} return ($a[0]<$b[0]) ? 1 : -1;'));
				foreach($languages as $language)
					$sortedLanguages[]=$language[1];
			}
			self::$wph_preferredLanguages=$sortedLanguages;
		}
		return self::$wph_preferredLanguages;
	}

	/**
	 * Returns the user-preferred language that should be used by this application.
	 * The language resolution is based on the user preferred languages and the languages
	 * supported by the application. The method will try to find the best match.
	 */
	public function getPreferredLanguage($languages=array())
	{
		$preferredLanguages=WPH_Request_Service::getPreferredLanguages();
		if(empty($languages)) {
			return !empty($preferredLanguages) ? strtolower(str_replace('-','_',$preferredLanguages[0])) : false;
		}
		foreach ($preferredLanguages as $preferredLanguage) {
			$preferredLanguage=strtolower(str_replace('-','_',$preferredLanguage));
			foreach ($languages as $language) {
				$language=strtolower(str_replace('-','_',$language));
				// en_us==en_us, en==en_us, en_us==en
				if($language===$preferredLanguage || strpos($preferredLanguage,$language.'_')===0 || strpos($language,$preferredLanguage.'_')===0) {
					return $language;
				}
			}
		}
		return reset($languages);
	}


	/**
	 * Returns the version of the HTTP protocol used by client.
	 */
	public function getHttpVersion()
	{
		if(self::$wph_httpVersion===null)
		{
			if(isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL']==='HTTP/1.0')
				self::$wph_httpVersion='1.0';
			else
				self::$wph_httpVersion='1.1';
		}
		return self::$wph_httpVersion;
	}

	public function getPath()
	{
		return WPH_Request_Service::getPathInfo();
	}

	private function _getSegments($path)
	{
		return array_values(array_filter(explode('/', $path), function($segment)
		{
			// Explicitly check in case there is a 0 in a segment (i.e. foo/0 or foo/0/bar)
			return $segment !== '';
		}));
	}

	public function segments()
	{
		$path = WPH_Request_Service::getPathInfo();

		// Get the path segments
		return WPH_Request_Service::_getSegments($path);

	}

	public function getSegments()
	{	
		return WPH_Request_Service::segments();
	}

	public function getSegment($num)
	{	
		$segmnets = WPH_Request_Service::segments();
		if ($num > 0 && isset($segmnets[$num-1]))
		{
			return $segmnets[$num-1];
		}
		else if ($num < 0)
		{
			$totalSegs = count($segmnets);

			if (isset($segmnets[$totalSegs + $num]))
			{
				return $segmnets[$totalSegs + $num];
			}
		}
	}

	// public function getCookie($name)
	// {
	// 	if (isset($this->cookies[$name]))
	// 	{
	// 		return $this->cookies[$name];
	// 	}
	// }

	public function getClientOs()
	{
		$userAgent = WPH_Request_Service::getUserAgent();

		if (preg_match('/Linux/', $userAgent))
		{
			return 'Linux';
		}
		elseif (preg_match('/Win/', $userAgent))
		{
			return 'Windows';
		}
		elseif (preg_match('/Mac/', $userAgent))
		{
			return 'Mac';
		}
		else
		{
			return 'Other';
		}
	}

	public function isMobileBrowser($detectTablets = false)
	{
		if($detectTablets){
			$key = self::$wph_isMobileOrTabletBrowser;
		}else{
			$key = self::$wph_isMobileBrowser;
		}

		$userAgent = WPH_Request_Service::getUserAgent();

		if (!isset($key))
		{
			if ($userAgent)
			{
				$key = (
					preg_match(
						'/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino'.($detectTablets ? '|android|ipad|playbook|silk' : '').'/i',
						$userAgent
					) ||
					preg_match(
						'/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',
						mb_substr($userAgent, 0, 4)
					)
				);
			}
			else
			{
				$key = false;
			}
		}

		return $key;
	}

	public function getQueryStringWithoutPath()
	{
		// Get the full query string
		$queryString = WPH_Request_Service::getQueryString();
		$parts = explode('&', $queryString);
		return implode('&', $parts);
	}

	public function getIpAddress()
	{
		if (self::$wph_ipAddress === null)
		{
			$ipMatch = false;

			// Check for shared internet/ISP IP
			if (!empty($_SERVER['HTTP_CLIENT_IP']) && self::$wph_validateIp($_SERVER['HTTP_CLIENT_IP']))
			{
				$ipMatch = $_SERVER['HTTP_CLIENT_IP'];
			}
			else
			{
				// Check for IPs passing through proxies
				if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
				{
					// Check if multiple IPs exist in var
					$ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

					foreach ($ipList as $ip)
					{
						if (self::$wph_validateIp($ip))
						{
							$ipMatch = $ip;
						}
					}
				}
			}

			if (!$ipMatch)
			{
				if (!empty($_SERVER['HTTP_X_FORWARDED']) && self::$wph_validateIp($_SERVER['HTTP_X_FORWARDED']))
				{
					$ipMatch = $_SERVER['HTTP_X_FORWARDED'];
				}
				else
				{
					if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && self::$wph_validateIp($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
					{
						$ipMatch = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
					}
					else
					{
						if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && self::$wph_validateIp($_SERVER['HTTP_FORWARDED_FOR']))
						{
							$ipMatch = $_SERVER['HTTP_FORWARDED_FOR'];
						}
						else
						{
							if (!empty($_SERVER['HTTP_FORWARDED']) && self::$wph_validateIp($_SERVER['HTTP_FORWARDED']))
							{
								$ipMatch = $_SERVER['HTTP_FORWARDED'];
							}
						}
					}
				}

				// The only one we're guaranteed to be accurate.
				if (!$ipMatch)
				{
					$ipMatch = $_SERVER['REMOTE_ADDR'];
				}
			}

			self::$wph_ipAddress = $ipMatch;
		}

		return self::$wph_ipAddress;
	}
}

$request = new WPH_Request_Service();