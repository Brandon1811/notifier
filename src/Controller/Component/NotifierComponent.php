<?php
/**
 * Bakkerij (https://github.com/bakkerij)
 * Copyright (c) https://github.com/bakkerij
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) https://github.com/bakkerij
 * @link          https://github.com/bakkerij Bakkerij Project
 * @since         1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Bakkerij\Notifier\Controller\Component;

use Bakkerij\Notifier\Utility\NotificationManager;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;
/**
 * Notifier component
 */
class NotifierComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'UsersModel' => 'Users'
    ];

    /**
     * The controller.
     *
     * @var \Cake\Controller\Controller
     */
    private $Controller = null;

    /**
     * initialize
     *
     * @param array $config Config.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->Controller = $this->_registry->getController();
    }

    /**
     * setController
     *
     * Setter for the Controller property.
     *
     * @param \Cake\Controller\Controller $controller Controller.
     * @return void
     */
    public function setController($controller)
    {
        $this->Controller = $controller;
    }

    /**
     * getNotifications
     *
     * Returns a list of notifications.
     *
     * ### Examples
     * ```
     *  // if the user is logged in, this is the way to get all notifications
     *  $this->Notifier->getNotifications();
     *
     *  // for a specific user, use the first parameter for the user_id
     *  $this->Notifier->getNotifications(1);
     *
     *  // default all notifications are returned. Use the second parameter to define read / unread:
     *
     *  // get all unread notifications
     *  $this->Notifier->getNotifications(1, true);
     *
     *  // get all read notifications
     *  $this->Notifier->getNotifications(1, false);
     * ```
     * @param int|null $userId Id of the user.
     * @param bool|null $state The state of notifications: `true` for unread, `false` for read, `null` for all.
     * @return array
     */
    public function getNotifications($userId = null, $state = null)
    {
        $query = $this->getNotificationsQuery($userId, $state);
        return $query->toArray();
    }

    /**
     * countNotifications
     *
     * Returns a number of notifications.
     *
     * ### Examples
     * ```
     *  // if the user is logged in, this is the way to count all notifications
     *  $this->Notifier->countNotifications();
     *
     *  // for a specific user, use the first parameter for the user_id
     *  $this->Notifier->countNotifications(1);
     *
     *  // default all notifications are counted. Use the second parameter to define read / unread:
     *
     *  // count all unread notifications
     *  $this->Notifier->countNotifications(1, true);
     *
     *  // count all read notifications
     *  $this->Notifier->countNotifications(1, false);
     * ```
     * @param int|null $userId Id of the user.
     * @param bool|null $state The state of notifications: `true` for unread, `false` for read, `null` for all.
     * @return int
     */
    public function countNotifications($userId = null, $state = null)
    {
        $query = $this->getNotificationsQuery($userId, $state);

        return $query->count();
    }

    /**
     * markAsRead
     *
     * Used to mark a notification as read.
     * If no notificationId is given, all notifications of the chosen user will be marked as read.
     *
     * @param int $notificationId Id of the notification.
     * @param int|null $user Id of the user. Else the id of the session will be taken.
     * @return void
     */
    public function markAsRead($notificationId = null, $user = null)
    {
        if (!$user) {
            $user = $this->Controller->Auth->user('id');
        }

        $model = TableRegistry::get('Bakkerij/Notifier.Notifications');

        if (!$notificationId) {
            $query = $model->find('all')->where([
                'user_id' => $user,
                'state' => 1
            ]);
        } else {
            $query = $model->find('all')->where([
                'user_id' => $user,
                'id' => $notificationId

            ]);
        }

        foreach ($query as $item) {
            $item->set('state', 0);
            $model->save($item);
        }
    }

    /**
     * markAsDeleted
     *
     * Used to mark a notification as deleted.
     * If no notificationId is given, all notifications of the chosen user will be marked as read.
     *
     * @param int $notificationId Id of the notification.
     * @param int|null $user Id of the user. Else the id of the session will be taken.
     * @return void
     */
    public function markAsDeleted($notificationId = null, $user = null)
    {
        if (!$user) {
            $user = $this->Controller->Auth->user('id');
        }

        $model = TableRegistry::get('Bakkerij/Notifier.Notifications');

        if (!$notificationId) {
            $query = $model->find('all')->where([
                'user_id' => $user,
                'state' => 1
            ]);
        } else {
            $query = $model->find('all')->where([
                'user_id' => $user,
                'id' => $notificationId

            ]);
        }

        foreach ($query as $item) {
            $item->set('deleted_at', Time::now());
            $model->save($item);
        }
    }

    /**
     * notify
     *
     * Sends notifications to specific users.
     * The first parameter `$data` is an array with multiple options.
     *
     * ### Options
     * - `users` - An array or int with id's of users who will receive a notification.
     * - `roles` - An array or int with id's of roles which all users ill receive a notification.
     * - `template` - The template wich will be used.
     * - `vars` - The variables used in the template.
     *
     * ### Example
     * ```
     *  NotificationManager::instance()->notify([
     *      'users' => 1,
     *      'template' => 'newOrder',
     *      'vars' => [
     *          'receiver' => $receiver->name
     *          'total' => $order->total
     *      ],
     *  ]);
     * ```
     *
     * @param array $data Data with options.
     * @return string
     */
    public function notify($data)
    {
        return NotificationManager::instance()->notify($data);
    }

    /**
     * getNotifications
     *
     * Returns a list of notifications.
     *
     * ### Examples
     * ```
     *  // if the user is logged in, this is the way to get all notifications
     *  $this->Notifier->getNotifications();
     *
     *  // for a specific user, use the first parameter for the user_id
     *  $this->Notifier->getNotifications(1);
     *
     *  // default all notifications are returned. Use the second parameter to define read / unread:
     *
     *  // get all unread notifications
     *  $this->Notifier->getNotifications(1, true);
     *
     *  // get all read notifications
     *  $this->Notifier->getNotifications(1, false);
     * ```
     * @param int|null $userId Id of the user.
     * @param bool|null $state The state of notifications: `true` for unread, `false` for read, `null` for all.
     * @return Query
     */
    public function getNotificationsQuery($userId = null, $state = null)
    {
        if (!$userId) {
            $userId = $this->Controller->Auth->user('id');
        }

        $model = TableRegistry::get('Bakkerij/Notifier.Notifications');

        $query = $model->find()
            ->where(['Notifications.user_id' => $userId])
            ->where(['Notifications.deleted_at IS' => null]);

        if (isset($state)) {
            $query->where(['Notifications.state' => (int)$state]);
        }

        return $query;
    }
}
