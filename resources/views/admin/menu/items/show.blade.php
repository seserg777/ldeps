@extends('admin.layouts.app')

@section('title', 'Просмотр пункта меню')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $menu->title }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.menu.items.index', $menuType->menutype) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Назад к списку
                        </a>
                        <a href="{{ route('admin.menu.items.edit', [$menuType->menutype, $menu]) }}" class="btn btn-warning btn-sm">
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
                                    <td>{{ $menu->id }}</td>
                                </tr>
                                <tr>
                                    <th>Заголовок:</th>
                                    <td><strong>{{ $menu->title }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Псевдоним:</th>
                                    <td><code>{{ $menu->alias }}</code></td>
                                </tr>
                                <tr>
                                    <th>Ссылка:</th>
                                    <td>{{ $menu->link }}</td>
                                </tr>
                                <tr>
                                    <th>Тип ссылки:</th>
                                    <td>
                                        <span class="badge badge-info">{{ $menu->type }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Родительский пункт:</th>
                                    <td>
                                        @if($menu->parent)
                                            <a href="{{ route('admin.menu.items.show', [$menuType->menutype, $menu->parent]) }}">
                                                {{ $menu->parent->title }}
                                            </a>
                                        @else
                                            <span class="text-muted">Корневой уровень</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Уровень:</th>
                                    <td>
                                        <span class="badge badge-info">{{ $menu->level }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Статус:</th>
                                    <td>
                                        @if($menu->published == 1)
                                            <span class="badge badge-success">Опубликовано</span>
                                        @elseif($menu->published == 0)
                                            <span class="badge badge-secondary">Не опубликовано</span>
                                        @else
                                            <span class="badge badge-danger">В корзине</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Уровень доступа:</th>
                                    <td>
                                        @switch($menu->access)
                                            @case(0)
                                                <span class="badge badge-success">Публичный</span>
                                                @break
                                            @case(1)
                                                <span class="badge badge-info">Зарегистрированные</span>
                                                @break
                                            @case(2)
                                                <span class="badge badge-warning">Специальные</span>
                                                @break
                                            @case(3)
                                                <span class="badge badge-danger">Супер пользователи</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ $menu->access }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <th>Иконка:</th>
                                    <td>
                                        @if($menu->img)
                                            <i class="{{ $menu->img }}"></i> {{ $menu->img }}
                                        @else
                                            <span class="text-muted">Не указана</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Язык:</th>
                                    <td>
                                        @if($menu->language)
                                            <span class="badge badge-info">{{ $menu->language }}</span>
                                        @else
                                            <span class="text-muted">Все языки</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Примечание:</th>
                                    <td>{{ $menu->note ?: 'Не указано' }}</td>
                                </tr>
                                <tr>
                                    <th>Дата публикации:</th>
                                    <td>
                                        @if($menu->publish_up)
                                            {{ $menu->publish_up->format('d.m.Y H:i') }}
                                        @else
                                            <span class="text-muted">Не указана</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Дата снятия с публикации:</th>
                                    <td>
                                        @if($menu->publish_down)
                                            {{ $menu->publish_down->format('d.m.Y H:i') }}
                                        @else
                                            <span class="text-muted">Не указана</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Порядок:</th>
                                    <td>{{ $menu->ordering }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($menu->children->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Дочерние пункты меню</h5>
                                    </div>
                                    <div class="card-body">
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
                                                    @foreach($menu->children as $child)
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $child->title }}</strong>
                                                                @if($child->note)
                                                                    <br><small class="text-muted">{{ $child->note }}</small>
                                                                @endif
                                                            </td>
                                                            <td><code>{{ $child->alias }}</code></td>
                                                            <td>{{ Str::limit($child->link, 30) }}</td>
                                                            <td>
                                                                <span class="badge badge-info">{{ $child->level }}</span>
                                                            </td>
                                                            <td>
                                                                @if($child->published == 1)
                                                                    <span class="badge badge-success">Опубликовано</span>
                                                                @elseif($child->published == 0)
                                                                    <span class="badge badge-secondary">Не опубликовано</span>
                                                                @else
                                                                    <span class="badge badge-danger">В корзине</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('admin.menu.items.show', [$menuType->menutype, $child]) }}" 
                                                                   class="btn btn-info btn-xs" title="Просмотр">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('admin.menu.items.edit', [$menuType->menutype, $child]) }}" 
                                                                   class="btn btn-warning btn-xs" title="Редактировать">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
