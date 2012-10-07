<?
	session_start();
	
	require_once('config.php');
	require_once('func.php');
	
	/********************************** Execute POST actions ******************************/
	
	// trying to log in?
	if (!empty($_POST['user'])) {
		$user = getUser($_POST['user']);
		if (!empty($user)) {
			$_SESSION['user'] = $user;
		}
	}
	
	// trying to log out?
	if (!empty($_POST['logout'])) {
		unset($_SESSION['user']);
	}
	
	// trying to donate?
	if (!empty($_POST['donate']) && !empty($_SESSION['user'])) {
	
		$apiEndpoint = parseApiEndpointFromWidgetCode(WIDGET_CODE);
		
		$user = $_SESSION['user'];
		
		// call widget setup endpoint
		$orderData = setupWidget($apiEndpoint, API_KEY, $user['email'], $user['firstName'], $user['lastName'], $user['position']);
	
		// calculate signature
		$signature = getOrderSignature($orderData['orderId'], $orderData['salt'], API_SECRET);
	
		// append signature params to the widget code
		$signedWidgetCode = getSignedWidgetCode(WIDGET_CODE, $orderData['orderId'], $signature);
	}
	
	
	/*************************************** Render page **********************************/
	if (empty($_SESSION['user'])) {
		include('pages/loginform.php');
		exit;
	} else {
		include('pages/loggedin.php');
		exit;
	}
	
	
	
	/****************** A hardcoded implementation of the "log in" routine ****************/
	function getUser($login)
	{
		$users = array(
			'roy' => array(
				'firstName' => 'Roy',
				'lastName' => 'Trenneman',
				'email' => 'roy@example.com',
				'position' => 'IT Engineer',
			),
			'maurice' => array(
				'firstName' => 'Maurice',
				'lastName' => 'Moss',
				'email' => 'maurice@example.com',
				'position' => 'IT Engineer',
			),
			'jen' => array(
				'firstName' => 'Jen',
				'lastName' => 'Barber',
				'email' => 'jen@example.com',
				'position' => 'Relationship Manager',
			),
		);
		
		return !empty($users[$login]) ? $users[$login] : null;
	}
