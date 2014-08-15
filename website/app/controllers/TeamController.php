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
        
        //$teams = Team::where('dungeon_id', '=', $dungeon_id)->whereIn('leader_id', $userMonsters)->whereIn('monster1_id', $userMonsters)->whereIn('monster2_id', $userMonsters)->whereIn('monster3_id', $userMonsters)->whereIn('monster4_id', $userMonsters)->get();
        
        $teams = Team::where('dungeon_id', '=', $dungeon_id)->get();
        /*
        $full = $teams->filter(function($team) use($userMonsters){
            $count = 0;
            if(in_array($team->leader_id, $userMonsters)) {
                $count = $count + 1;
            }
            if(in_array($team->monster1_id, $userMonsters)) {
                $count = $count + 1;
            }
            if(in_array($team->monster2_id, $userMonsters)) {
                $count = $count + 1;
            }
            if(in_array($team->monster3_id, $userMonsters)) {
                $count = $count + 1;
            }
            if(in_array($team->monster4_id, $userMonsters)) {
                $count = $count + 1;
            }
            if(in_array($team->friend_id, $userMonsters)) {
                $count = $count + 1;
            }
            
            if($count == 6){
                return $team;
            }
        });
        
        $one = $teams->filter(function($team) use($userMonsters){
            $count = 0;
            if(in_array($team->leader_id, $userMonsters)) {
                $count = $count + 1;
            }
            if(in_array($team->monster1_id, $userMonsters)) {
                $count = $count + 1;
            }
            if(in_array($team->monster2_id, $userMonsters)) {
                $count = $count + 1;
            }
            if(in_array($team->monster3_id, $userMonsters)) {
                $count = $count + 1;
            }
            if(in_array($team->monster4_id, $userMonsters)) {
                $count = $count + 1;
            }
            if(in_array($team->friend_id, $userMonsters)) {
                $count = $count + 1;
            }
            
            if($count == 5){
                return $team;
            }
        });
        
        $two = $teams->filter(function($team) use($userMonsters){
            $count = 0;
            if(in_array($team->leader_id, $userMonsters)) {
                $count = $count + 1;
            }
            if(in_array($team->monster1_id, $userMonsters)) {
                $count = $count + 1;
            }
            if(in_array($team->monster2_id, $userMonsters)) {
                $count = $count + 1;
            }
            if(in_array($team->monster3_id, $userMonsters)) {
                $count = $count + 1;
            }
            if(in_array($team->monster4_id, $userMonsters)) {
                $count = $count + 1;
            }
            if(in_array($team->friend_id, $userMonsters)) {
                $count = $count + 1;
            }
            
            if($count == 4){
                return $team;
            }
        });
        
        return json_encode(array('full'=>$full,'one'=>$one,'two'=>$two));
        */
        $full = array();
        $one = array();
        $two = array();
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
            
            if($count == 6) {
                array_push($full, $team);
            }
            else if($count == 5) {
                array_push($one, array('team'=>$team,'no'=>$no));
            }
            else if($count == 4) {
                array_push($two, array('team'=>$team,'no'=>$no));
            }
        }
        return json_encode(array('full'=>$full,'one'=>$one,'two'=>$two));
    }
}
