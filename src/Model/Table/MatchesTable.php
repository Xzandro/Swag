<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Core\Configure;
use Cake\I18n\Time;

class MatchesTable extends Table
{
    public function initialize(array $config)
    {
        $this->belongsTo('Guild', [
            'foreignKey' => 'guild_id',
            'className' => 'Guilds'
        ]);
        $this->belongsTo('GuildOpp', [
            'foreignKey' => 'opp_guild_id',
            'className' => 'Guilds'
        ]);
        $this->hasMany('Fights', [
            'dependent' => true
            'sort' => ['Fights.battle_end' => 'DESC']
        ]);
    }

    public function checkAndSave($data) {
        $match = $this->newEntity($data);

        $this->save($match);
    }

    public function getStats($data, $guild_id)
    {
        if(empty($data))
            return $data;

        $timezoneToRegion = Configure::read('TimezoneToRegion');
        $regionToTimezone = array_flip($timezoneToRegion);
        $timezone = $regionToTimezone[$data[0]['guild']['region_id']];
        $resultToProperty = array( 1 => 'round_wins', 2 => 'round_loses', 3 => 'round_draws');
        $resultToPropertyOpp = array( 1 => 'round_loses', 2 => 'round_wins', 3 => 'round_draws');

        foreach($data as &$match) {
            $fight_count = count($match['fights']);
            $match['round_count'] = $fight_count * 2;
            $match['round_wins'] = 0;
            $match['round_loses'] = 0;
            $match['round_draws'] = 0;
            $match['last_fight'] = Time::parse($match['last_fight'])->i18nFormat('yyyy-MM-dd HH:mm:ss', $timezone);
            foreach($match['fights'] as $fight) {
                if($fight['guild_id'] == $guild_id) {
                    $match[$resultToProperty[$fight['round1']]]++;
                    $match[$resultToProperty[$fight['round2']]]++;
                } else {
                    $match[$resultToPropertyOpp[$fight['round1']]]++;
                    $match[$resultToPropertyOpp[$fight['round2']]]++;
                }

            }

        }
        return $data;
    }
}

?>