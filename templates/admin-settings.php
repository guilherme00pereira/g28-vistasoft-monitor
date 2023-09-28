<?php

use G28\VistasoftMonitor\Core\Plugin;

$default_tab = null;
$tab         = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
?>

<div class="wrap">
    <h1>VistaSoft</h1>
    <h2>Comparação de imóveis cadastrados no CRM e no Site</h2>
    <nav class="nav-tab-wrapper">
    <a href="?page=g28-vistasoft-monitor" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>">Integração</a>
    <a href="?page=g28-vistasoft-monitor&tab=add" class="nav-tab <?php if($tab==='add'):?>nav-tab-active<?php endif; ?>">Adicionar Imóvel</a>
    </nav>

    <div class="g28-vistasoft-tab-content">
    <?php switch($tab) :
    case 'add':
        include sprintf( "%sadd.php", Plugin::getTemplateDir() );
        break;
    default:
        include sprintf( "%scron.php", Plugin::getTemplateDir() );
        break;
    endswitch; 
        ?>
    </div>
</div>