@extends('LIFF.app')

@section('body')
    <table class="table">
        <thead>
            <tr>
                <th scope="col">id</th>
                <th scope="col">Name</th>
                <th scope="col">UserId</th>
                <th scope="col">isVerified</th>
                <th scope="col">Confirm</th>
                <th scope="col">Cancel</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <th scope="row"> {{$user->id}} </th>
                    <td> {{ $user->name }} </td>
                    <td> {{ $user->user_id }} </td>
                    <td> {{ $user->isVerified }} </td>
                    <td>
                        <form action="/liff/{{ $user->id }}" method="post">
                            @csrf
                            @method('PUT')
                            <button id="confirm" type="sumit" class="btn btn-primary">
                                Confirm
                            </button>
                        </form>

                    </td>
                    <td>
                        <form action="/liff/{{ $user->id }}/cancel" method="get">
                            @csrf
                            <button id="confirm" type="sumit" class="btn btn-primary">
                                Cancel
                            </button>
                        </form>

                    </td>
                </tr>
            @endforeach


        </tbody>
    </table>
@endsection
