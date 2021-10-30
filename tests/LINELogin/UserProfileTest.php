<?php

namespace EJLin\Tests\LINELogin;

use EJLin\LINELogin\UserProfile;
use PHPUnit\Framework\TestCase;

class UserProfileTest extends TestCase
{
    public function testParseMapping()
    {
        $userProfile = UserProfile::parseToUserProfileObject([
            'userId' => 'U4af4980629...',
            'displayName' => 'Brown',
            'pictureUrl' => 'https://profile.line-scdn.net/abcdefghijklmn',
            'statusMessage' => 'Hello, LINE!',
            'email' => 'taro.line@example.com',
        ]);

        $this->assertEquals('U4af4980629...', $userProfile->getUserId());
        $this->assertEquals('Brown', $userProfile->getDisplayName());
        $this->assertEquals('https://profile.line-scdn.net/abcdefghijklmn', $userProfile->getPictureUrl());
        $this->assertEquals('Hello, LINE!', $userProfile->getStatusMessage());
        $this->assertEquals('taro.line@example.com', $userProfile->getEmail());
    }

    public function testParseMappingWithoutOptionalValue()
    {
        $userProfile = UserProfile::parseToUserProfileObject([
            'userId' => 'U4af4980629...',
        ]);

        $this->assertEquals('U4af4980629...', $userProfile->getUserId());
        $this->assertEquals(null, $userProfile->getDisplayName());
        $this->assertEquals(null, $userProfile->getPictureUrl());
        $this->assertEquals(null, $userProfile->getStatusMessage());
        $this->assertEquals(null, $userProfile->getEmail());
    }

    public function testGetPictureWithSize()
    {
        $userProfile = UserProfile::parseToUserProfileObject([
            'userId' => 'U4af4980629...',
            'pictureUrl' => 'https://profile.line-scdn.net/abcdefghijklmn',
        ]);

        $this->assertEquals('https://profile.line-scdn.net/abcdefghijklmn/large', $userProfile->getPictureUrl('large'));
        $this->assertEquals('https://profile.line-scdn.net/abcdefghijklmn/small', $userProfile->getPictureUrl('small'));
    }
}