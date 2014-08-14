@extends('l.team', array('active' => 'team'))

@section('title') 队伍匹配 @parent @stop

@section('style')
    @parent
    
    select {
       display: inline-block;
        
        margin-bottom: 0;
        
        font-weight: 400;
        line-height: 1.42857143;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        
        background: transparent;
        width: 268px;
        padding: 5px;
        font-size: 16px;
        border: 1px solid #ccc;
        height: 34px;
        -webkit-appearance: none; /*for chrome*/
    }
    
    table th,table td {
        text-align: center;
        
        display: table-cell;
        vertical-align: middle !important;
    }
@stop

@section('container')

    <input type="text" name="dungeon_id" id="dungeon_id" hidden/>
    <button class="btn btn-default" id='submit'>查询</button>
    <table class="table" id="teams">
        <thead>
            <tr>
              <th>
                队长
              </th>
              <th>
                队员
              </th>
              <th>
                战友 
              </th>
              <th>
                队伍血量
              </th>
              <th>
                使用魔法石
              </th>
              <th>
                简单描述
              </th>
            </tr>
          </thead>
          <tbody>
          </tbody>
    </table>

@stop

@section('end')
    @parent
    <script>
        $(document).ready(function(){ 
            $.ajax({
                type: 'GET',
                url: "/team/dungeon/",
                async: true,
                success: function(data){
                    var options = {empty_value: -1, choose: '...',leaf_class: 'leaf'};
                    $('input[name=dungeon_id]').optionTree(data, options).change(function() {});
                },
                dataType: "json"
            });//end of $.ajax
            
            $('#submit').click(function(){
                $.ajax({
                    type: 'POST',
                    url: "/team/index",
                    data: {'dungeon_id':$("#dungeon_id").val()},
                    async: true,
                    success: function(data){
                        for(i in data){
                            var leader_id = data[i].leader_id;
                            var monster1_id = data[i].monster1_id;
                            var monster2_id = data[i].monster2_id;
                            var monster3_id = data[i].monster3_id;
                            var monster4_id = data[i].monster4_id;
                            var friend_id = data[i].friend_id;
                            var hp = data[i].hp;
                            var stone = data[i].stone;
                            var description = data[i].description;
                            
                            var leader = '<img src="../img/monsters/' + leader_id + '.jpg"/>';
                            var members = '<img src="../img/monsters/' + monster1_id + '.jpg"/>'+'<img src="../img/monsters/' + monster2_id + '.jpg"/>'+'<img src="../img/monsters/' + monster3_id + '.jpg"/>'+'<img src="../img/monsters/' + monster4_id + '.jpg"/>';
                            var friend = '<img src="../img/monsters/' + friend_id + '.jpg"/>';
                            $("#teams>tbody").append('<tr>'+'<td>'+leader+'</td>'+'<td>'+members+'</td>'+'<td>'+friend+'</td>'+'<td><b>'+hp+'</b></td>'+'<td><b>'+stone+'</b></td>'+'<td>'+description+'</td>'+'</tr>');
                        }
                    },
                    dataType: "json"
                });//end of $.ajax
            });
        });
    </script>
@stop