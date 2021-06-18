@extends('LIFF.app')

@section('body')

    <body>
        <div class="row">
            <div class="col-md-6" style="margin:5px">
                <form action="/file" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label>Name:</label>
                    <input class="form-control" type="text" id="username" name="name"><br />

                    <label>user id:</label>
                    <input class="form-control" type="text" id="userid" name="userId"> <br />

                    <label>上傳檔案</label>
                    <input class="form-control" type="file" name="file"> <br />


                    <button class="btn btn-primary float-right" type="submit">送出</button>
                </form>

            </div>
        </div>

        @include('LIFF.js')
    </body>

@endsection
