<?php
include 'db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            $error = 'Username or email already exists.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed_password])) {
                header('Location: login.php?registered=1');
                exit;
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
        <h2>Register</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="register.php" method="post">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
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
