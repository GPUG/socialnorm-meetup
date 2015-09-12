<?php

use Mockery as M;
use SocialNorm\Meetup\MeetupProvider;
use SocialNorm\Request;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Client as HttpClient;

class MeetupProviderTest extends TestCase
{
    private function getStubbedHttpClient($fixtures = [])
    {
        $mock = new MockHandler($this->createResponses($fixtures));
        $handler = HandlerStack::create($mock);
        return new HttpClient(['handler' => $handler]);
    }

    private function createResponses($fixtures)
    {
        $responses = [];
        foreach ($fixtures as $fixture) {
            $response = require $fixture;
            $responses[] = new Response($response['status'], $response['headers'], $response['body']);
        }

        return $responses;
    }

    /** @test */
    public function it_can_retrieve_a_normalized_user()
    {
        $client = $this->getStubbedHttpClient([
            __DIR__ . '/_fixtures/meetup_accesstoken.php',
            __DIR__ . '/_fixtures/meetup_user.php',
        ]);

        $provider = new MeetupProvider([
            'client_id' => 'abcdefgh',
            'client_secret' => '12345678',
            'redirect_uri' => 'http://example.com/login',
        ], $client, new Request(['code' => 'abc123']));

        $user = $provider->getUser();

        $this->assertEquals('20564771', $user->id);
        $this->assertNull($user->nickname);
        $this->assertEquals('Colin DeCarlo', $user->full_name);
        $this->assertNull($user->email);
        $this->assertEquals('http://photos4.meetupstatic.com/photos/member/1/6/1/3/thumb_19085651.jpeg', $user->avatar);
        $this->assertEquals('e2ebab1751e209daa050b3e2246b7ea9', $user->access_token);
    }

    /**
     * @test
     * @expectedException SocialNorm\Exceptions\ApplicationRejectedException
     */
    public function it_fails_to_retrieve_a_user_when_the_authorization_code_is_omitted()
    {
        $client = $this->getStubbedHttpClient([
            __DIR__ . '/_fixtures/meetup_accesstoken.php',
            __DIR__ . '/_fixtures/meetup_user.php',
        ]);

        $provider = new MeetupProvider([
            'client_id' => 'abcdefgh',
            'client_secret' => '12345678',
            'redirect_uri' => 'http://example.com/login',
        ], $client, new Request([]));

        $user = $provider->getUser();
    }
}
