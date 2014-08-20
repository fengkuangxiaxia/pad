@extends('l.team', array('active' => 'team'))

@section('title') @parent 队伍匹配  @stop

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
    
    .greyOut {
        opacity: 0.4;
        filter: alpha(opacity=40);
    }
    
    #teamsFullTable td div{
        white-space: nowrap;
    }
    
    #teams1Table td div{
        white-space: nowrap;
    }
    
    #teams2Table td div{
        white-space: nowrap;
    }
    
    .popover {
        width:auto;
        max-width: 1500px;
    }
@stop

@section('container')

    <input type="text" name="dungeon_id" id="dungeon_id" hidden/>
    <button class="btn btn-default" id='submit'>查询</button>
    <ul class="nav nav-tabs nav-justified" role="tablist" id="myTab"> 
      <li class="active"><a id="teamsFullText" href="#teamsFull">完全匹配</a></li> 
      <li><a id="teams1Text" href="#teams1">差1匹配</a></li> 
      <li><a id="teams2Text" href="#teams2">差2匹配</a></li> 
    </ul> 
    <div class="tab-content"> 
        @foreach($tableClasses as $key => $tableClass)
            <div class="tab-pane {{($key == 0) ? 'active' : ''}}" id="{{$tableClass}}">
                <table class="table" id="{{$tableClass}}Table">
                    <thead>
                        <tr>
                          <th>
                            队长
                          </th>
                          <th style="width:408px">
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
            </div> 
        @endforeach
    </div>

@stop

