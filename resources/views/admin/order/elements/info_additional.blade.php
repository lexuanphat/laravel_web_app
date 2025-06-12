<div class="col-md-4">
    <div class="card" id="card_info_addition">
        <div class="card-body p-2">
            <h5>Thông tin bổ sung</h5>
            <div class="info_addition">
                <div class="mb-2 row">
                    <label for="colFormLabelSm" class="col-sm-4 col-form-label col-form-label-sm">
                        Bán tại <span class="text-danger">(*)</span>
                    </label>
                    <div class="col-sm-8">
                        {{-- <input type="text" class="form-control" value="{{auth()->user()->store->name}}" disabled> --}}
                        <select name="store_id" id="store_id" data-toggle="select2">
                            @foreach($get_store as $store)
                            @if(auth()->user()->role === 'admin')
                            <option value="{{$store->id}}">{{$store->name}}</option>
                            @else
                                @if(auth()->user()->store_id === $store->id)
                                <option value="{{$store->id}}" selected>{{$store->name}}</option>
                                @endif
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-2 row">
                    <label for="colFormLabelSm" class="col-sm-4 col-form-label col-form-label-sm">
                        Bán bởi <span class="text-danger">(*)</span>
                    </label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" value="{{auth()->user()->full_name}}" disabled>
                    </div>
                </div>
                <div class="mb-2 row">
                    <label for="colFormLabelSm" class="col-sm-4 col-form-label col-form-label-sm">
                        Nguồn <span class="text-danger">(*)</span>
                    </label>
                    <div class="col-sm-8">
                        <select name="source" required id="source" class="form-control select2" data-toggle="select2">
                            <option value="tiktok">Tiktok</option>
                            <option value="facebook">Facebook</option>
                            <option value="instagram">Instagram</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>
                </div>
                <div class="mb-2 row">
                    <label for="colFormLabelSm" class="col-sm-4 col-form-label col-form-label-sm">
                        Hẹn giao <span class="text-danger">(*)</span>
                    </label>
                    <div class="col-sm-8">
                        <input type="date" name="" value="{{date('Y-m-d')}}" min="{{date('Y-m-d')}}" id="schedule_delivery_date" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>