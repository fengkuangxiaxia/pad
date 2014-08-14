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
     * 模型对象关系：队伍包含的宠物
     * @return object Illuminate\Database\Eloquent\Collection
     */
    public function monsters()
    {
        ;
    }
}