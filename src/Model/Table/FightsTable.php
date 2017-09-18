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
        $fight_query = $this->find('all')
            ->where(['Fights.match_id' => $data['match_id'], 'Fights.wizard_id' => $data['wizard_id'], 'Fights.opp_wizard_id' => $data['opp_wizard_id']])
            ->orWhere(['Fights.match_id' => $data['match_id'], 'Fights.wizard_id' => $data['opp_wizard_id'], 'Fights.opp_wizard_id' => $data['wizard_id']]);

        if($fight_query->isEmpty()) {
            $fight = $this->newEntity($data);
            $fight->origin_id = $data['rid'];
            $fight->round1 = $data['result'][0];
            $fight->round2 = $data['result'][1];

            $this->save($fight);
        }
    }
}

?>