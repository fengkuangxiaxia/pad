<?php

class TeamController extends BaseController
{
    /**
     * 页面：首页
     * @return Response
     */
    public function getIndex()
    {
        return View::make('team.index',array('tableClasses'=>array('teamsFull','teams1','teams2')));
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
           
        //$teams = Team::where('dungeon_id', '=', $dungeon_id)->get();
        $teams = DB::table('teams')->where('dungeon_id', '=', $dungeon_id)
            ->leftJoin('monsters as LEADER', 'teams.leader_id', '=', 'LEADER.id')
            ->leftJoin('monsters as MONSTER1', 'teams.monster1_id', '=', 'MONSTER1.id')
            ->leftJoin('monsters as MONSTER2', 'teams.monster2_id', '=', 'MONSTER2.id')
            ->leftJoin('monsters as MONSTER3', 'teams.monster3_id', '=', 'MONSTER3.id')
            ->leftJoin('monsters as MONSTER4', 'teams.monster4_id', '=', 'MONSTER4.id')
            ->leftJoin('monsters as FRIEND', 'teams.friend_id', '=', 'FRIEND.id')
            ->select('teams.*', 'LEADER.name as leader_name', 'MONSTER1.name as monster1_name', 'MONSTER2.name as monster2_name', 'MONSTER3.name as monster3_name', 'MONSTER4.name as monster4_name', 'FRIEND.name as friend_name')
            ->get();
        

        $teamsFull = array();
        $teams1 = array();
        $teams2 = array();
        foreach($teams as $team) {
            $count = 0;
            $no = array();
            if(in_array($team->leader_id, $userMonsters)) {
                $count = $count + 1;
            }
            else {
                array_push($no, 'leader_id');
            }
            if(in_array($team->monster1_id, $userMonsters)) {
                $count = $count + 1;
            }
            else {
                array_push($no, 'monster1_id');
            }
            if(in_array($team->monster2_id, $userMonsters)) {
                $count = $count + 1;
            }
            else {
                array_push($no, 'monster2_id');
            }
            if(in_array($team->monster3_id, $userMonsters)) {
                $count = $count + 1;
            }
            else {
                array_push($no, 'monster3_id');
            }
            if(in_array($team->monster4_id, $userMonsters)) {
                $count = $count + 1;
            }
            else {
                array_push($no, 'monster4_id');
            }
            if(in_array($team->friend_id, $userMonsters)) {
                $count = $count + 1;
            }
            else {
                array_push($no, 'friend_id');
            }

            /*寻找相同技能宠物,效率过低
            foreach ($teams as &$team) {
                $team->leader_skill = Monster::whereIn('skill_id', Monster::where('id', '=', $team->leader_id)->lists('skill_id'))->lists('id');
                $team->monster1_skill = Monster::whereIn('skill_id', Monster::where('id', '=', $team->monster1_id)->lists('skill_id'))->lists('id');
                $team->monster2_skill = Monster::whereIn('skill_id', Monster::where('id', '=', $team->monster2_id)->lists('skill_id'))->lists('id');
                $team->monster3_skill = Monster::whereIn('skill_id', Monster::where('id', '=', $team->monster3_id)->lists('skill_id'))->lists('id');
                $team->monster4_skill = Monster::whereIn('skill_id', Monster::where('id', '=', $team->monster4_id)->lists('skill_id'))->lists('id');
                $team->friend_skill = Monster::whereIn('skill_id', Monster::where('id', '=', $team->friend_id)->lists('skill_id'))->lists('id');
            }
            */
            if($count == 6) {
                array_push($teamsFull, array('team'=>$team,'no'=>$no));
            }
            else if($count == 5) {
                array_push($teams1, array('team'=>$team,'no'=>$no));
            }
            else if($count == 4) {
                array_push($teams2, array('team'=>$team,'no'=>$no));
            }
        }
        return json_encode(array('teamsFull'=>$teamsFull,'teams1'=>$teams1,'teams2'=>$teams2));
    }
    
    /**
     * 页面：获取相同技能的宠物
     * @return json
     */
    public function getSameSkillMonsters($id)
    {
        $skill = Skill::whereIn('id', Monster::where('id', '=', $id)->lists('skill_id'))->lists('name');
        if(in_array('無', $skill)){
            return json_encode('无技能');
        }
        else {
            $ids = Monster::whereIn('skill_id', Monster::where('id', '=', $id)->lists('skill_id'))->orderBy('id')->get();
            return $ids;
        }
    }
}