@section('end')
    @parent
    <script>
        $(document).ready(function(){ 
            $('#myTab a').click(function (e) { 
                e.preventDefault();//阻止a链接的跳转行为 
                $(this).tab('show');//显示当前选中的链接及关联的content 
            });
            
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
                        for (key in data){
                            $("#" + key + "Table>tbody").empty();
                            var count = 0;
                            for(i in data[key]){
                                count = count + 1;
                                var leader_id = data[key][i]['team'].leader_id;
                                var monster1_id = data[key][i]['team'].monster1_id;
                                var monster2_id = data[key][i]['team'].monster2_id;
                                var monster3_id = data[key][i]['team'].monster3_id;
                                var monster4_id = data[key][i]['team'].monster4_id;
                                var friend_id = data[key][i]['team'].friend_id;
                                var hp = data[key][i]['team'].hp;
                                var stone = data[key][i]['team'].stone;
                                var description = data[key][i]['team'].description;
                                var no = data[key][i]['no'];

                                description = '<p style=\"width:400px;\">' + description + '</p>';
                                
                                var leader = '<img src="../img/monsters/' + leader_id + '.jpg" title=\"No.' + leader_id + ' ' + data[key][i]['team'].leader_name + '\"/>';
                                var monster1 = '<img src="../img/monsters/' + monster1_id + '.jpg" title=\"No.' + monster1_id + ' ' + data[key][i]['team'].monster1_name + '\"/>';
                                var monster2 = '<img src="../img/monsters/' + monster2_id + '.jpg" title=\"No.' + monster2_id + ' ' + data[key][i]['team'].monster2_name + '\"/>';
                                var monster3 = '<img src="../img/monsters/' + monster3_id + '.jpg" title=\"No.' + monster3_id + ' ' + data[key][i]['team'].monster3_name + '\"/>';
                                var monster4 = '<img src="../img/monsters/' + monster4_id + '.jpg" title=\"No.' + monster4_id + ' ' + data[key][i]['team'].monster4_name + '\"/>';                        
                                var friend = '<img src="../img/monsters/' + friend_id + '.jpg" title=\"No.' + friend_id + ' ' + data[key][i]['team'].friend_name + '\"/>';
                                
                                /*
                                var leader = '<img src="../img/monsters/' + leader_id + '.jpg" title=\"No.' + leader_id + '\"/>';
                                var monster1 = '<img src="../img/monsters/' + monster1_id + '.jpg" title=\"No.' + monster1_id + '\"/>';
                                var monster2 = '<img src="../img/monsters/' + monster2_id + '.jpg" title=\"No.' + monster2_id + '\"/>';
                                var monster3 = '<img src="../img/monsters/' + monster3_id + '.jpg" title=\"No.' + monster3_id + '\"/>';
                                var monster4 = '<img src="../img/monsters/' + monster4_id + '.jpg" title=\"No.' + monster4_id + '\"/>';                        
                                var friend = '<img src="../img/monsters/' + friend_id + '.jpg" title=\"No.' + friend_id + '\"/>';
                                */
                                
                                for(j in no) {
                                    if(no[j] == 'leader_id') {
                                        leader = '<img class="greyOut"' + leader.slice(leader.indexOf('<img'));
                                    }
                                    else if(no[j] == 'monster1_id') {
                                        monster1 = '<img class="greyOut"' + monster1.slice(monster1.indexOf('<img'));
                                    }
                                    else if(no[j] == 'monster2_id') {
                                        monster2 = '<img class="greyOut"' + monster2.slice(monster2.indexOf('<img'));
                                    }
                                    else if(no[j] == 'monster3_id') {
                                        monster3 = '<img class="greyOut"' + monster3.slice(monster3.indexOf('<img'));
                                    }
                                    else if(no[j] == 'monster4_id') {
                                        monster4 = '<img class="greyOut"' + monster4.slice(monster4.indexOf('<img'));
                                    }
                                    else if(no[j] == 'friend_id') {
                                        friend = '<img class="greyOut"' + friend.slice(friend.indexOf('<img'));
                                    }
                                }    

                                leader = '<div class="popover-click" rel="popover">' + leader + '</div>';       
                                monster1 = '<div class="popover-click" rel="popover" style="display:inline-block;">' + monster1 + '</div>'; 
                                monster2 = '<div class="popover-click" rel="popover" style="display:inline-block;">' + monster2 + '</div>';       
                                monster3 = '<div class="popover-click" rel="popover" style="display:inline-block;">' + monster3 + '</div>';       
                                monster4 = '<div class="popover-click" rel="popover" style="display:inline-block;">' + monster4 + '</div>';       
                                friend = '<div class="popover-click" rel="popover">' + friend + '</div>';       
                                
                                var members = '<div class="row">' + monster1 + monster2 + monster3 + monster4 + '</div>';
                                
                                $("#" + key + "Table>tbody").append('<tr>'+'<td>'+leader+'</td>'+'<td>'+members+'</td>'+'<td>'+friend+'</td>'+'<td><b>'+hp+'</b></td>'+'<td><b>'+stone+'</b></td>'+'<td>'+description+'</td>'+'</tr>');
                                
                                $('.popover-click').popover({
                                    html: true,
                                    animation: false,
                                    title: "相同技能的宠物",
                                    content: function(){
                                        var imgSrc = $(this).children().attr('src');
                                        var id = imgSrc.slice(imgSrc.indexOf('../img/monsters/') + '../img/monsters/'.length, imgSrc.indexOf('.jpg'));
                                        var sameSkillMonsters = '';
                                        $.ajax({
                                            type: 'GET',
                                            url: "/team/sameSkillMonsters/" + id,
                                            async: false,
                                            success: function(data){
                                                for (i in data){
                                                    sameSkillMonsters = sameSkillMonsters + '<img src="../img/monsters/' + data[i].id + '.jpg"' + ' title=\"No.' + data[i].id + ' ' + data[i].name + '\"/>';
                                                }
                                            },
                                            dataType: "json"
                                        });//end of $.ajax
                                        return sameSkillMonsters;
                                    },
                                    placement: "bottom"
                                });
                                
                            }
                            
                            if(key == 'teamsFull'){
                                $('#' + key + 'Text').text('完全匹配(' + count + ')');
                            }
                            else if(key == 'teams1'){
                                $('#' + key + 'Text').text('差1匹配(' + count + ')');
                            }
                            else if(key == 'teams2'){
                                $('#' + key + 'Text').text('差2匹配(' + count + ')');
                            }
                        }
                    },
                    dataType: "json"
                });//end of $.ajax
            });
        });
    </script>
@stop