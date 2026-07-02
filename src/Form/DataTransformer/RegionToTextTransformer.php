<?php

namespace App\Form\DataTransformer;

use App\Entity\Region;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class RegionToTextTransformer implements DataTransformerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function transform($region): string
    {
        if (null === $region) {
            return '';
        }

        return $region->getName();
    }

    public function reverseTransform($regionName): ?Region
    {
        if (!$regionName) {
            return null;
        }

        $regionName = trim($regionName);
        $region = $this->entityManager
            ->getRepository(Region::class)
            ->findOneBy(['name' => $regionName]);

        if (null === $region) {
            $region = new Region();
            $region->setName($regionName);
        }

        return $region;
    }
}
