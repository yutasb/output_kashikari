@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{__('Lent List')}}</h2>

    <div class="row">
        @foreach($kashikaris as $kashikari)
        <div class="col-sm-4">
            <div class="card">
                <div class="card-body  text-center">
                    <a href="{{ route('kashikari.show',$kashikari->id ) }}">
                        <img src="{{asset('storage/post_images/'.$kashikari->pic1)}}" alt='イメージ画像' width=150px>
                        <h5 class="card-title">{{$kashikari->title}}</h5>
                        <!-- いいね -->
                        <!-- <a href='/lent/{id}/like' >
                            <button type="submit">
                                <i class="fas fa-heart"></i>
                            </button>

                            </a>-->
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
