<?php

namespace App\Artia;

use App\Artia\Request\Request;

class Api extends Request
{
    /** @var $required \stdClass */
    private $required;
    /** @var $graphQL string */
    private $graphQL;

    private $response;

    /**
     * Retorna a reposta da API.
     *
     * @return null|object
    */
    public function getResponse(): ?object
    {
        return $this->response;
    }

    /**
     * Captura as informações do array e transoforma em um @object.
     *
     * @param array $fields
     * @return Api
    */
    public function required(array $fields): Api
    {
        $this->required = new \stdClass();
        foreach ($fields as $key => $value) {
            $this->required->$key = $value;
        }
        return $this;
    }

    /**
     * Verifica o @object 'api' e chama a função correspondente.
     *
     * @return Api
    */
    public function build(): Api
    {
        if (!empty($this->required)) {
            switch ($this->required->callback) {
                case 'createActivity':
                    $this->createActivity();
                    break;
                case 'changeCustomStatusActivity':
                    $this->changeCustomStatusActivity();
                    break;
                case 'showActivity':
                    $this->showActivity();
                    break;
                case 'listingActivities':
                    $this->listingActivities();
                    break;
                case 'createComment':
                    $this->createComment();
                    break;
                case 'listingCommentsNotViewed':
                    $this->listingCommentsNotViewed();
                    break;
                case 'authenticationByClient':
                    $this->authenticationByClient();
                    break;
            }
        }
        return $this;
    }

    public function send()
    {
        try {
            $this->response = $this->curl($this->graphQL);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    /**
     * Cria atividade.
     *
     * @return void
    */
    private function createActivity(): void
    {
        $this->graphQL = \QueryBuilder\Builder::createMutationBuilder()
        ->name('createActivity')
        ->arguments([
            'title' => $this->required->title,
            'accountId' => intval($_ENV['ARTIA_ACCOUNT_ID']),
            'folderId' => $this->required->folderId,
            'description' => $this->required->description,
            'responsibleId' => $this->required->responsibleId,
            'estimatedStart' => dateFormat()->format('Y-m-d'),
            'estimatedEnd' => $this->required->estimatedEnd,
            'actualStart' => dateFormat()->format('Y-m-d'),
            'actualEnd' => '',
            'estimatedEffort' => floatval($this->required->estimatedEffort),
            'categoryText' => $this->required->categoryText,
            'priority' => 100,
            'timeEstimatedStart' => dateFormat()->format('H:i'),
            'timeEstimatedEnd' => '',
            'timeActualEnd' => '',
            'completedPercent' => 00.00
        ])
        ->body([
            'id', 'uid', 'communityId', 'customStatus ' => [
                'id', 'statusName', 'status'
            ],
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
            'createdById', 'createdForUser', 'responsible' => [
                'id', 'name', 'email',
            ], 'parent' => [
                'id', 'name'
            ]
        ])
        ->build();
    }

    private function listingActivities(): void
    {
        $this->graphQL = \QueryBuilder\Builder::createQueryBuilder()
        ->name('listingActivities')
        ->arguments([
            'accountId' => (int) $_ENV['ARTIA_ACCOUNT_ID'],
            'folderId' => (int) $this->required->folderId
        ])
        ->body([
            'id',
        ])
        ->build();
    }

    /**
     * Altera o status de uma atividade.
     *
     * @return void
    */
    private function changeCustomStatusActivity(): void
    {
        $this->graphQL = \QueryBuilder\Builder::createMutationBuilder()
        ->name('changeCustomStatusActivity')
        ->arguments([
            'id' => $this->required->id,
            'accountId' => intval($_ENV['ARTIA_ACCOUNT_ID']),
            'folderId' => $this->required->folderId,
            'customStatusId' => $this->required->customStatusId,
            'status' => $this->required->status
        ])
        ->body([
            'id', 'title', 'customStatus' => [
                'id', 'statusName', 'status'
            ]
        ])
        ->build();
    }

    /**
     * Lista as informações da atividade.
     *
     * @return void
    */
    private function showActivity(): void
    {
        $this->graphQL = \QueryBuilder\Builder::createQueryBuilder()
        ->name('showActivity')
        ->arguments([
            'id' => $this->required->id,
            'accountId' => intval($_ENV['ARTIA_ACCOUNT_ID']),
            'folderId' => $this->required->folderId
        ])
        ->body([
            'id', 'uid', 'communityId', 'customStatus' => [
                'id', 'statusName', 'status',
            ], 'actualEnd', 'timeActualEnd'
        ])
        ->build();
    }

    /**
     * Cria um comentário na atividade.
     *
     * @return void
    */
    private function createComment(): void
    {
        $this->graphQL = \QueryBuilder\Builder::createMutationBuilder()
        ->name('createComment')
        ->arguments([
            'id' => $this->required->id,
            'object' => $this->required->object,
            'content' => $this->required->content,
            'accountId' => intval($_ENV['ARTIA_ACCOUNT_ID']),
            'createBy' => $this->required->createBy,
            'users' => $this->required->users
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
     * Lista os comentários da atividade.
     *
     * @return void
    */
    private function listingCommentsNotViewed(): void
    {
        $this->graphQL = \QueryBuilder\Builder::createQueryBuilder()
        ->name('listingCommentsNotViewed')
        ->arguments([
            'ids' => $this->required->ids,
            'type' => $this->required->type,
            'viewed' => $this->required->viewed,
            'accountId' => (int) $_ENV['ARTIA_ACCOUNT_ID']
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

    /**
     * Gera um token de autenticação.
     *
     * @return void
    */
    private function authenticationByClient(): void
    {
        $this->graphQL = \QueryBuilder\Builder::createMutationBuilder()
        ->name('authenticationByClient')
        ->arguments([
            'clientId' => $_ENV['ARTIA_CLIENT_ID'],
            'secret' => $_ENV['ARTIA_SECRET']
        ])
        ->body(['token'])
        ->build();
    }
}
