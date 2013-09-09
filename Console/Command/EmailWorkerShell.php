<?php
App::uses('Shell', 'Console');
App::uses('EmailWorker', 'GearMailer.Vendor');
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
    public function startup() {
        parent::startup();
        $this->out(__d('gearman_worker', 'Cake GearmanWorker Shell'));
        $this->hr();

        $this->EmailWorker = new EmailWorker();
    }

/**
 * Override main
 *
 * @return void
 */
    public function main() {
        $this->out(__d('gearman_worker', 'Working...'));
        $this->EmailWorker->init();
        $this->out(__d('gearman_worker', 'Finishing.'));
    }
}
