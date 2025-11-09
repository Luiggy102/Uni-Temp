<?php

namespace App\Lambda;

use Bref\Event\Sqs\SqsEvent;
use Bref\Event\Sqs\SqsHandler as BrefBaseSqsHandler;
use Bref\Context\Context;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Psr\Log\LoggerInterface;

class SqsHandler extends BrefBaseSqsHandler
{
     private $dynamoDb;
     private $marshaler;
     private $logger;

     public function __construct(
          DynamoDbClient $dynamoDb,
          Marshaler $marshaler,
          LoggerInterface $logger
     ) {
          $this->dynamoDb = $dynamoDb;
          $this->marshaler = $marshaler;
          $this->logger = $logger;
     }

     public function handleSqs(SqsEvent $event, Context $context): void
     {
          $this->logger->info("Worker SQS iniciado, procesando " . count($event->getRecords()) . " mensajes.");

          foreach ($event->getRecords() as $record) {
               try {
                    $body = $record->getBody();
                    $data = json_decode($body, true);

                    $dynamoItem = $this->marshaler->marshalJson(json_encode($data));

                    $this->dynamoDb->putItem([
                         'TableName' => 'registro_temperaturas_iot',
                         'Item' => $dynamoItem
                    ]);

                    $this->logger->info("Mensaje " . $record->getMessageId() . " procesado exitosamente.");

               } catch (\Exception $e) {
                    $this->logger->error("Error procesando mensaje " . $record->getMessageId() . ": " . $e->getMessage());
                    // Lanza la excepción para que SQS sepa que falló y reintente
                    throw $e;
               }
          }
     }
}