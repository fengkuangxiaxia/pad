<?php

class MonsterController extends BaseController
{
    /**
     * 页面：首页
     * @return Response
     */
    public function getIndex()
    {
        $monsters = Monster::all();
        
        $data['monsters'] = array();
        
        foreach ($monsters as $monster) {
            if(empty($data['monsters'][$monster['series']])) {
                $data['monsters'][$monster['series']] = array($monster);
            }
            else {
                array_push($data['monsters'][$monster['series']], $monster);
            }
        }
        
        return View::make('monster.index', $data);
    }

    /**
     * 动作：保存宠物
     * @return Response
     */
    public function saveMonsters()
    {
        $monsters = Input::get('checkbox');
        $user_id = Auth::user()->id;
        
        UserMonster::where('user_id', '=', $user_id)->delete();
        
        if($monsters){
            foreach($monsters as $monster) {
                $temp = new UserMonster;
                $temp->user_id = $user_id;
                $temp->monster_id = $monster;
                $temp->save();
            }
        }
        return Redirect::route('monster.index');
    }
    
    /**
     * 页面：获取用户的宠物
     * @return Json
     */
    public function getUserMonster()
    {
        $user_id = Auth::user()->id;
        
        $monsters = UserMonster::where('user_id', '=', $user_id)->lists('monster_id');
        
        return json_encode($monsters);
    }
}
