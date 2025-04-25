@extends('_layout_auth')
@section('content')
<form action="{{route('admin.login.post')}}" method="POST">
    @csrf
    <div class="mb-2">
        <label for="emailaddress">Tên đăng nhập hoặc Email</label>
        <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" id="email" required="required" value="{{ old('email') }}" placeholder="Nhập tên đăng nhập hoặc email" autocomplete="email" autofocus>
        @if ($errors->has('email'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
        @endif
        
        @if ($errors->has('password'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('password') }}</strong>
            </span>
        @endif
    </div>

    <div class="mb-2">
        <a href="#" class="text-muted float-right"><small>Quên mật khẩu?</small></a>
        <label for="password">Mật khẩu</label>
        <div class="input-group input-group-merge">
            <input type="password" name="password" id="password" class="form-control" placeholder="Nhập mật khẩu" required autocomplete="current-password">
            <div class="input-group-append" data-password="false">
                <div class="input-group-text">
                    <span class="password-eye"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group mb-3">
        <div class="custom-control custom-checkbox">
            <input class="custom-control-input" type="checkbox" name="remember" id="checkbox-signin" {{ old('remember') ? 'checked' : '' }}>
            <label class="custom-control-label" for="checkbox-signin">Ghi nhớ tài khoản</label>
        </div>
    </div>

    <div class="form-group mb-0 text-center">
        <button class="btn btn-primary" type="submit"> Đăng nhập </button>
    </div>

</form>
@endsection