<?php
namespace App\Controller;

use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\I18n\Time;

class ApiController extends AppController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Security->config('unlockedActions', ['matches']);
    }

    public function beforeRender(Event $event)
    {
        $this->RequestHandler->renderAs($this, 'json');
        $this->response->type('application/json');
        $this->set('_serialize', true);
    }

    public function match($id = 0) {
        $this->loadModel('Matches');
        $contain = ['Guild', 'GuildOpp', 'Fights'];
        $query = $this->Matches->find('all', [
            'contain' => $contain,
            'conditions' => ['Matches.match_id' => $id]
        ]);

        $matches = $query->toArray();

        $timezoneToRegion = Configure::read('TimezoneToRegion');
        $regionToTimezone = array_flip($timezoneToRegion);
        $timezone = $regionToTimezone[$matches[0]['guild']['region_id']];
        $matches[0]['last_fight'] = Time::parse($matches[0]['last_fight'])->i18nFormat('yyyy-MM-dd HH:mm:ss', $timezone);

        $this->set(compact('matches'));
    }

    public function matches($id = 0)
    {
        $this->loadModel('Matches');
        $contain = ['Guild', 'GuildOpp'];
        if($this->request->query('fights')) {
            $contain = ['Guild', 'GuildOpp', 'Fights'];
        }
        $limitWeek = [];

        $year = ($this->request->query('year')) ? $this->request->query('year') : date('Y');
        $week = $this->request->query('week');
        if($year && $week) {
            $dto = new Time();
            $start = $dto->setISODate($year, $week)->format('Y-m-d');
            $end = $dto->modify('+6 days')->format('Y-m-d');
            $limitWeek = ['Matches.last_fight >' => $start, 'Matches.last_fight <' => $end];
        }

        switch ($this->request->query('type')) {
            case 'attack':
                $where = array_merge(['Matches.guild_id' => $id, 'Matches.log_type' => 1], $limitWeek);
                $orWhere = array_merge(['Matches.opp_guild_id' => $id, 'Matches.log_type' => 2], $limitWeek);
                $query = $this->Matches->find('all', [
                    'contain' => $contain,
                    'order' => ['Matches.match_id' => 'DESC']
                ])->where($where)->orWhere($orWhere);
                break;
            case 'defense':
                $where = array_merge(['Matches.guild_id' => $id, 'Matches.log_type' => 2], $limitWeek);
                $orWhere = array_merge(['Matches.opp_guild_id' => $id, 'Matches.log_type' => 1], $limitWeek);
                $query = $this->Matches->find('all', [
                    'contain' => $contain,
                    'order' => ['Matches.match_id' => 'DESC']
                ])->where($where)->orWhere($orWhere);
                break;
            default:
                $where = array_merge(['Matches.guild_id' => $id], $limitWeek);
                $orWhere = array_merge(['Matches.opp_guild_id' => $id], $limitWeek);
                $query = $this->Matches->find('all', [
                    'contain' => $contain,
                    'order' => ['Matches.match_id' => 'DESC']
                ])->where($where)->orWhere($orWhere);
        }

        $matches = $query->toArray();
        if($this->request->query('stats'))
            $matches = $this->Matches->getStats($matches, $id);

        $this->set(compact('matches'));
    }

    public function regions()
    {
        $this->loadModel('Regions');

        $query = $this->Regions->find('all');
        $regions = $query->all();
        $this->set(compact('regions'));
    }

    public function guilds($region = 0)
    {
        $this->loadModel('Guilds');
        $query = $this->Guilds->find('all', [
            'conditions' => ['Guilds.region_id' => $region],
            'order' =>['Guilds.name' => 'ASC']
        ]);
        $guilds = $query->all();
        $this->set(compact('guilds'));
    }

    public function guildMatches($guild = 0)
    {
        $this->loadModel('Guilds');

        $query = $this->Guilds->find('all', [
            'conditions' => ['Guilds.guild_id' => $guild],
            'contain' =>['Matches', 'MatchesOpp']
        ]);
        $guilds = $query->all();
        $this->set(compact('guilds'));
    }
}
?>