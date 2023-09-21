<?php

use G28\VistasoftMonitor\Core\Logger;

[ $file, $log ] = Logger::getInstance()->getLogFileContent();
?>

<div class="wrap">
    <h1>VistaSoft</h1>
    <h3>Comparação de imóveis cadastrados no CRM e no Site</h3>
    <div class="log-container">
        <div class="log-select-section">
            <div class="toggle-section">
                <span style="margin-right: 10px">Manual</span>
                <label class="toggle-button">
                    <input id="toggleStatus" type="checkbox">
                    <span class="knob"></span>
                </label>
                <span style="margin-left: 10px;">Automático</span>
            </div>

            <div>
                <button id="btnProcess" class="button g28-button-green">Iniciar processamento manual</button>
                <span id="loadingLogs" style="display: none; padding-left: 15px;">
                    <img src="<?php echo esc_url( get_admin_url() . 'images/spinner.gif' ); ?>" alt="loading"/>
                </span>
            </div>

        </div>

        <div id="logFileContent" class="log-content">
			<?php echo $log ?>
        </div>

    </div>


</div>