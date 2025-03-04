<?php

namespace AssistantEngine\OpenFunctions\Slack;

use AssistantEngine\OpenFunctions\Core\Contracts\AbstractOpenFunction;
use AssistantEngine\OpenFunctions\Core\Helpers\FunctionDefinition;
use AssistantEngine\OpenFunctions\Core\Helpers\Parameter;
use AssistantEngine\OpenFunctions\Core\Models\Responses\TextResponseItem;
use AssistantEngine\OpenFunctions\Slack\Clients\SlackClient;

class SlackOpenFunction extends AbstractOpenFunction
{
    private SlackClient $slackClient;

    public function __construct(string $teamId, string $botToken)
    {
        $this->slackClient = new SlackClient($botToken, $teamId);
    }

    /**
     * List public channels.
     *
     * @param int $limit
     * @param string|null $cursor
     * @return TextResponseItem
     */
    public function listChannels(int $limit = 100, ?string $cursor = null)
    {
        $result = $this->slackClient->getChannels($limit, $cursor);
        return new TextResponseItem(json_encode($result));
    }

    /**
     * Post a message to a channel.
     *
     * @param string $channelId
     * @param string $text
     * @return TextResponseItem
     */
    public function postMessage(string $channelId, string $text)
    {
        $result = $this->slackClient->postMessage($channelId, $text);
        return new TextResponseItem(json_encode($result));
    }

    /**
     * Reply to a message thread.
     *
     * @param string $channelId
     * @param string $threadTs
     * @param string $text
     * @return TextResponseItem
     */
    public function replyToThread(string $channelId, string $threadTs, string $text)
    {
        $result = $this->slackClient->replyToThread($channelId, $threadTs, $text);
        return new TextResponseItem(json_encode($result));
    }

    /**
     * Add a reaction emoji to a message.
     *
     * @param string $channelId
     * @param string $timestamp
     * @param string $reaction
     * @return TextResponseItem
     */
    public function addReaction(string $channelId, string $timestamp, string $reaction)
    {
        $result = $this->slackClient->addReaction($channelId, $timestamp, $reaction);
        return new TextResponseItem(json_encode($result));
    }

    /**
     * Get recent messages from a channel.
     *
     * @param string $channelId
     * @param int $limit
     * @return TextResponseItem
     */
    public function getChannelHistory(string $channelId, int $limit = 10)
    {
        $result = $this->slackClient->getChannelHistory($channelId, $limit);
        return new TextResponseItem(json_encode($result));
    }

    /**
     * Get all replies in a message thread.
     *
     * @param string $channelId
     * @param string $threadTs
     * @return TextResponseItem
     */
    public function getThreadReplies(string $channelId, string $threadTs)
    {
        $result = $this->slackClient->getThreadReplies($channelId, $threadTs);
        return new TextResponseItem(json_encode($result));
    }

    /**
     * Get a list of users in the workspace.
     *
     * @param int $limit
     * @param string|null $cursor
     * @return TextResponseItem
     */
    public function getUsers(int $limit = 100, ?string $cursor = null)
    {
        $result = $this->slackClient->getUsers($limit, $cursor);
        return new TextResponseItem(json_encode($result));
    }

    /**
     * Get detailed profile information for a user.
     *
     * @param string $userId
     * @return TextResponseItem
     */
    public function getUserProfile(string $userId)
    {
        $result = $this->slackClient->getUserProfile($userId);
        return new TextResponseItem(json_encode($result));
    }

