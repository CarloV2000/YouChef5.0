<?php
// CONFIGURAZIONE DB
$host = "localhost";
$user = "root";
$password = "root";
$dbname = "user_auth";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Funzione helper per pulizia input (semplice)
function cleanInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// GESTIONE FORM POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = cleanInput($_POST['nome'] ?? '');
    $categoria = cleanInput($_POST['categoria'] ?? '');
    $tempoStimato = intval($_POST['tempoStimato'] ?? 0);
    $ingredienti = cleanInput($_POST['ingredienti'] ?? '');
    $ricettaCertificata = 0; // default 0 per ogni nuova ricetta

    if (!$nome || !$categoria || !$tempoStimato || !$ingredienti) {
        $error = "Compila tutti i campi obbligatori!";
    } else {
        $stmt = $conn->prepare("INSERT INTO recipes (nome, categoria, tempoStimato, ingredienti, ricettaCertificata) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisi", $nome, $categoria, $tempoStimato, $ingredienti, $ricettaCertificata);

        if ($stmt->execute()) {
            $ricettaId = $stmt->insert_id;
            $stmt->close();

            if (!empty($_POST['passaggi']) && is_array($_POST['passaggi'])) {
                $stmtStep = $conn->prepare("INSERT INTO steps (ricetta_id, passaggio, ordine, tempo, temperaturamin1, temperaturamax1, temperaturamin2, temperaturamax2, timerRichiesto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

                foreach ($_POST['passaggi'] as $step) {
                    $passaggio = cleanInput($step['descrizione'] ?? '');
                    $ordine = intval($step['ordine'] ?? 0);
                    $tempo = intval($step['tempo'] ?? 0);
                    $tmin1 = isset($step['temperaturamin1']) ? intval($step['temperaturamin1']) : null;
                    $tmax1 = isset($step['temperaturamax1']) ? intval($step['temperaturamax1']) : null;
                    $tmin2 = isset($step['temperaturamin2']) ? intval($step['temperaturamin2']) : null;
                    $tmax2 = isset($step['temperaturamax2']) ? intval($step['temperaturamax2']) : null;
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
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <title>Aggiungi Ricetta</title>
    <script>
    let stepCount = 0;
    function addStep() {
        stepCount++;
        const container = document.getElementById("stepsContainer");
        const stepDiv = document.createElement("div");

        // Identificativi unici per ogni step
        const stepId = `step${stepCount}`;
        const timerSelectId = `timerSelect${stepCount}`;
        const tempoDivId = `tempoDiv${stepCount}`;
        const tempoInputId = `tempoInput${stepCount}`;

        stepDiv.innerHTML = `
            <h4>Passaggio ${stepCount}</h4>
            <label>Descrizione:</label>
            <input name="passaggi[${stepCount}][descrizione]" required placeholder="Inserire descrizione precisa"><br>
            
            <label>Ordine:</label>
            <input type="number" name="passaggi[${stepCount}][ordine]" value="${stepCount}" readonly><br>
            
            <label>Timer richiesto:</label>
            <select name="passaggi[${stepCount}][timerRichiesto]" id="${timerSelectId}" class="select-categoria" onchange="toggleTempo('${tempoDivId}', '${tempoInputId}', this.value)">
                <option value="1">Sì</option>
                <option value="0">No</option>
            </select><br><br>
            
            <div id="${tempoDivId}">
                <label>Tempo (min):</label>
                <input type="number" id="${tempoInputId}" name="passaggi[${stepCount}][tempo]" placeholder="timer da inserire nel passaggio, 0 se non necessario"><br>
            </div>
            
            <label>Temperatura min 1:</label>
            <input type="number" name="passaggi[${stepCount}][temperaturamin1]" placeholder="T(°C) min sensore1 (inserire solo il numero!), 0 se non necessario"><br>
            
            <label>Temperatura max 1:</label>
            <input type="number" name="passaggi[${stepCount}][temperaturamax1]" placeholder="T(°C) max sensore1 (inserire solo il numero!), 0 se non necessario"><br>
            
            <label>Temperatura min 2:</label>
            <input type="number" name="passaggi[${stepCount}][temperaturamin2]" placeholder="T(°C) min sensore2 (inserire solo il numero!), 0 se non necessario"><br>
            
            <label>Temperatura max 2:</label>
            <input type="number" name="passaggi[${stepCount}][temperaturamax2]" placeholder="T(°C) max sensore2 (inserire solo il numero!), 0 se non necessario"><br>
        `;

        container.appendChild(stepDiv);
    }

    // Funzione per mostrare/nascondere "Tempo (min)" e gestire valore
    function toggleTempo(tempoDivId, tempoInputId, value) {
        const tempoDiv = document.getElementById(tempoDivId);
        const tempoInput = document.getElementById(tempoInputId);
        if (value === "1") {
            tempoDiv.style.display = "block";
        } else {
            tempoDiv.style.display = "none";
            tempoInput.value = 0;
        }
    }
</script>

</head>
<body>
    <div class="card">
        <h1>Aggiungi una Nuova Ricetta</h1>
        <h2>Aggiungi Ricetta</h2>
        <p style="color:red;": >*Si ricorda di seguire il formato degli esempi, per evitare errori nel caricamento delle ricette nel database</p>
        <?php if(!empty($error)): ?>
            <div style="color:red;"><?= $error ?></div>
        <?php endif; ?>

        <?php if(!empty($success)): ?>
            <div style="color:green;"><?= $success ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Nome:</label><input type="text" name="nome" required placeholder="es.: pasta alla norma"><br>
            <label>Categoria:</label>
            <select name="categoria" required class="select-categoria">
                <option value="">-- Seleziona categoria --</option>
                <option value="antipasto">antipasto</option>
                <option value="primo">primo</option>
                <option value="secondo">secondo</option>
                <option value="contorno">contorno</option>
                <option value="dolce">dolce</option>
            </select><br>
            <label>Tempo Stimato (minuti):</label><input type="number" name="tempoStimato" required placeholder="es.: 30"><br>
            <label>Ingredienti:</label><input type="text" name="ingredienti" required placeholder="es.: Pollo(800 g) - rosmarino() - olio extravergine di oliva(30 g) - sale()"><br>
            <h3>Passaggi</h3>
            <div id="stepsContainer"></div>
            <button type="button" onclick="addStep()">Crea nuovo Passaggio</button><br><br>
            <button type="submit">Salva Ricetta</button>
        </form>
    </div>
</body>
</html>
