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
            <h1>YouChef</h1>
        </header>
        <div class="welcome">
            <h3>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>

        <?php
            if ($azione === 'esegui') {
                include 'main.php';
                include 'nav.php';
            } elseif ($azione === 'sensori') {
                include 'nav.php';
            } elseif ($azione === 'aggiungi') {
            //echo "<p>Funzionalità in sviluppo: Aggiungere Ricetta</p>";
                include 'aggiungi_ricetta.php';
            }
        ?>
    <?php include 'footer.php'; ?>
    </div>
</div>
</body>
</html>
