<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use Illuminate\Http\Request;

class CustomerAdminController extends Controller
{
    public function index(Request $request)
    {
        $customers = User::with(['profiles', 'groups'])
            ->orderBy('id', 'desc')
            ->get();

        $columns = [
            [
                'key' => 'id',
                'label' => 'ID',
                'sortable' => true
            ],
            [
                'key' => 'username',
                'label' => 'Логин',
                'sortable' => true,
                'searchable' => true
            ],
            [
                'key' => 'email',
                'label' => 'Email',
                'sortable' => true,
                'searchable' => true
            ],
            [
                'key' => 'name',
                'label' => 'Имя',
                'searchable' => true,
                'render' => function ($item) {
                    return $item->profiles->first()->first_name ?? '-';
                }
            ],
            [
                'key' => 'groups',
                'label' => 'Группы',
                'render' => function ($item) {
                    $groups = $item->groups->pluck('title')->toArray();
                    return $groups ? implode(', ', $groups) : '<span class="text-muted">Нет групп</span>';
                }
            ],
            [
                'key' => 'block',
                'label' => 'Статус',
                'sortable' => true,
                'render' => function ($item) {
                    $statusClass = $item->block ? 'danger' : 'success';
                    $statusText = $item->block ? 'Заблокирован' : 'Активен';
                    return '<span class="badge bg-' . $statusClass . '">' . $statusText . '</span>';
                }
            ],
            [
                'key' => 'registerDate',
                'label' => 'Дата регистрации',
                'sortable' => true,
                'format' => 'datetime'
            ]
        ];

        $sortableColumns = ['id', 'username', 'email', 'block', 'registerDate'];

        $actions = [
            // Actions temporarily disabled - routes not defined yet
            // [
            //     'label' => 'Просмотр',
            //     'icon' => 'eye',
            //     'class' => 'info',
            //     'url' => function($item) {
            //         return route('admin.customers.show', $item->id);
            //     }
            // ],
            // [
            //     'label' => 'Редактировать',
            //     'icon' => 'edit',
            //     'class' => 'primary',
            //     'url' => function($item) {
            //         return route('admin.customers.edit', $item->id);
            //     }
            // ],
            // [
            //     'label' => 'Заблокировать',
            //     'icon' => 'ban',
            //     'class' => 'warning',
            //     'url' => function($item) {
            //         return route('admin.customers.block', $item->id);
            //     },
            //     'condition' => function($item) {
            //         return !$item->block;
            //     }
            // ],
            // [
            //     'label' => 'Разблокировать',
            //     'icon' => 'unlock',
            //     'class' => 'success',
            //     'url' => function($item) {
            //         return route('admin.customers.unblock', $item->id);
            //     },
            //     'condition' => function($item) {
            //         return $item->block;
            //     }
            // ]
        ];

        $bulkActions = [
            [
                'key' => 'block',
                'label' => 'Заблокировать выбранных'
            ],
            [
                'key' => 'unblock',
                'label' => 'Разблокировать выбранных'
            ],
            [
                'key' => 'delete',
                'label' => 'Удалить выбранных'
            ]
        ];

        return view('admin.customers.index', compact(
            'customers',
            'columns',
            'sortableColumns',
            'actions',
            'bulkActions'
        ));
    }
}
