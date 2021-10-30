<?php

namespace EJLin\LINELogin;

class IdTokenPayload
{
    /** @var string URL used to generate the ID token. */
    public $iss;
    /** @var string User ID for which the ID token was generated. */
    public $sub;
    /** @var string Channel ID */
    public $aud;
    /** @var integer The expiry date of the ID token in UNIX time. */
    public $exp;
    /** @var integer Time when the ID token was generated in UNIX time. */
    public $iat;
    /** @var integer Time the user was authenticated in UNIX time. */
    public $authTime;
    /** @var string The nonce value specified in the authorization URL. */
    public $nonce;
    /** @var string[] A list of authentication methods used by the user. */
    public $amr;
    /** @var string User's display name. */
    public $name;
    /** @var string User's profile image URL. */
    public $picture;
    /** @var string User's email address. */
    public $email;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->iss = $data['iss'];
        $this->sub = $data['sub'];
        $this->aud = $data['aud'];
        $this->exp = $data['exp'];
        $this->iat = $data['iat'];
        $this->authTime = $data['auth_time'] ?? null;
        $this->nonce = $data['nonce'] ?? null;
        $this->amr = $data['amr'] ?? [];
        $this->name = $data['name'] ?? null;
        $this->picture = $data['picture'] ?? null;
        $this->email = $data['email'] ?? null;
    }

    /**
     * @param array $data
     * @return IdTokenPayload
     */
    public static function parseToIdTokenPayloadObject(array $data)
    {
        return new IdTokenPayload($data);
    }

    /**
     * @return UserProfile
     */
    public function toUserProfile()
    {
        return new UserProfile([
            'userId' => $this->getUserId(),
            'displayName' => $this->getUserDisplayName(),
            'pictureUrl' => $this->getUserPictureUrl(),
            'email' => $this->getUserEmail(),
        ]);
    }

    /**
     * @return mixed|string
     */
    public function getUserId()
    {
        return $this->sub;
    }

    /**
     * @return mixed|string|null
     */
    public function getUserDisplayName()
    {
        return $this->name;
    }

    /**
     * @return mixed|string|null
     */
    public function getUserPictureUrl()
    {
        return $this->picture;
    }

    /**
     * @return mixed|string|null
     */
    public function getUserEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed|string|null
     */
    public function getNonce()
    {
        return $this->nonce;
    }
}