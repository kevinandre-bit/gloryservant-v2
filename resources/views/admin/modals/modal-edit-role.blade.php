<div class="ui modal small edit">
    <div class="header">{{ __("Edit Role") }}</div>
    <div class="content">
    <form id="edit_role_form" action="{{ url('users/roles/update') }}" class="ui form" method="post" accept-charset="utf-8">
        @csrf
        <div class="field">
            <label>{{ __("Role Name") }}</label>
            <input class="uppercase" name="role_name" value="" type="text">
        </div>
        <div class="field">
            <label>{{ __("Status") }}</label>
            <select name="state" class="ui dropdown state uppercase">
                <option value="Active">{{ __("Active") }}</option>
                <option value="Disabled">{{ __("Disabled") }}</option>
            </select>
        </div>
        <div class="field">
            <label><i class="filter icon"></i> {{ __("Data Access Scope") }}</label>
            <select name="scope_level" class="ui dropdown scope_level">
                <option value="all"><i class="globe icon"></i> {{ __("All Data (Full Access)") }}</option>
                <option value="campus"><i class="building icon"></i> {{ __("Campus Only") }}</option>
                <option value="ministry"><i class="users icon"></i> {{ __("Ministry Only") }}</option>
                <option value="department"><i class="briefcase icon"></i> {{ __("Department Only") }}</option>
            </select>
            <div class="ui pointing label" style="margin-top:0.5em;">
                <i class="info circle icon"></i> Controls what data users with this role can see
            </div>
        </div>
        <div class="field">
            <div class="ui error message">
                <i class="close icon"></i>
                <div class="header"></div>
                <ul class="list">
                    <li class=""></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="actions">
        <input type="hidden" value="" name="id" class="" readonly="">
        <button class="ui positive small button" type="submit" name="submit"><i class="ui checkmark icon"></i> {{ __("Update") }}</button>
        <button class="ui grey cancel small button" type="button"><i class="ui times icon"></i> {{ __("Cancel") }}</button>
    </div>
    </form>  
</div>
