<?php


class User
{
    private $email;
    private $password;
    private $group;

    public function __construct($email, $password, $group)
    {
        $this->email = $email;
        $this->password = $password;
        $this->group = $group;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function setGroup($group)
    {
        $this->group = $group;
    }


}