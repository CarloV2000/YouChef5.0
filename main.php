<?php
error_reporting(0);
ini_set('display_errors', 0);

include 'db.php';

function cleanInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// GESTIONE FORM POST PER INSERIMENTO RICETTA E PASSAGGI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inserisciRicetta'])) {
    // Prendi e pulisci dati principali
    $nome = cleanInput($_POST['nome'] ?? '');
    $categoria = cleanInput($_POST['categoria'] ?? '');
    $tempoStimato = intval($_POST['tempoStimato'] ?? 0);
    $ingredienti = cleanInput($_POST['ingredienti'] ?? '');
    $ricettaCertificata = isset($_POST['ricettaCertificata']) ? 1 : 0;

    if (!$nome || !$categoria || !$tempoStimato || !$ingredienti) {
        $error = "Compila tutti i campi obbligatori!";
    } else {
        // Inserisci ricetta
        $stmt = $conn->prepare("INSERT INTO recipes (nome, categoria, tempoStimato, ingredienti, ricettaCertificata) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisi", $nome, $categoria, $tempoStimato, $ingredienti, $ricettaCertificata);
        if ($stmt->execute()) {
            $ricettaId = $stmt->insert_id;
            $stmt->close();

            // Inserisci i passaggi
            if (!empty($_POST['passaggi']) && is_array($_POST['passaggi'])) {
                $stmtStep = $conn->prepare("INSERT INTO steps (ricetta_id, passaggio, ordine, tempo, temperaturamin1, temperaturamax1, temperaturamin2, temperaturamax2, timerRichiesto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

                foreach ($_POST['passaggi'] as $step) {
                    $passaggio = cleanInput($step['descrizione'] ?? '');
                    $ordine = intval($step['ordine'] ?? 0);
                    $tempo = intval($step['tempo'] ?? 0);
                    $tmin1 = isset($step['temperaturamin1']) ? intval($step['temperaturamin1']) : 0;
                    $tmax1 = isset($step['temperaturamax1']) ? intval($step['temperaturamax1']) : 0;
                    $tmin2 = isset($step['temperaturamin2']) ? intval($step['temperaturamin2']) : 0;
                    $tmax2 = isset($step['temperaturamax2']) ? intval($step['temperaturamax2']) : 0;
                    $timerRichiesto = intval($step['timerRichiesto'] ?? 0);

                    $stmtStep->bind_param("isiiiiiii", $ricettaId, $passaggio, $ordine, $tempo, $tmin1, $tmax1, $tmin2, $tmax2, $timerRichiesto);
                    $stmtStep->execute();
                }
                $stmtStep->close();

                $success = "Ricetta aggiunta con successo!";
            } else {
                $error = "Devi aggiungere almeno un passaggio!";
            }
        } else {
            $error = "Errore nell'inserimento della ricetta: " . $stmt->error;
        }
    }
}

$recipes = [];
$passaggi = [];
$currentStep = 0;
$soloRicetteCertificate = isset($_POST['soloRicetteCertificate']) ? 1 : 0;

