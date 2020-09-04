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

Tra le varie alternative di hosting online è stato selezionato [DigitalOcean](https://digitalocean.com), per la facilità di utilizzo e i costi contenuti dei piani hosting. Dopo aver creato un account, si è proceduto con la creazione di un VPS (un “droplet", così come denominato da DigitalOcean).

#### Impostazione del firewall
# Personalizzazione
