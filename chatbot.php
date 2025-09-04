
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Chatbot</title>
	<link rel="stylesheet" href="assets/css/style.css">
	<style>
		.chat-container { max-width: 500px; margin: 40px auto; border: 1px solid #ccc; border-radius: 8px; padding: 20px; background: #fff; }
		.chat-messages { height: 300px; overflow-y: auto; border: 1px solid #eee; padding: 10px; margin-bottom: 10px; background: #fafafa; }
		.chat-input { display: flex; }
		.chat-input input { flex: 1; padding: 10px; border-radius: 4px; border: 1px solid #ccc; }
		.chat-input button { padding: 10px 20px; margin-left: 10px; border-radius: 4px; border: none; background: #007bff; color: #fff; }
		.user-msg { text-align: right; color: #007bff; margin: 5px 0; }
		.bot-msg { text-align: left; color: #333; margin: 5px 0; }
	</style>
</head>
<body>
	<div class="chat-container">
		<h2>Gemini Chatbot</h2>
		<div class="chat-messages" id="chatMessages"></div>
		<form id="chatForm" class="chat-input">
			<input type="text" id="userInput" placeholder="Type your message..." autocomplete="off" required />
			<button type="submit">Send</button>
		</form>
	</div>
	<script>
		const chatForm = document.getElementById('chatForm');
		const chatMessages = document.getElementById('chatMessages');
		const userInput = document.getElementById('userInput');

		function appendMessage(text, sender) {
			const msgDiv = document.createElement('div');
			msgDiv.className = sender === 'user' ? 'user-msg' : 'bot-msg';
			msgDiv.textContent = text;
			chatMessages.appendChild(msgDiv);
			chatMessages.scrollTop = chatMessages.scrollHeight;
		}

		chatForm.addEventListener('submit', function(e) {
			e.preventDefault();
			const message = userInput.value.trim();
			if (!message) return;
			appendMessage(message, 'user');
			userInput.value = '';
			fetch('ajax/gemini_chat.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: 'message=' + encodeURIComponent(message)
			})
			.then(res => res.json())
			.then(data => {
				appendMessage(data.reply, 'bot');
			})
			.catch(() => {
				appendMessage('Error: Could not get response.', 'bot');
			});
		});
	</script>
</body>
</html>
