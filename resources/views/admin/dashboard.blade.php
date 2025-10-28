@extends('admin.layout')

@section('title', 'Дашборд')

@section('content')
    <div class="kpi">
        <div class="card">
            <div class="muted">Товары</div>
            <div style="font-size:22px; font-weight:600;">{{ \App\Models\Product::count() }}</div>
        </div>
        <div class="card">
            <div class="muted">Категории</div>
            <div style="font-size:22px; font-weight:600;">{{ \App\Models\Category::count() }}</div>
        </div>
        <div class="card">
            <div class="muted">Просмотры</div>
            <div style="font-size:22px; font-weight:600;">—</div>
        </div>
        <div class="card">
            <div class="muted">Пользователь</div>
            <div style="font-size:22px; font-weight:600;">{{ auth('custom')->user()->username ?? auth('custom')->user()->name ?? 'Admin' }}</div>
        </div>
    </div>

    <div class="admin-card">
        <table class="list-table">
            <thead>
                <tr>
                    <th style="width:36px;">#</th>
                    <th>Название</th>
                    <th>Категория</th>
                    <th>Производитель</th>
                    <th>Код</th>
                    <th>Цена</th>
                    <th>Просмотры</th>
                    <th>Дата</th>
                </tr>
            </thead>
            <tbody>
                {{-- Placeholder row to mirror list look; will swap to real grid later --}}
                <tr>
                    <td>1</td>
                    <td class="muted">Пример товара</td>
                    <td class="muted">Категория</td>
                    <td class="muted">Бренд</td>
                    <td class="muted">000000</td>
                    <td class="muted">0.00</td>
                    <td class="muted">0</td>
                    <td class="muted">—</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection


