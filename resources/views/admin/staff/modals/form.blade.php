<form action="{{$action}}" method="POST" id="{{$id}}">
    @csrf
    <div class="mb-2">
        <label for="full_name" class="required">Họ và tên <span class="text-danger">(*)</span></label>
        <input type="text" id="full_name" name="full_name" placeholder="-- Nhập họ và tên --" class="form-control" required>
        @include('admin._partials.div-error')
    </div>
    <div class="mb-2">
        <label for="email" class="required">Địa chỉ email <span class="text-danger">(*)</span></label>
        <input type="email" id="email" name="email" placeholder="-- Nhập địa chỉ email --" class="form-control" required>
        @include('admin._partials.div-error')
    </div>
    <div class="mb-2">
        <label for="password" class="required">Mật khẩu <span class="text-danger">(*)</span></label>
        <input type="password" id="password" name="password" placeholder="-- Nhập mật khẩu --" class="form-control" required>
        @include('admin._partials.div-error')
    </div>
    <div class="mb-2">
        <label for="phone" class="required">Số điện thoại <span class="text-danger">(*)</span></label>
        <input type="text" id="phone" name="phone" placeholder="-- Nhập số điện thoại --" class="form-control" required>
        @include('admin._partials.div-error')
    </div>
    <div class="mb-2">
        <label for="store_id" class="required">Cửa hàng <span class="text-danger">(*)</span></label>
        <select class="form-control role" id="store_id" name="store_id">
            @if(auth()->user()->role === 'admin')
            <option value="">Tất cả</option>
            @endif
            @foreach($render_store as  $store)
            <option value="{{$store->id}}">{{$store->name}}</option>
            @endforeach
        </select>
        @include('admin._partials.div-error')
    </div>
    <div class="mb-2">
        <label for="role" class="required">Vai trò <span class="text-danger">(*)</span></label>
        <select class="form-control role" id="role" name="role">
            @foreach($list_role as $value => $text)
            <option value="{{$value}}">{{$text}}</option>
            @endforeach
        </select>
        @include('admin._partials.div-error')
    </div>
</form>