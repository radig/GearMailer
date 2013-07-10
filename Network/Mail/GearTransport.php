<?php
App::uses('CakeSocket', 'Network');

class GearTransport extends AbstractTransport {

	public function send(CakeEmail $email) {
		$state = serialize($email);

		$client = new GearmanClient();
		$client->addServer();
		$client->doBackground('sendMail', $state);

		if ($gmclient->returnCode() != GEARMAN_SUCCESS) {
			CakeLog::write('assync_job', $state);
			throw new CakeException('Não foi possível repassar dados ao Gearman');
		}

		return $this->_content;
	}
}
