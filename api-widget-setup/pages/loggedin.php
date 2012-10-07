<!doctype html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Logged in</title>
	</head>
	<body>
		<h1>You are: <?=htmlspecialchars($_SESSION['user']['firstName'].' '.$_SESSION['user']['lastName'].', '.$_SESSION['user']['position']);?></h1>
		<form method="post" action="">
			<input type="hidden" name="logout" value="true" />
			<button type="submit">Log out</button>
		</form>
		
		<? if (!empty($signedWidgetCode)) { ?>
			
			<h2>Donate:</h2>
			<?=$signedWidgetCode;?>
		
		<? } else { ?>
		
			<form method="post" action="">
				<input type="hidden" name="donate" value="true" />
				<button type="submit">Donate</button>
			</form>
		
		<? } ?>
	</body>
</html>