    /**
     * Generate function definitions for the Slack open function.
     *
     * @return array
     */
    public function generateFunctionDefinitions(): array
    {
        $definitions = [];

        // listChannels
        $defListChannels = new FunctionDefinition(
            'listChannels',
            'List public channels in the workspace with pagination.'
        );
        $defListChannels->addParameter(
            Parameter::number('limit')
                ->description('Maximum number of channels to return (default 100, max 200)')
                ->nullable()
                ->required()
        );
        $defListChannels->addParameter(
            Parameter::string('cursor')
                ->description('Pagination cursor for next page of results')
                ->nullable()
                ->required()
        );
        $definitions[] = $defListChannels->createFunctionDescription();

        // postMessage
        $defPostMessage = new FunctionDefinition(
            'postMessage',
            'Post a new message to a Slack channel.'
        );
        $defPostMessage->addParameter(
            Parameter::string('channelId')
                ->description('The ID of the channel to post to')
                ->required()
        );
        $defPostMessage->addParameter(
            Parameter::string('text')
                ->description('The message text to post')
                ->required()
        );
        $definitions[] = $defPostMessage->createFunctionDescription();

        // replyToThread
        $defReplyToThread = new FunctionDefinition(
            'replyToThread',
            'Reply to a specific message thread in Slack.'
        );
        $defReplyToThread->addParameter(
            Parameter::string('channelId')
                ->description('The ID of the channel containing the thread')
                ->required()
        );
        $defReplyToThread->addParameter(
            Parameter::string('threadTs')
                ->description('The timestamp of the parent message')
                ->required()
        );
        $defReplyToThread->addParameter(
            Parameter::string('text')
                ->description('The reply text')
                ->required()
        );
        $definitions[] = $defReplyToThread->createFunctionDescription();

        // addReaction
        $defAddReaction = new FunctionDefinition(
            'addReaction',
            'Add a reaction emoji to a Slack message.'
        );
        $defAddReaction->addParameter(
            Parameter::string('channelId')
                ->description('The ID of the channel containing the message')
                ->required()
        );
        $defAddReaction->addParameter(
            Parameter::string('timestamp')
                ->description('The timestamp of the message to react to')
                ->required()
        );
        $defAddReaction->addParameter(
            Parameter::string('reaction')
                ->description('The name of the emoji reaction (without colons)')
                ->required()
        );
        $definitions[] = $defAddReaction->createFunctionDescription();

        // getChannelHistory
        $defGetChannelHistory = new FunctionDefinition(
            'getChannelHistory',
            'Get recent messages from a Slack channel.'
        );
        $defGetChannelHistory->addParameter(
            Parameter::string('channelId')
                ->description('The ID of the channel')
                ->required()
        );
        $defGetChannelHistory->addParameter(
            Parameter::number('limit')
                ->description('Number of messages to retrieve (default 10)')
                ->nullable()
                ->required()
        );
        $definitions[] = $defGetChannelHistory->createFunctionDescription();

        // getThreadReplies
        $defGetThreadReplies = new FunctionDefinition(
            'getThreadReplies',
            'Get all replies in a Slack message thread.'
        );
        $defGetThreadReplies->addParameter(
            Parameter::string('channelId')
                ->description('The ID of the channel containing the thread')
                ->required()
        );
        $defGetThreadReplies->addParameter(
            Parameter::string('threadTs')
                ->description('The timestamp of the parent message')
                ->required()
        );
        $definitions[] = $defGetThreadReplies->createFunctionDescription();

        // getUsers
        $defGetUsers = new FunctionDefinition(
            'getUsers',
            'Get a list of users in the Slack workspace.'
        );
        $defGetUsers->addParameter(
            Parameter::number('limit')
                ->description('Maximum number of users to return (default 100, max 200)')
                ->required()
                ->nullable()
        );
        $defGetUsers->addParameter(
            Parameter::string('cursor')
                ->description('Pagination cursor for next page of results')
                ->required()
                ->nullable()
        );
        $definitions[] = $defGetUsers->createFunctionDescription();

        // getUserProfile
        $defGetUserProfile = new FunctionDefinition(
            'getUserProfile',
            'Get detailed profile information for a specific Slack user.'
        );
        $defGetUserProfile->addParameter(
            Parameter::string('userId')
                ->description('The ID of the user')
                ->required()
        );
        $definitions[] = $defGetUserProfile->createFunctionDescription();

        return $definitions;
    }
}