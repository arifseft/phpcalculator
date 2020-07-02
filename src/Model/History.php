<?php

namespace Jakmall\Recruitment\Calculator\Model;

/**
 * @Entity @Table(name="histories")
 */
class History
{
    /** @Id @Column(type="integer") @GeneratedValue */
    protected $id;
    /** @Column(type="string") */
    protected $command;
    /** @Column(type="string") */
    protected $description;
    /** @Column(type="integer") */
    protected $result;
    /** @Column(type="string") */
    protected $output;
    /** @Column(type="datetime") */
    protected $time;

    public function getId()
    {
        return $this->id;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function setCommand($command)
    {
        $this->command = $command;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result)
    {
        $this->result = $result;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function setOutput($output)
    {
        $this->output = $output;
    }

    public function getTime()
    {
        $time = $this->time;
        $time = $time->format('Y-m-d H:i:s');
        return $time;
    }

    public function setTime($time)
    {
        $this->time = $time;
    }
}
