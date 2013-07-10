<?php
class SendEmailWorker {
    public $mailConfig = 'aws_ses';

    public function init() {
        $worker= new GearmanWorker();
        $worker->addServer();
        $worker->addFunction("sendMail", "send");

        while ($worker->work()) {
            if ($worker->returnCode() != GEARMAN_SUCCESS) {
                throw new Exception('Falha em alguma parte do Gearman');
            }


        }
    }

    public function send(GearmanJob $job) {
        $CakeMail = unserialize($job->workload());

        if (!is_a($CakeMail, 'CakeMail')) {
            throw new Exception('Parâmetro inválido passado como Email para Worker');
        }

        $recipients = $CakeMail->to();

        foreach ($recipients as $email => $name) {
            if (is_numeric($email)) {
                $email = $name;
            } else {
                $email = array($email => $name);
            }

            $CakeMail->to($email);
            $CakeMail->config($this->mailConfig);

            // envia as mensagens usando CakeEmail
            if (!$CakeMail->send()) {
                CakeLog::write('assync_mail', serialize($CakeMail));

                $job->sendFail();

                return false;
            }
        }

        // notifica sucesso
        $job->sendComplete();

        return true;
    }
}
