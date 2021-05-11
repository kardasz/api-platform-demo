<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Dto\DummyDto;

/**
 * Class DummyDataPersister.
 */
class DummyDataPersister implements ContextAwareDataPersisterInterface
{
    /**
     * @param DummyDto $data
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof DummyDto;
    }

    /**
     * @param DummyDto $data
     *
     * @return DummyDto
     */
    public function persist($data, array $context = [])
    {
        // Some custom logic to persist dummy object (call external api or whatever)
        return $data;
    }

    /**
     * @param DummyDto $data
     *
     * @return void
     */
    public function remove($data, array $context = [])
    {
        // Remove dummy object (call external api or whatever)
    }
}
