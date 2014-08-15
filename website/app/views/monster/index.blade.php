@extends('l.monster', array('active' => 'monster'))

@section('title') @parent 我的宠物  @stop

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
    
    .remove {
        display: none;
    }
@stop

@section('container')

    {{ Form::open(array('class' => 'form-monster', 'role' => 'form')) }}
        <div class="row">
            <button class="btn btn-lg btn-success btn-block" type="submit">保存</button>
        </div>
        <div class="row" style="margin-bottom: 10px;">
            <span class="label label-primary" id="controllAll" style="cursor:pointer;">全部收起</span>
        </div>
        @foreach($monsters as $key => $series)
            <div>
                <div class="row series" style="margin-bottom: 10px;">
                    <span class="label label-default" id="{{$key}}" style="cursor:pointer;">{{$key}}</span>
                </div>
                <div class="row series_img" id="{{$key.'_monsters'}}">
                    @foreach($series as $monster)
                        <label class="checkbox greyOut" id="label_{{$monster->id}}" >
                            <input class="input_checkbox" type="checkbox" name="checkbox[]" value="{{$monster->id}}" id="checkbox_{{$monster->id}}" style="display:none">
                            {{HTML::image('img/monsters/'.$monster->id.'.jpg', $monster->name, array('id' => 'img_'.$monster->id, 'title' => 'No.'.$monster->id.' '.$monster->name))}}
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
        <div class="row">
            <button class="btn btn-lg btn-success btn-block" type="submit">保存</button>
        </div>
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
            
            $('.series').click(function(){
                var id = $(this).attr("id");
                var imgId = id + '_monsters';
                var imgs = $(this).next();
                if(imgs.attr("class").indexOf('remove') >= 0) {
                    imgs.removeClass("remove");                    
                }
                else {
                    imgs.addClass('remove');
                }
            });
            
            $('#controllAll').click(function(){
                if($(this).text() == '全部收起') {
                    $('.series_img').addClass('remove');
                    $(this).text('全部展开');
                }
                else {
                    $('.series_img').removeClass('remove');
                    $(this).text('全部收起');
                }
            });
        });
    </script>
@stop