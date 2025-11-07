<div class="ui modal medium add">
    <div class="header">{{ __("Post Devotion") }}</div>
    <div class="content">
        <form id="post_devotion_form" action="{{ url('personal/devotion/post') }}" class="ui form" method="post" accept-charset="utf-8">
        @csrf

        <div class="field">
            <label for="">{{ __("Devotion Date") }}</label>
            <input id="returndate" type="date" placeholder="Devotion date" required="" name="devotion_date" class="airdatepicker uppercase" />
        </div>
        <div class="field">
            <label>{{ __("Devotion Text") }}</label>
            <textarea class="uppercase" rows="5" name="devotion_text" value="" required></textarea>
        </div>
        {{-- Hidden Fields --}}
    <input type="hidden" name="reference" value="{{ Auth::user()->id }}">
    <input type="hidden" name="idno" value="{{ Auth::user()->idno }}">
    <input type="hidden" name="employee" value="{{ Auth::user()->lastname }}, {{ Auth::user()->firstname }}">
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
        <button class="ui positive small button" type="submit" name="submit">
            <i class="ui checkmark icon"></i> {{ __("Submit Devotion") }}
        </button>
        <button class="ui grey small button cancel" type="button">
            <i class="ui times icon"></i> {{ __("Cancel") }}
        </button>
    </div>
    </form>
</div>