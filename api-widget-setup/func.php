<?
	function parseWidgetUrlFromWidgetCode($widgetCode)
	{
		if (!preg_match('/src=["\']([^"\']+)["\']/', $widgetCode, $m)) {
			throw new Exception("Unable to parse the Giving Widget code");
		}
		
		return $m[1];
	}
	
	define('API_VERSION', 'v1');
	function parseApiEndpointFromWidgetCode($widgetCode)
	{
		$widgetUrl = parseWidgetUrlFromWidgetCode($widgetCode);
		$parsedUrl = parse_url($widgetUrl);
		
		$apiHost = str_replace('www.', 'api.', $parsedUrl['host']);
		if (!preg_match('/^(\/.*\/givingwidget)(\/embed\.js)?$/U', $parsedUrl['path'], $m)) {
			throw new Exception("Unable to parse the Giving Widget URL");
		}
		
		$apiEndpoint = '/'.API_VERSION.$m[1];
		
		return 'https://'.$apiHost.$apiEndpoint;
	}

	function setupWidget($endpoint, $apiKey, $donorEmail, $donorFirstName, $donorLastName, $extraInfo)
	{
		// prepare curl
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		
		// setup request - POST to the endpoint with user data
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array(
			'apiKey' => $apiKey,
			'donorEmail' => $donorEmail,
			'donorFirstName' => $donorFirstName,
			'donorLastName' => $donorLastName,
			'extraInfo' => $extraInfo,
		));
		
		// execute request
		$result = curl_exec($ch);
		if ($result === false) {
			throw new Exception('Curl error: ' . curl_error($ch));
		}
		
		$info = curl_getinfo($ch);
		$json = json_decode($result, true);
		$httpStatus = $info['http_code'];
		
		// API returned an error
		if ($httpStatus != 201) {
			$error = 'unknown';
			if (!empty($json['errorCodes'])) {
				$codes=implode(', ', $json['errorCodes']);
			}
			throw new Exception('API returned errors: '.$error);
		}
		
		// API returned success
		return $json;
	}
	
	function getOrderSignature($orderId, $salt, $secret)
	{
		return strtoupper(sha1($orderId.'.'.$salt.'.'.$secret));
	}
	
	function getSignedWidgetCode($widgetCode, $orderId, $signature)
	{
		$widgetUrl = parseWidgetUrlFromWidgetCode($widgetCode);
		
		$append  = (strpos($widgetUrl, '?') !== false ? '&' : '?');
		$append .= 'orderId=';
		$append .= urlencode($orderId);
		$append .= '&';
		$append .= 'signature=';
		$append .= urlencode($signature);
		
		$modifiedUrl = $widgetUrl.$append;
		return str_replace($widgetUrl, $modifiedUrl, $widgetCode);
	}
