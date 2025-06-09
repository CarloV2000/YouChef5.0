# YouChef5.0
Ora l'applicazione è strutturata nel seguente modo:  
1)login/register  
2)Scelta tra eseguiRicetta, usaSoloTimereTemperature e aggiungiRicetta  
3a)aggiungiRicetta: campi da compilare nel formato specificato permettono agli utenti di inserire direttamente nel database le ricette, tramite inserimento step by step dei passaggi; queste ricette verranno comunque spuntate con l'attributo ricettaUfficiale=0, mentre quelle inserite dal team YouChef e certificate sono =1.  
3b)usaSoloTimereTemperature: mostra la tabella con le temperature misurate e i timer inseribili (utile per chi conosce già le ricette e bvuole solo controllare tempi e temperature)  
3c)eseguiRicetta: selezione categoria ricetta e spunta sul filtro se intende filtrare solo le ricette ufficiali(quelle non inserite da utenti).  
4c)selezione ricetta da eseguire tramite lettura da database  
5c)guida passaggio per passaggio nella ricetta tramite lettura da database(tabella steps) e check continuo sulla temperatura rilevata sulla padella/pentola con possibilità di inserimento manuale di timer(anche più di uno contemp.)
