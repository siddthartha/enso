<?php declare(strict_types=1);

namespace Enso;

/**
 * Класс Single
 *
 * NOTE: Magic constructor <b>YouClass</b>::<b>__init</b>() can be declared instead fo regular one!
 *
 * $object = <b>new</b> SingleClass();<br/>
 * $object->field = <i>3.141</i>;
 *
 * echo SingleClass::getInstance()->field; // output 3.141
 *
 *
 * @author Anton Sadovnikov <sadovnikoff@gmail.com>
 */
trait Single
{

    /**
     * Special constructor for singletons
     * Runs once before storing object
     *
     */
    abstract function __init();

    /**
     * Конструктор
     *
     * @return Single
     */
    final function __construct()
    {
        if (!Singles::hasSingle(get_class($this)))
        {
            $this->__init();
            Singles::storeSingle($this);
        }
        else
        {
            $object = Singles::getSingle(get_class($this));
            $this->__set_object_data($object->__get_object_data());
        }
    }

    /**
     * Custom __clone() set data
     */
    private function __set_object_data(string $data): void
    {

        $data = unserialize($data);
        foreach ($data as $name => $value)
        {
            $this->$name = $value;
        }

        $rawdata = (array) $data;
        foreach ($rawdata as $name => $value)
        {
            $private_name = mb_ereg_replace("\0" . get_class($this) . "\0", "", $name);
            if ($private_name != $name)
            {
                $this->$private_name = $value;
            }
        }
    }

    /**
     * Custom __clone() get data
     */
    public function __get_object_data(): string
    {
        return serialize($this);
    }

    /**
     * Custom clone
     *
     * @return Object
     */
    public function __clone()
    {
        return Singles::getSingle(get_class($this));
    }

    /**
     * Get instance of children class
     *
     * @return string
     */
    public static function getInstance(): self
    {
        return Singles::getSingle(__CLASS__);
    }
}