<?php
class Team extends BaseModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'teams';
    
    public $timestamps = false;
    
    /**
     * 模型对象关系：队伍所属地下城
     * @return object Illuminate\Database\Eloquent\Collection
     */
    public function dungeon()
    {
         return $this->belongsTo('Dungeon', 'dungeon_id');
    }
    
    /**
     * 模型对象关系：队伍包含的队长
     * @return object Illuminate\Database\Eloquent\Collection
     */
    public function leader()
    {
        return $this->belongsTo('Monster', 'leader_id', 'id');
    }
    
    /**
     * 模型对象关系：队伍包含的队员1
     * @return object Illuminate\Database\Eloquent\Collection
     */
    public function monster1()
    {
        return $this->belongsTo('Monster', 'monster1_id', 'id');
    }
    
    /**
     * 模型对象关系：队伍包含的队员2
     * @return object Illuminate\Database\Eloquent\Collection
     */
    public function monster2()
    {
        return $this->belongsTo('Monster', 'monster2_id', 'id');
    }
    
    /**
     * 模型对象关系：队伍包含的队员3
     * @return object Illuminate\Database\Eloquent\Collection
     */
    public function monster3()
    {
        return $this->belongsTo('Monster', 'monster3_id', 'id');
    }
    
    /**
     * 模型对象关系：队伍包含的队员4
     * @return object Illuminate\Database\Eloquent\Collection
     */
    public function monster4()
    {
        return $this->belongsTo('Monster', 'monster4_id', 'id');
    }
    
    /**
     * 模型对象关系：队伍包含的好友
     * @return object Illuminate\Database\Eloquent\Collection
     */
    public function friend()
    {
        return $this->belongsTo('Monster', 'friend_id', 'id');
    }
}