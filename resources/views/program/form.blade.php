<div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
    <label for="name" class="control-label">{{ 'Name' }}</label>
    <input class="form-control" name="name" type="text" id="name" value="{{ isset($program->name) ? $program->name : ''}}" >
    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('department_id') ? 'has-error' : '' }}">
    <label for="department_id" class="control-label">{{ 'Department' }}</label>
    <select name="department_id" class="form-control" id="department_id">
        @foreach ($departments as $optionKey => $optionValue)
            <option value="{{ $optionKey }}" {{ (isset($program->department_id) && $program->department_id == $optionKey) ? 'selected' : '' }}>
                {{ $optionValue }}
            </option>
        @endforeach
    </select>
    {!! $errors->first('department_id', '<p class="help-block">:message</p>') !!}
</div>



<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
