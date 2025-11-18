<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['email'])) {
    echo "<script>alert('Access denied! Please log in first.');window.location='../auth/login.php';</script>";
    exit;
}

$user_email = $_SESSION['email'];
$admin_email = "admin@gmail.com"; // ✅ Set your real admin email
$selected_user = $admin_email;

$uploadDir = "../uploads/messages/";
if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Messenger | Lost & Found</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
/* Same dark neon theme */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: #0a0a23;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 100vh;
    padding: 30px;
    color: #fff;
}
.top-bar {
    width: 100%;
    max-width: 800px;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 20px;
}
h3 {
    color: #00ff99;
    text-align: center;
    margin: 0;
    font-weight: 600;
    font-size: 1.8rem;
    letter-spacing: 1px;
}
.chat-container {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 18px;
    box-shadow: 0 0 25px rgba(0, 255, 153, 0.2);
    width: 100%;
    max-width: 800px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid rgba(0, 255, 153, 0.3);
}
.chat-box {
    flex: 1;
    padding: 25px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    height: 70vh;
    scrollbar-width: thin;
}
.msg {
    margin: 10px 0;
    padding: 14px 18px;
    border-radius: 16px;
    max-width: 70%;
    word-wrap: break-word;
    font-size: 15px;
    line-height: 1.4;
    animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(5px);}
    to {opacity: 1; transform: translateY(0);}
}
.sent {
    background: #00ff99;
    color: #0a0a23;
    margin-left: auto;
    border-bottom-right-radius: 4px;
    box-shadow: 0 0 12px rgba(0, 255, 153, 0.6);
    font-weight: 500;
}
.received {
    background: rgba(255, 255, 255, 0.08);
    color: #e0e0e0;
    border-bottom-left-radius: 4px;
    border: 1px solid rgba(0, 255, 153, 0.2);
}
.msg img.chat-img {
    max-width: 220px;
    border-radius: 10px;
    margin-top: 8px;
    display: block;
    border: 1px solid rgba(0, 255, 153, 0.3);
}
.timestamp {
    font-size: 11px;
    text-align: right;
    color: #999;
    margin-top: 6px;
}
form {
    display: flex;
    flex-direction: column;
    padding: 18px;
    background: rgba(255, 255, 255, 0.05);
    border-top: 1px solid rgba(0, 255, 153, 0.3);
}
textarea {
    resize: none;
    border: none;
    border-radius: 10px;
    padding: 14px;
    font-size: 15px;
    outline: none;
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    min-height: 70px;
}
input[type=file] {
    margin-top: 10px;
    font-size: 14px;
    color: #00ff99;
}
button {
    margin-top: 12px;
    padding: 12px;
    border: none;
    border-radius: 10px;
    background: #00ff99;
    color: #0a0a23;
    font-weight: 600;
    cursor: pointer;
    font-size: 15px;
    transition: 0.3s;
}
button:hover {
    transform: translateY(-2px);
    background: #00cc7a;
    box-shadow: 0 0 18px rgba(0, 255, 153, 0.6);
}
</style>
</head>
<body>

<div class="top-bar">
    <h3>💬 Chat with Admin</h3>
</div>

<div class="chat-container">
    <div class="chat-box" id="chatMessages">
        <!-- Messages will load here dynamically -->
    </div>

    <form id="chatForm" enctype="multipart/form-data">
      <textarea name="message" id="messageInput" placeholder="Type your message..."></textarea>
      <input type="file" name="image" accept="image/*">
      <button type="submit">Send</button>
    </form>
</div>

<script>
// Load messages repeatedly (AJAX polling)
function loadMessages() {
  fetch('fetch_messages.php?receiver=<?php echo urlencode($selected_user); ?>')
    .then(response => response.text())
    .then(html => {
      const chatBox = document.getElementById('chatMessages');
      chatBox.innerHTML = html;
      chatBox.scrollTop = chatBox.scrollHeight; // Auto-scroll
    });
}

// Load every 2 seconds
setInterval(loadMessages, 2000);
loadMessages();

// Send message without reload
document.getElementById('chatForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  formData.append('selected_user', '<?php echo $selected_user; ?>');

  fetch('send_message.php', {
    method: 'POST',
    body: formData
  }).then(() => {
    document.getElementById('messageInput').value = '';
    loadMessages();
  });
});
</script>

</body>
</html>
