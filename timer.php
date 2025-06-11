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