# Menu principale
In questo documento trovi:

# Introduzione
SEPT (Social Engineering Prevention Tool) nasce con l'intento di proporre una infrastruttura all-in-one in grado di effettuare test interattivi sul modello di prevenzione SEADMv2 e simulazioni di attacchi di ingegneria sociale per fini di ricerca. Lo strumento fornisce un ambiente sicuro e facilmente installabile; alcune funzionalità non sono implementate nativamente e richiedono l'utilizzo di servizi: Typeform (per la creazione del modello SEADMv2, del sondaggio finale e del centro assistenza) e Iubenda (per la Privacy e Cookie Policy, e i termini e condizioni).

SEPT può essere facilmente installato in locale o su un server collegato ad un dominio pubblico; in ogni caso necessita di un server web Apache>2.4 con PHP>7, MySQL>2.3 e un servizio di posta elettronica. Nel caso in cui se ne fossi sprovvisti, leggere la parte _Preparazione dell'Ambiente di sviluppo_ della sezione _Installazione ed Utilizzo_ per lo sviluppo e l'installazione completa.

# Funzionamento
L’applicazione gestisce tanto l’interfaccia front-end, per l’utente a cui va somministrato il test, quanto l’interfaccia dell’amministratore, che permette visivamente di informare l’utente finale riguardo il progresso del test e di perpetrare l’attacco.

Durante la preparazione del test per la valutazione del modello SEADMv2 si è preferito sviluppare un applicativo “tutto in uno" in grado di offrire contemporaneamente tutte le funzioni necessarie per il completamento del test:

-   iscrizione anonimizzata degli utenti con somministrazione di un sondaggio iniziale;

-   invio automatico di e-mail per informare l’utente riguardo alle varie fasi del test;

-   somministrazione del modello SEADMv2 e del manuale sull’ingegneria sociale all’interno del portale;

-   sviluppo di un’area “amministratore" che permetta di:

    -   assegnare gli utenti ad un gruppo di lavoro;

    -   perpetrare un attacco di ingegneria sociale automatizzato,inviando un’email malevola (da un indirizzo falso) con il link al sito clone;

    -   attivare il sondaggio finale in-site informando l’utente via e-mail;

    -   informare l’utente quando il test è completato.

Utilizzando tale applicativo, colui che partecipa al test è in grado di trovare tutte le risorse di supporto necessarie direttamente sul sito (SEADMv2, il manuale sull’ingegneria sociale, un centro assistenza per chiedere chiarimenti); d’altra parte, il somministratore del test potrà facilmente adempiere a tutti i passaggi dell’esperimento attivandoli mediante “pulsanti" presenti nella relativa area di amministrazione.

# Installazione ed Utilizzo

***Nota di installazione***: Se non hai ancora strutturato un ambiente di sviluppo in grado di contenere l'infrastruttura, segui le istruzioni contenute nella sezione _Preparazione dell'ambiente di sviluppo_, altrimenti passa alla sezione _Installazione dell'Infrastruttura_.

## Progettazione e sviluppo del portale

### Preparazione dell’ambiente di sviluppo

Prima di procedere con la creazione del portale vero e proprio, è stato fondamentale impostare l’ambiente che l’avrebbe ospitato.

#### Creazione del VPS

