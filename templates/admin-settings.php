<?php

use G28\VistasoftMonitor\Core\Logger;

[ $file, $log ] = Logger::getInstance()->getLogFileContent();
?>

<div class="wrap">
    <h1>VistaSoft</h1>
    <h2>Comparação de imóveis cadastrados no CRM e no Site</h2>
    <div class="log-container">
        <div class="log-select-section">
            <div class="toggle-section">
                <span style="margin-right: 10px">Ativar função: </span>
                <label class="toggle-button">
                    <input id="toggleStatus" type="checkbox">
                    <span class="knob"></span>
                </label>
            </div>
        </div>
        <h3>Log de execução</h3>
        <div id="logFileContent" class="log-content">
			<?php echo $log ?>
        </div>

    </div>


</div>