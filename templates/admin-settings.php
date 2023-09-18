<?php

use G28\IntegraJetengineVistasoft\Config\Plugin;

$default_tab = null;
$tab         = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
?>

<div class="wrap">
    <h1>VistaSoft</h1>
    <nav class="nav-tab-wrapper">
    <a href="?page=integra-jetengine-vistasoft" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>">Campos do Imóvel</a>
    <!--a href="?page=integra-jetengine-vistasoft&tab=features" class="nav-tab <?php if($tab==='features'):?>nav-tab-active<?php endif; ?>">Características do Imóvel</a-->
    <a href="?page=integra-jetengine-vistasoft&tab=logs" class="nav-tab <?php if($tab==='logs'):?>nav-tab-active<?php endif; ?>">Logs</a>
    </nav>

    <div class="g28-integra-tab-content">
    <?php switch($tab) :
    case 'logs':
        include sprintf( "%slogs.php", Plugin::getTemplateDir() );
        break;
    case 'features':
        include sprintf( "%sfeatures-mapper.php", Plugin::getTemplateDir() );
        break;    
    default:
        include sprintf( "%sfields-mapper.php", Plugin::getTemplateDir() );
        break;
    endswitch; 
        ?>
    </div>
</div>