<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Lettura dati ThingSpeak</title>
</head>
<div class="card">
    <h1>Dati sulle temperature</h1>
        
    <div class="table-container">
        <table class="responsive-table" id="dati-tabella">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Sensore1</th>
                    <th>Sensore2</th>
                </tr>
            </thead>
            <tbody id="tabella-dati">
                <tr>
                    <td colspan="3">Caricamento...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</div>
    <!-- Timer multipli -->
    <div id="timers-container" class="card">
        <h2>Imposta uno o più Timer</h2>
        <input type="number" id="minuti" placeholder="Minuti" min="1">
        <button onclick="avviaTimer()">Start Timer</button>
        <div id="lista-timer"></div>
    </div>

    <script>
        if (Notification.permission !== "granted") {
            Notification.requestPermission();
        }

        let timerCount = 0;
        const intervalli = {}; // per tenere traccia degli intervalli attivi

        function avviaTimer() {
            const minuti = parseInt(document.getElementById("minuti").value);
            if (isNaN(minuti) || minuti <= 0) {
                alert("Inserisci un numero di minuti valido.");
                return;
            }

            let secondi = minuti * 60;
            const idTimer = "timer-" + (++timerCount);
            const container = document.getElementById("lista-timer");
            const blocco = document.createElement("div");
            blocco.className = "timer-block";
            blocco.id = idTimer;
            blocco.innerHTML = `
                <div class ="timerCircondato">
                    <strong>Timer ${timerCount}</strong><br>
                    <span>Tempo rimanente: ${minuti}m 0s</span><br>
                    <button class="delete-btn" onclick="eliminaTimer('${idTimer}')">Elimina Timer</button>
                </div>
            `;

            container.appendChild(blocco);

            const span = blocco.querySelector("span");

            intervalli[idTimer] = setInterval(() => {
                const m = Math.floor(secondi / 60);
                const s = secondi % 60;
                span.textContent = `Tempo rimanente: ${m}m ${s}s`;

                if (secondi <= 0) {
                    clearInterval(intervalli[idTimer]);
                    span.textContent = "⏰ Timer scaduto!";
                    inviaNotifica("Timer " + timerCount, "⏰ Il timer impostato è terminato!");
                }

                secondi--;
            }, 1000);
        }

        function eliminaTimer(idTimer) {
            clearInterval(intervalli[idTimer]);
            delete intervalli[idTimer];
            const blocco = document.getElementById(idTimer);
            if (blocco) {
                blocco.remove();
            }
        }

        function inviaNotifica(titolo, messaggio) {
            if (Notification.permission === "granted") {
                new Notification(titolo, {
                    body: messaggio,
                    icon: "https://cdn-icons-png.flaticon.com/512/992/992700.png"
             });
            } else {
                alert(messaggio);
            }
        }
    </script>
    <script>
        const tempMin1 = <?php echo isset($tempMin1) ? $tempMin1 : 0; ?>;
        const tempMax1 = <?php echo isset($tempMax1) ? $tempMax1 : 0; ?>;
        const tempMin2 = <?php echo isset($tempMin2) ? $tempMin2 : 0; ?>;
        const tempMax2 = <?php echo isset($tempMax2) ? $tempMax2 : 0; ?>;

        function aggiornaDati() {
            fetch('get_data.php')
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById("tabella-dati");

                if (data.error) {
                    tbody.innerHTML = `<tr><td colspan="3">${data.error}</td></tr>`;
                    return;
                }

                const { timestamp, field2, field4 } = data;

                const status1 = (tempMin1 === 0 && tempMax1 === 0)
                    ? "Non richiesto o scollegato"
                    : (field2 >= tempMin1 && field2 <= tempMax1
                    ? "<span class='status in-range'>In range</span>"
                    : "<span class='status out-range'>Out of range</span>");

                const status2 = (tempMin2 === 0 && tempMax2 === 0)
                    ? "Non richiesto o scollegato"
                    : (field4 >= tempMin2 && field4 <= tempMax2
                    ? "<span class='status in-range'>In range</span>"
                    : "<span class='status out-range'>Out of range</span>");

                tbody.innerHTML = `
                    <tr>
                        <td>${timestamp}</td>
                        <td>${field2}</td>
                        <td>${field4}</td>
                    </tr>
                    <tr>
                        <td><strong>Stato</strong></td>
                        <td>${status1}</td>
                        <td>${status2}</td>
                    </tr>
                `;
            })
            .catch(error => {
                document.getElementById("tabella-dati").innerHTML = `<tr><td colspan="3">Errore: ${error}</td></tr>`;
            });
        }
        // Avvia aggiornamento ogni secondo
        setInterval(aggiornaDati, 1000);

        // Carica dati iniziali
        aggiornaDati();
    </script>
</body>
</html>