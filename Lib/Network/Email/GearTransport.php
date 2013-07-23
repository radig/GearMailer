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
 * Send mail
 *
 * @param CakeEmail $email CakeEmail
 * @return array
 */
    public function send(CakeEmail $email) {
        $state = serialize($email);

        $client = new GearmanClient();
        $client->addServer();
        $client->doBackground('sendMail', $state);

        if ($client->returnCode() != GEARMAN_SUCCESS) {
            CakeLog::write('assync_job', "Erro: ".$state);
            throw new CakeException('Não foi possível repassar dados ao Gearman');
        }

        return true;
    }
}
