<?php

use G28\IntegraJetengineVistasoft\Config\Logger;

$files          = logger::getInstance()->getLogFiles();
[ $file, $log ] = Logger::getInstance()->getLogFileContent( $files[0] );


?>
<div class="log-container">
    <div class="log-select-wrapper">
        <label for="logFiles">Arquivos de log: </label>
        <select id="logFiles" name="logFiles">
            <?php foreach ($files as $file) { ?>
                <option value="<?php echo $file?>"><?php echo $file ?></option>
            <?php } ?>
        </select>
        <span id="loadingLogs" style="display: none; padding-left: 15px;">
            <img src="<?php echo esc_url( get_admin_url() . 'images/spinner.gif' ); ?>"  alt="loading"/>
        </span>
    </div>

    <div id="logFileContent" class="log-content">
        <?php echo $log ?>
    </div>

</div>

