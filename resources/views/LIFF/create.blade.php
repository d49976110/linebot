@extends('LIFF.app')

@section('body')

    <body>
        <div class="row">
            <div class="col-md-6" style="margin:5px">
                <form action="{{ route('store') }}" method="POST">
                    @csrf
                    <label>Name:</label>
                    <input class="form-control" type="text" id="username" name="name"><br />
                    <label>user id:</label>
                    <input class="form-control" type="text" id="userid" name="userId"> <br />

                    {{-- <button class="btn btn-primary" id="ButtonGetProfile">Get Profile</button>
                <input class="form-control" type="text" id="UserInfo" /><br /> --}}

                    {{-- <label>備註</label>
                <input class="form-control" type="text" id="msg" value="測試" /><br />
                <button class="btn btn-primary" id="ButtonSendMsg" onclick="btn()">要傳送的訊息</button> --}}

                    <button class="btn btn-primary float-right" type="submit">送出</button>
                </form>
                <a href="/admin">
                    <button>連到admin</button>
                </a>

            </div>
        </div>

        @include('LIFF.js')
    </body>

@endsection
