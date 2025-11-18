<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register</title>
<link rel="icon" type="image/jpg" href="../images/L&F.jpg"></link>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap');

    body {
        margin: 0;
        font-family: 'Roboto', sans-serif;
        background-color: #0a0a23;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #00ff99;
    }

    .register-container {
        background-color: #0a0a23;
        border: 2px solid #00ff99;
        padding: 40px 30px;
        border-radius: 12px;
        width: 100%;
        max-width: 380px;
        text-align: center;
        box-shadow: 0 0 20px #00ff99;
    }

    h2 {
        margin-bottom: 30px;
        color: #00ff99;
    }

    input[type="text"], input[type="email"], input[type="password"], select {
        width: 92%;
        padding: 12px 15px;
        margin: 10px 0;
        border-radius: 8px;
        border: 1px solid #00ff99;
        background-color: #0a0a23;
        color: #00ff99;
        font-size: 16px;
        outline: none;
        transition: 0.3s;
    }

    input::placeholder, select option {
        color: #00ff99;
        opacity: 0.7;
    }

    input:focus, select:focus {
        border-color: #00ff99;
        box-shadow: 0 0 10px #00ff99;
    }

    button {
        width: 100%;
        padding: 12px;
        margin-top: 20px;
        border: none;
        border-radius: 8px;
        background-color: #00ff99;
        color: #0a0a23;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: 0.3s;
    }

    button:hover {
        background-color: #00cc7a;
    }

    p {
        margin-top: 20px;
        font-size: 16px;
        color: #00ff99;
        opacity: 0.8;
    }

    a {
        color: #00ff99;
        text-decoration: none;
        font-weight: 600;
        transition: 0.3s;
    }

    a:hover {
        text-shadow: 0 0 5px #00ff99;
    }
</style>
</head>
<body>

<div class="register-container">
    <h2>Create Account</h2>
    <form method="POST" action="process_register.php">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="">Select Role</option>
            <option value="finder">Finder</option>
            <option value="owner">Owner</option>
        </select>
        <button type="submit" name="register">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
</div>

</body>
</html>
