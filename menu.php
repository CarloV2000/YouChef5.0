<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['azione'])) {
        $_SESSION['azione'] = $_POST['azione'];
        header('Location: dashboard.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Menu Azione</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="card">
        <h2>Scegli cosa fare:</h2>
        <form method="post">
            <button type="submit" name="azione" value="esegui">Esegui Ricetta</button>
            <button type="submit" name="azione" value="sensori">Usa solo sensori</button>
            <button type="submit" name="azione" value="aggiungi">Aggiungi Ricetta</button>
        </form>
    </div>
</body>
</html>