Tra le varie alternative di hosting online è stato selezionato [DigitalOcean](https://digitalocean.com), per la facilità di utilizzo e i costi contenuti dei piani hosting. Dopo aver creato un account, si è proceduto con la creazione di un VPS (un “droplet", così come denominato da DigitalOcean). Nel caso in cui non fossi in grado di creare autonomamente un droplet e scegliessi DigitalOcean come host della tua infrastruttura, allora [segui questa procedura per la creazione del droplet](https://www.digitalocean.com/docs/droplets/how-to/create/).

***Nota di installazione:*** Per semplicità di utilizzo e coerenza con i passaggi qui mostrati, installare Ubuntu (ultima versione LTS) nel proprio droplet.

#### Installazione dell’infrastruttura web

Dopo aver completato la procedura di accesso al droplet, procedere con l’installazione dell’infrastruttura necessaria per far “girare" il server web:

1.  installazione di LAMP: avendo generato un VPS basato su Linux e utilizzando PHP come linguaggio di programmazione backend e MySQL come DBMS, si è deciso di installare la piattaforma software LAMP, contenente tutti gli applicativi necessari per far girare il portale web su distribuzione Linux, tra cui PHP, MySQL e il server web Apache ;

2.  installazione di PhpMyAdmin: per rendere più semplice l’utilizzo di MySQL, è stato installato PhpMyAdmin nel VPS, in questo modo le tabelle e gli utenti del DBMS possono essere gestite visivamente.

##### Inizializzazione del DBMS e creazione degli utenti
L'infrastruttura richiede per il collegamento con il DBMS due tipologie utenti con diritti diversi:

- utente con permessi di sola lettura: SELECT, SHOW VIEW
- utente con soli permessi di lettura e scrittura delle tabelle: SELECT, SHOW VIEW, INSERT INTO, UPDATE

_Attenzione: tienine a mente l'username e la password che serviranno in fase di configurazione dell'infrastruttura, nel file `config.php`._

***Note di installazione***: 
- per l'installazione di LAMP [seguire la procedura qui descritta](https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-ubuntu-18-04), selezionando la propria versione di Ubuntu
- per l'implementazione di PhpMyAdmin [seguire la procedura qui descritta](https://www.digitalocean.com/community/tutorials/how-to-install-and-secure-phpmyadmin-on-ubuntu-18-04)
- per creare utenti con MySQL puoi [seguire la procedura qui descritta](https://www.digitalocean.com/community/tutorials/how-to-create-a-new-user-and-grant-permissions-in-mysql) oppure [leggere la documentazione MySQL completa](https://dev.mysql.com/doc/mysql-getting-started/en/)


#### Impostazione del firewall
E' consigliabile attivare UFW dalla shell del VPS, bloccando tutte le connessioni eccetto SSH, HTTPS, FTP e SMTP.

#### Creazione e implementazione del FQDN sept.tech

Dopo aver definito il nome del portale (nel nostro caso: SEPT - Social Engineering Prevention Tool), è consiglato acquistare un dominio di secondo livello o un dominio gratuito di terzo livello, nel caso in cui si volesse utilizzare un FQDN. Concluso l’acquisto, i nameserver che ospitano il dominio devono essere modificati con quelli di proprietà di DigitalOcean (o del proprietario del VPS a cui ci si appoggia), cosicché tutte le funzionalità del sito vengano gestite da un’unica posizione.
Successivamente il FQDN deve essere puntato all’indirizzo IPv4 e IPv6 del VPS precedente creato, mediante gli appositi resource record A, AAAA e CNAME (quest’ultimo, utilizzato per indirizzare le richieste` www` a `*`).

***Nota di installazione:*** Se è stato utilizzato un dominio Namecheap, Godaddy, etc., è possibile seguire [la seguente procedura](https://www.digitalocean.com/community/tutorials/how-to-point-to-digitalocean-nameservers-from-common-domain-registrars).

#### Virtual host e certificato SSL

In seguito, procedere con i passaggi:

1.  impostazione di un virtual host nel VPS per contenere le risorse del portale: dato che il server web Apache implementa la possibilità di creare virtual host in cui ospitare risorse web, si è deciso di creare un host virtuale che contenesse tutte le risorse di sistema; questo per semplificare le procedure di gestione del portale in un secondo momento. Per la creazione del Virtual Host ed il collegamento con il dominio, [seguire la seguente guida](https://www.digitalocean.com/community/tutorials/how-to-set-up-apache-virtual-hosts-on-ubuntu-16-04).


2.  installazione di un certificato per il sito web: dopo aver completato la procedura di configurazione dell’ambiente web, installare un certificato SSL sicché la connessione tra server e utente finale risulti sicura (protocollo https) per entrambe le parti. Per un certificato SSL gratuito, si può ricorrere a [Let's Encrypt](https://letsencrypt.org). 

#### Servizio di posta elettronica

Per poter inoltrare i messaggi di posta elettronica agli utenti è necessario creare almeno una casella di posta elettronica per le comunicazioni con gli utenti partecipanti al test. Nel caso si sia acquistato un dominio di secondo (o terzo) livello, il consiglio è di aggiungere contestualmente all'acquisto la casella e-mail, altrimenti è possibile crearne una gratuitamente utilizzando, ad esempio, Gmail, Outlook, etc.

Per perpetrare l’attacco pensato per il test, verrà inviata una e-mail, contenente un link ad un sito web clone in grado di catturare i dati di posizione della vittima, utilizzando una casella di posta simile, ma non uguale, a quella normalmente utilizzata per informare l’utente finale.

> Ad esempio, se la casella di posta principale è `no-reply@sept.tech`, allora la casella malevola potrebbe essere `no.reply.sept.tech@gmail.com`.

#### Configurazione del file httpd.conf e architettura degli .htaccess

Apache offre struttura e funzionalità estremamente personalizzabili: la configurazione del server HTTP può essere modificata per limitare gli accessi alle cartelle che contengono i files relativi alla logica operativa del portale, reindirizzare le richieste http al canale sicuro https e mostare agli utenti pagine personalizzate, coerenti con l’interfaccia utente dell’intero portale, quando incappano negli errori di stato più frequenti (ad esempio, 404 Not Found e 403 Forbidden).

Per fare ciò, si procede alla modifica dei files del tipo `httpd.conf`. Normalmente essi sono due (con il nome della cartella relativa alla cartella del virtual host presente in `var/www`, uno con suffisso “ssl", per le richieste https, e uno per le richieste standard) localizzati nella cartella `etc/apache2/sites-available` o `etc/apache2/`. Se il virtual host è stato inserito all'interno della cartella `sept.tech` , allora molto probabilmente i due files di configurazione di Apache sono dislocati in `etc/apache/sites-available` e denominati:

-   `sept.tech.conf`: per le richieste http;

-   `sept.tech-le-ssl.conf`: per le richieste https. 

Così facendo, non si dovrà ricorrere all’utilizzo di files `.htaccess` per gestire gli accessi e rischiare potenziali minacce che ne compromettano il funzionamento.

* * *
**Codice del file di configurazione `sept.tech-le-ssl.conf`**

    <IfModule mod_ssl.c>
        <VirtualHost *:443>
            # Impostazioni generali - normalmente già presenti e da non toccare
            ServerAdmin webmaster@localhost
            ServerName sept.tech
            ServerAlias www.sept.tech
            DocumentRoot /var/www/sept.tech
            ErrorLog ${APACHE_LOG_DIR}/error.log
            CustomLog ${APACHE_LOG_DIR}/access.log combined
            
            Include /etc/letsencrypt/options-ssl-apache.conf
            SSLCertificateFile /etc/letsencrypt/live/sept.tech/fullchain.pem
            SSLCertificateKeyFile /etc/letsencrypt/live/sept.tech/privkey.pem
            
            # Disabilita .htaccess in /var/www per questioni di sicurezza
            <Directory "/var/www">
                Allowoverride none
            </Directory>
            
            # Blocca l'accesso esterno al contenuto della cartella "components" 
            # e delle sotto cartelle
            <Directory "/var/www/sept.tech/components/*">
                Require all denied
            </Directory>
            
            <Directory "/var/www/sept.tech/components">
                Require all denied
            </Directory>
            
            # Reindirizzamento a pagine personalizzate in caso di errore (da modificare URI)
            ErrorDocument 404 https://sept.tech/404.php
            ErrorDocument 403 https://sept.tech/403.php
        </VirtualHost>
    </IfModule>

* * *
**Codice del file di configurazione `sept.tech.conf`**

    <VirtualHost *:80>
        # Impostazioni generali - normalmente già presenti e da non toccare
        ServerAdmin webmaster@localhost
        
        ServerName sept.tech
        ServerAlias www.sept.tech
        DocumentRoot /var/www/sept.tech
        
        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined
        
        # Reindirizza le richieste non-https al canale https
        Redirect permanent / https://sept.tech/
        
        # Disabilita .htaccess in /var/www per questioni di sicurezza
        <Directory "/var/www">
            Allowoverride none
        </Directory>
        
        # Blocca l'accesso esterno al contenuto della cartella "components"
        # e delle sotto cartelle
        <Directory "/var/www/sept.tech/components/*">
            Require all denied
        </Directory>
    </VirtualHost>

***Nota di installazione***: per modificare i file `.conf` è possibile utilizzare un semplice editor di testo da linea di comando, come `nano` o `vim`.

#### Ultimazione dell’ambiente di sviluppo

Dopo aver creato la cartella che conterrà l'infrastruttura (nel nostro caso: `var/www/sept.tech`), quest'ultima deve essere configurata per essere riconosciuta dal sistema come la cartella radice del server web. Se la procedura precedente è stata seguita pedestremente, nessuna azione deve essere effettuata. Tale ospiterà le risorse web da visualizzare dall’URI del FQDN scelto (nel nostro caso: `https://sept.tech`).

## Progettazione del portale 

### Mappa del sito

È la struttura del portale e contiene tutte le pagine potenzialmente visitabili; dato che esistono utenti con diversi livelli di privilegi, per determinate pagine essa presenta logiche basilari di permessi di visione. 

### Creazione del database e delle relative tabelle

Il database contiene le informazini sugli utenti utilizzando due tabelle:

-   una tabella per mantenere le informazioni di accesso degli utenti: la tabella ‘users‘ conterrà le informazioni di accesso degli utenti e verrà utilizzata per le relative operazioni;

-   una tabella per mantenere i dati di avanzamento del test per singolo utente e altri dati demografici acquisiti con il sondaggio iniziale (in fase di registrazione): esso corrisponderà con la tabella ‘user-informations‘.

Aprire PhpMyAdmin e compilare da linea di comando il seguente codice sorgente: esso genererà il database e le relative tabelle.

```sql
    -- Database: `app`
    CREATE DATABASE `app`;
    
    
    -- Struttura della tabella `user-informations`

    CREATE TABLE `user-informations` (
    `email` varchar(255) NOT NULL,
    `gender` tinyint(1) NOT NULL,
    `level` tinyint(1) NOT NULL,
    `age` int(3) NOT NULL,
    `privacy` tinyint(1) DEFAULT '1',
    `attack-sent` tinyint(1) NOT NULL DEFAULT '0',
    `attack-result` tinyint(1) NOT NULL DEFAULT '0'
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

    -- Struttura della tabella `users`

    CREATE TABLE `users` (
    `email` varchar(255) NOT NULL,
    `password` varchar(1024) NOT NULL,
    `group` int(1) NOT NULL DEFAULT '0',
    `active` int(1) NOT NULL DEFAULT '0',
    `hash` varchar(1024) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

    -- Indici per le tabelle `user-informations`
    ALTER TABLE `user-informations`
    ADD PRIMARY KEY (`email`),
    ADD UNIQUE KEY `email` (`email`);

    -- Indici per le tabelle `users`
    ALTER TABLE `users`
    ADD PRIMARY KEY (`email`),
    ADD UNIQUE KEY `email` (`email`);

    -- Limiti per la tabella `user-informations`
    ALTER TABLE `user-informations`
    ADD CONSTRAINT `ext-ue` FOREIGN KEY (`email`) REFERENCES `users` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;
```

#### Funzionamento operativo dei campi presenti nelle tabelle
##### Tabella ‘users‘

La tabella ‘users‘ viene utilizzata per memorizzare le informazioni di accesso degli utenti registrazioni; essa contiene:

-   ‘email‘: indirizzo e-mail dell’utente codificato in Base32, per esigenze di pseudonimizzazione. Come già detto, è una chiave primaria e unica;

-   ‘password‘: contiene l’‘hash‘ della password immessa dall’utente in fase di registrazione (o cambio password), la procedura di crittografazione è basata sull’algoritmo Blowfish e gestita in PHP utilizzando la funzione nativa `password_hash($pwd, PASSWORD_DEFAULT)`;

-   ‘hash‘: è un hash creato utilizzando l’algoritmo SHA2 su 512 bit e viene utilizzato per le operazioni di verifica e-mail e cambio password. È rigenerato ogni volta che viene richiamato da una funzione PHP (cambio password e verifica e-mail);

-   ‘group‘: indica il gruppo di appartenenza dell’utente e viene utilizzato per visualizzare determinate aree del sito sulla base del valore assegnato; i valori possibili sono:

    -   0: nessun gruppo assegnato, l’utente è in attesa di assegnazione, non è possibile accedere alla pagina personale del portale finché l’utente non riceve un’assegnazione;

    -   1: gruppo base, senza l’utilizzo di SEADMv2;

    -   2: gruppo con la possibilità di utilizzare SEADMv2;

    -   9: gruppo amministratore: ha le funzioni di amministrazione e può gestire tutti gli utenti (appartenenti ai gruppi 0, 1, 2);

-   ‘active‘: indica se l’utente ha completato con successo la procedura di verifica dell’e-mail e può assumere due valori (0: FALSE, 1: TRUE); se il campo ha un valore diverso da 1, non è possibile accedere al sito;

##### Tabella ‘user-informations’ 

La tabella ‘user-informations‘ è utilizzata per memorizzare le informazioni sull’avanzamento del test e riguardo ai dati demografici dell’utente; i campi presenti sono:

-   ‘email’: come nella tabella precedente;

-   ‘gender’: sesso dell’utente, i valori possibili sono [0:uomo, 1:femmina, 2:altro];

-   ‘age’: fascia d’età dell’utente, i valori possibili sono [18:18-24, 25:25-34, 35:35-44, 45:45-54, 55:55-64, 65:65+];

-   ‘level’: livello di competenze informatiche, corrispondente ad un valore intero compreso nella scala di valutazione qualitativa tra 1 (base) e 5 (esperto);

-   ‘attack-sent’: un valore booleano (0: FALSE,1: TRUE) per indicare se l’attacco sia stato attivato (quindi l’e-mail malevola inviata), viene utilizzata dall’applicativo per gestire lo stato del test sia parte utente che amministratore;

-   ‘attack-result’: gestisce gli stati del test successivi all’invio dell’attacco; può assumere i valori:

    -   0: l’attacco non è stato concluso;

    -   1: l’attacco è stato concluso e il sondaggio finale somministrato;

    -   9: il test è stato completato.

## Installazione della piattaforma 

Per procedere con l'installazione dell'infrastruttura

-   scaricare dalla cartella ‘Website’ di questo repository il progetto del portale e spostarlo nella cartella principale del server web pubblico o locale (nel nostro caso `var/www/sept.tech`;

-   preparare i file necessari per il funzionamento del database e delle funzioni di invio e-mail: nel ‘git’ appena scaricato manca la cartella `config` che contiene al suo interno i files di configurazione per connettersi al database ed ai server di posta elettronica. Procedere con la configurazione come segue:

    -   creazione della cartella: aprire il percorso `components/applications/database` del portale e creare all’interno di `database` la cartella `config`;

    -   creazione del file di configurazione del database: dopo essere entrati nel percorso relativo alla cartella `config`, creare il file `config.php` e inserire al suo interno il contenuto del listato sottostante, modificando i valori in corrispondenza delle variabili con quelli del proprio ambiente di sviluppo (il nome utente e le password degli utenti del DBMS sono stati creati in fase di preparazione dell'ambiente);

```php
                    <?php
                    $host = 'HOST_DBMS'; // normalmente è 'localhost'
                    $database = 'NOME_DATABASE'; // se hai seguito la procedura, è 'app'
                    
                    $username = 'USERNAME_READONLY'; // Username utente database read-only
                    $password = 'PASSWORD_READONLY'; // Password --
                    
                    $usernameRO = 'USERNAME_READWRITE'; // Username utente database read-write
                    $passwordRO = 'PASSWORD_READWRITE'; // Password --
                    ?>
```
   -   creazione del file di configurazione dell’e-mail: creare il file `mail.settings.php` nello stesso percorso del file precedente (`components/applications/database`). Copiare il codice del listato sottostante, adattandolo ai propri server mail e, in generale, al proprio ambiente di sviluppo.
    
```php
    <!-- -->
    <?php
    // Configurazione e-mail per i messaggi di posta informativi
    $emailHost = 'mail.privateemail.com';
    $emailUser = 'no-reply@sept.tech';
    $emailPassword = '*********';

    $emailFrom = 'no-reply@sept.tech';
    $emailLabelFrom = 'SEPT - Social Engineering Prevention Tool';

    // Configurazione e-mail "fake" per i messaggi di posta malevoli da inviare
    // nella fase d'attacco

    $emailFakeHost = 'smtp.gmail.com';
    $emailFakeUsername = 'no.reply.sept.tech@gmail.com';
    $emailFakePassword ='*********';
    $emailFakeFrom = 'no.reply.sept.tech@gmail.com';
    $emailFakeFromLabel = 'SEPT - Social Engineering Prevention Tools';
    ?>
```

Rifiniti gli ultimi dettagli, la piattaforma sarà pronta per essere utilizzata. 

#### Inizializzazione ed impostazione dei servizi esterni
Il portale si avvale del servizio Typeform e Iubenda per l'erogazione dei sondaggi e della privacy e cookie policy; essendo nativamente calzati per le esigenze del test compiuto, nel caso in cui si voglia ripetere il test in un ambiente di sviluppo e condizioni di contorno diverse, bisogna reimpostare tutta questa parte: per farlo, seguire la spiegazione seguente.

Le pagine interessate e che necessitano di modifiche sono:
- `tool.external.php`
- `seadmv2.php`
- `helpdesk.php`
- `index.php > sezione sondaggio finale`
e le relative sezioni `xml` utilizzate per fornire i contenuti.

Operativamente parlando, è necessario svolgere la seguente procedura:
1. Preparare un questionario basato sul modello SEADMv2 con Typeform o simile, o un applicativo da richiamare attraverso il pulsante posto nella pagina `seadmv2.php` e `tool.external.php` {1}
2. Preparare il sondaggio finale da somministrare a tutti gli utenti, con Typeform o simile; {2}
3. Preparare il questionario per assistenza, con Typeform o simili. {3}

Successivamente, modificare le seguenti righe di codice:
- Nel file `components/parts/site/seadmv2.xml` modificare la **riga 54** con il collegamento ipertestuale al proprio sondaggio (*QUI_IL_LINK_AL_FORM*):
```html
<a class="typeform-share button" href="QUI_IL_LINK_AL_FORM" data-mode="popup" style="display:inline-block;text-decoration:none;background-color:#4D72E0;color:white;cursor:pointer;font-family:Helvetica,Arial,sans-serif;font-size:16px;line-height:40px;text-align:center;margin:0;height:40px;padding:0px 26px;border-radius:20px;max-width:100%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:bold;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;" data-hide-headers=true data-hide-footer=true data-submit-close-delay="2" target="_blank">Avvia lo strumento </a> <script> (function() { var qs,js,q,s,d=document, gi=d.getElementById, ce=d.createElement, gt=d.getElementsByTagName, id="typef_orm_share", b="https://embed.typeform.com/"; if(!gi.call(d,id)){ js=ce.call(d,"script"); js.id=id; js.src=b+"embed.js"; q=gt.call(d,"script")[0]; q.parentNode.insertBefore(js,q) } })() </script>
```

***Nota di personalizzazione:*** nel caso in cui si voglia personalizzare il contenuto del portale ed il template delle e-mail inviate agli utenti finali, leggere la sezione *Personalizzazione*.

## Preparazione dell’ambiente software per il vettore d’attacco

L’applicativo utilizzato per compiere l’attacco è da compilare da linea di comando in ambiente Linux; per questo, è consigliato installare Kali Linux come distribuzione, in modo tale da avere un ambiente robusto e completo per poter compiere attacchi di ingegneria sociale.

#### Note sul metodo di installazione per Kali Linux

La distribuzione Kali deve essere installata standalone su una partizione del computer; l’installazione come sotto-sistema su Windows (utilizzando WSL, 1 e 2) o su macchina virtuale compromette alcune funzionalità e non permette l’utilizzo dei servizi ngrok e serveo, necessari per creare un tunnel pubblico attraverso cui gli utenti esterni possano fruire delle risorse originariamente disponibili su rete locale generate del computer (e in questo caso, il sito web clone).

#### Note sulla procedura di installazione per Kali Linux

Kali Linux può essere installato facilmente scaricando la distribuzione direttamente dal portale ufficiale o mediante il torrent fornito da Offensive Security (l’organizzazione autrice la distribuzione); durante la procedura di installazione, è preferibile lasciare invariati i parametri predefiniti e selezionare le opzioni consigliate.

### Installazione di HiddenEye

Per perpetrare l’attacco, verrà utilizzato HiddenEye; è stato scelto questo rispetto ad altri applicativi per la capacità di mantenere più connessioni simultanee con l’esterno e per le possibilità di sviluppare un vettore d’attacco che tenti di catturare informazioni sulla localizzazione (gli altri applicativi si limitano a tentare di catturare le informazioni di accesso per servizi online famosi, come e-mail e password di Gmail).

Per installare HiddenEye su Kali Linux, seguire la procedura descritta nella sezione “Usage and Installation" della fonte @git:hiddeneye.

### Esecuzione di HiddenEye e utilizzo


Dopo aver completato la procedura di installazione, compilare da linea di comando:

    cd /path/to/hiddeneye 
    sudo python3 HiddenEye.py

Avviare il tool e accettare i termini e condizioni, così come richiesto dalla procedura di attivazione di HiddenEye. Assicurarsi inoltre di essere connessi ad internet.

Per replicare l’esecuzione *in quantum huiusmodi* in questa monografia: aprire l’applicativo e da linea di comando selezionare le seguenti opzioni:

1.  dal menu principale di HiddenEye: “0A";

2.  successivamente l’opzione “2" per impostare Google Drive come sito clone attraverso cui ottenere le informazioni sulla posizione;

3.  nei seguenti campi, leggere le informazioni riportate e scegliere se digitare ‘N’ o ‘Y’ se si vuole rifiutare o abilitare la funzionalità in fase di attacco;

4.  inserire una porta, strettamente maggiore di 1024;

5.  fornire quindi un URI verso cui reindirizzare l’utente successivamente all’attacco.


# Utilizzo della piattaforma

In questa sezione è illustrata la procedura di normale utilizzo della piattaforma. Essa coinvolge sia l’utente finale che l’amministratore del sistema, dunque nel corso della trattazione saranno mostrati tanto i procedimenti “back-end" quanto le funzionalità “front-end".

Per chiarire concetti difficilmente immaginabili attraverso una semplice lettura, il testo è arricchito grazie ad alcune figure di sintesi, contenenti le schermate mostrate agli utenti nel corso del test.

## Procedura di utilizzo

Il test richiede l’interazione di due attori:

-   l’amministratore del sistema: colui che “attiva" le fasi d’attacco dal pannello di amministrazione;

-   l’utente finale: colui che effettua, direttamente e indirettamente, le operazioni da svolgere per ogni passaggio;

e si svolge in quattro passaggi principali:

1.  registrazione iniziale e conferma dell’indirizzo e-mail;

2.  attivazione dell’account e scelta del gruppo;

3.  avvio dell’attacco d’ingegneria sociale;

4.  somministrazione del sondaggio finale.

#### Registrazione iniziale e conferma dell’indirizzo e-mail

Nella fase iniziale, l’utente apre la schermata principale del portale web: la pagina di registrazione (vedi la figura [fig:sept-register]). Egli dovrà immettere alcune informazioni personali:

-   dati utili per l’accesso al portale:

    -   indirizzo e-mail valido;

    -   password personale: deve contenere almeno un carattere maiuscolo, uno minuscolo ed un segno numerico;

-   dati demografici utili per le analisi:

    -   sesso;

    -   fascia d’età (divise in “18-24", “25-34", “35-44", “45-54", “55-64", “over 65");

    -   autovalutazione delle proprie competenze digitali, secondo una scala qualitativa con valori compresi tra 1 (competenze minime) e 5 (competenze massime);

e accettare l’informativa sulla Privacy.

##### Conferma dell’indirizzo e-mail

Ciascun utente dovrà confermare il proprio indirizzo e-mail prima di attivare il proprio account. In tal modo, si è sicuri che l’indirizzo e-mail effettivamente esista e possa essere utilizzato per perpetrare l’attacco di ingegneria sociale del test, ovvero lo scopo di sviluppo del portale.

La conferma della casella postale avviene tramite un collegamento ipertestuale di attivazione inviato nella medesima casella, a cui l’utente deve cliccare per completare l’attivazione del proprio profilo.

#### Attivazione dell’account e scelta del gruppo

Una volta che l’utente ha confermato il proprio indirizzo e-mail, l’account è considerato “parzialmente attivo"; infatti, l’amministratore del sistema del test, dovrà confermare l’utente e allocarlo in un gruppo di test.

In pratica, l’amministratore del sistema accederà alla propria pagina personale, andrà nella sezione relativa all’attivazione degli utenti, selezionerà dal corrispondente menù a tendina l’utente, assegnandolo ad un gruppo, e cliccherà il pulsante di attivazione. Successivamente, il partecipante al test riceverà una e-mail di completamento della procedura di iscrizione: da questo momento l’utente potrà accedere al proprio portale, leggere le informazioni sul test e utilizzare gli strumenti di supporto.

L’attivazione dell’account è sviluppata in due parti:

-   attivazione lato utente: per la verifica della e-mail;

-   attivazione amministratore: per confermare l’account e allocarlo ad un “gruppo di lavoro"[^21].

#### Avvio dell’attacco di ingegneria sociale

Dopo aver completato la fase preliminare di registrazione, conferma ed assegnazione ad un gruppo di lavoro, inizia la fase centrale del test. In questo passaggio si sviluppano tre passaggi:

-   lettura delle informazioni sul test;

-   avvio dell’attacco da parte dell’amministratore;

-   completamento dell’attacco.

#### Lettura delle informazioni sul test

A tutti gli utenti è richiesta la lettura di un documento informativo iniziale, presente in corrispondenza della pagina principale dell’area riservata (`index.php`). Esso contiene informazioni variabili in base al gruppo di appartenenza.

Quando il profilo di un utente viene attivato da parte dell’amministratore, esso riceve un’e-mail informativa nella casella di posta immessa in fase di registrazione. Il contenuto del messaggio di posta ricorda all’utente di leggere attentamente le informazioni contenute nella pagina principale dell’area privata del portale web.

-   Gruppo 1: riceve informazioni generali sul funzionamento del portale e del test, nessuna informazione particolare viene fornita riguardo agli strumenti di supporto viene data, in quanto SEADMv2 non è abilitato per il gruppo.

-   Gruppo 2: riceve informazioni generali sul funzionamento del portale e del test, nonché viene chiesto di adoperare il modello SEADMv2 ogni qualvolta si riceva un messaggio di posta sospetto; infine, è presentato il funzionamento del tool relativo al modello SEADMv2.

L’utente può visualizzare tutte le sue informazioni dalla pagina ’Profilo’; inoltre, entrambi i gruppi vengono informati riguardo all’anonimizzazione e pseudonimizzazione delle proprie informazioni personali.

##### Avvio dell’attacco da parte dell’amministratore {#part:practical-attack-vector}

Dopo essere stato assegnato ad un gruppo (abilitato) e aver letto le informazioni contenute nella pagina principale del portale, l’utente è pronto per iniziare il test. Esso consiste in un attacco di phishing e può essere attivato direttamente dall’amministratore.

In pratica, l’amministratore del sistema dovrà selezionare la vittima dell’attacco, inserire l’URI del sito web clone (il portale che potenzialmente catturerà le informazioni sensibili della vittima) e abilitare l’attacco.

L’utente finale riceverà un messaggio di posta elettronica da un mittente con una casella postale simile all’originale contenente un messaggio personalizzato e un pulsante che, una volta cliccato, reindirizza l’utente al sito web clone. La creazione e gestione del sito web clone e del dominio da inserire prima di perpetrare l’attacco devono essere sviluppati separatamente.

Per com’è stata elaborata l’infrastruttura del test, l’indirizzo e-mail ufficiale da cui gli utenti ricevono messaggi di posta è `no-reply@sept.tech`, mentre la casella postale finta (utilizzata per effettuare l’attacco vero e proprio) è `no-reply.sept.tech@gmail.com`. Il nome associato alle caselle di posta è identico: `SEPT - Social Engineering Prevention Tool`. Il vettore di attacco è invece sviluppato utilizzando HiddenEye[^23]; l’URI è generato automaticamente dal suddetto tool sfruttando le infrastrutture Ngrok e Serveo.

Per maggiori informazioni sulla creazione ed utilizzo del vettore d’attacco, consultare la sezione [part:userguide-attack-vector]

##### Completamento dell’attacco

L’amministratore rileva l’apertura del messaggio di posta elettronica da parte dell’utente “vittima" (vedi la Figura [fig:sept-attackcomplete-back]); da questo momento possono presentarsi due scenari principali:

-   test completato, attacco completato con successo: in questo caso l’utente ha aperto il link ed effettuato le azioni necessarie per la cattura dei dati; l’amministratore dichiara concluso l’attacco nel momento in cui riceve i dati nel proprio terminale;

-   test completato, attacco fallito: in questo caso l’utente non apre il link ed evita la minaccia; l’amministratore non riceve dati nel proprio terminale: in questo caso è necessario determinare arbitrariamente la fine del test.

In ogni caso, l’attacco si considera concluso un’ora dopo l’apertura dell’e-mail da parte del potenziale utente “vittima".

#### Somministrazione del sondaggio finale

Il test termina con la somministrazione del sondaggio finale; esso viene abilitato dall’amministratore per ciascun utente e consiste in una serie di domande chiuse per valutare l’efficacia del test, del modello di prevenzione SEADMv2 (nel caso del gruppo due) e riguardo all’ingegneria sociale. Il sondaggio finale è stato sviluppato utilizzando il servizio online Typeform.

L’esito del test e i sondaggi finali verranno utilizzati per proporre una versione rivisitata e migliorata di SEADMv2, considerando anche la possibilità che essa venga applicata in contesti non aziendali.

#### Completamento del test

L’ultima parte del processo corrisponde con il completamento del test. L’amministratore deve confermare il completamento del test dall’apposita sezione del pannello di amministrazione: l’utente riceverà un’e-mail informativa a riguardo. Da qui, il test potrà considerarsi concluso.


Le pagine del portale
---------------------

L’applicazione implementa un’area pubblica utile per le operazioni di registrazione e accesso ed un’area riservata attraverso cui vengono somministrati i vari elementi per il test all’utente finale.

#### Area pubblica

Le pagine disponibili pubblicamente offrono i seguenti servizi:

-   **registrazione al test**, comprendente anche le pagine intermedie per verificare l’indirizzo e-mail e gestire gli errori;

-   **accesso al test**, comprendente anche le pagine intermedie di gestione degli errori;

-   possibilità di **reimpostare una password** dimenticata.

#### Area privata

Le pagine disponibili per gli utenti registrati al test offrono i seguenti servizi, a seconda dell’appartenenza ad uno specifico gruppo.

##### Pagina principale

Contiene informazioni sullo svolgimento del test, sulla condotta da adottare durante il processo e fornisce informazioni generali sul funzionamento del test e dell’applicativo in sè. In base allo stato del test, presenta all’utente informazioni differenti (ad esempio, se è in corso l’attacco, compare un pannello di avviso, mentre quando viene abilitato il sondaggio finale, compare la sezione con il pulsante per raggiungere il sondaggio).

Si apre di default ad ogni nuovo accesso, o può essere raggiunta cliccando su “pagina Principale" dal menu laterale.

##### Pagina del profilo

presenta le informazioni personali dell’utente registrato. Per essere consultata: cliccare sul proprio indirizzo e-mail dal menu in alto, dunque selezionare dal menu a tendina che compare la voce ‘Profilo’.

##### SEADMv2

Se l’utente appartiene al secondo gruppo, può utilizzare l’infrastruttura di verifica SEADMv2 aprendo la corrispondente pagina. Da essa è possibile raggiungere il modulo SEADMv2, sviluppato con TypeForm, cliccando sul relativo pulsante.

##### Centro assistenza

Per ogni evenienza, l’utente può contattare l’amministratore del test utilizzando un modulo di contatto o (opzionalmente) mediante contatti diretti; è possibile raggiungere la pagina dal menu laterale, cliccando sulla omonima etichetta.

##### Privacy e Cookie Policy

Sono le pagine contenenti le due informative, vengono aperte come pop-up selezionando le omonime sezioni dal menu laterale dell’area privata o dai pulsanti in basso nell’area pubblica.

##### Manuale Utente

Un compendio di ingegneria sociale che l’utente può leggere interattivamente per informarsi riguardo a tale fenomeno; è raggiungibile dal menu laterale dell’area privata.

##### Pagina di logout

In ogni momento l’utente può chiudere la sessione e uscire dall’applicazione. Per effettuare il logout: cliccare sul proprio indirizzo e-mail, scritto nel menu in alto dell’area privata, dunque selezionare dal menu a tendina che compare l’etichetta “logout". Completare la procedura di Logout cliccando su “Conferma" dal popup che compare.

##### Pagina d’amministrazione

Se l’utente che effettua l’accesso è l’amministratore del sistema, allora può visualizzare la pagina di amministrazione e attuare le varie fasi del test. Può essere aperta cliccando sull’omonima etichetta dal menu laterale.

# Personalizzazione
