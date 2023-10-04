<?php

namespace App\Model;

class Test
{

    const TABLE = 'table_test';

    private int $id;

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

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Test
     */
    public function setId(int $id): Test
    {
        $this->id = $id;
        return $this;
    }
    
}