// Gestione selezione categoria
if (isset($_POST['category'])) {
    $categoria = $_POST['category'];

    if ($soloRicetteCertificate) {
        $query = "SELECT * FROM recipes WHERE categoria = ? AND ricettaCertificata = 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$categoria]);
    } else {
        $query = "SELECT * FROM recipes WHERE categoria = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$categoria]);
    }

    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Gestione selezione ricetta
if (isset($_POST['nome'])) {
    $queryRicetta = "SELECT id FROM recipes WHERE nome = ?";
    $stmtRicetta = $pdo->prepare($queryRicetta);
    $stmtRicetta->execute([$_POST['nome']]);
    $ricetta = $stmtRicetta->fetch(PDO::FETCH_ASSOC);

    if ($ricetta) {
        $ricetta_id = $ricetta['id'];

        $queryPassaggi = "SELECT * FROM steps WHERE ricetta_id = ? ORDER BY ordine ASC";
        $stmtPassaggi = $pdo->prepare($queryPassaggi);
        $stmtPassaggi->execute([$ricetta_id]);
        $passaggi = $stmtPassaggi->fetchAll(PDO::FETCH_ASSOC);

        $currentStep = isset($_POST['step']) ? intval($_POST['step']) : 0;
    }
}
?>

<main class="main">
    <h2>Selezionare la categoria di ricetta</h2>

    <!-- MOSTRA I BOTTONI SOLO SE NON CI SONO PASSAGGI DA MOSTRARE -->
    <?php if (empty($passaggi)): ?>
    <form method="post" style="margin-bottom: 20px;">
        <label style="border: 1px solid #000; padding: 5px; display: inline-block;">
            <input type="checkbox" name="soloRicetteCertificate" value="1" <?php echo $soloRicetteCertificate ? 'checked' : ''; ?>>
            Solo ricette certificate
        </label>
        <div class="buttons" style="margin-top: 10px;">
            <button name="category" value="primo">Primo</button>
            <button name="category" value="secondo">Secondo</button>
            <button name="category" value="contorno">Contorno</button>
        </div>
    </form>
    <?php endif; ?>

    <?php if (!empty($recipes) && empty($passaggi)): ?>
    <h3>Ricette: <?php echo ucfirst(htmlspecialchars($categoria)); ?>.</h3>
    <div class="table-container">
        <table class="responsive-tableRicette">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Tempo</th>
                    <th>Ingredienti</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recipes as $recipe): ?>
                    <tr>
                        <td>
                            <form method="post">
                                <input type="hidden" name="nome" value="<?php echo htmlspecialchars($recipe['nome']); ?>">
                                <button type="submit">
                                    <?php echo htmlspecialchars($recipe['nome']); ?>
                                </button>
                            </form>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($recipe['tempoStimato']); ?> min
                        </td>
                        <td>
                            <div class="ingredienti-scroll">
                                <?php echo htmlspecialchars($recipe['ingredienti']); ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php elseif (!empty($passaggi)): ?>
        <?php
        if ($currentStep < count($passaggi)) {
            $stepData = $passaggi[$currentStep];
            $descrizione = $stepData['passaggio'];
            $timer = $stepData['tempo'];
            $tempMin1 = $stepData['temperaturamin1'];
            $tempMax1 = $stepData['temperaturamax1'];
            $tempMin2 = $stepData['temperaturamin2'];
            $tempMax2 = $stepData['temperaturamax2'];
            $timerRichiesto = $stepData['timerRichiesto'];
        ?>
        <h3>Passaggio <?php echo $currentStep + 1; ?> di <?php echo count($passaggi); ?></h3>
        <p><?php echo htmlspecialchars($descrizione); ?></p>
        <table class="responsive-table">
            <tr>
                <td><strong>Timer</strong></td>
                <td>
                    <?php echo $timerRichiesto ? $timer . " secondi" : "Non richiesto"; ?>
                </td>
            </tr>

            <tr>
                <td><strong>Temperatura sensore 1</strong></td>
                <td>
                    <?php if ($tempMin1 == 0 && $tempMax1 == 0): ?>
                        Non necessaria
                    <?php else: ?>
                        <?php echo $tempMin1; ?> - <?php echo $tempMax1; ?> °C
                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <td><strong>Temperatura sensore 2</strong></td>
                <td>
                    <?php if ($tempMin2 == 0 && $tempMax2 == 0): ?>
                        Non necessaria
                    <?php else: ?>
                        <?php echo $tempMin2; ?> - <?php echo $tempMax2; ?> °C
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    
        <form method="post">
            <input type="hidden" name="nome" value="<?php echo htmlspecialchars($_POST['nome']); ?>">
            <input type="hidden" name="step" value="<?php echo $currentStep + 1; ?>">
            <button type="submit">Procedi</button>
        </form>

        <?php if ($currentStep > 0): ?>
            <form method="post">
                <input type="hidden" name="nome" value="<?php echo htmlspecialchars($_POST['nome']); ?>">
                <input type="hidden" name="step" value="<?php echo $currentStep - 1; ?>">
                <button type="submit">Indietro</button>
            </form>           
        <?php endif; ?>
        <?php if ($currentStep == 0): ?>
            <form method="post">
                <button type="submit">Esci</button>
            </form>
        <?php endif; ?>

        <?php 
        } else { ?>
            <h3>Ricetta completata!</h3>
            <form method="post">
                <button type="submit">Torna alla selezione</button>
            </form>
        <?php } ?>
    <?php endif; ?>
</main>
