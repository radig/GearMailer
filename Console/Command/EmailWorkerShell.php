<?php
App::uses('Shell', 'Console');
App::uses('SendEmailWorker', 'GearMailer.Lib');
/**
 * Shell responsable to create and init the Gearman Worker
 *
 * @package         radig.GearMailer.Lib.Network.Email
 * @copyright       Radig Soluções em TI
 * @author          Radig Dev Team - suporte@radig.com.br
 * @license         MIT
 * @link            http://radig.com.br
 */
class EmailWorkerShell extends Shell {
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
