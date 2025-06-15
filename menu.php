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
        transition: opacity 1.8s ease-in-out;
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
                <p>Se vuoi inserire una ricetta nel ricettario, segui le istruzioni per farlo, si raccomanda di rispettare il formato indicato, per evitare errori. (La ricetta aggiunta risulterà come non certificata)</p>
            </div>
        </form>
    </div>

    <!-- Script per lo sfondo dinamico con dissolvenza -->
    <script>
        const backgrounds = [
            'zzzsfondoPaesaggio.jpg',
            'zzzsfondoCibo3.jpg',
            'zzzsfondo13.jpg',
            'zzzsfondo15.jpg'
        ];

        let index = 1; // partiamo dal secondo, perché il primo è iniziale
        let current = 0;

        const bgDivs = [
            document.getElementById('background1'),
            document.getElementById('background2')
        ];

        window.onload = () => {
            // Imposta subito il primo sfondo
            bgDivs[0].style.backgroundImage = `url('${backgrounds[0]}')`;
            bgDivs[0].classList.add('active');

            // Avvia la rotazione dopo un piccolo delay
            setTimeout(() => {
                setInterval(changeBackground, 9000);
            }, 5000); // 5 secondi di pausa prima di iniziare il ciclo
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
