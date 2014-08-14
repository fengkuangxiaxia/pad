<?php

class TeamController extends BaseController
{
    /**
     * 页面：首页
     * @return Response
     */
    public function getIndex()
    {
        return View::make('team.index');
    }
    
    /**
     * 页面：获取地下城列表
     * @return json
     */
    public function getDungeons()
    {        
        $level1Dungeons = Dungeon::where('level', '=', 1)->get();
        
        $level3Dungeons = Dungeon::where('level', '=', 3)->get();
        
        $result = array();      
        foreach ($level1Dungeons as $level1Dungeon) {     
            $level2Dungeons = Dungeon::where('father_id', '=', $level1Dungeon->id)->get();
            foreach ($level2Dungeons as $level2Dungeon) {
                $level3Dungeons = Dungeon::where('father_id', '=', $level2Dungeon->id)->get();
                foreach ($level3Dungeons as $level3Dungeon) {
                    $result[$level1Dungeon->name][$level2Dungeon->name][$level3Dungeon->name] = $level3Dungeon->id;
                }
            }
        }
        
        /*
        echo '<pre>';
        print_r($result);
        echo '</pre>';
        die();
        */
        
        return json_encode($result);
    }
    
    /**
     * 页面：获取地下城对应的匹配队伍
     * @return json
     */
    public function getTeams()
    {
        $dungeon_id = Input::get('dungeon_id');
        
        $userId = Auth::user()->id;
        
        $userMonsters = Monster::whereIn('id', UserMonster::where('user_id', '=', $userId)->lists('monster_id'))->lists('id');
        
        $teams = Team::where('dungeon_id', '=', $dungeon_id)->whereIn('leader_id', $userMonsters)->whereIn('monster1_id', $userMonsters)->whereIn('monster2_id', $userMonsters)->whereIn('monster3_id', $userMonsters)->whereIn('monster4_id', $userMonsters)->get();
        return $teams;
        
    }
}
