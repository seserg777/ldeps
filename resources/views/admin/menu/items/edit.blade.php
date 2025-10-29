@extends('admin.layouts.app')

@section('title', 'Редактировать пункт меню')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Редактировать пункт меню: {{ $menu->title }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.menu.items.index', $menuType->menutype) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Назад к списку
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.menu.items.update', [$menuType->menutype, $menu]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Заголовок <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $menu->title) }}" 
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="alias">Псевдоним <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('alias') is-invalid @enderror" 
                                           id="alias" 
                                           name="alias" 
                                           value="{{ old('alias', $menu->alias) }}" 
                                           required>
                                    @error('alias')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="link">Ссылка <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('link') is-invalid @enderror" 
                                           id="link" 
                                           name="link" 
                                           value="{{ old('link', $menu->link) }}" 
                                           required>
                                    @error('link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type">Тип ссылки <span class="text-danger">*</span></label>
                                    <select class="form-control @error('type') is-invalid @enderror" 
                                            id="type" 
                                            name="type" 
                                            required>
                                        <option value="Component" {{ old('type', $menu->type) == 'Component' ? 'selected' : '' }}>Компонент</option>
                                        <option value="URL" {{ old('type', $menu->type) == 'URL' ? 'selected' : '' }}>URL</option>
                                        <option value="Alias" {{ old('type', $menu->type) == 'Alias' ? 'selected' : '' }}>Псевдоним</option>
                                        <option value="Separator" {{ old('type', $menu->type) == 'Separator' ? 'selected' : '' }}>Разделитель</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="parent_id">Родительский пункт <span class="text-danger">*</span></label>
                                    <select class="form-control @error('parent_id') is-invalid @enderror" 
                                            id="parent_id" 
                                            name="parent_id" 
                                            required>
                                        <option value="1" {{ old('parent_id', $menu->parent_id) == 1 ? 'selected' : '' }}>Корневой уровень</option>
                                        @foreach($parentItems as $parent)
                                            <option value="{{ $parent->id }}" {{ old('parent_id', $menu->parent_id) == $parent->id ? 'selected' : '' }}>
                                                {{ str_repeat('— ', $parent->level - 1) }}{{ $parent->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="access">Уровень доступа <span class="text-danger">*</span></label>
                                    <select class="form-control @error('access') is-invalid @enderror" 
                                            id="access" 
                                            name="access" 
                                            required>
                                        <option value="0" {{ old('access', $menu->access) == 0 ? 'selected' : '' }}>Публичный</option>
                                        <option value="1" {{ old('access', $menu->access) == 1 ? 'selected' : '' }}>Зарегистрированные</option>
                                        <option value="2" {{ old('access', $menu->access) == 2 ? 'selected' : '' }}>Специальные</option>
                                        <option value="3" {{ old('access', $menu->access) == 3 ? 'selected' : '' }}>Супер пользователи</option>
                                    </select>
                                    @error('access')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="note">Примечание</label>
                                    <input type="text" 
                                           class="form-control @error('note') is-invalid @enderror" 
                                           id="note" 
                                           name="note" 
                                           value="{{ old('note', $menu->note) }}">
                                    @error('note')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="img">Иконка</label>
                                    <input type="text" 
                                           class="form-control @error('img') is-invalid @enderror" 
                                           id="img" 
                                           name="img" 
                                           value="{{ old('img', $menu->img) }}"
                                           placeholder="fas fa-home">
                                    @error('img')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="language">Язык</label>
                                    <select class="form-control @error('language') is-invalid @enderror" 
                                            id="language" 
                                            name="language">
                                        <option value="">Все языки</option>
                                        <option value="ru-RU" {{ old('language', $menu->language) == 'ru-RU' ? 'selected' : '' }}>Русский</option>
                                        <option value="uk-UA" {{ old('language', $menu->language) == 'uk-UA' ? 'selected' : '' }}>Украинский</option>
                                        <option value="en-GB" {{ old('language', $menu->language) == 'en-GB' ? 'selected' : '' }}>Английский</option>
                                    </select>
                                    @error('language')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" 
                                               class="form-check-input" 
                                               id="published" 
                                               name="published" 
                                               value="1" 
                                               {{ old('published', $menu->published) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="published">
                                            Опубликовано
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="publish_up">Дата публикации</label>
                                    <input type="datetime-local" 
                                           class="form-control @error('publish_up') is-invalid @enderror" 
                                           id="publish_up" 
                                           name="publish_up" 
                                           value="{{ old('publish_up', $menu->publish_up ? $menu->publish_up->format('Y-m-d\TH:i') : '') }}">
                                    @error('publish_up')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="publish_down">Дата снятия с публикации</label>
                                    <input type="datetime-local" 
                                           class="form-control @error('publish_down') is-invalid @enderror" 
                                           id="publish_down" 
                                           name="publish_down" 
                                           value="{{ old('publish_down', $menu->publish_down ? $menu->publish_down->format('Y-m-d\TH:i') : '') }}">
                                    @error('publish_down')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Сохранить изменения
                            </button>
                            <a href="{{ route('admin.menu.items.index', $menuType->menutype) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Отмена
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
