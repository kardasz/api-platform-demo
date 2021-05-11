<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\DummyDto;
use DateTime;

/**
 * Class DummyDataProvider.
 */
class DummyDataProvider implements ContextAwareCollectionDataProviderInterface, ItemDataProviderInterface, RestrictedDataProviderInterface
{
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return DummyDto::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        return $this->data();
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        /** @var DummyDto $item */
        foreach ($this->data() as $item) {
            if ($id === $item->getId()) {
                return $item;
            }
        }
    }

    private function data(): iterable
    {
        yield (new DummyDto())
            ->setId('dummy-1')
            ->setTitle('Dummy 1')
            ->setIntro('Intro ....')
            ->setDetails('Example')
            ->setPublishedAt(new DateTime('+1 day'));

        yield (new DummyDto())
            ->setId('dummy-2')
            ->setTitle('Dummy 2')
            ->setIntro('Intro ....')
            ->setDetails('Example')
            ->setPublishedAt(new DateTime('+2 day'));

        yield (new DummyDto())
            ->setId('dummy-3')
            ->setTitle('Dummy 3')
            ->setIntro('Intro ....')
            ->setDetails('Example')
            ->setPublishedAt(new DateTime('+3 day'));
    }
}
