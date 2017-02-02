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
        if(!$this->exists(['guild_id' => $data['guild_id']])) {
            $guild = $this->newEntity($data);
            $guild->region_id = $timezoneToRegion[$data['tzone']];

            $this->save($guild);
        }
    }
}

?>