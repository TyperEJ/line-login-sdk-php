<?php

namespace EJLin\LINELogin;

class UserProfile
{
    /** @var string */
    private $userId;
    /** @var string */
    private $displayName;
    /** @var string */
    private $pictureUrl;
    /** @var string */
    private $email;
    /** @var string */
    private $statusMessage;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->userId = $data['userId'];
        $this->displayName = $data['displayName'] ?? null;
        $this->pictureUrl = $data['pictureUrl'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->statusMessage = $data['statusMessage'] ?? null;
    }

    /**
     * @param array $data
     * @return UserProfile
     */
    public static function parseToUserProfileObject(array $data)
    {
        return new UserProfile($data);
    }

    /**
     * @return mixed|string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return mixed|string|null
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $size
     * @return mixed|string|null
     */
    public function getPictureUrl(string $size = '')
    {
        return $size ? $this->pictureUrl . "/$size" : $this->pictureUrl;
    }

    /**
     * @return mixed|string|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed|string|null
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }
}