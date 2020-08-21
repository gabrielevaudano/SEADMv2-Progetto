<?php
$public = false;

require_once ('components/parts/header.php');
?>
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-4 text-gray-800">Manuale Utente</h1>

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
            <div class="col-lg-12 mb-12">
                <div class="card shadow mb-12">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Introduzione</h6>
                    </div>
                    <div class="card-body">
                        <p>
                            <?php
                            include('components/parts/templates/user-guide/chapter-one.xml');
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <hr />
        </div>
        <div class="row">
            <div class="col-lg-12 mb-12">
                <div class="card shadow mb-12">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Esempi d'attacco</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        include('components/parts/templates/user-guide/chapter-two.xml');
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <hr />
        </div>
        <div class="row">
            <div class="col-lg-12 mb-12">
                <div class="card shadow mb-12">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Tecniche di difesa</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        include('components/parts/templates/user-guide/chapter-three.xml');
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 mb-12">
                <div class="card shadow mb-12">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Strumenti e tecniche</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        include('components/parts/templates/user-guide/chapter-four.xml');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->
<?php
require_once ('components/parts/footer.php');
?>