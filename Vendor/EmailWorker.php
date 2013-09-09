<?php
use Aws\Ses\SesClient;

/**
 * Gearman Worker
 *
 * @package         radig.GearMailer.Lib
 * @copyright       Radig Soluções em TI
 * @author          Radig Dev Team - suporte@radig.com.br
 * @license         MIT
 * @link            http://radig.com.br
 */
class EmailWorker {

/**
 * init gearman worker
 *
 * @throws Exception
 */
    public function init() {
        $worker= new GearmanWorker();
        $worker->addServer();
        $worker->addFunction("sendMail", array($this, "send"));

        while ($worker->work()) {
            if ($worker->returnCode() != GEARMAN_SUCCESS) {
                throw new Exception('Falha em alguma parte do Gearman');
            }
        }
    }

/**
 * Read a job from GearmanServer and send messages
 *
 * @param GearmanJob $job
 * @return boolean
 */
    public function send(GearmanJob $job) {
        $messageData = unserialize($job->workload());

        $credentials = $this->_getCredentials($messageData);
        $message = $messageData['message'];

        $SesEmail = new SesClient($credentials);

        if (!empty($credentials['dkim'])) {
            $SesEmail->setIdentityDkimEnabled(array('Identity' => $credentials['dkim'], 'DkimEnabled' => true));
        }

        try {
            $SesEmail->sendRawEmail(array('RawMessage' => array('Data' => base64_encode($message))));
        } catch (MessageRejectedException $e) {
            CakeLog::write('warning', print_r($e->getMessage(), true), 'assync_mail');
            return false;
        }

        return true;
    }

/**
 * Recover, from $data, all credentials relative info.
 * 
 * @param  array  $data Data from job
 * @return array
 */
    protected function _getCredentials($data) {
        $defaults = ['username' => null, 'password' => null, 'dkim' => null];
        $credentials = array_merge($default, $data);

        $credentials['key'] = $credentials['username'];
        $credentials['secret'] = $credentials['password'];

        unset($credentials['username'], $credentials['password']);

        return $credentials;
    }
}
