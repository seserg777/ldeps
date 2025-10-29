@extends('admin.layouts.app')

@section('title', 'Создать тип меню')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Создать тип меню</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.menu.types.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Назад к списку
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.menu.types.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="menutype">Тип меню <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('menutype') is-invalid @enderror" 
                                           id="menutype" 
                                           name="menutype" 
                                           value="{{ old('menutype') }}" 
                                           placeholder="main-menu"
                                           required>
                                    @error('menutype')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Уникальный идентификатор типа меню (только латинские буквы, цифры и дефисы)
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Название <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title') }}" 
                                           placeholder="Главное меню"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="description">Описание</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="3" 
                                              placeholder="Описание типа меню">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="client_id">Клиент <span class="text-danger">*</span></label>
                                    <select class="form-control @error('client_id') is-invalid @enderror" 
                                            id="client_id" 
                                            name="client_id" 
                                            required>
                                        <option value="0" {{ old('client_id', 0) == 0 ? 'selected' : '' }}>Фронтенд</option>
                                        <option value="1" {{ old('client_id') == 1 ? 'selected' : '' }}>Админка</option>
                                    </select>
                                    @error('client_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Создать тип меню
                            </button>
                            <a href="{{ route('admin.menu.types.index') }}" class="btn btn-secondary">
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
