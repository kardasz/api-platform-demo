<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\ArticlePreviewDto;
use App\Entity\Article;

/**
 * Class ArticlePreviewDtoDataTransformer.
 */
class ArticlePreviewDtoDataTransformer implements DataTransformerInterface
{
    /**
     * @param Article $object
     *
     * @return ArticlePreviewDto
     */
    public function transform($object, string $to, array $context = [])
    {
        return new ArticlePreviewDto(
            $object->getId(),
            $object->getTitle(),
            $this->getDescription($object),
            $object->getPublishedAt(),
            $object->getAuthor()
        );
    }

    private function getDescription(Article $article): ?string
    {
        $description = $article->getContent();
        if (empty($description)) {
            return null;
        }

        $description = strip_tags($description);

        return substr(
            $description,
            0,
            strpos($description, ' ', 500)
        );
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return ArticlePreviewDto::class === $to && $data instanceof Article;
    }
}
