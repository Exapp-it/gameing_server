<?php

namespace App\Services\TomHorn\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class Game extends DataTransferObject
{
    public int $Id;
    public string $Channel;
    public string $Version;
    public string $Name;
    public string $Key;
    public string $Provider;
    public string $Type;

    public function saveToDB()
    {
        $prefix = 'https://vm4131020.62ssd.had.wf/pics/';
        $images = [
            "243 Crystal Fruits - 94RTP" => $prefix."243CrystalFruits.png",
            "Triple Thunder - 92RTP" => $prefix."TrippleThunder.png",
            "Triple Thunder - 95RTP" => $prefix."TrippleThunder.png",
            "French Roulette - La Partage" => $prefix."FrenchRouletteLaPartage.png",
            "243 Christmas Fruits - 95RTP" => $prefix."243ChristmasFruits.png",
            "243 Christmas Fruits - 92RTP" => $prefix."243ChristmasFruits.png",
            "Rot Stormo - 92RTP" => $prefix."RotStormo.png",
            "Rot Stormo - 95RTP" => $prefix."RotStormo.png",
            "Book of Vampires - 92RTP" => $prefix."BookOfVampires.png",
            "Book of Vampires - 95RTP" => $prefix."BookOfVampires.png",
            "3 Mermaids - 92RTP" => $prefix."3Mermaids.png",
            "3 Mermaids - 95RTP" => $prefix."3Mermaids.png",
            "Hawaiian Fever - 92RTP" => $prefix."HawaiianFever.png",
            "Hawaiian Fever - 95RTP" => $prefix."HawaiianFever.png",
            "Hawaiian Fever" => $prefix."HawaiianFever.png",
            "Fluxberry" => $prefix."Fluxbery.png",
            "Dragon VS Phoenix" => $prefix."DragonVsPhoenix.png",
            "PengWins" => $prefix."PengWins.png",
            "Frutopia" => $prefix."Frutopia.png",
            "Cricket Mania" => $prefix."CricketMania.png",
            "Mine Mine Quest" => $prefix."MineMineQuest.png",
            "Space Jammers" => $prefix."Spacejammers.png",
            "Fruits Go Pop" => $prefix."FruitsGoPop.png",
            "Beastie Bux" => $prefix."BeastieBux.png",
            "La Tomatina" => $prefix."LaTomatina.png",
            "81 Frutas Grandes" => $prefix."81FrutasGrandes.png",
            "Roulette EU" => $prefix."RouletteEU.png",
            "Wheel Of Luck" => $prefix."WheelOfLuck.png",
            "Sweet Crush" => $prefix."SweetCrush.jpg",
            "GoldX" => $prefix."GoldX.png",
            "Wolf Sierra" => $prefix."WolfSierra.png",
            "243 Crystal Fruits Reversed" => $prefix."243CrystallFruitsReserved.png",
            "Joker Reelz" => $prefix."JokerReelz.png",
            "The Secret of Ba" => $prefix."TheSecretOfBa.png",
            "Spinball" => $prefix."Spinball.png",
            "Diamond Hill" => $prefix."DiamondHill.png",
            "Hot'n'Fruity" => $prefix."HotNFruity.png",
            "Incas's Treasure	" => $prefix."Inca'sTreasure.png",
            "Kongo Bongo" => $prefix."KongoBongo.png",
            "Sherlock" => $prefix."SherlockScandalInBohemia.png",
            "Frozen Queen" => $prefix."FrozenQueen.png",
            "Blackbeard's Quest Mini" => $prefix."BlackBeardsQuest.png",
            " Dragon Riches " => $prefix."DragonRiches.png",
            "Monster Madness" => $prefix."MonsterMadness.png",
            "Red Lights" => $prefix."RedLights.png",
            "Wild Weather" => $prefix."WildWeather.png",
            "243 Crystal Fruits" => $prefix."243CrystalFruits.png",
            "Panda's Run" => $prefix."Panda'sRun.png",
            "Dragon Richies" => $prefix."DragonRiches.png",
            "Fire 'n' Hot TNP" => $prefix."FireNHot.png",
            "Shaolin's Tiger" => $prefix."Shaolin'sTiger.png",
            "Geisha's Fan" => $prefix."Geisha'sFan.png",
            " Thrones Of Persia" => $prefix."ThronesOfPersia.png",
            " Don Juan's Peppers" => $prefix."DonJuan'sPeppers.png",
            " Monkey 27" => $prefix."Monkey's27.png",
            " Book Of Spells" => $prefix."BookOfSpells.png",
            " Blackbeard's Quest" => $prefix."BlackBeardsQuest.png",
            " Savannah King" => $prefix."Savannah'sKing.png",
            " Hot Blizzard" => $prefix."HotBlizzard.png",
            " Sizable Win" => $prefix."SizableWin.png",
            "Black Mummy TNP" => $prefix."BlackMummy.png",
            " Triple Joker" => $prefix."TrippleJoker.png",
            " Flaming Fruit" => $prefix."FlammingFruit.png",
            " The Cup" => $prefix."TheCup.png",
            " Sky Barons" => $prefix."SkyBaron.png",
            " Feng Fu" => $prefix."FengFu.png",
            " Dragon Egg" => $prefix."DragonEgg.png",
            "81 Vegas Magic - 95RTP" => $prefix."81VegasMagic_Logo250x157.png",
        ];

        \App\Models\Game::updateOrCreate([
            "name"     => $this->Name
        ],
        [
            "provider" => $this->Provider,
            "category" => $this->Type,
            "type"     => config('enums.game_types')['tomhorn'],
            "info"     => $this->Key,
            "image"    => array_key_exists($this->Name, $images) ? $images[$this->Name] : "",
        ]);
    }
}