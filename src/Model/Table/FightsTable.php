<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class FightsTable extends Table
{
    public function initialize(array $config)
    {
        $this->belongsTo('Matches');
    }

    public function checkAndSave($data) {
        $alternativeFightID = ($data['log_type'] === 1) ? ($data['rid'] + 1) : ($data['rid'] - 1);
        $fight_query = $this->find('all')
            ->where(['Fights.match_id' => $data['match_id'], 'Fights.fight_id' => $data['rid']])
            ->orWhere(['Fights.match_id' => $data['match_id'], 'Fights.fight_id' => $alternativeFightID]);

        if($fight_query->isEmpty()) {
            $fight = $this->newEntity($data);
            $fight->fight_id = $data['rid'];
            $fight->round1 = $data['result'][0];
            $fight->round2 = $data['result'][1];

            $this->save($fight);
        }
    }
}

?>