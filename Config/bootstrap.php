<?php
App::uses('CakeLog', 'Log');
CakeLog::config('gearmailer', array(
    'engine' => 'FileLog',
    'file' => 'gearmailer.log',
    'scopes' => array('async_mail')
));
