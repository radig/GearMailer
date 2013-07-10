<?php
App::uses('CakeLog', 'Log');
CakeLog::config('assync_job', array(
    'engine' => 'FileLog',
    'file' => 'assync_job',
));

CakeLog::config('assync_mail', array(
    'engine' => 'FileLog',
    'file' => 'assync_mail',
));
