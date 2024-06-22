<?php

declare(strict_types=1);

namespace App\Story;

use App\Factory\ItemFactory;
use App\Factory\Monster\MonsterFactory;
use App\Factory\Quest\QuestFactory;
use App\Factory\Skill\SkillFactory;
use App\Factory\Weapon\WeaponFactory;
use Zenstruck\Foundry\Story;

final class AppStory extends Story
{
    public function build(): void
    {
        ItemFactory::createMany(100);

        MonsterFactory::createMany(25);
        QuestFactory::createMany(50);

        SkillFactory::createMany(50);
        WeaponFactory::createMany(50);
    }
}
