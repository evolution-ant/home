<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="utf-8">
    <style>
        .icon_li {
            display: inline-block;
            width: 15px;
            height: 15px
        }
    </style>

    <link rel="stylesheet" href="http://at.alicdn.com/t/font_3311150_8yuhxsa0jva.css">
</head>

<body>
    <div>
        <input id="icon" name="icon" value="" class="form-control icon" placeholder="图标选择" type="text">
        <div class="dropdown">
            <button type="button" class="btn dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown">图标选择
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                @foreach ($icons as $icon)
                <li value="{{$icon['id']}}" class="icon_li" role="presentation">
                    <i class="iconfont {{$icon['text']}}" style="font-size: 12px;"></i>
                </li>
                @endforeach
            </ul>
        </div>
        {{--<select>
        @foreach ($icons as $icon)
            <option value ={{$icon['id']}}>
        <i class="iconfont {{$icon['text']}}" style="font-size: 12px;"></i>
        </option>
        @endforeach
        </select>--}}
    </div>
    <script src="http://at.alicdn.com/t/font_3311150_8yuhxsa0jva.js"></script>
    <script>
        $('.icon_li').click(function() {
            $('#icon').val($(this).attr('value'))
        })
    </script>
</body>

</html>
