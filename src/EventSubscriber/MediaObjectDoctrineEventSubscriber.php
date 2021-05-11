<?php

namespace App\EventSubscriber;

use App\Entity\MediaObject;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Vich\UploaderBundle\Storage\StorageInterface;

class MediaObjectDoctrineEventSubscriber implements EventSubscriber
{
    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function postLoad(LifecycleEventArgs $event): void
    {
        $this->apply($event);
    }

    public function postPersist(LifecycleEventArgs $event): void
    {
        $this->apply($event);
    }

    private function apply(LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();
        if (!$entity instanceof MediaObject) {
            return;
        }

        $entity->setContentUrl(
            $this->storage->resolveUri($entity, 'file')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postLoad,
            Events::postPersist,
        ];
    }
}
