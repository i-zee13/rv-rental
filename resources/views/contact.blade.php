@extends('layouts.app')

@section('title', 'Contact Us')
@section('content')

<div class="container-fluid page-header py-5 mb-5 wow fadeIn"
    style="background: linear-gradient(rgba(0,0,0,0.6),rgba(0,0,0,0.6)), url('{{ asset('theme/img/carousel-2.jpg') }}') center/cover; min-height:200px;">
    <div class="container py-5">
        <h1 class="display-3 text-white mb-3">Contact Us</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-white" href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item text-primary active">Contact</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5 mb-5">
    <div class="row g-5">
        <div class="col-lg-5 order-2 order-lg-1 wow fadeInLeft" data-wow-delay="0.1s">
            <h2 class="mb-4">Get In Touch</h2>
            <p class="text-muted mb-4">Have questions about our fleet or need help planning your Miami trip? Fill out the form and we'll get back to you within 1 business hour.</p>

            <div class="d-flex mb-4">
                <div class="flex-shrink-0 btn-square bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                    <i class="fas fa-map-marker-alt text-white"></i>
                </div>
                <div class="ms-3">
                    <h6 class="mb-1">Address</h6>
                    <span>Miami FL 33122</span>
                </div>
            </div>
            <div class="d-flex mb-4">
                <div class="flex-shrink-0 btn-square bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                    <i class="fas fa-phone-alt text-white"></i>
                </div>
                <div class="ms-3">
                    <h6 class="mb-1">Phone</h6>
                    <a href="tel:+17869785809">+1 (786) 978-5809</a>
                </div>
            </div>
            <div class="d-flex mb-4">
                <div class="flex-shrink-0 btn-square bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                    <i class="fas fa-envelope text-white"></i>
                </div>
                <div class="ms-3">
                    <h6 class="mb-1">Email</h6>
                    <a href="mailto:info@mvmiamirental.com">info@mvmiamirental.com</a>
                </div>
            </div>
            <a href="https://wa.me/+17869785809" class="btn btn-success rounded-pill px-4">
                <i class="fab fa-whatsapp me-2"></i>Chat on WhatsApp
            </a>
        </div>

        <div class="col-lg-7 order-1 order-lg-2 wow fadeInRight" data-wow-delay="0.1s">
            <div class="bg-secondary rounded p-4 p-md-5">
                <h4 class="text-white mb-4">Send Us a Message</h4>
                @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif
                <x-lead-form
                    source="contact"
                    :vehicles="$vehicles"
                />
            </div>
        </div>
    </div>
</div>

@if(isset($faqs) && $faqs->isNotEmpty())
    <x-faq-section :faqs="$faqs" id="contactFaq" />
@endif
@endsection
