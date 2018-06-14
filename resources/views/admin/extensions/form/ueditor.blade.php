<style>
.form-control.content {
	padding:0px;
	height:auto;
	border:none;
}
</style>
<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')
        <textarea class="form-control {{$class}}" id="{{$id}}" name="{{$name}}" placeholder="{{ $placeholder }}" {!! $attributes !!} >{!! htmlspecialchars_decode( old($column, $value) ) !!}</textarea>
        @include('admin::form.help-block')

    </div>
</div>
