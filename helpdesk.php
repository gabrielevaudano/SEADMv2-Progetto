<?php
$public = false;
require_once ('components/parts/header.php');
?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Centro Assistenza</h1>

    <noscript>
        <div class="row">

            <!-- Content Column -->
            <div class="col-lg-12 mb-12">

                <!-- Intro -->
                <div class="card shadow mb-12">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Hai disabilitato Javascript?</h6>
                    </div>
                    <div class="card-body">
                        <p>Sembra che il tuo browser non abbia Javascript abilitato. Alcune funzionalità del sito potrebbero essere limitate o bloccate. Per fruire di una esperienza completa, riabilita Javascript.</p>
                    </div>
                </div>
            </div>
        </div>
    </noscript>
    <div class="row">

        <!-- Content Column -->
        <div class="col-lg-12 mb-12">

            <!-- Intro -->
            <div class="card shadow mb-12">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Centro Assistenza</h6>
                </div>
                <div class="card-body">
                    <p>Attraverso questo strumento potrai verificare e-mail o richieste sospette ed evitare di cadere vittima di un attacco di ingegneria sociale conoscendone il funzionamento e sfruttando le potenzialità di strumenti in grado di svolgere funzione di supporto decisionale.</p>
                    <p>Nel riquadro sottostante è presente il modello. Semplicemente rispondi alle domande e riceverai un consiglio su come procedere nel caso di situazioni potenzialmente rischiose. Nel caso in cui alcune risposte non siano chiare o non sai cosa rispondere, seleziona il campo "Non lo so".</p>
                    <p class="text-danger small">Il tuo ID è <strong><?=$_SESSION['uid']?></strong></p>
                    <hr />
                    <div class="text-center"><a class="typeform-share button text-center" href="https://form.typeform.com/to/teMRkej8" data-mode="popup" style="display:inline-block;text-decoration:none;background-color:#4D72E0;color:white;cursor:pointer;font-family:Helvetica,Arial,sans-serif;font-size:16px;line-height:40px;text-align:center;margin:0;height:40px;padding:0px 26px;border-radius:20px;max-width:100%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:bold;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;" data-hide-headers=true data-hide-footer=true data-submit-close-delay="0" target="_blank">Richiedi Assistenza</a> <script> (function() { var qs,js,q,s,d=document, gi=d.getElementById, ce=d.createElement, gt=d.getElementsByTagName, id="typef_orm_share", b="https://embed.typeform.com/"; if(!gi.call(d,id)){ js=ce.call(d,"script"); js.id=id; js.src=b+"embed.js"; q=gt.call(d,"script")[0]; q.parentNode.insertBefore(js,q) } })() </script></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <hr>
    </div>
</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php
require_once ('components/parts/footer.php');
?>








