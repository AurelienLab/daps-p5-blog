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
}
