<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['email'])) {
    echo "<script>alert('Access denied! Please log in first.');window.location='../auth/login.php';</script>";
    exit;
}

$admin_email = $_SESSION['email'];

// Ensure upload folder exists
$uploadDir = "../uploads/messages/";
if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

$selected_user = isset($_GET['user']) ? $_GET['user'] : null;

// Send reply
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['selected_user'])) {
    $selected_user = $_POST['selected_user'];
    $message = trim($_POST['message']);
    $imagePath = null;

    if (!empty($_FILES['image']['name'])) {
        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $uploadDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowed)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $imagePath = "uploads/messages/" . $fileName;
            }
        }
    }

    if (!empty($message) || $imagePath) {
        $sql = "INSERT INTO messages (sender_email, receiver_email, message, image) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $admin_email, $selected_user, $message, $imagePath);
        mysqli_stmt_execute($stmt);
    }

    header("Location: messenger.php?user=" . urlencode($selected_user));
    exit;
}

// ✅ Fetch ALL registered users (excluding the admin)
$sql_users = "SELECT email FROM users WHERE email != '$admin_email' ORDER BY email ASC";
$users_result = mysqli_query($conn, $sql_users);

// Fetch chat with selected user
$chat_result = null;
if ($selected_user) {
    $sql = "SELECT * FROM messages 
            WHERE (sender_email=? AND receiver_email=?) 
               OR (sender_email=? AND receiver_email=?) 
            ORDER BY date_sent ASC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $admin_email, $selected_user, $selected_user, $admin_email);
    mysqli_stmt_execute($stmt);
    $chat_result = mysqli_stmt_get_result($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Messenger</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

* {
  box-sizing: border-box;
}

body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  background: #0a0a23;
  color: #fff;
  display: flex;
  flex-direction: column;
  align-items: center;
  min-height: 100vh;
  padding: 30px;
}

/* ===== Layout ===== */
.container {
  display: flex;
  gap: 20px;
  width: 100%;
  max-width: 1100px;
  flex-wrap: wrap;
}

/* ===== User List Panel ===== */
.users-list {
  flex: 1 1 260px;
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(12px);
  border-radius: 18px;
  border: 1px solid rgba(0, 255, 153, 0.3);
  box-shadow: 0 0 25px rgba(0, 255, 153, 0.15);
  max-height: 550px;
  overflow-y: auto;
  padding: 20px;
}

.users-list h3 {
  text-align: center;
  color: #00ff99;
  margin-bottom: 20px;
  font-weight: 600;
  text-shadow: 0 0 8px rgba(0, 255, 153, 0.6);
}

.user-link {
  display: block;
  padding: 12px 14px;
  margin-bottom: 10px;
  text-decoration: none;
  color: #ccc;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.08);
  transition: 0.3s ease;
}

.user-link:hover,
.user-link.active {
  background: #00ff99;
  color: #0a0a23;
  font-weight: 600;
  box-shadow: 0 0 12px rgba(0, 255, 153, 0.6);
}

/* ===== Chat Box ===== */
.chat-box {
  flex: 3 1 600px;
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(12px);
  border-radius: 18px;
  border: 1px solid rgba(0, 255, 153, 0.3);
  box-shadow: 0 0 25px rgba(0, 255, 153, 0.2);
  display: flex;
  flex-direction: column;
  height: 550px;
  position: relative;
  overflow: hidden;
}

/* ===== Chat Header ===== */
.chat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 18px;
  background: rgba(255, 255, 255, 0.08);
  border-bottom: 1px solid rgba(0, 255, 153, 0.3);
}

.chat-header h3 {
  color: #00ff99;
  font-weight: 600;
  font-size: 1.2rem;
  margin: 0;
  letter-spacing: 0.5px;
}

.back-btn {
  background: #00ff99;
  color: #0a0a23;
  border: none;
  padding: 8px 14px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: bold;
  transition: 0.3s;
}
.back-btn:hover {
  background: #00cc7a;
  box-shadow: 0 0 12px rgba(0, 255, 153, 0.6);
}

