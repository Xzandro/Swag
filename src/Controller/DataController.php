<?php
namespace App\Controller;

use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\Http\Response;
use Cake\Core\Configure;

class DataController extends AppController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Security->config('unlockedActions', ['upload']);
        $this->autoRender = false;
    }

    public function upload()
    {
        if(!$this->request->is('post'))
            return;

        $data = $this->request->data;
        if(empty($data))
            return;

        if($data['command'] === 'GetGuildWarBattleLogByGuildId') {
            $this->parseByGuild($data);
        } elseif ($data['command'] === 'GetGuildWarBattleLogByWizardId') {
            $this->parseByMember($data);
        }

    }

    public function view($id = 0)
    {
        $this->loadModel('Matches');
        $query = $this->Matches->find('all', [
            'order' => ['Matches.match_id' => 'DESC']
        ])->where(['Matches.guild_id' => $id])->orWhere(['Matches.opp_guild_id' => $id])->group(['yearweek(Matches.last_fight)']);
        debug($query->toArray());
    }

    public function parseByGuild($data)
    {
        $this->loadModel('Guilds');
        $this->loadModel('Matches');
        $this->loadModel('Fights');

        //check own guild
        if(!isset($data['battle_log_list_group']))
            return;

        if(isset($data['battle_log_list_group'][0]['battle_log_list'][0])) {
            $fight = $data['battle_log_list_group'][0]['battle_log_list'][0];
            $guild = ['origin_id' => $fight['guild_id'], 'name' => $fight['guild_name'], 'tzone' => $data['tzone']];
            $guild_id = $this->Guilds->checkAndSave($guild);
        }

        foreach($data['battle_log_list_group'] as $match) {
            //check opponent guilds
            $opponent = $match['opp_guild_info'];
            $opponent['tzone'] = $data['tzone'];
            $opponent['origin_id'] = $opponent['guild_id'];
            unset($opponent['guild_id']);
            $opp_guild_id = $this->Guilds->checkAndSave($opponent);
            $i = 0;
            foreach($match['battle_log_list'] as &$fight) {
                //check war (only once)
                $fight['guild_id'] = $guild_id;
                $fight['opp_guild_id'] = $opp_guild_id;
                if ($i === 0) {
                    $match_data = $fight;
                    $match_data['last_fight'] = $fight['battle_end'];
                    $this->Matches->checkAndSave($match_data);
                }
                $this->Fights->checkAndSave($fight);
                $i++;
            }
        }
    }

    public function parseByMember($data)
    {
        $this->loadModel('Guilds');
        $this->loadModel('Matches');
        $this->loadModel('Fights');

        //check own guild
        if(!isset($data['battle_log_list']))
            return;

        foreach($data['battle_log_list'] as &$fight) {
            $guild = ['origin_id' => $fight['guild_id'], 'name' => $fight['guild_name'], 'tzone' => $data['tzone']];
            $guild_id = $this->Guilds->checkAndSave($guild);

            $guild_opp = ['origin_id' => $fight['opp_guild_id'], 'name' => $fight['opp_guild_name'], 'tzone' => $data['tzone']];
            $opp_guild_id = $this->Guilds->checkAndSave($guild_opp);

            $fight['guild_id'] = $guild_id;
            $fight['opp_guild_id'] = $opp_guild_id;

            $match_data = $fight;
            $match_data['last_fight'] = $fight['battle_end'];
            $match_query = $this->Matches->find('all', [
                'conditions' => ['Matches.match_id' => $fight['match_id']]
            ]);

            $row = $match_query->first();
            if($match_query->isEmpty()) {
                $this->Matches->checkAndSave($match_data);
            } else {
                //check the battle_end
                if(new Time($row['last_fight']) < new Time($match_data['last_fight'])) {
                    $this->Matches->checkAndSave($match_data);
                }
            }

            $this->Fights->checkAndSave($fight);
        }
    }

    public function purge()
    {
        $purgeToken = Configure::read('purgeToken');
        $token = $this->request->query('token');

        if(!$token || $token != $purgeToken) {
            $this->response->statusCode(403);
            return;
        }

        $this->loadModel('Matches');
        $query = $this->Matches->find('all', [
            'conditions' => ['last_fight <' => new Time('6 months ago')]
        ]);
        $data = $query->all();

        foreach ($data as $match) {
            $this->Matches->delete($match);
        }
    }
    
}
?>