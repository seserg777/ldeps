@extends('layouts.app')

@section('title', 'Панель управління')
@section('description', 'Панель управління користувача')

@section('sidebar')
    {{-- Empty sidebar for dashboard page --}}
@endsection

@section('content')
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Головна</a></li>
            <li class="breadcrumb-item active" aria-current="page">Панель управління</li>
        </ol>
    </nav>

    <!-- Welcome Message -->
    <div class="alert alert-success" role="alert">
        <h4 class="alert-heading">
            <i class="fas fa-user-circle me-2"></i>Ласкаво просимо, {{ Auth::guard('custom')->user()->name }}!
        </h4>
        <p class="mb-0">Ви успішно увійшли в систему. Тут ви можете переглядати інформацію про свій обліковий запис.</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>Інформація про користувача
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">ID:</dt>
                        <dd class="col-sm-9">{{ Auth::guard('custom')->user()->id }}</dd>
                        
                        <dt class="col-sm-3">Ім'я:</dt>
                        <dd class="col-sm-9">{{ Auth::guard('custom')->user()->name }}</dd>
                        
                        <dt class="col-sm-3">Логін:</dt>
                        <dd class="col-sm-9">{{ Auth::guard('custom')->user()->username }}</dd>
                        
                        <dt class="col-sm-3">Email:</dt>
                        <dd class="col-sm-9">{{ Auth::guard('custom')->user()->email }}</dd>
                        
                        <dt class="col-sm-3">Дата реєстрації:</dt>
                        <dd class="col-sm-9">{{ Auth::guard('custom')->user()->registerDate ? Auth::guard('custom')->user()->registerDate->format('d.m.Y H:i') : 'Не вказано' }}</dd>
                        
                        <dt class="col-sm-3">Останній візит:</dt>
                        <dd class="col-sm-9">{{ Auth::guard('custom')->user()->lastvisitDate ? Auth::guard('custom')->user()->lastvisitDate->format('d.m.Y H:i') : 'Не вказано' }}</dd>
                        
                        <dt class="col-sm-3">Статус:</dt>
                        <dd class="col-sm-9">
                            @if(Auth::guard('custom')->user()->isBlocked())
                                <span class="badge bg-danger">Заблокований</span>
                            @else
                                <span class="badge bg-success">Активний</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Групи користувача
                    </h5>
                </div>
                <div class="card-body">
                    @if(Auth::guard('custom')->user()->groups->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach(Auth::guard('custom')->user()->groups as $group)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $group->title }}
                                    <span class="badge bg-primary rounded-pill">{{ $group->id }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            Групи не призначені
                        </p>
                    @endif
                </div>
            </div>
            
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-id-card me-2"></i>Додаткові дані
                    </h5>
                </div>
                <div class="card-body">
                    @if(Auth::guard('custom')->user()->profiles->count() > 0)
                        <dl class="row">
                            @foreach(Auth::guard('custom')->user()->profiles as $profile)
                                <dt class="col-12">{{ $profile->profile_key }}:</dt>
                                <dd class="col-12 mb-2">{{ $profile->profile_value }}</dd>
                            @endforeach
                        </dl>
                    @else
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            Додаткові дані відсутні
                        </p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Швидкі дії
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-home me-2"></i>Повернутися до каталогу
                        </a>
                        <form method="POST" action="{{ route('auth.logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-sign-out-alt me-2"></i>Вийти з системи
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