/* ===== Chat Messages ===== */
.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 25px;
  display: flex;
  flex-direction: column;
  background: rgba(10, 10, 35, 0.4);
}

.msg {
  margin: 10px 0;
  padding: 14px 18px;
  border-radius: 16px;
  max-width: 70%;
  word-wrap: break-word;
  font-size: 15px;
  position: relative;
  animation: fadeIn 0.3s ease-in-out;
  line-height: 1.4;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
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

.received .timestamp {
  color: #777;
}

/* ===== Message Form ===== */
form {
  display: flex;
  flex-direction: column;
  padding: 18px;
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(10px);
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

/* ===== Scrollbar ===== */
::-webkit-scrollbar {
  width: 8px;
}
::-webkit-scrollbar-thumb {
  background: #00ff99;
  border-radius: 4px;
}

/* ===== Responsive ===== */
@media (max-width: 900px) {
  .container { flex-direction: column; }
  .chat-box { height: auto; }
  .users-list { max-height: none; }
}

@media (max-width: 700px) {
  .msg {
    max-width: 85%;
    font-size: 14px;
  }
  .chat-box {
    height: 60vh;
  }
}

</style>
</head>
<body>

<div class="container">
  <div class="users-list">
    <h3>💬 All Users</h3>
    <?php while ($u = mysqli_fetch_assoc($users_result)): ?>
      <a class="user-link <?php echo ($selected_user == $u['email']) ? 'active' : ''; ?>" href="?user=<?php echo urlencode($u['email']); ?>">
        <?php echo htmlspecialchars($u['email']); ?>
      </a>
    <?php endwhile; ?>
  </div>

  <div class="chat-box">
    <?php if ($selected_user && $chat_result): ?>
      <div class="chat-header">
        <h3>Chat with <?php echo htmlspecialchars($selected_user); ?></h3>
        <button class="back-btn" onclick="window.location='messenger.php'">← Back</button>
      </div>

      <div class="chat-messages" id="chatMessages">
        <?php while ($row = mysqli_fetch_assoc($chat_result)): ?>
          <div class="msg <?php echo ($row['sender_email'] == $admin_email) ? 'sent' : 'received'; ?>">
            <?php if (!empty($row['message'])) echo nl2br(htmlspecialchars($row['message'])); ?>
            <?php if (!empty($row['image'])): ?>
              <img src="../<?php echo htmlspecialchars($row['image']); ?>" class="chat-img">
            <?php endif; ?>
            <div class="timestamp"><?php echo date("F j, Y g:i A", strtotime($row['date_sent'])); ?></div>
          </div>
        <?php endwhile; ?>
      </div>

      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="selected_user" value="<?php echo htmlspecialchars($selected_user); ?>">
        <textarea name="message" placeholder="Type your reply..."></textarea>
        <input type="file" name="image" accept="image/*">
        <button type="submit">Send</button>
      </form>

    <script>
    // 🟢 Function to load messages repeatedly
    function loadMessages() {
      fetch('fetch_messages.php?receiver=<?php echo urlencode($selected_user); ?>')
        .then(response => response.text())
        .then(html => {
          const chatBox = document.getElementById('chatMessages');
          chatBox.innerHTML = html;
          chatBox.scrollTop = chatBox.scrollHeight; // auto scroll to bottom
        });
    }

    // Load every 2 seconds
    setInterval(loadMessages, 2000);
    loadMessages(); // initial load

    // 🟢 Send message without reloading
    document.getElementById('chatForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);

      fetch('send_message.php', {
        method: 'POST',
        body: formData
      }).then(() => {
        document.getElementById('messageInput').value = '';
        loadMessages();
      });
    });
    </script>

    <?php else: ?>
      <h3 style="text-align:center; color:#aaa;">Select a user to start chatting 💬</h3>
    <?php endif; ?>
  </div>
</div>
    

</body>
</html>
