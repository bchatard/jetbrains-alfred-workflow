<?php
/**
 * created: 2017
 *
 * @author    bchatard
 * @license   MIT
 */


class Result
{

    /**
     * @var Item[]
     */
    private $items = [];
    /**
     * @var array (key => value)
     */
    private $variables = [];
    /**
     * @var bool
     */
    private $debug = false;

    /**
     * @param Item $item
     * @return $this
     */
    public function addItem(Item $item)
    {
        if ($item->validate()) {
            $this->items[] = $item;
        }

        return $this;
    }

    /**
     * $key will be prefixed with 'jb_' to avoid conflicts
     *
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function addVariable($key, $value)
    {
        $this->variables['jb_' . $key] = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasItems()
    {
        return (bool)count($this->items);
    }

    public function enableDebug()
    {
        $this->debug = true;
    }

    /**
     * Return json encoded result (pretty printed if debug is enabled)
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode(
            ['items' => $this->items, 'variables' => $this->variables],
            $this->debug ? JSON_PRETTY_PRINT : 0
        );
    }

}
