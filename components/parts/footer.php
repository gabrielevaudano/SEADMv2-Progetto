<?php
if (isset($public) && $public || !isset($_SESSION['user']))
    include ('components/parts/site/footer.ext.xml');
else if (isset($public) && !$public || isset($_SESSION['user']))
    include ('components/parts/site/footer.int.xml');
else
    include ('components/parts/site/footer.ext.xml')
?>


