<?php
/**
 * Shell to start Gearman Worker
 */
App::uses('Shell', 'Console');
App::uses('SendEmailWorker', 'GearMailer.Lib');

class SendEmailWorkerShell extends Shell {
    public $EmailWorker;

/**
 * Override startup
 *
 * @return void
 */
    public function startup()
    {
        parent::startup();
        $this->out(__d('GearmanWorker', 'Cake GearmanWorker Shell'));
        $this->hr();

        $this->EmailWorker = new SendEmailWorker();
    }

/**
 * Override main
 *
 * @return void
 */
    public function main()
    {
        $this->EmailWorker->init();
    }
}
