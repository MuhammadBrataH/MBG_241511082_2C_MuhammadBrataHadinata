<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            width: 350px;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            text-align: center;
        }

        .login-box h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .login-box input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .login-box button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            background: #007bff;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .login-box button:hover {
            background: #0056b3;
        }

        .error {
            margin-bottom: 10px;
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <img src="https://img.icons8.com/color/96/000000/student-male--v2.png" alt="Logo" style="margin-bottom:18px;">
        <h2>Login</h2>

        <?php if(session()->getFlashdata('error')): ?>
            <p class="error"><?= session()->getFlashdata('error') ?></p>
        <?php endif; ?>

        <form method="post" action="/login/auth">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
    <style>
        .login-box {
            animation: fadeInDown 0.7s;
        }
        @keyframes fadeInDown {
            from { opacity:0; transform:translateY(-40px); }
            to { opacity:1; transform:translateY(0); }
        }
    </style>
</body>
</html>
