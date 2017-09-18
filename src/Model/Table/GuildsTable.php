<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Core\Configure;

class GuildsTable extends Table
{
    public function initialize(array $config)
    {
        $this->belongsTo('Regions');
        $this->hasMany('Matches', [
            'foreignKey' => 'guild_id',
            'className' => 'Matches'
        ]);
        $this->hasMany('MatchesOpp', [
            'foreignKey' => 'opp_guild_id',
            'className' => 'Matches'
        ]);

    }

    public function checkAndSave($data) {
        $timezoneToRegion = Configure::read('TimezoneToRegion');
        $region_id = $timezoneToRegion[$data['tzone']];

        $guild_query = $this->find('all', [
            'conditions' => ['origin_id' => $data['origin_id'], 'region_id' => $region_id]
        ]);

        $row = $guild_query->first();

        if($guild_query->isEmpty()) {
            $guild = $this->newEntity($data);
            $guild->region_id = $region_id;

            if($this->save($guild)) {
                return $guild->guild_id;
            }
            
        } else {
            return $row['guild_id'];
        }
    }
}

?>