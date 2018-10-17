<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Log\Log;
use Cake\I18n\Time;

/**
 * Notifications Controller
 *
 * @property \App\Model\Table\NotificationsTable $Notifications
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class NotificationsController extends AppController
{
    /**
	 * @return \Cake\Http\Response|null
	 */
	public function index()
    {
        Log::write("debug", 'inside index notifications controller');
    }
}
