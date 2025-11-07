<div class="ui modal add medium">
    <div class="header" style="background:var(--secondary);color:var(--white);">{{ __("Add New Schedule") }}</div>
    <div class="content" style="background:var(--secondary);">
        <form id="add_schedule_form" action="{{ url('schedules/add') }}" class="ui form" method="post" accept-charset="utf-8" >
            @csrf
            <div class="field">
                <label>{{ __('Employee') }}</label>
                <select class="ui search dropdown getid uppercase" name="employee">
                    <option value="">Select Employee</option>
                    @isset($employee)
                        @foreach ($employee as $data)
                            <option value="{{ $data->lastname }}, {{ $data->firstname }}" data-id="{{ $data->id }}">{{ $data->lastname }}, {{ $data->firstname }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
            <div class="two fields">
                <div class="field">
                    <label for="">{{ __('Start time') }}</label>
                    <input type="text" placeholder="00:00:00 AM" name="intime" class="jtimepicker" />
                </div>
                <div class="field">
                    <label for="">{{ __('Off time') }}</label>
                    <input type="text" placeholder="00:00:00 PM" name="outime" class="jtimepicker" />
                </div>
            </div>
            <div class="field">
                <label for="">{{ __('From') }}</label>
                <input type="text" placeholder="Date" name="datefrom" id="datefrom" class="airdatepicker" />
            </div>
            <div class="field">
                <label for="">{{ __('To') }}</label>
                <input type="text" placeholder="Date" name="dateto" id="dateto" class="airdatepicker" />
            </div>
            <div class="eight wide field">
                <label for="">{{ __('Total hours') }}</label>
                <input type="number" placeholder="0" name="hours" />
            </div>
           <div class="grouped fields field">
                <label style="color:var(--light)">{{ __('Choose Rest days') }}</label>
                <div class="field">
                    <div class="ui checkbox sunday">
                        <input type="checkbox" name="restday[]" value="Sunday">
                        <label>{{ __('Sunday') }}</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox ">
                        <input type="checkbox" name="restday[]" value="Monday">
                        <label style="color:var(--light)">{{ __('Monday') }}</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox ">
                        <input type="checkbox" name="restday[]" value="Tuesday">
                        <label style="color:var(--light)">{{ __('Tuesday') }}</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox ">
                        <input type="checkbox" name="restday[]" value="Wednesday">
                        <label style="color:var(--light)">{{ __('Wednesday') }}</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox ">
                        <input type="checkbox" name="restday[]" value="Thursday">
                        <label style="color:var(--light)">{{ __('Thursday') }}</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox ">
                        <input type="checkbox" name="restday[]" value="Friday">
                        <label style="color:var(--light)">{{ __('Friday') }}</label>
                    </div>
                </div>
                <div class="field" style="padding:0">
                    <div class="ui checkbox saturday">
                        <input type="checkbox" name="restday[]" value="Saturday">
                        <label>{{ __('Saturday') }}</label>
                    </div>
                </div>
                <div class="ui error message">
                    <i class="close icon"></i>
                    <div class="header"></div>
                    <ul class="list">
                        <li class=""></li>
                    </ul>
                </div>
            </div>
        </div>
            
        <div class="actions" >
            <input type="hidden" name="id" value="">
            <button class="ui positive small button" type="submit" name="submit"><i class="ui checkmark icon"></i> {{ __('Save') }}</button>
            <button class="ui grey small button cancel" type="button"><i class="ui times icon"></i> {{ __('Cancel') }}</button>
        </div>
        </form>  
</div>
