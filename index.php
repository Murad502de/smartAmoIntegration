<!DOCTYPE html>
<html>
<head>
	<title>amoTest Fern</title>
</head>
<body>
	<form action="oauthintegration_von_integrat/integration.php" method="post">
		<p>
			<input size="25" name="nameLead">
			<input id = "amoId" type="hidden" name="amoField">
		</p>
		<p><input type="submit" value="Отправить"></p>
	</form> 

	<script>
		function loadScript(src, callback)
		{
			let script = document.createElement('script');
			script.src = src;
			script.id = "amo_pixel_identifier_js";
			script.async = "async";

			window.onload = () => callback();

			document.head.append(script);
		}

		loadScript("https://piper.amocrm.ru/pixel/js/identifier/pixel_identifier.js", function(){
			setTimeout(() => {
				let id = window.AMOPIXEL_IDENTIFIER.getVisitorUid();
				console.log('hello ' + id);

				let amoField = document.querySelector('#amoId');
				amoField.value = id;
				console.log(amoField);
			}, 500);
		});
	</script>

	<script type="text/javascript">(function (w, d) {w.amo_pixel_token = 'p1Ip4iB8yqaEyXU9HPmcTA5oQYpKvIcrjLSQ7SHQL1ERgnJKZFbWcMk8qMiuPsoH';var s = document.createElement('script'), f = d.getElementsByTagName('script')[0];s.id = 'amo_pixel_js';s.type = 'text/javascript';s.async = true;s.src = 'https://piper.amocrm.ru/pixel/js/tracker/pixel.js?token=' + w.amo_pixel_token;f.parentNode.insertBefore(s, f);})(window, document);</script>
</body>
</html>