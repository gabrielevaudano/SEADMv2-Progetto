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

**Nota**: Se non hai ancora strutturato un ambiente di sviluppo in grado di contenere l'infrastruttura, segui le istruzioni contenute nella sezione _Preparazione dell'ambiente di sviluppo_, altrimenti passa alla sezione _Installazione dell'Infrastruttura_.

## Progettazione e sviluppo del portale

### Preparazione dell’ambiente di sviluppo

Prima di procedere con la creazione del portale vero e proprio, è stato fondamentale impostare l’ambiente che l’avrebbe ospitato.

#### Creazione del VPS

Tra le varie alternative di hosting online è stato selezionato [DigitalOcean](https://digitalocean.com), per la facilità di utilizzo e i costi contenuti dei piani hosting. Dopo aver creato un account, si è proceduto con la creazione di un VPS (un “droplet", così come denominato da DigitalOcean). Nel caso in cui non fossi in grado di creare autonomamente un droplet e scegliessi DigitalOcean come host della tua infrastruttura, allora [segui questa procedura per la creazione del droplet](https://www.digitalocean.com/docs/droplets/how-to/create/).

**Nota:** Per semplicità di utilizzo e coerenza con i passaggi qui mostrati, installare Ubuntu (ultima versione LTS) nel proprio droplet.

#### Installazione dell’infrastruttura web

Dopo aver completato la procedura di accesso al droplet, procedere con l’installazione dell’infrastruttura necessaria per far “girare" il server web:

1.  installazione di LAMP: avendo generato un VPS basato su Linux e utilizzando PHP come linguaggio di programmazione backend e MySQL come DBMS, si è deciso di installare la piattaforma software LAMP, contenente tutti gli applicativi necessari per far girare il portale web su distribuzione Linux, tra cui PHP, MySQL e il server web Apache ;

2.  installazione di PhpMyAdmin: per rendere più semplice l’utilizzo di MySQL, è stato installato PhpMyAdmin nel VPS, in questo modo le tabelle e gli utenti del DBMS possono essere gestite visivamente.

**Consigli e guide**: 
- per l'installazione di LAMP [seguire la procedura qui descritta](https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-ubuntu-18-04), selezionando la propria versione di Ubuntu
- per l'implementazione di PhpMyAdmin [seguire la procedura qui descritta](https://www.digitalocean.com/community/tutorials/how-to-install-and-secure-phpmyadmin-on-ubuntu-18-04)

##### Configurazione del DBMS

Per configurare il DBMS si è proceduto come segue:

-   installazione ed implementazione di MySQL con il motore InnoDB;

-   installazione di phpMyAdmin;

-   modifica dei permessi di accesso e creazione di due utenti del database utili per i fini del portale: un utente “read-only" con soli permessi di lettura (SELECT e SHOW VIEW) e un utente “read-write" con permessi estesi (SELECT, UPDATE, INSERT INTO e SHOW VIEW).

#### Impostazione del firewall
E' consigliabile attivare UFW dalla shell del VPS, bloccando tutte le connessioni eccetto SSH, HTTPS, FTP e SMTP.

#### Creazione e implementazione del FQDN sept.tech

Dopo aver definito il nome del portale (nel nostro caso: SEPT - Social Engineering Prevention Tool), è consiglato acquistare un dominio di secondo livello o un dominio gratuito di terzo livello, nel caso in cui si volesse utilizzare un FQDN. Concluso l’acquisto, i nameserver che ospitano il dominio devono essere modificati con quelli di proprietà di DigitalOcean (o del proprietario del VPS a cui ci si appoggia), cosicché tutte le funzionalità del sito vengano gestite da un’unica posizione.
Successivamente il FQDN deve essere puntato all’indirizzo IPv4 e IPv6 del VPS precedente creato, mediante gli appositi resource record A, AAAA e CNAME (quest’ultimo, utilizzato per indirizzare le richieste` www` a `*`).

**NOTA:** Se è stato utilizzato un dominio Namecheap, Godaddy, etc., è possibile seguire [la seguente procedura](https://www.digitalocean.com/community/tutorials/how-to-point-to-digitalocean-nameservers-from-common-domain-registrars).

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

#### Ultimazione dell’ambiente di sviluppo

Dopo aver creato la cartella che conterrà l'infrastruttura (nel nostro caso: `var/www/sept.tech`), essa deve essere configurata per essere riconosciuta dal sistema come la cartella radice del server web. Se la procedura precedente è stata seguita pedestremente, nessuna azione deve essere effettuata. Tale ospiterà le risorse web da visualizzare dall’URI del FQDN scelto (nel nostro caso: `https://sept.tech`).

Progettazione del portale {#part:prog-portal-prep-dbms}
-------------------------

Prima di procedere con lo sviluppo del codice, è stato progettato come implementare le funzioni fondamentali per l’espletamento del test, anche tenendo conto della necessità di creare un’interfaccia facile e intuitiva per l’utente finale. A partire dalle considerazioni teoriche sviluppate nella sezione [part:progettazione] si è proceduto con la definizione di:

-   mappa del sito;

-   implementazione del database;

-   logiche di funzionamento del sito.

#### Mappa del sito

È la struttura del portale e contiene tutte le pagine potenzialmente visitabili; dato che esistono utenti con diversi livelli di privilegi, per determinate pagine essa presenta logiche basilari di permessi di visione. Vedi la Figura [fig:website-sitemap].

![Mappa del sito.<span data-label="fig:website-sitemap"></span>](resource/website-sitemap.pdf){width="85.00000%"}

#### Creazione del database e delle relative tabelle

Il database dovrà contenere informazioni sugli utenti; analizzando le basi teoriche presentate nella sezione [part:progettazione], il database utilizzato dal portale (chiamato ‘app‘) dovrà contenere due tabelle:

-   una tabella per mantenere le informazioni di accesso degli utenti: la tabella ‘users‘ conterrà le informazioni di accesso degli utenti e verrà utilizzata per le relative operazioni;

-   una tabella per mantenere i dati di avanzamento del test per singolo utente e altri dati demografici acquisiti con il sondaggio iniziale (in fase di registrazione): esso corrisponderà con la tabella ‘user-informations‘.

![Modello Entità relazione delle tabelle del database.<span data-label="fig:website-er-database"></span>](resource/userguide-phpmyadmintables.png){width="85.00000%"}

La Figura [fig:website-er-database] illustra il modello entità-relazione tra le due tabelle; si noti che entrambe le tabelle hanno `‘email‘` come chiave primaria e unica, essa è inoltre chiave esterna.

Il codice sorgente illustrato nel listato [sql-creator] è stato utilizzato per generare il database e le relative tabelle.

    -- Database: `app`
    CREATE DATABASE app;

    ----------------------------------------------
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

[code:userguide-sept-sql-code]

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

#### Logiche di funzionamento del portale

Per essere in grado di sviluppare in maniera consistente il portale, è importante avere a mente come esso funzionerà. Prima di procedere con lo sviluppo vero e proprio, si è scelto di sviluppare tre diagrammi che descrivessero il funzionamento front-end e back-end delle tre funzioni principali e scegliere quali infrastrutture esterne sarebbero servite per completare tutti i servizi da fornire.

I diagrammi creati corrispondono a:

-   procedura di registrazione: il diagramma è diviso in quattro parti: il front-end utente, il front-end amministratore, il back-end applicativo e l’interazione con il DBMS. Vedi la Figura [fig:flowchart-reg];

-   procedura di accesso e di reimpostazione password: esso descrive la procedura di accesso al sito, dividendo le aree come nel diagramma precedente: vedi la Figura [fig:flowchart-log];

-   funzionamento generale delle pagine del sito: esso illustra le pagine del portale che possono essere visualizzate, ed eventualmente quali permessi (appartenenza ad un determinato gruppo, attivazione dell’account, area privata o pubblica) bisogna possedere per la visualizzazione; vedi la Figura [fig:website-sitemap];

-   procedura di esecuzione del test ed interazione con gli elementi funzionali del portale: viene descritta graficamente la procedura utilizzata per effettuare il test: conoscendola preliminarmente, è più semplice procedere con lo sviluppo; essa è presentata con la Figura [fig:test-execution]

![Procedura di registrazione.<span data-label="fig:flowchart-reg"></span>](resource/register-wro.pdf){width="85.00000%"}

![Procedura di accesso e reimpostazione password.<span data-label="fig:flowchart-log"></span>](resource/login-wro.pdf){width="85.00000%"}

![Procedura di esecuzione del test: nella figura vengono mostrate le interazioni tra front-end, lato utente ed amministratore, back-end (logiche applicative implementate con PHP e SQL) e DBMS.<span data-label="fig:test-execution"></span>](resource/procedure-wro.pdf){width="85.00000%"}

Sviluppo del portale
--------------------

Le considerazioni e valutazioni esposte nella sezione [part:prog-portal] pongono la base progettuale per sviluppare il portale web.

Per mantenere versatile la struttura del portale, è stato deciso di svilupparlo sfruttando le potenzialità della programmazione ad oggetti e creando una ‘struttura’ modulare facilmente adattabile in base alle esigenze di testing del singolo.

#### Scelte di programmazione

Per sviluppare il portale è stato deciso di utilizzare i seguenti linguaggi di programmazione, librerie e servizi esterni[^6]:

-   interfaccia grafica: essa verrà sviluppata utilizzando HTML5 e CSS, per semplificare la creazione di una UI user-friendly sarà utilizzato il framework Bootstrap v4.5 e il tema open-source precompilato “SB Admin 2". Inoltre la dinamicità del portale (come, ad esempio, la creazione ed apertura di popup, menu laterali, etc.), sarà implementata a partire da Javascript, la libreria JQuery, le funzionalità native di Bootstrap e le librerie DataTables, utilizzata per la gestione dinamica delle tabelle (filtering, divisione per numero di elementi, ricerca per nome), e JQuery Easing, utilizzato per la gestione “smooth" delle animazioni[^7];

-   logica applicativa: il linguaggio di programmazione impiegato per gestire la logica applicativa del portale sarà PHP versione 7, saranno inoltre implementate le librerie PHPMailer, utilizzato per inviare e-mail, e PHP Libsec, utilizzata per le funzione di crittografia;

-   interazione con il database: la gestione della connessione sarà lasciata ai driver MYSQLi di PHP, il linguaggio di programmazione delle queries sarà SQL;

-   servizi esterni: la Privacy Policy e la Cookie Policy, come già detto, verrà gestita da Iubenda, mentre i sondaggi e i moduli (come il modello di gestione di SEADMv2) verranno sviluppati utilizzando Typeform.

Nei prossimi paragrafi saranno discusse alcune funzionalità specifiche, le scelte di sviluppo personali e le criticità occorse durante la programmazione; per una visione completa del codice di sviluppo è possibile consultare la fonte @git:github-personale[^8].

#### Lista dei files

Vengono ora presentati tutti i file sviluppati per il funzionamento dell’applicativo, ne viene anche data una descrizione delle funzionalità contenute.

-   `components`

    -   `applications`: contiene le logiche applicative del portale

        -   `SendMail.php`: la classe SendMail gestisce l’invio di e-mail all’utente finale;

        -   `Session.php`: la classe Session gestisce le intere funzionalità di sessione di un utente: registrazione, login, password dimenticata e funzionamenti interni. È la classe principale del portale, nonché quella che rende possibile l’esecuzione di tutte le dinamiche dell’area privata;

        -   `User.php`: è una classe utilizzata per salvare le informazioni di accesso dell’utente, può essere dunque considerata un po’ come una ‘classe di trasporto’;

        -   `UserInfo.php`: è una classe figlia di ‘User.php’, con essa possono essere salvati i dettagli demografici riguardo l’utente che ha effettuato l’accesso. Anch’essa viene utilizzata come classe di trasporto;

        -   `database`: contiene le classi utili per la connessione con il database;

            -   `Base32.php`: libreria utilizzata per convertire l’input in stringhe a base 32, è inserita in questo percorso in quanto `SiteDAO.php` è la classe che ne fruisce maggiormente;

            -   `SiteDAO.php`: gestisce le richieste con il database e fornisce i risultati alle altre classi “operative", come ad esempio `Session.php`;

            -   `DbConnect.php`: stabilisce la connessione con il database;

            -   `config`: variabili di configurazione:

            -   $>$ `config.php`: contiene le variabili di configurazione per l’accesso al database;

            -   $>$ `keyAgent.php`: contiene alcune chiavi crittografiche pubbliche;

            -   $>$ `mail.settings.php`: contiene le credenziali di accesso e-mail per poter inviare i messaggi di posta con la libreria PHPMailer;

        -   `lib`

            -   `common-functions.php`: contiene alcune funzioni comuni di conversione o trasformazione utilizzate spesso nel portale;

    -   `parts`

        -   `footer.php`: contiene la logica applicativa utilizzata per mostrare il piè di pagina; in base allo stato dell’utente (area privata o pubblica), il piè di pagina stampato varia;

        -   `header.php`: simile al punto precedente, relativo all’intestazione del portale;

        -   `session.php`: gestisce l’avvio della sessione e contiene un oggetto della classe `Session.php` così da poter usufruire dei processi applicativi in esso contenuti;

            -   `site`: contiene tutti gli spezzoni di codice HTML da mostrare, in base alla pagina aperta e alle variabili di stato impostate;

            -   `templates`: contiene i template preimpostati per i messaggi di posta elettronica informativi e malevolo da inviare all’utente ed il testo della sezione “guida utente" dell’area riservata;

-   `css`: contiene i fogli di stile per il portale[^9];

-   `img`: contiene le immagini utilizzate nel portale[^10];

-   `js`: contiene i codici di script per gestire le componenti client dinamiche[^11];

-   `vendor`: contiene i framework e le librerie di terze parti;

    -   `bootstrap`

    -   `datatables`

    -   `fontawesome-free`: collezione di icone facilmente implementabili in HTML e CSS;

    -   `jquery`

    -   `query-easing`

    -   `phpmailer`

    -   `phpseclib`

    -   `autoload.php`: script utilizzato per avviare la libreria `phpmailer`;

-   `403.php`: pagina di errore 403 personalizzata;

-   `404.php`: pagina di errore 404 personalizzata;

-   `admin.php`: pagina di amministrazione;

-   `forgot-password.php`: pagina front-end per la gestione delle procedure relative alla creazione di una nuova password (password dimenticata);

-   `helpdesk.php`: pagina relativa al centro assistenza del portale, consultabile sia pubblicamente che dall’area privata;

-   `index.php`: pagina principale dell’area riservata; quando non è stato effettuato l’accesso, l’utente viene reindirizzato a `register.php`;

-   `login.php`

-   `logout.php`

-   `profile.php`: la pagina viene utilizzata per mostrare le informazioni sul proprio profilo all’utente registrato, è consultabile aprendo il link dal menu a tendina da ogni pagina dell’area riservata;

-   `register.php`

-   `seadmv2.php`: mostra il questionario relativo al modello SEADMv2;

-   `tool.external.php`: visualizza il questionario relativo al modello SEADMv2, consultabile in questo caso senza aver effettuato l’accesso al portale;

-   `user-guide.php`: presenta la guida all’ingegneria sociale.

Dunque si procede con la presentazione approfondita delle classi e funzionalità più importanti del portale[^12].

#### La logica applicativa e la classe `Session.php`

La classe `Session.php` gestisce la logica dell’intera applicazione, vengono discussi ora i metodi principali in esso contenuti.

##### Costruttore

Il costruttore gestisce l’avvio della sessione e definisce le “variabili di sessione" utili per l’area riservata. Inoltre, inizializza un nuovo oggetto della classe `SiteDAO.php`, deputato alla gestione dell’interazione con il database. Il codice completo del costruttore è presente nel listato [constructor-session-class].

    private $user;
    private $dao;

    public function __construct()
    {
        if( session_status() === PHP_SESSION_DISABLED  )
            header('HTTP/1.1 403 Forbidden');
        
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        if (isset($_SESSION['user']))
            $this->user = $_SESSION['user'];
        else
            $this->user = null;
        
        $this->dao = new SiteDAO();
    }

##### `public function login($email, $password)`

: il file `login.php` passa e-mail e password (crittografata) come parametri, il suddetto metodo penserà a controllare eventuali inconsistenze nei dati immessi (errori, valori non validi) e tenterà il login. In caso di errore notifica l’utente scatenando un’eccezione, catturata dal file di destinazione.

##### `public function logout()`

: scatenato dal file `logout.php`, richiama la funzione di logout dal DAO.

##### `public function register($userData)`

: richiamata da `register.php`, `$userData` contiene un oggetto della classe `UserInfo` con tutti i parametri utili per la registrazione. Il metodo controllerà eventuali inconsistenze nei dati in ingresso e procederà al completamento della prima fase di registrazione, richiamando prima la funzione `register` del DAO, per salvare i dati nel database, e poi il metodo `sendMail` per inviare l’e-mail di verifica della casella postale all’utente finale. In caso di errori, scatena un’eccezione.

##### `public function sendMail($to, $subject, $message)`

: forniti destinatario del messaggio di posta, oggetto e corpo, la funzione gestisce l’invio del messaggio di posta. Per farlo, richiama prima la classe `SendMail`, che implementa la logica per l’invio delle e-mail e la libreria PHPMailer, successivamente le passa i dati e tenta l’invio. In caso di errori, scatena un’eccezione.

##### `private function sendAttackVector($to, $subject, $message)`

: simile al precedente metodo, invia un’e-mail alla casella passata con la variabile `$to`, con l’unica differenza che utilizza la casella di posta “finta" (`no-reply.sept.tech@gmail.com`) e non quella ufficiale, infatti richiama la funzione `sendFake()` e non la funzione `send()` per l’invio.

##### `public function doFinalSurvey($email)` e `private function sendFinalSurvey($to` `, $subject, $message`)

: gestiscono l’invio dell’e-mail informativa per il sondaggio finale. Il primo metodo aggiorna i parametri del database, richiamando la funzione `updateAttack(1,1, $to)`, per segnalare l’attivazione del sondaggio finale, e successivamente richiama il secondo metodo per inviare un’e-mail informativa riguardo all’attivazione dello stesso.

##### `public function validateRegister($auth, $token)`

: gestisce la validazione dell’indirizzo e-mail e riceve i dati dal link aperto dal messaggio di posta preposto alla conferma della casella di posta elettronica. `$auth` e `$token` corrispondono rispettivamente all’indirizzo e-mail dell’utente in Base 32 e al token d’autorizzazione per la conferma delle modifiche. Il metodo semplicemente invia i dati all’omonima classe presente nel DAO, che si connette con il database e aggiorna i parametri per attivare l’account.

##### `public function resendData($email)`

: nel caso in cui l’utente non abbia ricevuto l’e-mail di conferma, può richiederla. Questa funzione pensa a inviare nuovamente il messaggio di posta per la verifica della casella.

##### `resendForgotData($email)`

: simile al precedente, gestisce la logica per reimpostare la password nel caso l’utente l’avesse dimenticata.

##### `public function emCompleted($email)`

: invia un’e-mail informativa all’utente finale informandolo del completamento del test.

##### `public function changePassword($auth, $token, $new)`

: parte della logica utilizzata per reimpostare la password, contatta il DAO per aggiornare la password presente nel database e associata all’utente che ne ha richiesto il cambio con la nuova.

##### `public function isUserExists($email)`

: a partire dall’indirizzo e-mail (chiave primaria e unica) verifica l’esistenza di un utente ad esso associato, contattando l’omonima classe presente in `SiteDAO`. Se esiste, ritorna un oggetto `UserInfo` con i relativi dati, altrimenti ritorna un valore falso.

##### `public function getNotGroupedUsers()` e `... getGroupedUsers()`

: ritornano i valori degli omonimi metodi presenti in `SiteDAO`.

##### `public function changePermissions($email, $group)`

: viene richiamato dal pannello di amministrazione per attivare un’utente ed assegnarlo ad un gruppo. Richiama l’omonima funzione presente in `SiteDAO` per impostare il gruppo di appartenenza dell’utente in corso di attivazione, se l’impostazione va a buon fine, invia l’e-mail di conferma dell’attivazione dell’account.

##### `public function startTest($email, $link)`

: viene richiamata dal pannello di amministrazione per avviare l’attacco, richiama la funzione `updateAttack` del `SiteDAO` per aggiornare i parametri d’attacco dell’utente nel database e invia l’e-mail malevola.

##### `getUsersData()`, `getAtkData($auth), finalizaInTest($auth)`

: sono funzioni pubbliche che richiamano le omonime funzioni presenti nel `SiteDAO`. Ritornano semplicemente i valori ottenuti dai metodi richiamati.

#### Interazione con il database, DAO e la classe `SiteDAO.php`

Per gestire l’interazione con il database si è scelto di utilizzare un pattern architetturale spesso utilizzato nell’ambito delle applicazioni web: il DAO, Data Access Object. In pratica, esso corrisponde ad una classe, con relativi metodi, che coordina e opera le azioni da/per la logica applicativa verso il database; esso permette di mantenere un livello di astrazione più elevato e di rende facilmente mantenibile il codice.

Per istanziare l’accesso al database, la classe `SiteDAO` si appoggia a `DbConnect`, che crea un oggetto di tipo `mysqli` per poter interoperare con il DBMS.

##### Sicurezza, SQL Injection e prepared statements

Il rischio che un utente malevolo cerchi di corrompere l’integrità del DBMS “iniettando" codice dannoso mediante i campi di testo è alta, per ridurre il rischio di rimanere vittima di attacchi SQL Injection sono state implementate le seguenti funzionalità durante lo sviluppo del codice:

-   utilizzo dei prepared statement nell’economia delle query in php;

-   utilizzo di utenti del DBMS con privilegi limitati;

-   sviluppo di un’infrastruttura di supporto per verificare la consistenza delle richieste lato client.

Ampliando lo sguardo, è possibile riscontrare il paradigma MVC nel modo di sviluppare tale applicazione web: in tal senso vengono mantenute divise il modello (la logica operativa), corrispondente alla classe `Session.php`, `SiteDAO.php` e a tutte le classi e i files appendici, la vista (visualizza i dati), corrispondenti ai file .xml di visualizzazione, ed il controller (che riceve i comandi utente e li attua modificando lo stato degli altri componenti), identificato dai files php presenti nella cartella principale.

Dopo aver presentato generalmente le caratteristiche adoperate per comunicare con il DBMS, si discutono i principali metodi della classe `SiteDAO`.

##### `encryptData($data)` e `decrypt($data)`

Le due funzioni codificano e decodificano i dati passati come parametro; sono utilizzate per pseudonimizzare i dati dell’utente e rendere anonimo il test.

##### `private function getToken($length)`

Passata la lunghezza del token da sviluppare (normalmente 32 caratteri), il metodo produce un token casuale e successivamente ne genera l’hash utilizzando l’algoritmo SHA2 a 512 bit.

##### `public function getUserData($email)`

Dopo aver stabilito una connessione “read-only" con il database mediante un oggetto della classe `DbConnect`, prepara ed esegue la query da utilizzare per recuperare i dati di un utente a partire dall’indirizzo e-mail (passato come parametro). Le informazioni sull’utente vengono “salvate" in un oggetto `UserInfo`[^13].

##### `public function loginUser($email, $password)`

Dopo aver stabilito una connessione in sola lettura con il database mediante un oggetto della classe `DbConnect`, controlla l’esistenza dell’indirizzo e-mail immesso dall’utente che sta tentando di effettuare l’accesso, successivamente verifica la password attraverso un confronto in due passaggi: codifica con hash e verifica utilizzando il metodo `password_verify` implementato nativamente in php. Successivamente verifica che l’account sia stato verificato ed attivato dall’amministratore. In caso di errori scatena un’eccezione, catturata dai metodi agli strati applicativi superiori.

##### `public function logout()`

Dopo aver verificato l’esistenza della variabile di sessione, a partire dal cookie volatile `$_SESSION[’user’]`, la distrugge e ritorna il valore vero nel caso in cui la distruzione vada a buon fine, altrimenti falso.

##### `public function isUserExist($email)`

Dopo aver stabilito una connessione “read-only" con il database mediante un oggetto della classe `DbConnect`, verifica l’esistenza di un utente, dato il suo indirizzo e-mail come parametro.

##### `public function getUserToken($email)`

Dopo aver stabilito una connessione “read-only" con il database mediante un oggetto della classe `DbConnect`, recupera il token attuale relativo all’indirizzo e-mail passato come parametro, dopo averne verificata l’esistenza nel database.

Il metodo ritorna un array contenente l’indirizzo e-mail dell’utente e il relativo token.

##### `private function registerUser($userData, $hash) `

Il metodo riceve a partire dalla variabile`$userData` l’oggetto `UserInfo` e il relativo `$hash`; dopo aver recuperato e crittografato l’indirizzo e-mail dal relativo oggetto, verifica l’esistenza dell’utente utilizzando il metodo `isUserExists()`. Se l’utente esiste, istanzia un nuovo oggetto della classe `DbConnect` con permessi di lettura e scrittura per gestire le connessioni con il database; successivamente, preleva le informazioni di registrazione dall’oggetto `$userData`, avvia una transazione SQL e tenta di caricare tutte le informazioni sull’utente nelle tabelle ‘users’ e ‘user-informations’. Se la transazione va a buon fine, ne viene effettuato il “commit", e restituisce un valore vero; viceversa, si richiama il “roll-back" e il metodo scatena una eccezione.

##### `public function register($userData)`

Il metodo riceve un oggetto `UserInfo`, successivamente crea un token utilizzando il metodo `getToken`; successivamnete, tenta la registrazione chiamando il metodo `registerUser` e passandogli `$userData` e il token appena generato.

Se la registrazione va a buon fine (`registerUser->true`), allora il metodo restituisce un array contenente e-mail e token dell’utente appena registrato. Tali parametri verranno utilizzati dal metodo chiamante presente in `Session.php` per inviare l’e-mail di verifica della casella di posta all’utente. Se il processo di registrazione fallisce, il metodo scatena un’eccezione.

##### `public function validateRegister($auth, $token)`

Dopo aver stabilito una connessione con permessi di lettura e scrittura con il database mediante un oggetto della classe `DbConnect`, controlla l’esistenza dell’utente con e-mail `$auth`. Nel caso esista, procede aggiornando il campo ‘active’ e di fatto validando l’indirizzo e-mail dell’utente. Dopo aver completato la procedura di verifica, aggiorna il valore del token, generandolo con `getToken` e caricandolo nella riga del database corrispondente all’utente. In caso di errori in fase di verifica, attivazione dell’account o caricamento del nuovo token, il metodo scatena un’eccezione personalizzata.

##### `public function setNewPassword($email, $new, $hash)`

Dopo aver stabilito una connessione con permessi di lettura e scrittura con il database mediante un oggetto della classe `DbConnect`, il metodo verifica che l’indirizzo e-mail dell’account sia stato verificato (la verifica d’esistenza dell’account è stata fatta in precedenza, nel metodo chiamante della classe `Session`) e la corrispondenza con il token di controllo. Se entrambe le verifiche vengono completate con successo, il metodo si connette al database e richiede il cambio della password. Nel caso in cui il processo di verifica o cambio password non vada a buon fine, la funzione scatena un’eccezione.

##### `public function getNotGroupedUsers()`

Dopo aver stabilito una connessione con permessi di sola lettura con il database mediante un oggetto della classe `DbConnect`, il metodo recupera dal database tutte le informazioni sugli utenti non ancora assegnati ad un gruppo e li formatta per essere utilizzati in un campo “option" HTML. Il metodo viene infatti utilizzato per restituire i nomi dei profili utente ancora da attivare dal pannello di amministrazione. In caso di errore, scatena un’eccezione.

##### `private function getAnonymousString($string)`

È un metodo utilizzato per pseudonimizzare l’indirizzo e-mail dell’utente. Data una stringa in ingresso (`$string`) genera uno pseudonimo calcolando la somma ASCII dei caratteri della stringa e lo codifica in base 64, utilizzando il relativo metodo. Restituisce la stringa ottenuta.

##### `public function changePermissions($email, $group)`

Dopo aver stabilito una connessione con permessi di lettura e scrittura con il database mediante un oggetto `DbConnect`, imposta il gruppo `$group` per l’utente `$email`. Restituisce vero nel caso in cui l’operazione vada a buon fine, altrimenti falso. Il metodo è parte del processo di cambio dei permessi e attivazione dell’account operato dalla relativa funzione della classe `Session` e scatenato dal pannello di amministrazione al momento dell’attivazione ed assegnazione ad un gruppo per un determinato utente.

##### `public function getGroupedUsers($type)`

Dopo aver stabilito una connessione con permessi di sola lettura con il database mediante un oggetto della classe `DbConnect`, il metodo recupera dal database tutte le informazioni sugli utenti appartenenti ad uno specifico gruppo, in base al valore di `$type`:

-   `$type = 1`: utenti attivati, attacco non ancora effettuato;

-   `$type = 2`: utenti attivati, attacco effettuato, sondaggio ancora da somministrare;

-   `$type = 3`: sondaggio somministrato, in attesa del completamento del test così da concludere il test per l’utente.

In caso di errori o problemi, scatena un’eccezione.

##### `public function getUsersData()`

Dopo aver stabilito una connessione con permessi di sola lettura con il database mediante un oggetto della classe `DbConnect`, il metodo preleva dal database tutte le informazioni riguardanti tutti gli utenti iscritti al portale. Successivamente, formatta i valori prelevati a mò di tabella, per la loro visualizzazione all’interno della “tabella degli utenti" presente nell’area di amministrazione.

Nel processo, anonimizza le informazioni sensibili utilizzando il metodo `getAnonymousString`. Nel caso in cui il processo vada a buon fine, il metodo ritorna la tabella, altrimenti scatena un’eccezione.

##### `public function updateAttack($atke, $atkr, $auth)`

Dopo aver stabilito una connessione con permessi di lettura e scrittura con il database con un oggetto della classe `DbConnect`, il metodo aggiorna le informazioni sullo stato del test per il relativo utente (`$auth`) assegnando ai campi ‘attack-sent’ e ‘attack-result’, della tabella ‘user-informations’, i valori delle variabili `$atke` e `$atkr`.

Il metodo ritorna vero in caso di successo, altrimenti falso.

##### `public function getAtk($auth)`

Dopo aver stabilito una connessione con permessi di sola lettura con il database mediante un oggetto della classe `DbConnect`, il metodo preleva per l’utente con e-mail `$auth` i valori dei campi ‘attack-sent’ e ‘attack-result’, della tabella ‘user-informations’, e li restituisce in forma di array. In caso di errore, il metodo scatena un’eccezione.

#### Utilizzo dei file `xml` per la visualizzazione dei contenuti

Piuttosto che scrivere tutto il testo da visualizzare all’utente nelle pagine php front-end, è stato deciso di separare il contenuto dal contenitore e salvare tutte le “stringhe" da visualizzare in file xml distinti in base al nome della pagina finale, allo stato (ad esempio registrazione, conferma registrazione, completamento registrazione sono tutte sotto-sezioni sempre visualizzate nella pagina `register.php`, in base allo stato della richiesta di registrazione) e alla sotto-sezione nella pagina.

Inoltre, le le pagine finali (il contenitori) sono presenti nella cartella principale del sito, mentre tutte le stringhe da stampare vengono salvate nella cartella `components/parts/site`[^14].

#### Il file `session.php` e la gestione della sessione

Il file `session.php` è implementato in tutte le pagine del portale in quanto è il responsabile della gestione della sessione. All’apertura, crea un oggetto di tipo `Session`, dunque preparando e avviando la sessione; successivamente, in base alla tipologia di pagina (privata o pubblica) ed al gruppo di appartenenza dell’utente, personalizza il contenuto mostrato all’utente, fornendo e restringendo la lettura di contenuti. Il listato [session-php] contiene un estratto del file.

    require_once ('components/applications/Session.php');

    //** ROUTINES */
    $session = new Session();

    if ((isset($public) && !$public && !isset($_SESSION['user']) ||
     (isset($public) && !$public && isset($_SESSION['user']) 
     && $_SESSION['user']->getGroup()==0)))
        header('Location: register.php');
    else if (isset($public) && $public && isset($_SESSION['user']) &&
     $_SESSION['user']->getGroup()!=0)
        header("Location: index.php");

    if (isset($restricted) && isset($_SESSION['user']))
        if($restricted==1 && $_SESSION['user']->getGroup()<2)
            header('Location: 403.php');

Le variabili che gestiscono lo stato della sessione e mostrano i contenuti in base ai loro valori di ritorno sono pertanto:

-   `$public`: esprime se il contenuto di una pagina sia disponibile pubblicamente o meno. Assume due valori: 0 (contenuto privato), 1 (contenuto pubblico);

-   `$restricted`: l’accesso ad alcune pagine private è permesso solamente agli utenti del gruppo 2; quando `$restricted` assume il valore ‘1’ la pagina è ristretta, ‘0’ è il viceversa;

-   `$_SESSION[’user’]`: salva i dati dell’utente utili per la sessione. Se è impostata, è stato effettuato l’accesso; per questo viene utilizzata come variabile di stato.

Come già visto, l’inizializzazione della sessione avviene al momento della creazione dell’oggetto `$session`, in quanto è il costruttore della classe `Session` ad avviarla.

#### PHPMailer e l’invio di e-mail dal server {#implement-composer-phpmailer}

Come già detto, per inviare e-mail è stato installato prima un MTA nel server, implementando l’infrastruttura Postfix, e successivamente è stato scelto di utilizzare la libreria PHPMailer per l’invio di e-mail.

Per utilizzare PHPMailer, è stata seguita la procedura:

-   implementazione di Composer [v. [email-1-composer]][^15];

-   installazione di PHPMailer utilizzando Composer [v. [email-2-composer]];

-   implementazione nel codice PHP [v. [email-3-composer]];

-   impostazione delle variabili di invio [v. [email-4-composer]][^16];

<!-- -->

    ## Aprire il terminale del server
    # 1- Download Composer
    wget https://getcomposer.org/installer

    # 2- Rendere il file eseguibile
    chmod +x installer

    # 3 - Scaricare composer.phar e renderlo eseguibile
    sudo php ./installer

    # 4- Rimuovere l'installer
    rm installer

    # 5 - Impostare permessi globali di eseguibilità di composer
    sudo mv composer.phar /usr/local/bin/composer

    ## Da linea di comando
    composer require phpmailer/phpmailer

    ## Da implementare nel file in cui verrà utilizzato
    use PHPMailerPHPMailerPHPMailer;
    use PHPMailerPHPMailerException;

    # Caricamento dell'autoloader di Composer
    require 'vendor/autoload.php'


    $mail = new PHPMailer(true);
        
    try {
        // Impostazioni del server
        $mail->SMTPDebug = 0;                  // Nessun output per debugging
        $mail->isSMTP();                       // Invio con SMTP
        $mail->Host = 'smtp1.example.com';     // Host SMTP
        $mail->SMTPAuth  = true;               // Autenticazione SMTP abilitata
        $mail->Username  = 'user@example.com'; // SMTP username
        $mail->Password   = 'secret';          // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS attivata
        $mail->Port       = 587;              // porta TCP per connettersi via TLS
        
        //Contenuto
        $mail->setFrom('from@example.com', 'Mailer');
        $mail->addAddress('joe@example.net', 'Joe User');     
        $mail->addAddress('ellen@example.com');   
        [...]            
        
        $mail->addReplyTo('info@example.com', 'Information');
            
        // Contenuto
        $mail->isHTML(true);             
        $mail->Subject = $subject;       // prelevato da una variabile esterna
        $mail->Body    = $message;       // prelevato da una variabile esterna
        
        $mail->send();                   // Tentativo di invio del messaggio
        echo 'Il messaggio di posta è stato inviato';
    } catch (Exception $e) {
        echo "Non è stato possibile inviare il messaggio: {$mail->ErrorInfo}";
    }

#### PHPSeclib e la sicurezza del portale

##### PHPSeclib

La libreria PHPSeclib è stata implementata utilizzando Composer (v. la procedura di installazione di PHPMailer per maggiori dettagli). Essa propone una serie di metodi di crittografia, in linea con i più aggiornati standard di sicurezza[^17].

##### Anonimizzazione e pseudonimizzazione dei dati

E’ stato scelto di anonimizzare i dati sensibili degli utenti partecipanti al test attraverso pseudonimi, così da non poter ricondurre nessuna azione ad un indirizzo e-mail specifico. In tal senso, ogni indirizzo e-mail viene crittografato con la tecnica Blowfish a chiave segreta[^18], ed il risultato codificato in base 64[^19]

Per garantire la sicurezza necessaria agli utenti che si iscrivono al test, la password è crittografata generando un hash a partire dal comando bcrypt (algoritmo blowfish)[^20].

##### Informativa sulla privacy

Nonostante i dati degli utenti siano pseudonimizzati, è stato scelto [...] di sviluppare due informative che l’utente deve accettare prima di completare la registrazione al portale:

-   “Privacy Policy": informativa sulla privacy;

-   “Cookie Policy": informativa sui cookie collezionati e sul loro impiego.

Per scrivere ed implementare un’informativa completa, che contenesse informazioni riguardo i dati collezionati dal sito e quelli potenzialmente ottenibili con l’attacco di ingegneria sociale, è stato deciso di appoggiarsi ad una piattaforma erogatrice di servizi di policy: Iubenda (vedi <https://iubenda.it>).

Iubenda fornisce servizi personalizzati in grado di generare automaticamente le informative sulla privacy e sui cookie necessarie per i propri scopi e conformi al GDPR (Regolamento generale sulla protezione dei dati Europeo), a partire da un editor visuale.

#### Bootstrap e l’interfaccia grafica

Per semplificare la creazione di un’interfaccia grafica gradevole ed intuitiva è stato deciso di utilizzare il framework Bootstrap, versione 4.7. Inoltre, è stato utilizzato il template SB Admin 2 e riadattato alle richieste del portale.

Entrambi gli strumenti sono rilasciati sotto licenza MIT e quindi di libero utilizzo.

##### I popup di stato

Per semplificare la fruizione di alcune funzionalità dell’applicativo, anche grazie alle potenzialità di Bootstrap, sono stati implementati tre popup:

1.  consultazione dello stato del test: dalla pagina principale, in alto a destra, è possibile cliccare sul pulsante “Stato del Test"; si aprirà un popup che fornisce informazioni riguardo allo stato attuale del test per l’utente;

2.  menu a tendina in corrispondenza dell’indirizzo e-mail: cliccando sull’indirizzo e-mail associato al proprio utente nel menu in alto a destra, compare un menu a tendina attraverso cui è possibile consultare i dati del proprio profilo ed effettuare il logout;

3.  logout: la pagina di logout si apre in un pop-up.

Installazione della piattaforma {#part:installation-instructions-for-sept}
===============================

Per installare su un proprio server la piattaforma, è consigliato seguire la procedura descritta nella sezione ‘Manual’ del repository GitHub dell’infrastruttura (vedi fonte @git:github-personale); in questa documentazione viene presentata la procedura di installazione della versione iniziale dell’infrastruttura.

-   seguire la procedura di configurazione descritta nella sezione [part:prog-portal-prep-am-dev] e il paragrafo sulla creazione del database e delle relative tabelle alla sezione [part:prog-portal-prep-dbms]. Ricordarsi di creare due utenti del database con diversi diritti: uno con soli permessi di lettura e l’altro con permessi di lettura e scrittura;

-   scaricare dalla cartella ‘website’ di GitHub il progetto del portale e spostarlo nella cartella principale visualizzata dal server web (pubblico o locale);

-   preparare i file necessari per il funzionamento del database e delle funzioni di invio e-mail: nel ‘git’ appena scaricato manca la cartella “config" che contiene al suo interno i files di configurazione per connettersi al database ed ai server di posta elettronica. Procedere con la configurazione come segue:

    -   creazione della cartella: aprire il percorso `components/applications/database` e creare all’interno di `database` la cartella `config`;

    -   creazione del file di configurazione del database: dopo essere entrati nel percorso relativo alla cartella `config`, creare il file `config.php` e inserire al suo interno il contenuto del listato [conf-database],

                    <?php
                    $host = 'HOST_DBMS'; // normalmente è 'localhost'
                    $database = 'NOME_DATABASE'; // se hai seguito la procedura, è 'app'
                    
                    $username = 'USERNAME_READONLY';
                    $password = 'PASSWORD_READONLY';
                    
                    $usernameRO = 'USERNAME_READWRITE';
                    $passwordRO = 'PASSWORD_READWRITE';
                    ?>

        modificando i valori in corrispondenza delle variabili con quelli del proprio ambiente di sviluppo;

    -   creazione del file di configurazione dell’e-mail: dopo aver seguito la procedura di installazione di Composer e PHPMailer (v.[implement-composer-phpmailer]), senza impostare le variabili di configurazione per le caselle di posta, procedere creando il file `mail.settings.php` nello stesso percorso del file precedente. Copiare il codice del listato [set-mail-global], adattandolo ai propri server mail e, in generale, al proprio ambiente di sviluppo.

-   personalizzazione del contenuto: se si vuole personalizzare il testo da visualizzare all’utente finale, è importante ricordare che tutte le stringhe testuali sono presenti, catalogate, nella cartella `components/parts/site`, mentre i templates dei messaggi di posta nella cartella `components/parts/templates`. Per cambiare il testo, semplicemente modificare il contenuto dei file `xml` presenti all’interno delle due cartelle.

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

Rifiniti gli ultimi dettagli, la piattaforma sarà pronta per essere utilizzata.

Utilizzo della piattaforma
==========================

In questa sezione è illustrata la procedura di normale utilizzo della piattaforma. Essa coinvolge sia l’utente finale che l’amministratore del sistema, dunque nel corso della trattazione saranno mostrati tanto i procedimenti “back-end" quanto le funzionalità “front-end".

Per chiarire concetti difficilmente immaginabili attraverso una semplice lettura, il testo è arricchito grazie ad alcune figure di sintesi, contenenti le schermate mostrate agli utenti nel corso del test.

Procedura di utilizzo
---------------------

La piattaforma è stata sviluppata a partire da [part:prog-portal], pertanto il test richiede l’interazione di due attori:

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

![Pagina di registrazione dell’applicativo.<span data-label="fig:sept-register"></span>](resource/userguide-sept-register.png){width="85.00000%"}

![Pagina di conferma preliminare della registrazione all’applicativo.<span data-label="fig:sept-register-prel"></span>](resource/useguide-sept-registersucc.png){width="85.00000%"}

##### Conferma dell’indirizzo e-mail

Ciascun utente dovrà confermare il proprio indirizzo e-mail prima di attivare il proprio account. In tal modo, si è sicuri che l’indirizzo e-mail effettivamente esista e possa essere utilizzato per perpetrare l’attacco di ingegneria sociale del test, ovvero lo scopo di sviluppo del portale.

La conferma della casella postale avviene tramite un collegamento ipertestuale di attivazione inviato nella medesima casella, a cui l’utente deve cliccare per completare l’attivazione del proprio profilo (vedi figura [fig:sept-registerok-mail], [fig:sept-register-final]).

![Messaggio di posta elettronica inviato per richiedere la conferma della casella e-mail.<span data-label="fig:sept-registerok-mail"></span>](resource/userguide-sept-registerok-mail.png){width="85.00000%"}

#### Attivazione dell’account e scelta del gruppo

Una volta che l’utente ha confermato il proprio indirizzo e-mail, l’account è considerato “parzialmente attivo"; infatti, l’amministratore del sistema del test, dovrà confermare l’utente e allocarlo in un gruppo di test.

In pratica, l’amministratore del sistema accederà alla propria pagina personale, andrà nella sezione relativa all’attivazione degli utenti, selezionerà dal corrispondente menù a tendina l’utente, assegnandolo ad un gruppo, e cliccherà il pulsante di attivazione. Successivamente, il partecipante al test riceverà una e-mail di completamento della procedura di iscrizione: da questo momento l’utente potrà accedere al proprio portale, leggere le informazioni sul test e utilizzare gli strumenti di supporto.

L’attivazione dell’account è sviluppata in due parti:

-   attivazione lato utente: per la verifica della e-mail;

-   attivazione amministratore: per confermare l’account e allocarlo ad un “gruppo di lavoro"[^21].

![Schermata di completamento della procedura di verifica dell’indrizzo e-mail.<span data-label="fig:sept-register-final"></span>](resource/userguide-sept-register-final.png){width="85.00000%"}

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

Per com’è stata elaborata l’infrastruttura del test, l’indirizzo e-mail ufficiale da cui gli utenti ricevono messaggi di posta è `no-reply@sept.tech`, mentre la casella postale finta (utilizzata per effettuare l’attacco vero e proprio) è `no-reply.sept.tech@gmail.com`. Il nome associato alle caselle di posta è identico: `SEPT - Social Engineering Prevention Tool`.[^22] Il vettore di attacco è invece sviluppato utilizzando HiddenEye[^23]; l’URI è generato automaticamente dal suddetto tool sfruttando le infrastrutture Ngrok e Serveo[^24] (vedi le Figure [fig:sept-attack-back], [fig:sept-attack-front] e [fig:sept-attack-front-2]).

Per maggiori informazioni sulla creazione ed utilizzo del vettore d’attacco, consultare la sezione [part:userguide-attack-vector]

![Pannello utilizzato per abilitare la procedura di attacco: è necessario definire l’utente verso cui compiere l’attacco e il link al sito web malevolo.<span data-label="fig:sept-attack-back"></span>](resource/userguide-sept-attack-back.png){width="85.00000%"}

![Messaggio di posta elettronica malevolo inviato con l’indirizzo e-mail `no-reply.sept.tech@gmail.com`. Si noti il pulsante che indirizza al sito web clone.<span data-label="fig:sept-attack-front"></span>](resource/userguide-sept-attack-front.png){width="85.00000%"}

![Schermata visualizzata nella pagina principale dell’area privata dell’utente, per informarlo del test in corso.<span data-label="fig:sept-attack-front-2"></span>](resource/userguide-sept-attack-front-2.png){width="85.00000%"}

##### Completamento dell’attacco

L’amministratore rileva l’apertura del messaggio di posta elettronica da parte dell’utente “vittima" (vedi la Figura [fig:sept-attackcomplete-back]); da questo momento possono presentarsi due scenari principali:

-   test completato, attacco completato con successo: in questo caso l’utente ha aperto il link ed effettuato le azioni necessarie per la cattura dei dati; l’amministratore dichiara concluso l’attacco nel momento in cui riceve i dati nel proprio terminale;

-   test completato, attacco fallito: in questo caso l’utente non apre il link ed evita la minaccia; l’amministratore non riceve dati nel proprio terminale: in questo caso è necessario determinare arbitrariamente la fine del test.

In ogni caso, l’attacco si considera concluso un’ora dopo l’apertura dell’e-mail da parte del potenziale utente “vittima".

![Schermata di cattura delle informazioni dal sito clone utilizzato per l’attacco. In questo caso, l’attacco dovrebbe ritenersi concluso con successo. Se nessuna informazione giunge al terminale dell’esecutore del test, bisognerà attendere un’ora dall’apertura dell’e-mail utente prima di sancire la conclusione dell’attacco e considerarlo completato con esito negativo.<span data-label="fig:sept-attackcomplete-back"></span>](resource/userguide-sept-attackcomplete-back.png){width="85.00000%"}

#### Somministrazione del sondaggio finale

Il test termina con la somministrazione del sondaggio finale; esso viene abilitato dall’amministratore per ciascun utente e consiste in una serie di domande chiuse per valutare l’efficacia del test, del modello di prevenzione SEADMv2 (nel caso del gruppo due) e riguardo all’ingegneria sociale (vedi Figure [fig:sept-finalsurvey-back], [fig:sept-finalsurvey-front], [fig:sept-finalsurvey-front-2] e [fig:sept-finalsurvey-front-3])). Il sondaggio finale è stato sviluppato utilizzando il servizio online Typeform[^25].

L’esito del test e i sondaggi finali verranno utilizzati per proporre una versione rivisitata e migliorata di SEADMv2, considerando anche la possibilità che essa venga applicata in contesti non aziendali.

![Schermata di amministrazione utilizzata per abilitare il sondaggio finale ad un determinato utente.<span data-label="fig:sept-finalsurvey-back"></span>](resource/userguide-sept-finalsurvey-back.png){width="85.00000%"}

![Schermata visualizzata nella pagina principale dell’area privata dell’utente per informarlo dell’attivazione del sondaggio finale e fornirgli il collegamento per aprirlo.<span data-label="fig:sept-finalsurvey-front"></span>](resource/userguide-sept-finalsurvey-front.png){width="85.00000%"}

![Pagina iniziale del sondaggio finale. Esso è sviluppato utilizzando TypeForm<span data-label="fig:sept-finalsurvey-front-2"></span>](resource/userguide-sept-finalsurvey-front-2.png){width="85.00000%"}

![E-mail informativa inviata automaticamente all’utente finale per informarlo dell’attivazione del sondaggio finale.<span data-label="fig:sept-finalsurvey-front-3"></span>](resource/userguide-sept-finalsurvey-front-3.png){width="85.00000%"}

#### Completamento del test

L’ultima parte del processo corrisponde con il completamento del test. L’amministratore deve confermare il completamento del test dall’apposita sezione del pannello di amministrazione (v. [fig:sept-close-1]): l’utente riceverà un’e-mail informativa a riguardo (v. (v. [fig:sept-close-2])). Da qui, il test potrà considerarsi concluso.

![Pannello d’amministrazione per confermare il completamento del test.<span data-label="fig:sept-close-1"></span>](resource/userguide-sept-close-2.png){width="85.00000%"}

![E-mail informativa inviata automaticamente all’utente finale per informarlo del completamento del test.<span data-label="fig:sept-close-2"></span>](resource/userguide-sept-close-1.png){width="85.00000%"}

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

Preparazione ed esecuzione del vettore d’attacco {#part:userguide-attack-vector}
================================================

Preparazione dell’ambiente software per il vettore d’attacco
------------------------------------------------------------

L’applicativo utilizzato per compiere l’attacco è da compilare da linea di comando in ambiente Linux; per questo, è consigliato installare Kali Linux come distribuzione, in modo tale da avere un ambiente robusto e completo per poter compiere attacchi di ingegneria sociale.

#### Note sul metodo di installazione per Kali Linux

La distribuzione Kali deve essere installata standalone su una partizione del computer; l’installazione come sotto-sistema su Windows (utilizzando WSL, 1 e 2) o su macchina virtuale compromette alcune funzionalità e non permette l’utilizzo dei servizi ngrok e serveo, necessari per creare un tunnel pubblico attraverso cui gli utenti esterni possano fruire delle risorse originariamente disponibili su rete locale generate del computer (e in questo caso, il sito web clone).

#### Note sulla procedura di installazione per Kali Linux

Kali Linux può essere installato facilmente scaricando la distribuzione direttamente dal portale ufficiale o mediante il torrent fornito da Offensive Security (l’organizzazione autrice la distribuzione); durante la procedura di installazione, è preferibile lasciare invariati i parametri predefiniti e selezionare le opzioni consigliate.

Installazione di HiddenEye
--------------------------

Per perpetrare l’attacco, verrà utilizzato HiddenEye; è stato scelto questo rispetto ad altri applicativi per la capacità di mantenere più connessioni simultanee con l’esterno e per le possibilità di sviluppare un vettore d’attacco che tenti di catturare informazioni sulla localizzazione (gli altri applicativi si limitano a tentare di catturare le informazioni di accesso per servizi online famosi, come e-mail e password di Gmail).

Per installare HiddenEye su Kali Linux, seguire la procedura descritta nella sezione “Usage and Installation" della fonte @git:hiddeneye.

Esecuzione di HiddenEye e utilizzo
----------------------------------

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

Ulteriori informazioni sull’installazione ed esecuzione del vettore d’attacco, dell’infrastruttura e del portale
================================================================================================================

In questo documento è stato presentata l’installazione ed utilizzo del vettore d’attacco e dell’infrastruttura del portale per il particolare test, utilizzando la versione iniziale fornita nel GitHub @git:github-personale. Per versioni aggiornate e la documentazione completa di installazione dell’infrastruttura e del vettore d’attacco, visitare la pagina GitHub del progetto (v. @git:github-personale).

Analisi degli strumenti forniti per il test {#appendix:mu-and-seadmv2-practic}
===========================================

Manuale Utente
--------------

Il manuale utente contiene un riassunto rivisto e semplificato della parte “Compendio di Ingegneria Sociale" presente in questo documento. L’obiettivo è illustrare in maniera generale cosa sia il fenomeno dell’ingegneria sociale, proporre alcuni esempi reali e descrivere alcune tecniche di prevenzione che un utente medio può attuare.

Per rendere più semplice la fruizione del manuale utente, si è deciso di accorpare in una pagina specifica del portale l’intero manuale, cercando inoltre di renderlo interattivo; in tal senso, sono state effettuate le seguenti operazioni:

-   sviluppo di una pagina web in cui inserire il manuale utente;

-   gestione interattiva con ancore all’interno del testo;

-   inserimento di collegamenti ipertestuali per approfondire taluni argomenti presentati solo sommariamente nel documento;

-   presenza di video ed illustrazioni per migliorare l’apprendimento alla materia;

-   sviluppo di un relativo menu per navigare con facilità attraverso le sezioni del manuale.

Il manuale utente viene fornito ad entrambi i gruppi per tutta la durata del test; nonostante sia riconosciuto come uno dei potenziali strumenti di prevenzione, il sondaggio finale non ne analizzerà potenzialità e limiti, in quanto l’obiettivo della trattazione è testare SEADMv2.

Applicazione SEADMv2
--------------------

L’applicazione SEADMv2 implementata nel portale consiste in un sondaggio a risposte chiuse (‘Sì’, No’, ’Non So / Non Credo’) e con condizionali logici, al termine del quale l’utente riceve un consiglio. La quantità di domande dipende dalle risposte date. Lo schema per questo test è stato sviluppato utilizzando il servizio esterno Typeform.

A partire dal modello presentato nella sezione [part:seadmv2], è stato sviluppato un applicativo online a forma di sondaggio che si attenesse il più possibile all’originale; l’obiettivo è infatti testare limiti e potenzialità di tali domande, e migliorarle in modo tale che possano essere facilmente utilizzate, in maniera efficace, da una platea più ampia possibile. Infatti, nonostante le domande del modello siano astratte e generali, è stata scelta un implementazione al pari.

![Modello SEADMv2 con operatori logici utilizzato durante il test. Le linee rosse indicano una risposta negativa (No), le linee verdi positiva (Sì).[fig:seadmv2-full-portal]](resource/seadmv2-full-portal.png){width="textwidth"}

##### Struttura della domanda

Ogni quesito contiene una domanda principale e una descrizione con specificazioni ed esempi diretti, utili per chiarire all’utente la finalità della richiesta.

L’utente può rispondere ‘Sì’, ‘No’, ‘Non so / Non chiaro’:

-   Sì, No: il flusso delle domande segue quanto indicato in figura [fig:seadmv2-full-portal];

-   ‘Non so / Non chiaro’: verrà sempre posta una domanda successiva, questa opzione, non considerata nel modello originale, è utile per valutare l’effettiva comprensibilità dei quesiti ed eventualmente, in fase conclusiva, proporne di migliorati.

[^1]: La scelta di Namecheap è vincolata a sole ragioni di costo, in quanto era il registrar con prezzo esposto inferiore.

[^2]: P
[^3]: Molti browser informano l’utente quando la connessione non è sicura, ovvero quando il protocollo non è https (porta 443) ma http (porta 80); in tal senso, la scelta di installare un certificato SSL è atta sia ad aumentare la sicurezza di trasferimento dei dati sia a infondere sicurezza nell’utente finale.

[^4]: Postfix è un popolare Mail Transfer Agent (MTA) open source che può essere utilizzato per instradare e recapitare la posta su un sistema Linux. Si stima che circa il 25% dei server di posta pubblici su Internet esegua Postfix. Fonte: DigitalOcean.

[^5]: Per completare la procedura di implementazione di Postfix, sono state seguite alcune delle indicazioni fornite nella guida <https://www.digitalocean.com/community/tutorials/how-to-install-and-configure-postfix-on-ubuntu-20-04>.

[^6]: La scelta è stata ponderata in base alle conoscenze pregresse, le potenzialità, la sicurezza, la robustezza e gli eventuali costi.

[^7]: Le relative documentazioni sono consultabili dalle fonti: Bootstrap (e-mail @site:bootstrap), JQuery (v. @site:jquery), JQuery Easing (v. @site:jquery-easing), Data Tables (v. @site:datatables), SB Admin 2 (v. @site:sb-admin-2).

[^8]: Nel caso si voglia installare il portale su un proprio server, seguire le istruzioni della sezione [part:installation-instructions-for-sept].

[^9]: La presentazione dei fogli di stile viene omessa.

[^10]: La presentazione delle immagini viene omessa.

[^11]: La presentazione dei fogli Javascript viene omessa.

[^12]: Si noti che tutte le pagine sviluppate sono commentate e visionabili dal relativo Github, precedentemente fornito.

[^13]: L’oggetto `UserInfo` creato non conterrà informazioni sul campo password, che sarà impostato a `null`.

[^14]: Per maggiori informazioni, consultare il codice sorgente presente nel relativo GitHub.

[^15]: Per maggiori dettagli, cfr. <https://getcomposer.org/>.

[^16]: Per maggiori dettagli e altre funzionalità disponibili con la libreria, consultare la documentazione dal portale <https://github.com/PHPMailer/PHPMailer>.

[^17]: Per maggiori informazioni visitare la documentazione presente alla fonte <https://github.com/phpseclib/phpseclib>.

[^18]: Blowfish è un meccanismo crittografico simmetrico a blocchi che può essere utilizzato come sostituto immediato di DES. Richiede una chiave di lunghezza variabile, da 32 bit a 448 bit, rendendola ideale sia per uso personale che globale. Blowfish è stato progettato nel 1993 [...], da allora è stato analizzato considerevolmente e sta lentamente guadagnando l’accettazione come un potente algoritmo di crittografia. Blowfish non è brevettato e senza licenza ed è disponibile gratuitamente per tutti gli usi. (fonte <https://www.schneier.com/academic/blowfish/>.)

[^19]: Base64 è un sistema di codifica che consente la traduzione di dati binari in stringhe di testo ASCII, rappresentando i dati sulla base di 64 caratteri ASCII diversi. (cit. fonte <https://it.wikipedia.org/wiki/Base64>).

[^20]: Le scelte progettuali verranno descritte nella sezione riguardante la progettazione dell’applicativo web.

[^21]: Per le specifiche dei gruppi, consultare la sezione [part:campione].

[^22]: L’utente preparato e in grado di utilizzare il modello SEADMv2 potrà scoprire che l’indirizzo e-mail non corrisponde con quello solitamente utilizzato e non procederà a cliccare il pulsante.

[^23]: La progettazione è spiegata nella sezione successiva, il funzionamento è invece presentato nel capitolo [part:vettore-attacco].

[^24]: Maggiori dettagli sul funzionamento e le caratteristiche dei due servizi sono disponibili alle fonti: <https://ngrok.com/> e <https://github.com/milio48/serveo>.

[^25]: Maggiori informazioni sulle caratteristiche e le funzionalità di Typeform all’URI <https://typeform.com>; per il sondaggio finale, leggere la sezione [appendix:sondaggi].

# Personalizzazione
