@extends('share.layouts.app')

@section('title', 'Профіль користувача')
@section('description', 'Особистий профіль користувача')

@section('sidebar')
    {{-- Empty sidebar for profile page --}}
@endsection

@section('content')
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Головна</a></li>
            <li class="breadcrumb-item active" aria-current="page">Профіль</li>
        </ol>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user me-2"></i>Особиста інформація
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Особисті дані</h6>
                            <dl class="row">
                                <dt class="col-sm-4">Ім'я:</dt>
                                <dd class="col-sm-8">{{ $user->name ?: 'Не вказано' }}</dd>
                                
                                <dt class="col-sm-4">Логін:</dt>
                                <dd class="col-sm-8">{{ $user->username }}</dd>
                                
                                <dt class="col-sm-4">Email:</dt>
                                <dd class="col-sm-8">{{ $user->email ?: 'Не вказано' }}</dd>
                                
                                <dt class="col-sm-4">Компанія:</dt>
                                <dd class="col-sm-8">{{ $profileData['profile.company'] ?? 'Не вказано' }}</dd>
                            </dl>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-muted">Контакти</h6>
                            <dl class="row">
                                <dt class="col-sm-4">Телефон:</dt>
                                <dd class="col-sm-8">{{ $profileData['profile.phone'] ?? 'Не вказано' }}</dd>
                                
                                <dt class="col-sm-4">Країна:</dt>
                                <dd class="col-sm-8">{{ $profileData['profile.country'] ?? 'Не вказано' }}</dd>
                                
                                <dt class="col-sm-4">Місто:</dt>
                                <dd class="col-sm-8">{{ $profileData['profile.city'] ?? 'Не вказано' }}</dd>
                                
                                <dt class="col-sm-4">Адреса:</dt>
                                <dd class="col-sm-8">{{ $profileData['profile.address'] ?? 'Не вказано' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-cog me-2"></i>Дії
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-success">
                            <i class="fas fa-edit me-2"></i>Редагувати
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-home me-2"></i>Повернутися до каталогу
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Інформація про акаунт
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-6">ID:</dt>
                        <dd class="col-6">{{ $user->id }}</dd>
                        
                        <dt class="col-6">Реєстрація:</dt>
                        <dd class="col-6">{{ $user->registerDate ? $user->registerDate->format('d.m.Y') : 'Не вказано' }}</dd>
                        
                        <dt class="col-6">Останній візит:</dt>
                        <dd class="col-6">{{ $user->lastvisitDate ? $user->lastvisitDate->format('d.m.Y H:i') : 'Не вказано' }}</dd>
                        
                        <dt class="col-6">Статус:</dt>
                        <dd class="col-6">
                            @if($user->isBlocked())
                                <span class="badge bg-danger">Заблокований</span>
                            @else
                                <span class="badge bg-success">Активний</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection
