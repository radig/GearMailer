<?php
/**
 * Gearman Worker
 */
App::uses('CakeEmail', 'Network/Email');

class SendEmailWorker {
    public $mailConfig = 'aws_ses';

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

            // envia as mensagens usando CakeEmail
            if (!$CakeEmail->send()) {
                CakeLog::write('assync_mail', serialize($CakeEmail));

                $job->sendFail();

                return false;
            }
        }

        // notifica sucesso
        $job->sendComplete('Success');

        return true;
    }
}
