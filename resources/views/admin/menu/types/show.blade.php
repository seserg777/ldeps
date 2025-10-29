@extends('admin.layouts.app')

@section('title', 'Просмотр типа меню')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $menuType->title }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.menu.types.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Назад к списку
                        </a>
                        <a href="{{ route('admin.menu.types.edit', $menuType) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Редактировать
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">ID:</th>
                                    <td>{{ $menuType->id }}</td>
                                </tr>
                                <tr>
                                    <th>Тип меню:</th>
                                    <td><code>{{ $menuType->menutype }}</code></td>
                                </tr>
                                <tr>
                                    <th>Название:</th>
                                    <td><strong>{{ $menuType->title }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Описание:</th>
                                    <td>{{ $menuType->description ?: 'Не указано' }}</td>
                                </tr>
                                <tr>
                                    <th>Клиент:</th>
                                    <td>
                                        @if($menuType->client_id == 0)
                                            <span class="badge badge-info">Фронтенд</span>
                                        @else
                                            <span class="badge badge-warning">Админка</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Порядок:</th>
                                    <td>{{ $menuType->ordering }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Статистика</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success">
                                                    <i class="fas fa-check"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Опубликовано</span>
                                                    <span class="info-box-number">{{ $menuType->published_count }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-secondary">
                                                    <i class="fas fa-eye-slash"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Не опубликовано</span>
                                                    <span class="info-box-number">{{ $menuType->unpublished_count }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-danger">
                                                    <i class="fas fa-trash"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">В корзине</span>
                                                    <span class="info-box-number">{{ $menuType->trash_count }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Пункты меню</h5>
                                    <div class="card-tools">
                                        <a href="{{ route('admin.menu.items.index', $menuType->menutype) }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-list"></i> Управление пунктами
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($menuType->menuItems->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Заголовок</th>
                                                        <th>Псевдоним</th>
                                                        <th>Ссылка</th>
                                                        <th>Уровень</th>
                                                        <th>Статус</th>
                                                        <th>Действия</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($menuType->menuItems as $item)
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $item->title }}</strong>
                                                                @if($item->note)
                                                                    <br><small class="text-muted">{{ $item->note }}</small>
                                                                @endif
                                                            </td>
                                                            <td><code>{{ $item->alias }}</code></td>
                                                            <td>{{ Str::limit($item->link, 30) }}</td>
                                                            <td>
                                                                <span class="badge badge-info">{{ $item->level }}</span>
                                                            </td>
                                                            <td>
                                                                @if($item->published == 1)
                                                                    <span class="badge badge-success">Опубликовано</span>
                                                                @elseif($item->published == 0)
                                                                    <span class="badge badge-secondary">Не опубликовано</span>
                                                                @else
                                                                    <span class="badge badge-danger">В корзине</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('admin.menu.items.show', [$menuType->menutype, $item]) }}" 
                                                                   class="btn btn-info btn-xs" title="Просмотр">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('admin.menu.items.edit', [$menuType->menutype, $item]) }}" 
                                                                   class="btn btn-warning btn-xs" title="Редактировать">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">Пункты меню не найдены</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
