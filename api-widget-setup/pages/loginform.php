<!doctype html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Log in</title>
	</head>
	<body>
		<h1>Log in:</h1>
		<? if (!empty($_POST['user'])) { ?>
			<p>Unable to log in</p>
		<? } ?>
		<form method="post" action="">
			<select name="user">
				<option value="roy">Roy Trenneman</option>
				<option value="maurice">Maurice Moss</option>
				<option value="jen">Jen Barber</option>
				<option value="evil">Evil Hacker</option>
			</select>
			<button type="submit">Log in</button>
		</form>
	</body>
</html>
