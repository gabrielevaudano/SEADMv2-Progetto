<?php
$public =  false;
require_once('components/parts/header.php');

if (!isset($_SESSION['user']) || $_SESSION['user']->getGroup() !=9) // where 9 means admin
    header('Location: index.php');

if (isset($_POST['perma'])) {
    if (!$session->changePermissions($_POST['email'], $_POST['group']))
        echo "<script>alert('La modifica dei permessi non è andata a buon fine.'); </script>";
}
else if (isset($_POST['attack-vector'])) {
    if (empty($_POST['link']))
        echo "<script>alert('L\'inizio dell\'attacco non è stato eseguito. Link al server mancante.'); </script>";
    else if (!$session->startTest($_POST['email'], $_POST['link']))
        echo "<script>alert('L\'inizio dell\'attacco non è stato eseguito.'); </script>";
    else
        echo "<script>alert('L\'inizio dell\'attacco  è stato eseguito.'); </script>";

} else if (isset($_POST['aluet']))
    $session->doFinalSurvey($_POST['email']);
else if (isset($_POST['doetk'])) {
    if($session->finalizeInTest($_POST['email']))
        echo "<script>alert('Completamento effettuato.'); </script>";
    else
        echo "<script>alert('Completamento non effettuato.'); </script>";
    }
?>


<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Pannello di amministrazione</h1>
    <div class="row">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-header p-4">
                    <h6 class="m-0 font-weight-bold text-primary">Abilita Utente</h6>
                </div>
                <div class="card-body p-4">
                    <form action="admin.php" method="post">
                        <div class="form-group row">
                            <div class="col-sm-12 mb-12 mb-sm-0">
                                <select required name="email" class="form-control-user" style="border-color: #cecece; padding: 1.2em; width: 100%; outline: none;" title="Scegli una opzione" required>
                                    <?php echo $session->getNotGroupedUsers() ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12 mb-12 mb-sm-0">
                                <select required name="group" class="form-control-user" style="border-color: #cecece; padding: 1.2em; width: 100%; outline: none;" title="Scegli una opzione" required>
                                    <option value="1">Gruppo 1</option>
                                    <option value="2">Gruppo 2</option>
                                </select>
                            </div>
                            <input type="hidden" name="perma" />
                        </div>
                        <input type="submit" value="Abilita" class="btn btn-user btn-primary" />
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100">
                <div class="card-header p-4">
                    <h6 class="m-0 font-weight-bold text-danger">Esegui Attacco</h6>
                </div>
                <div class="card-body p-4">
                    <form action="admin.php" method="post">
                        <div class="form-group row">
                            <div class="col-sm-12 mb-12 mb-sm-0">
                                <select required name="email" class="form-control-user" style="border-color: #cecece; padding: 1.2em; width: 100%; outline: none;" title="Scegli una opzione" required>
                                    <?php echo $session->getGroupedUsers(1) ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12 mb-12 mb-sm-0">
                                <input type="text" style="border-color: #cecece; padding: 1.2em; width: 100%; outline: none;" class="form-control form-control-user" placeholder="Link al server malevolo" name="link" required />
                            </div>
                            <input type="hidden" name="attack-vector" />
                        </div>
                        <input type="submit" value="Invia e-mail malevola" class="btn btn-user btn-danger" />
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <hr />
    </div>
    <div class="row">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-header p-4">
                    <h6 class="m-0 font-weight-bold text-primary">Attiva Sondaggio Finale</h6>
                </div>
                <div class="card-body p-4">
                    <form action="admin.php" method="post">
                        <div class="form-group row">
                            <div class="col-sm-12 mb-12 mb-sm-0">
                                <select required name="email" class="form-control-user" style="border-color: #cecece; padding: 1.2em; width: 100%; outline: none;" title="Scegli una opzione" required>
                                    <?php echo $session->getGroupedUsers(2) ?>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="aluet" />
                        <input type="submit" value="Abilita" class="btn btn-user btn-primary" />
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-header p-4">
                    <h6 class="m-0 font-weight-bold text-info">Termina il test per l'account</h6>
                </div>
                <div class="card-body p-4">
                    <form action="admin.php" method="post">
                        <div class="form-group row">
                            <div class="col-sm-12 mb-12 mb-sm-0">
                                <select required name="email" class="form-control-user" style="border-color: #cecece; padding: 1.2em; width: 100%; outline: none;" title="Scegli una opzione" required>
                                    <?php echo $session->getGroupedUsers(3) ?>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="doetk" />
                        <input type="submit" value="Termina e informa l'utente via e-mail" class="btn btn-user btn-info" />
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <hr />
    </div>
    <div class="row">
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card shadow mb-12">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informazioni sugli utenti</h6>
                </div>
                <div class="card-body">
                    <?php include('components/parts/site/admin/table.xml'); ?>
                </div>
            </div>
        </div>
    </div>

</div>
<?php
require_once ('components/parts/footer.php');


?>
