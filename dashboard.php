<?php
session_start();
if (!isset($_SESSION['azione'])) {
    header('Location: menu.php');
    exit;
}

$azione = $_SESSION['azione'];

include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class ="card">
        <header>
            <div class="card">
                <img src="YouChef.jpg" alt="Logo YouChef" style="max-width: 200px; display: block; margin: 0 auto;">
            </div>
        </header>
        <div class="welcome">
            <?php
                if ($azione === 'esegui') {
                    include 'main.php';
                    include 'nav.php';
                    include 'timer.php'; 
                } elseif ($azione === 'sensori') {
                    include 'nav.php';
                    include 'timer.php'; 
                } elseif ($azione === 'aggiungi') {
                    include 'aggiungi_ricetta.php';
                }
            ?>
            <?php include 'footer.php'; ?>
        </div>
    </div>
</body>
</html>
