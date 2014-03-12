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
        $worker->addFunction("sendMail", [$this, "send"]);

        while ($worker->work()) {
            if ($worker->returnCode() != GEARMAN_SUCCESS) {
                throw new Exception("Job failed: can't send emails.");
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

        $credentials = $this->_getCredentials($messageData['credentials']);
        $message = $messageData['message'];

        $SesEmail = SesClient::factory($credentials);

        try {
            echo date('[Y-m-d H:i:s]'), " Sending message...";
            $SesEmail->sendRawEmail(['RawMessage' => ['Data' => base64_encode($message)]]);
            echo " | ", date('[Y-m-d H:i:s]'), " Message sended!\n";
        } catch (MessageRejectedException $e) {
            error_log('Can\'t enqueue job for message: ' . print_r($e->getMessage(), true));
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
        $credentials = ['key' => null, 'secret' => null, 'region' => 'us-east-1'];

        foreach ($credentials as $field => $default) {
            if (isset($data[$field])) {
                $credentials[$field] = $data[$field];
            }
        }

        return $credentials;
    }
}
