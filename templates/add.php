<?php

use G28\VistasoftMonitor\Core\Logger;

$logger = new Logger(Logger::LOGADD);
$log = $logger->getLogContent();

?>

<div class="wrap">
    <table class="form-table" style="width: auto;">
        <tbody>
            <tr>
                <th scope="row" style="width: 80px;"><label for="input_id">Código</label></th>
                <td><input name="codigo" type="text" id="codigo" class="regular-text"></td>
                <td>
                    <button type="button" class="button button-primary" id="btnAdd">Adicionar</button>
                </td>
                <td>
                    <span class="spinner is-active" id="spinAdd" style="display: none;"></span>
                </td>
            </tr>
        </tbody>
    </table>
    <h3>Log de execução</h3>
    <div id="addFileContent" class="log-content">
		<?php echo $log ?>
    </div>
</div>