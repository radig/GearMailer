<?php
App::uses('AbstractTransport', 'Network/Email');
/**
 * GearTransport and Gearman Client
 *
 * @package         radig.GearMailer.Lib.Network.Email
 * @copyright       Radig Soluções em TI
 * @author          Radig Dev Team - suporte@radig.com.br
 * @license         MIT
 * @link            http://radig.com.br
 */
class GearTransport extends AbstractTransport {

/**
 * Enqueue message into Gearman for action 'sendMail'
 *
 * @param CakeEmail $email CakeEmail
 * @return boolean
 */
    public function send(CakeEmail $email) {
        $headers = $email->getHeaders(['from', 'sender', 'replyTo', 'readReceipt', 'returnPath', 'to', 'bcc', 'cc', 'subject']);
        $headers = $this->_headersToString($headers);

        $lines = $email->message();
        $messages = [];
        foreach ($lines as $line) {
            if ((!empty($line)) && ($line[0] === '.')) {
                $messages[] = '.' . $line;
                continue;
            }
            
            $messages[] = $line;
        }

        $message = implode("\r\n", $messages);
        $rawMessage = $headers . "\r\n\r\n" . $message . "\r\n\r\n\r\n";

        $client = new GearmanClient();
        $client->addServer();

        $emailData = [
            'credentials' => Configure::read('GearMailer.credentials'),
            'message' => $rawMessage
        ];

        $client->doBackground('sendMail', serialize($emailData));

        if ($client->returnCode() != GEARMAN_SUCCESS) {
            CakeLog::write('warning', $rawMessage, 'async_mail');
            return false;
        }

        return true;
    }
}
