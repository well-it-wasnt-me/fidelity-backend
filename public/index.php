<?php
function returnLocales(){
    clearstatcache();
    $actual_locale = substr(str_replace("-", "_",$_SERVER['HTTP_ACCEPT_LANGUAGE']), 0, 5);
    $registered_locale = ['it_IT', 'en_US', 'es_ES', 'hr_HR'];

    if(!in_array($actual_locale, $registered_locale)){
        return 'en_US';
    } else {
        return $actual_locale;
    }
}
setlocale(LC_ALL, returnLocales().".UTF-8");
(require __DIR__ . '/../config/bootstrap.php')->run();