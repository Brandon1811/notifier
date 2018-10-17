<?php
namespace Bakkerij\Notifier\Controller;



use App\Controller\AppController;
use Cake\Log\Log;
use Cake\I18n\Time;

/**
 * Notifications Controller
 *
 * @property \App\Model\Table\NotificationsTable $Notifications
 *
 * @method \App\Model\Entity\Notification[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class NotificationsController extends AppController
{
    /**
	 * @var array
	 */
	public $components = [
		'Bakkerij/Notifier.Notifier',
	];

    /**
	 * @return \Cake\Http\Response|null
	 */
	public function index()
    {
        $notifications = $this->Notifier->getNotifications();
        $this->set(compact('notifications'));
    }

    /**
     * @return \Cake\Http\Response|null
     */
    public function read($id = null)
    {
        $this->request->allowMethod(['post', 'put']);
        $unreadCountBefore = $this->Notifier->countNotifications(null, true); // original unread count
        $plural = (isset($id)) ? 'notification':'notifications';
        if ($this->request->is(['post', 'put'])) {
            $this->Notifier->markAsRead($id);
        }
        if ($unreadCountBefore === $this->Notifier->countNotifications(null, true)) {
            $this->Flash->error('Failed to mark ' . $plural . 'as read. Please, try again.');
        } else {
            $this->Flash->success('Marked ' . $plural . ' as read.');
        }
    }
}
