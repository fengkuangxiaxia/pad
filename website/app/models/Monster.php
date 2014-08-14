<?php
class Monster extends BaseModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'monsters';
    
    public $timestamps = false;
    
    /**
     * 模型对象关系：宠物所属用户
     * @return object Illuminate\Database\Eloquent\Collection
     */
    public function users()
    {
        ;
    }

    /**
     * 模型对象关系：宠物所属队伍
     * @return object Illuminate\Database\Eloquent\Collection
     */
    public function teams()
    {
        ;
    }
}