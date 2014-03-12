<?php
App::uses('CakeLog', 'Log');
CakeLog::config('gearmailer', [
    'engine' => 'FileLog',
    'file' => 'gearmailer.log',
    'scopes' => ['async_mail']
]);
