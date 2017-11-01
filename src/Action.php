<?php
/**
 * created: 2017
 *
 * @author    bchatard
 * @license   MIT
 */

require_once 'actions/Cache.php';

class Action
{

    private static $allowedActions = [
        'cache:clean' => [Cache::class, 'clean'],
    ];

    /**
     * @var string
     */
    private $action;

    public function __construct($action)
    {
        $this->action = $action;
    }

    /**
     * @param null $query
     * @return string
     */
    public function execute($query = null)
    {
        if (array_key_exists($this->action, static::$allowedActions)) {
            $function = (array)static::$allowedActions[$this->action];

            return call_user_func($function, $query);
        }

        return "unknown action {$this->action}";
    }

}
