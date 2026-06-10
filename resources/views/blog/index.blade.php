@extends('layouts.app')
@section('title', 'Blog & News')
@section('content')

<div class="container-fluid page-header py-5 mb-5 wow fadeIn"
    style="background: linear-gradient(rgba(0,0,0,0.6),rgba(0,0,0,0.6)), url('/theme/img/blog-1.jpg') center/cover no-repeat; min-height:200px;">
    <div class="container py-5">
        <h1 class="display-3 text-white mb-3 animated slideInDown">Blog &amp; News</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-white" href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item text-primary active">Blog</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-fluid blog py-5">
    <div class="container py-3">
        <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width:800px;">
            <h1 class="display-5 text-capitalize mb-3">MV Rental<span class="text-primary"> Blog &amp; News</span></h1>
            <p>Stay informed with the latest updates, travel tips, and industry insights.</p>
        </div>

        @if($posts->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-newspaper fa-4x text-muted mb-4"></i>
            <h4 class="text-muted">No posts published yet.</h4>
            <a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-5 mt-3">Back to Home</a>
        </div>
        @else
        <div class="row g-4">
            @foreach($posts as $i => $post)
                @php $t = $post->translations->firstWhere('locale', app()->getLocale()) ?? $post->translations->first(); @endphp
                <div class="col-lg-4 wow fadeInUp" data-wow-delay="{{ ($i%3)*0.2+0.1 }}s">
                    <div class="blog-item">
                        <div class="blog-img">
                            <img src="/theme/img/blog-{{ ($i%3)+1 }}.jpg"
                                class="img-fluid rounded-top w-100"
                                alt="{{ $t->title ?? '' }}"
                                onerror="this.src='/theme/img/features-img.png'">
                        </div>
                        <div class="blog-content rounded-bottom p-4">
                            <div class="blog-date">{{ $post->created_at ? $post->created_at->format('d M Y') : '' }}</div>
                            <div class="blog-comment my-3">
                                <div class="small"><span class="fa fa-user text-primary"></span><span class="ms-2">Admin</span></div>
                            </div>
                            <a href="{{ route('blog.show', $post->slug) }}" class="h4 d-block mb-3">
                                {{ $t->title ?? 'Untitled' }}
                            </a>
                            <p class="mb-3">{{ Str::limit(strip_tags($t->excerpt ?? $t->content ?? ''), 120) }}</p>
                            <a href="{{ route('blog.show', $post->slug) }}">
                                Read More <i class="fa fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="d-flex justify-content-center mt-5">
            {{ $posts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
