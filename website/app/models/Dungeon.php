<?php
class Dungeon extends BaseModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'dungeons';
    
    public $timestamps = false;
    
    /**
     * 模型对象关系：地下城对应队伍
     * @return object Illuminate\Database\Eloquent\Collection
     */
    public function teams()
    {
        return $this->hasOne('Team', 'dungeon_id');
    }
}