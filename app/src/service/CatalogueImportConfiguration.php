<?php


namespace App\service;

use App\Entity\Catalogue;
use KunicMarko\SonataImporterBundle\SonataImportConfiguration;
use Doctrine\ORM\EntityManagerInterface;

class CatalogueImportConfiguration implements SonataImportConfiguration
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function adminClass(): string
    {
        return CatalogueAdmin::class;
    }

    public static function format(): string
    {
        return 'Excel';
    }

    public function map(array $item, array $additionalData)
    {
        $catalogue = new Catalogue();

        $catalogue->setName($item[0]);

        $this->entityManager->persist($catalogue);
    }

    public function save(array $items, array $additionalData): void
    {
        $this->entityManager->flush();
    }

}
