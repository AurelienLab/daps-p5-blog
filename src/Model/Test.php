<?php

namespace App\Model;

class Test
{

    const TABLE = 'table_test';

    /**
     * @var string
     */
    private string $test;

    /**
     * @var string
     */
    private string $url;


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
     * @param string $url
     *
     * @return Test
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
        return $this;
    }
}
