<?php namespace SocialNorm\Meetup;

use SocialNorm\Exceptions\InvalidAuthorizationCodeException;
use SocialNorm\Providers\OAuth2Provider;

class MeetupProvider extends OAuth2Provider
{
    protected $authorizeUrl = "https://secure.meetup.com/oauth2/authorize";
    protected $accessTokenUrl = "https://secure.meetup.com/oauth2/access";
    protected $userDataUrl = "https://api.meetup.com/2/member/self?fields=email";
    protected $scope = [
        'basic',
    ];

    protected $headers = [
        'authorize' => [],
        'access_token' => [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ],
        'user_details' => [],
    ];

    protected function getAuthorizeUrl()
    {
        return $this->authorizeUrl;
    }

    protected function getAccessTokenBaseUrl()
    {
        return $this->accessTokenUrl;
    }

    protected function getUserDataUrl()
    {
        return $this->userDataUrl;
    }

    protected function requestAccessToken()
    {
        $token = parent::requestAccessToken();

        $this->headers['user_details'] = [
            'Authorization' => 'Bearer ' . $token
        ];

        return $token;
    }

    protected function buildUserDataUrl()
    {
        return $this->getUserDataUrl();
    }

    protected function parseTokenResponse($response)
    {
        return $this->parseJsonTokenResponse($response);
    }

    protected function parseUserDataResponse($response)
    {
        $data = json_decode($response, true);
        return $data;
    }

    protected function userId()
    {
        return $this->getProviderUserData('id');
    }

    protected function nickname()
    {
        return;
    }

    protected function fullName()
    {
        return $this->getProviderUserData('name');
    }

    protected function avatar()
    {
        return $this->getProviderUserData('photo')['thumb_link'];
    }

    protected function email()
    {
        return $this->getProviderUserData('email');
    }
}
