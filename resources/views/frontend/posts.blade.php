@extends('frontend.layout.app')

@section('title', 'All Post')

@push('css')
    <link href="{{ asset('frontend/category/css/styles.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/category/css/responsive.css') }}" rel="stylesheet">

    <style>
        .slider{
            background-image: url("{{ asset('frontend/images/slider-1.jpg') }}");
        }
    </style>
@endpush

@section('content')

    <div class="slider display-table center-text">
        <h1 class="title display-table-cell"><b>ALL POSTS</b></h1>
    </div><!-- slider -->

    <section class="blog-area section">
        <div class="container">

            <div class="row">

                @foreach($posts as $post)
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100">
                            <div class="single-post post-style-1">

                                <div class="blog-image"><img src="{{ Storage::disk('public')->url('post/'.$post->image) }}" alt="Blog Image"></div>

                                <a class="avatar" href="#"><img src="{{ Storage::disk('public')->url('profile/'.$post->user->image) }}" alt="Profile Image"></a>

                                <div class="blog-info">

                                    <h4 class="title">
                                        <a href="{{ route('frontend.post.view', $post->slug) }}">
                                            <b>{{ $post->title }}</b>
                                        </a>
                                    </h4>

                                    <ul class="post-footer">
                                        <li>
                                            @guest
                                                <a href="javascript:void(0)" onclick="toastr.error('You have to login first!');"><i class="ion-heart"></i>{{ $post->favorite_users_count }}</a>
                                            @else
                                                <a href="{{ route('frontend.post.favorite.store', $post->id) }}">
                                                    <i class="ion-heart {{ Auth::user()->favoritePosts()->where('post_id', $post->id)->count() != 0 ? 'active-favorite-post':'' }}"></i>
                                                    {{ $post->favorite_users_count }}
                                                </a>
                                            @endguest
                                        </li>
                                        <li><a href="#"><i class="ion-chatbubble"></i>6</a></li>
                                        <li><a href="#"><i class="ion-eye"></i>{{ $post->view_count }}</a></li>
                                    </ul>

                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div><!-- row -->

           {{ $posts->links() }}

        </div><!-- container -->
    </section><!-- section -->

@endsection

