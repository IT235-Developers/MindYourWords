<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Users</title>
</head>
<body>
	<textarea id="textInput" rows="4" cols="50" placeholder="Type here"></textarea>
	<button onclick="textToSpeech()">Speak</button>

	<script type="text/javascript">
		function textToSpeech(){
			let text = document.getElementById('textInput').value;
			let speech = new SpeechSynthesisUtterance();
			speech.text = text;
			speech.volume = 1;
			speech.rate = 1;
			speech.pitch = 1;
			window.speechSynthesis.speak(speech);
		}
	</script>
</body>
</html>