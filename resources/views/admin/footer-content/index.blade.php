@extends('admin.layouts.app')

@section('title', 'Footer Content')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Footer Content</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.footer-content.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="about_title">About Title</label>
                            <input type="text" class="form-control @error('about_title') is-invalid @enderror" 
                                id="about_title" name="about_title" value="{{ old('about_title', $footerContent->about_title ?? '') }}" required>
                            @error('about_title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="about_description">About Description</label>
                            <textarea class="form-control @error('about_description') is-invalid @enderror" 
                                id="about_description" name="about_description" rows="3" required>{{ old('about_description', $footerContent->about_description ?? '') }}</textarea>
                            @error('about_description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="contact_email">Contact Email</label>
                            <input type="email" class="form-control @error('contact_email') is-invalid @enderror" 
                                id="contact_email" name="contact_email" value="{{ old('contact_email', $footerContent->contact_email ?? '') }}" required>
                            @error('contact_email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="contact_phone">Contact Phone</label>
                            <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" 
                                id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $footerContent->contact_phone ?? '') }}" required>
                            @error('contact_phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="contact_address">Contact Address</label>
                            <textarea class="form-control @error('contact_address') is-invalid @enderror" 
                                id="contact_address" name="contact_address" rows="2" required>{{ old('contact_address', $footerContent->contact_address ?? '') }}</textarea>
                            @error('contact_address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="facebook_url">Facebook URL</label>
                            <input type="url" class="form-control @error('facebook_url') is-invalid @enderror" 
                                id="facebook_url" name="facebook_url" value="{{ old('facebook_url', $footerContent->facebook_url ?? '') }}">
                            @error('facebook_url')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="twitter_url">Twitter URL</label>
                            <input type="url" class="form-control @error('twitter_url') is-invalid @enderror" 
                                id="twitter_url" name="twitter_url" value="{{ old('twitter_url', $footerContent->twitter_url ?? '') }}">
                            @error('twitter_url')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="instagram_url">Instagram URL</label>
                            <input type="url" class="form-control @error('instagram_url') is-invalid @enderror" 
                                id="instagram_url" name="instagram_url" value="{{ old('instagram_url', $footerContent->instagram_url ?? '') }}">
                            @error('instagram_url')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="linkedin_url">LinkedIn URL</label>
                            <input type="url" class="form-control @error('linkedin_url') is-invalid @enderror" 
                                id="linkedin_url" name="linkedin_url" value="{{ old('linkedin_url', $footerContent->linkedin_url ?? '') }}">
                            @error('linkedin_url')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Update Footer Content</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 