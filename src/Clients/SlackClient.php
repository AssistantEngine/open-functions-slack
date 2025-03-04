<?php

namespace AssistantEngine\OpenFunctions\Slack\Clients;

use GuzzleHttp\Client;

class SlackClient
{
    /**
     * The base URL for Slack API requests.
     *
     * @var string
     */
    protected $baseUrl = 'https://slack.com/api';

    /**
     * The Guzzle HTTP client.
     *
     * @var Client
     */
    protected $httpClient;

    /**
     * The Slack Bot Token.
     *
     * @var string
     */
    protected $botToken;

    /**
     * The Slack Team ID.
     *
     * @var string
     */
    protected $teamId;

    /**
     * Constructor.
     *
     * @param string $botToken
     * @param string $teamId
     */
    public function __construct(string $botToken, string $teamId)
    {
        $this->botToken = $botToken;
        $this->teamId = $teamId;
        $this->httpClient = new Client();
    }

    /**
     * List public channels with optional pagination.
     *
     * @param int $limit
     * @param string|null $cursor
     * @return array
     */
    public function getChannels(int $limit = 100, ?string $cursor = null): array
    {
        $params = [
            'types' => 'public_channel',
            'exclude_archived' => 'true',
            'limit' => min($limit, 200),
            'team_id' => $this->teamId,
        ];
        if ($cursor) {
            $params['cursor'] = $cursor;
        }

        $response = $this->httpClient->get($this->baseUrl . '/conversations.list', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->botToken,
                'Content-Type'  => 'application/json',
            ],
            'query' => $params,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Post a message to a channel.
     *
     * @param string $channelId
     * @param string $text
     * @return array
     */
    public function postMessage(string $channelId, string $text): array
    {
        $response = $this->httpClient->post($this->baseUrl . '/chat.postMessage', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->botToken,
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'channel' => $channelId,
                'text'    => $text,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Reply to a message thread.
     *
     * @param string $channelId
     * @param string $threadTs
     * @param string $text
     * @return array
     */
    public function replyToThread(string $channelId, string $threadTs, string $text): array
    {
        $response = $this->httpClient->post($this->baseUrl . '/chat.postMessage', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->botToken,
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'channel'   => $channelId,
                'thread_ts' => $threadTs,
                'text'      => $text,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Add a reaction emoji to a message.
     *
     * @param string $channelId
     * @param string $timestamp
     * @param string $reaction
     * @return array
     */
    public function addReaction(string $channelId, string $timestamp, string $reaction): array
    {
        $response = $this->httpClient->post($this->baseUrl . '/reactions.add', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->botToken,
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'channel'   => $channelId,
                'timestamp' => $timestamp,
                'name'      => $reaction,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Get recent messages from a channel.
     *
     * @param string $channelId
     * @param int $limit
     * @return array
     */
    public function getChannelHistory(string $channelId, int $limit = 10): array
    {
        $params = [
            'channel' => $channelId,
            'limit'   => $limit,
        ];

        $response = $this->httpClient->get($this->baseUrl . '/conversations.history', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->botToken,
                'Content-Type'  => 'application/json',
            ],
            'query' => $params,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Get all replies in a message thread.
     *
     * @param string $channelId
     * @param string $threadTs
     * @return array
     */
    public function getThreadReplies(string $channelId, string $threadTs): array
    {
        $params = [
            'channel' => $channelId,
            'ts'      => $threadTs,
        ];

        $response = $this->httpClient->get($this->baseUrl . '/conversations.replies', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->botToken,
                'Content-Type'  => 'application/json',
            ],
            'query' => $params,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Get a list of users in the workspace.
     *
     * @param int $limit
     * @param string|null $cursor
     * @return array
     */
    public function getUsers(int $limit = 100, ?string $cursor = null): array
    {
        $params = [
            'limit'   => min($limit, 200),
            'team_id' => $this->teamId,
        ];
        if ($cursor) {
            $params['cursor'] = $cursor;
        }

        $response = $this->httpClient->get($this->baseUrl . '/users.list', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->botToken,
                'Content-Type'  => 'application/json',
            ],
            'query' => $params,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Get detailed profile information for a user.
     *
     * @param string $userId
     * @return array
     */
    public function getUserProfile(string $userId): array
    {
        $params = [
            'user'           => $userId,
            'include_labels' => 'true',
        ];

        $response = $this->httpClient->get($this->baseUrl . '/users.profile.get', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->botToken,
                'Content-Type'  => 'application/json',
            ],
            'query' => $params,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}