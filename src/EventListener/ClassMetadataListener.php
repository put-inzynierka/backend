<?php

namespace App\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

class ClassMetadataListener
{
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();

        if ($classMetadata->namespace === 'League\Bundle\OAuth2ServerBundle\Model') {
            $originalTableName = $classMetadata->getTableName();
            $tableName = 'oauth2.' . substr($originalTableName, 7);

            $classMetadata->setPrimaryTable(['name' => $tableName]);
        }
    }
}