<?php
	// Header to make CORS work in local development
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: 'DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Content-Range,Range,Authorization'");
	require_once './core/init.php';
?>
<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="UTF-8">
	<title>api-dev.eatable.ch</title>
	<script src="https://admin.eatable.ch/js/axios/axios.min.js"></script>
</head>
<body>
	<button onclick="deleteToken()">deleteToken</button>
	<script>
		function deleteToken() {
			axios.post('http://api-dev.eatable.test/index.php', {
				action: 'getSingleShop',
				shopId: '1'
			}).then(function(response) {
				console.log(response.data);
			});
		}
	</script>
</body>
</html>