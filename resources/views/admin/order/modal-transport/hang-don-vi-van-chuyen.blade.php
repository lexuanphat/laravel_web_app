<div class="option-transport">
    @for($i = 0; $i <= 10; $i++)
    <div class="form-check mb-2 item">
        <input type="radio" id="customRadiocolor{{$i}}" name="customRadiocolor" class="form-check-input" checked>
        <label style="cursor: pointer" class="form-check-label" for="customRadiocolor{{$i}}">
            ViettelPost{{$i}} - CP nhanh thoả thuận: <b>15,999</b>
        </label>
    </div>
    @endfor
</div>