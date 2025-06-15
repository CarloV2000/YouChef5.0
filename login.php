<?php
include 'db.php';
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: menu.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            z-index: -1;
            opacity: 0;
            transition: opacity 2s ease-in-out;
        }
        .background.active {
            opacity: 1;
        }
    </style>
</head>
<body>

    <!-- Sfondo dinamico -->
    <div id="background1" class="background"></div>
    <div id="background2" class="background"></div>

    <div class="card">
        <h1>Login</h1>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['registered'])): ?>
            <div class="success">Registration successful! Please login.</div>
        <?php endif; ?>
        <form action="login.php" method="post">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>

    <script>
        const backgrounds = [
            'zzzsfondoPaesaggio.jpg',
            'zzzsfondoCibo3.jpg',
            'zzzsfondo13.jpg',
            'zzzsfondo15.jpg'
        ];

        let index = 1; // inizia dal secondo sfondo
        let current = 0;

        const bgDivs = [
            document.getElementById('background1'),
            document.getElementById('background2')
        ];

        window.onload = () => {
            // Imposta subito il primo sfondo
            bgDivs[0].style.backgroundImage = `url('${backgrounds[0]}')`;
            bgDivs[0].classList.add('active');

            // Avvia la rotazione dopo 5 secondi
            setTimeout(() => {
                setInterval(changeBackground, 9000);
            }, 5000);
        };

        function changeBackground() {
            const next = (current + 1) % 2;
            const image = `url('${backgrounds[index]}')`;

            bgDivs[next].style.backgroundImage = image;
            bgDivs[next].classList.add('active');
            bgDivs[current].classList.remove('active');

            current = next;
            index = (index + 1) % backgrounds.length;
            }
    </script>
</body>
</html>
