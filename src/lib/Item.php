<?php
/**
 * created: 2017
 *
 * @author    bchatard
 * @license   MIT
 */


class Item implements JsonSerializable
{
    /**
     * @var string
     */
    private $uid;
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $subtitle;
    /**
     * @var string
     */
    private $arg = '';
    /**
     * @var array
     */
    private $icon = [];
    /**
     * @var bool
     */
    private $valid = true;
    /**
     * @var string
     */
    private $autocomplete;
    /**
     * @var string
     */
    private $type = 'default';
    /**
     * @var array
     */
    private $mods;
    /**
     * @var array
     */
    private $text;
    /**
     * @var string
     */
    private $quicklookurl;
    /**
     * @var array
     */
    private $variables = [];

    public function validate()
    {
        // @todo ?
        return true;
    }

    /**
     * @param string $uid
     * @return Item
     */
    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * @param string $title
     * @return Item
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string $subtitle
     * @return Item
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * @param string $arg
     * @return Item
     */
    public function setArg($arg)
    {
        $this->arg = $arg;

        return $this;
    }

    /**
     * @param string        $path
     * @param string|string $type
     * @return Item
     */
    public function setIcon($path, $type = null)
    {
        if (strpos($path, '.icns') !== false) {
            $path = "/System/Library/CoreServices/CoreTypes.bundle/Contents/Resources/{$path}";
        }

        $this->icon = ['path' => $path];
        if ($type) {
            $this->icon['type'] = $type;
        }

        return $this;
    }

    /**
     * @param bool $valid
     * @return Item
     */
    public function setValid($valid)
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * @param string $autocomplete
     * @return Item
     */
    public function setAutocomplete($autocomplete)
    {
        $this->autocomplete = $autocomplete;

        return $this;
    }

    /**
     * @param string $type
     * @return Item
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param array $mods
     * @return Item
     */
    public function setMods($mods)
    {
        $this->mods = $mods;

        return $this;
    }

    /**
     * @param string|null $copy
     * @param string|null $largeType
     * @return Item
     */
    public function setText($copy = null, $largeType = null)
    {
        $this->text = [];
        if ($copy) {
            $this->text['copy'] = $copy;
        }
        if ($largeType) {
            $this->text['largetype'] = $largeType;
        }

        return $this;
    }

    /**
     * @param string $quicklookurl
     * @return Item
     */
    public function setQuicklookurl($quicklookurl)
    {
        $this->quicklookurl = $quicklookurl;

        return $this;
    }

    /**
     * $key will be prefixed with 'jb_i_' to avoid conflicts
     *
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setVariables($key, $value)
    {
        $this->variables['jb_i_' . $key] = $value;

        return $this;
    }

    public function __toString()
    {
        return json_encode($this);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
