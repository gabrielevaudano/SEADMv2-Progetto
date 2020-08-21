<?php


class UserInfo extends User
{
    private $rangeAge;
    private $gender;
    private $techLevel;
    private $privacy;

    /**
     * UserInfo constructor.
     * @param $email
     * @param $rangeAge
     * @param $gender
     * @param $techLevel
     * @param $privacy
     */
    public function __construct($email, $password, $group, $rangeAge, $gender, $techLevel, $privacy)
    {
        parent::__construct($email, $password, $group);
        $this->rangeAge = $rangeAge;
        $this->gender = $gender;
        $this->techLevel = $techLevel;
        $this->privacy = $privacy;
    }


    /**
     * @return mixed
     */
    public function getRangeAge()
    {
        return $this->rangeAge;
    }

    /**
     * @param mixed $rangeAge
     * @return UserInfo
     */
    public function setRangeAge($rangeAge)
    {
        $this->rangeAge = $rangeAge;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param mixed $gender
     * @return UserInfo
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTechLevel()
    {
        return $this->techLevel;
    }

    /**
     * @param mixed $techLevel
     * @return UserInfo
     */
    public function setTechLevel($techLevel)
    {
        $this->techLevel = $techLevel;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrivacy()
    {
        return $this->privacy;
    }

    /**
     * @param mixed $privacy
     * @return UserInfo
     */
    public function setPrivacy($privacy)
    {
        $this->privacy = $privacy;
        return $this;
    }

    public function setGroup($group) {
        parent::setGroup($group);
    }

    public function getGroup()
    {
        return parent::getGroup();
    }


}