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
        <div class="card">
            <img src="YouChef.jpg" alt="Logo YouChef" style="max-width: 200px; display: block; margin: 0 auto;">
            <h3>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>
            <h2>MENU</h2>
        </div>
        <form method="post">
            <div class="cardMenu">
                <button type="submit" name="azione" value="esegui">Esegui Ricetta</button>
                <p>Mettiti alla prova davanti ai fornelli! Scegli la ricetta che vuoi provare dal nostro ricettario e lasciati guidare dal nostro sistema nella preparazione, attraverso il monitoraggio della temperatura di cottura supportato da timer.</p>
                <button type="submit" name="azione" value="sensori">Usa solo sensori</button>
                <p>Se già conosci la procedura necessaria per la ricetta che vuoi eseguire, puoi utilizzare il sistema esclusivamente per monitorare la temperatura nella pentola/padella ed impostare timer.</p>
                <button type="submit" name="azione" value="aggiungi">Aggiungi Ricetta</button>
                <p>Se vuoi inserire una ricetta nel ricettario, segui le istruzioni per farlo, si raccomanda di rispettare il formato indicato, per evitare errori.(La ricetta aggiunta risulterà come non certificata)</p>
            </div>
        </form>
    </div>
</body>
</html>
