<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Kasir</title>
    <style>
        body {
            background: #f3f4f6;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-box {
            background: white;
            padding: 32px;
            width: 350px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        button {
            margin-top: 16px;
            width: 100%;
            padding: 10px;
            background: #24292f;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        button:hover {
            background: #000;
        }
        .error {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Login Kasir Laichi</h2>
    <form method="POST">
        @csrf
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <button type="submit">Masuk</button>
    </form>
</div>

</body>
</html>
