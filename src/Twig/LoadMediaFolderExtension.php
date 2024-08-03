<?php declare(strict_types=1);

namespace TestTheme\Twig;

use Shopware\Core\Content\Media\MediaCollection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Shopware\Core\Framework\Log\Package;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

#[Package('core')]
class LoadMediaFolderExtension extends AbstractExtension
{
    /**
     * @internal
     */
    public function __construct(private readonly EntityRepository $mediaRepository)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('loadMediaFolder', $this->loadMediaFolder(...)),
        ];
    }

    public function loadMediaFolder($folderId, Context $context): MediaCollection
    {
        if (empty($folderId)) {
            return new MediaCollection();
        }

        $criteria = new Criteria();
        $filter = new OrFilter([
            new EqualsFilter('mediaFolderId', $folderId),
            new EqualsFilter('mediaFolder.parentId', $folderId)
        ]);
        $criteria->addFilter($filter);

        $criteria->addAssociation('mediaFolder');
        return $this->mediaRepository->search($criteria, $context)->getEntities();
    }
}
