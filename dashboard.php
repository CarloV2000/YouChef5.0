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

    <!-- Script per lo sfondo dinamico con dissolvenza -->
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
