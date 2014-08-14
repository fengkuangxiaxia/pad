@extends('l.monster', array('active' => 'monster'))

@section('title') 我的宠物 @parent @stop

@section('style')
    @parent
    label {
        float: left;
        margin-left:2.5px;
        margin-right:2.5px;
    }
    
    .checkbox {
        position: relative;
        display: block;
        min-height: 20px;
        margin-top: -5px;
        margin-bottom: 10px;
    }
    
    .greyOut {
        opacity: 0.4;
        filter: alpha(opacity=40);
    } 
@stop

@section('container')

    {{ Form::open(array('class' => 'form-monster', 'role' => 'form')) }}
        <button class="btn btn-lg btn-success btn-block" type="submit">保存</button>
        @foreach($monsters as $key => $series)
            <div id="{{$key}}">
                <div class="row">
                    <label class="text">{{$key}}</label>
                </div>
                <div class="row">
                    @foreach($series as $monster)
                        <label class="checkbox greyOut" id="label_{{$monster->id}}" >
                            <input class="input_checkbox" type="checkbox" name="checkbox[]" value="{{$monster->id}}" id="checkbox_{{$monster->id}}" style="display:none">
                            {{HTML::image('img/monsters/'.$monster->id.'.jpg', $monster->name, array('id' => 'img_'.$monster->id, 'title' => 'No.'.$monster->id.' '.$monster->name))}}
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    {{ Form::close() }}

@stop

@section('end')
    @parent
    <script>
        $(document).ready(function(){  
            $.ajax({
                type: 'GET',
                url: "/monster/userMonster/",
                async: true,
                success: function(data){
                    for (i in data) {
                        var id = data[i];
                        var labelId = "label_" + id;
                        var checkboxId = "checkbox_" + id;
                        var label = $('#' + labelId);
                        var checkbox = $('#' + checkboxId);

                        checkbox.attr("checked", true);      
                        if(label.attr("class").indexOf('greyOut') >= 0) {
                            label.removeClass("greyOut");                         
                        }
                    }
                },
                dataType: "json"
            });//end of $.ajax
            
            $('img').click(function(){
                var imgId = $(this).attr("id");
                var labelId = "label" + imgId.slice(imgId.indexOf('_'));
                var checkboxId = "checkbox" + imgId.slice(imgId.indexOf('_'));
                var label = $('#' + labelId);
                var checkbox = $('#' + checkboxId);
                
                if(label.attr("class").indexOf('greyOut') >= 0) {
                    label.removeClass("greyOut");  
                    checkbox.checked = true;                    
                }
                else {
                    label.addClass('greyOut');
                    checkbox.checked = false; 
                }
            });
        });
    </script>
@stop