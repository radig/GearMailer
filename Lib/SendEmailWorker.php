<?php
App::uses('CakeEmail', 'Network/Email');
/**
 * Gearman Worker
 *
 * @package         radig.GearMailer.Lib
 * @copyright       Radig Soluções em TI
 * @author          Radig Dev Team - suporte@radig.com.br
 * @version         2.0
 * @license         Vide arquivo LICENCA incluído no pacote
 * @link            http://radig.com.br
 */
class SendEmailWorker {
    public $mailConfig = 'aws_ses';
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
 * send email
 *
 * @param GearmanJob $job
 * @return boolean
 * @throws Exception
 */
    public function send(GearmanJob $job) {
        $CakeEmail = unserialize($job->workload());

        if (!is_a($CakeEmail, 'CakeEmail')) {
            throw new Exception('Parâmetro inválido passado como Email para Worker');
        }

        $recipients = $CakeEmail->to();
        $CakeEmail->config($this->mailConfig);

        foreach ($recipients as $email => $name) {
            if (is_numeric($email)) {
                $email = $name;
            } else {
                $email = array($email => $name);
            }

            $CakeEmail->to($email);

            // Send message
            if (!$CakeEmail->send()) {
                CakeLog::write('assync_mail', "Erro: ".serialize($CakeEmail));

                $job->sendFail();

                return false;
            }
        }

        return true;
    }
}
