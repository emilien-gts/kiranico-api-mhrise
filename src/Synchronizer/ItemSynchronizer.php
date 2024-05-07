<?php

namespace App\Synchronizer;

use App\Entity\Item;
use App\Enum\Item\ItemSelector;
use App\Enum\Item\ItemType;
use App\Model\Crawler\BaseCrawler;
use App\Utils\CrawlerUtils;
use App\Utils\Utils;

class ItemSynchronizer extends AbstractSynchronizer
{
    private const int BATCH_SIZE = 50;
    private const string ITEMS_LIST_PATH = 'data/items';

    public function synchronize(): void
    {
        $this->ping();
        $this->helper()->disableSQLLog();
        $this->helper()->cleanEntityData(Item::class);

        foreach (ItemType::cases() as $type) {
            $this->synchronizeType($type);
        }
    }

    private function synchronizeType(ItemType $type): void
    {
        $this->logger()->debug(\sprintf('>>> start sync %s', \strtolower($type->label())));

        $url = \sprintf('%s?view=%s', $this->getListUrl(), $type->value);
        $crawler = new BaseCrawler($url);

        $nodes = $crawler->findNodesBySelector(ItemSelector::ITEM_LIST_DIV->value);
        $crawler->clear();

        foreach ($nodes as $i => $node) {
            $this->synchronizeItem($node, $type);

            if (0 === $i % self::BATCH_SIZE) {
                $this->logger()->info(\sprintf('... ... ... %d / %d', $i, $nodes->count()));
                $this->flushAndClear();
            }
        }

        $this->logger()->info(\sprintf('... ... ... %d / %d', $nodes->count(), $nodes->count()));
        $this->logger()->info(Utils::getMemoryConsumption());
        $this->flushAndClear();
    }

    private function synchronizeItem(\DOMNode $node, ItemType $type): void
    {
        $a = CrawlerUtils::findChildByName($node, 'a');
        if (null === $a) {
            return; // unprocessable
        }

        $href = CrawlerUtils::findAttributeByName($a, 'href');
        if (null === $href) {
            return; // unprocessable
        }

        $crawler = new BaseCrawler($href);
        $titleH1 = $crawler->findNodeBySelector(ItemSelector::ITEM_DETAIL_TITLE_H1->value);
        $descriptionP = $crawler->findNodeBySelector(ItemSelector::ITEM_DETAIL_DESCRIPTION_P->value);

        if (null === $titleH1 || null === $descriptionP) {
            return; // unprocessable
        }

        $crawler = new BaseCrawler($node);
        $iconImg = $crawler->findNodeBySelector(ItemSelector::ITEM_LIST_IMG->value);
        $crawler->clear();

        $item = new Item();
        $item->setType($type);
        $item->setName(Utils::cleanString($titleH1->textContent));
        $item->setDescription(Utils::cleanString($descriptionP->textContent));
        $item->setImageUrl($iconImg ? CrawlerUtils::findAttributeByName($iconImg, 'src') : null);

        $this->em()->persist($item);
    }

    private function getListUrl(): string
    {
        return \sprintf('%s/%s', $this->getKiranicoUrl(), self::ITEMS_LIST_PATH);
    }
}