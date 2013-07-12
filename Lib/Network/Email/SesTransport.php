<?php
/**
 * GearTransport and Gearman Client
 */
require APP . '/vendor/autoload.php';

App::uses('AbstractTransport', 'Network/Email');
use Aws\Ses\SesClient;

class SesTransport extends AbstractTransport {

    public function send(CakeEmail $cakeEmail) {
        $config['key'] = $this->_config['username'];
        $config['secret'] = $this->_config['password'];
        $config['region'] = $this->_config['region'];

        $sesEmail = SesClient::factory($config);

        if (!empty($this->_config['dkim'])) {
            $sesEmail->setIdentityDkimEnabled( ['Identity' => $this->_config['dkim'], 'DkimEnabled' => true] );
        }

        $headers = $cakeEmail->getHeaders(['from', 'sender', 'replyTo', 'readReceipt', 'returnPath', 'to', 'cc', 'subject']);
        $headers = $this->_headersToString($headers);

        $lines = $cakeEmail->message();
        $messages = [];
        foreach ($lines as $line) {
            if ((!empty($line)) && ($line[0] === '.')) {
                $messages[] = '.' . $line;
            } else {
                $messages[] = $line;
            }
        }

        $message = implode("\r\n", $messages);
        $raw_message = $headers . "\r\n\r\n" . $message . "\r\n\r\n\r\n";

        try {
            $sesEmail->sendRawEmail(['RawMessage' => ['Data' => base64_encode($raw_message)]]);
        } catch (MessageRejectedException $e) {
            CakeLog::write('assync_mail', print_r($e->getMessage(),true));
            return false;
        }

        return ['headers' => $headers, 'message' => $message];
    }
}
