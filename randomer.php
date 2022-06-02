<?php

class Randomer
{
    public function __construct($players)
    {
        $this->players = $players;
    }

    /**
     * @param $groupCount
     * @return array|false
     */
    public function randomSort($groupCount)
    {
        $tmpPlayers = $this->getArrayByPoint($this->players);
        $countPlayers = count($tmpPlayers);
        $countGroup = round($countPlayers / $groupCount);
        $i = 1;
        $b = 1;
        if(empty($tmpPlayers))
            return false;
        
        $playersByPoint = [];
        foreach ($tmpPlayers as $player) {
            if($i == $groupCount+1 && $b < $countGroup+1) {
                $i = 1;
                $b++;
            }
            $playersByPoint[$b][$i] = $player;
            $i++;
        };
        
        $groups = [];
        foreach ($playersByPoint as $key => $value){
            shuffle($value);
            $countValue = count($value);
            for ($j = 0; $j < $countValue; $j++){
                $groups[$j][] = $value[$j];
            }
        }
        
        $groupsSumPoint = 0;
        $this->each($groups, function ($key, $group) use (&$groupsSumPoint) {
            $groupsSumPoint += $this->sumGroupPoint($group);
        });

        return $groups;
    }

    public function sumGroupPoint($group)
    {
        $sum = 0;
        $this->each($group, function ($key, $player) use (&$sum) {
            $sum += $player['point'];
        });
        return $sum;
    }

    private function each($items, $callback)
    {
        foreach ($items as $key => $item) {
            $callback($key, $item);
        }
    }

    /**
     * @param $items
     * @return Divide into three groups on point
     */
    private function getArrayByPoint($items){
        $price = array_column($items, 'point');

        array_multisort($price, SORT_DESC, $items);

        return $items;
    }
}
/*
 * List of players
 */
$players = file_get_contents("players.json");

/**
 * Create object Randomer
 */
$r = new Randomer(json_decode($players,true));

/**
 * Get groups count from user
 */
$groupCount = (int)readline('Enter the amount of teams: ');

if($groupCount <= 1)
    die('The amount of teams must be greater than 1');

$groups = $r->randomSort($groupCount);
// print beauty
if(!empty($groups) && is_iterable($groups)){
    foreach ($groups as $key => $group) {
        echo "Group: #" . ($key + 1) . PHP_EOL;
        $sumGroupPoint = 0;
        foreach ($group as $player) {
            echo "    " . $player['first_name'] . "   - " . $player['point'] . PHP_EOL;
            $sumGroupPoint += $player['point'];
        }
        echo "  Point: " . $sumGroupPoint . PHP_EOL;
        echo " - - - - - - - - - - " . PHP_EOL;
    }
}

