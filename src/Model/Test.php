<?php

namespace App\Model;

class Test
{
    const TABLE = 'table_test';

    // private $id;
    private $test;
    private $url;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * @param mixed $test
     *
     * @return Test
     */
    public function setTest($test)
    {
        $this->test = $test;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     *
     * @return Test
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }
}
