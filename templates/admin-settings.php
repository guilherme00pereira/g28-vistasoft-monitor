<?php

use G28\VistasoftMonitor\Core\Logger;

[ $file, $log ] = Logger::getInstance()->getLogFileContent();
?>

<div class="wrap">
    <h1>VistaSoft</h1>
    <h3>Comparação de imóveis cadastrados no CRM e no Site</h3>
    <div class="log-container">
        <div class="log-select-wrapper">
            <button id="btnProcess" class="button g28-button-green">Iniciar processamento</button>
            <span id="loadingLogs" style="display: none; padding-left: 15px;">
                <img src="<?php echo esc_url( get_admin_url() . 'images/spinner.gif' ); ?>" alt="loading"/>
            </span>
        </div>

        <div id="logFileContent" class="log-content">
			<?php echo $log ?>
        </div>

    </div>


</div>