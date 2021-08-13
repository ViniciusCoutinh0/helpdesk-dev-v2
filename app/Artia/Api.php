<?php

namespace App\Artia;

use App\Artia\Request\Request;
use App\Artia\Token\Token;

class Api extends Request
{
    /**
     * @var string
    */
    protected $url = 'app.artia.com/graphql';

    /**
     * @var string
    */
    protected $method;

    /**
     * @var array
    */
    protected $headers;

    /**
     * @var string
    */
    protected $graphQl;

    /**
     * @var stdClass
    */
    protected $data;

    public function __construct(string $method = 'POST')
    {
        $this->method = $method;
        $this->headers = [
            'Content-Type: application/json',
            'OrganizationId: ' . env('CONFIG_API_ORGANIZATION_ID'),
            'Authorization: ' . Token::hash()
        ];
    }

    public function __get($name)
    {
        return ($this->data->$name ?? null);
    }

    /**
     * @param array $fields
     * @return Api
    */
    public function requireds(array $fields): Api
    {
        $this->data = new \stdClass();

        foreach ($fields as $key => $value) {
            $this->data->$key = $value;
        }

        return $this;
    }

    /**
     * @return void
    */
    public function createActivity(): void
    {
        $this->graphQl = \QueryBuilder\Builder::createMutationBuilder()
        ->name('createActivity')
        ->arguments([
            'title' => $this->title,
            'accountId' => $this->accountId,
            'folderId' => $this->folderId,
            'description' => $this->description,
            'responsibleId' => $this->responsibleId,
            'estimatedStart' => date('Y-m-d'),
            'estimatedEnd' => $this->estimatedEnd,
            'actualStart' => date('Y-m-d'),
            'actualEnd' => '',
            'estimatedEffort' => floatval($this->estimatedEffort),
            'categoryText' => $this->categoryText,
            'priority' => 100,
            'timeEstimatedStart' => date('H:i'),
            'timeEstimatedEnd' => '',
            'timeActualEnd' => '',
            'completedPercent' => 00.00
        ])
        ->body([
            'id', 'uid', 'communityId', 'customStatus ' => ['id', 'statusName', 'status'],
            'status', 'title', 'description',
            'groupCategories', 'priority', 'estimatedStart',
            'timeEstimatedStart', 'estimatedEnd', 'timeEstimatedEnd',
            'durationEstimatedCalculated', 'actualStart', 'timeActualStart',
            'actualEnd', 'timeActualEnd', 'durationCalculated', 'estimatedEffort',
            'actualEffort', 'remainingEffort', 'completedPercent',
            'lastCalculation', 'workDaysEstimated', 'remainingDays',
            'daysToCalculation', 'replanned', 'replannedCount', 'position',
            'financePredicted', 'financeAccomplished', 'isCriticalPath', 'customColumns',
            'tendencyEnd', 'tfsKey', 'verifyConflicts', 'typeColor', 'schedulePerformanceIndex',
            'distributeAllocationAutomatically', 'createdAt', 'updatedAt', 'deletedAt',
            'createdById', 'createdForUser', 'responsible' => ['id', 'name', 'email'],
            'parent' => ['id', 'name']
        ])
        ->build();
    }

    /**
     * @return Api
    */
    public function listingActivities(): Api
    {
        $this->graphQl = \QueryBuilder\Builder::createQueryBuilder()
        ->name('listingActivities')
        ->arguments(['accountId' => $this->accountId, 'folderId' => $this->folderId])
        ->body(['id'])
        ->build();

        return $this;
    }

    /**
     * @return void
    */
    public function changeCustomStatusActivity(): void
    {
        $this->graphQl = \QueryBuilder\Builder::createMutationBuilder()
        ->name('changeCustomStatusActivity')
        ->arguments([
            'id' => $this->id,
            'accountId' => $this->accountId,
            'folderId' => $this->folderId,
            'customStatusId' => $this->customStatusId,
            'status' => $this->status
        ])
        ->body([
            'id', 'title', 'customStatus' => [
                'id', 'statusName', 'status'
            ]
        ])
        ->build();
    }

    /**
     * @return void
    */
    public function showActivity(): void
    {
        $this->graphQl = \QueryBuilder\Builder::createQueryBuilder()
        ->name('showActivity')
        ->arguments([
            'id' => $this->id,
            'accountId' => $this->accountId,
            'folderId' => $this->folderId
        ])
        ->body([
            'id', 'uid', 'communityId', 'customStatus' => [
                'id', 'statusName', 'status',
            ], 'actualEnd', 'timeActualEnd'
        ])
        ->build();
    }

    /**
     * @return void
    */
    public function createComment(): void
    {
        $this->graphQl = \QueryBuilder\Builder::createMutationBuilder()
        ->name('createComment')
        ->arguments([
            'id' => $this->id,
            'object' => $this->object,
            'content' => $this->content,
            'accountId' => $this->accountId,
            //'createBy' => $this->createBy,
            //'users' => $this->users
        ])
        ->body([
            'id', 'content', 'createdAt', 'author' => [
                'id', 'name', 'email'
            ], 'registeredBy' => [
                'id', 'name', 'email'
            ], 'users' => [
                'id', 'name', 'email'
            ]
        ])
        ->build();
    }

    /**
     * @return void
    */
    public function listingCommentsNotViewed(): void
    {
        $this->graphQl = \QueryBuilder\Builder::createQueryBuilder()
        ->name('listingCommentsNotViewed')
        ->arguments([
            'ids' => $this->ids,
            'type' => $this->type,
            'viewed' => $this->viewed,
            'accountId' => $this->accountId
        ])
        ->body([
            'id', 'content', 'createdAt', 'createdByApi', 'author' => [
                'id', 'name', 'email'
            ], 'registeredBy' => [
                'id', 'name', 'email'
            ], 'users' => [
                'id', 'name', 'email'
            ]
        ])
        ->build();
    }

    public function updateActivity(): Api
    {
        $this->graphQl = \QueryBuilder\Builder::createMutationBuilder()
        ->name('updateActivity')
        ->arguments([
            'id' => $this->id,
            'title' => $this->title,
            'accountId' => $this->accountId,
            'folderId' => $this->folderId
        ])
        ->body([
            'id'
        ])
        ->build();

        return $this;
    }

    public function listingTimeEntries()
    {
        $this->graphQl = \QueryBuilder\Builder::createQueryBuilder()
        ->name('listingTimeEntries')
        ->arguments([
            'accountId' => $this->accountId,
            'folderId' => $this->folder_id,
            'activityId' => $this->activityId,
        ])
        ->body([
            'id', 'folderId', 'accountId', 'activityId', 'dateAt',
            'duration', 'startTime', 'endTime', 'observation', 'timeEntryStatusId'
        ])
        ->build();

        return $this;
    }

    /**
     * @return Api
    */
    public function authenticationByClient(): Api
    {
        $this->graphQl = \QueryBuilder\Builder::createMutationBuilder()
        ->name('authenticationByClient')
        ->arguments([
            'clientId' => $this->clientId,
            'secret' => $this->secret
        ])
        ->body(['token'])
        ->build();

        return $this;
    }

    /**
     * @param string $method
     * @param array $headers
     * @return Api
    */
    public function response(): object
    {
        $response = json_decode($this->curl());

        // if (isset($response->errors[0])) {
        //     throw new RequestException($response->errors[0]->message);
        // }

        return $response;
    }
}
