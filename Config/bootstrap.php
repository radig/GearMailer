<?php
App::uses('CakeLog', 'Log');
CakeLog::config('assync_job', array(
    'engine' => 'FileLog',
    'file' => 'assync_job',
    'scopes' => ['worker_error']
));

CakeLog::config('assync_mail', array(
    'engine' => 'FileLog',
    'file' => 'assync_mail',
    'scopes' => ['mail_error']
));
