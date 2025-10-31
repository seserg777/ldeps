<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderAdminController extends Controller
{
    public function index(Request $request)
    {
        // Mock data for orders - replace with actual Order model when available
        $orders = collect([
            (object)[
                'id' => 1,
                'order_number' => 'ORD-001',
                'customer_name' => 'Иван Петров',
                'customer_email' => 'ivan@example.com',
                'total' => 1500.00,
                'status' => 'pending',
                'created_at' => now()->subDays(2)
            ],
            (object)[
                'id' => 2,
                'order_number' => 'ORD-002',
                'customer_name' => 'Мария Сидорова',
                'customer_email' => 'maria@example.com',
                'total' => 2300.50,
                'status' => 'completed',
                'created_at' => now()->subDays(1)
            ]
        ]);

        $columns = [
            [
                'key' => 'order_number',
                'label' => 'Номер заказа',
                'sortable' => true,
                'searchable' => true
            ],
            [
                'key' => 'customer_name',
                'label' => 'Клиент',
                'sortable' => true,
                'searchable' => true
            ],
            [
                'key' => 'customer_email',
                'label' => 'Email',
                'searchable' => true
            ],
            [
                'key' => 'total',
                'label' => 'Сумма',
                'sortable' => true,
                'format' => 'currency'
            ],
            [
                'key' => 'status',
                'label' => 'Статус',
                'sortable' => true,
                'render' => function ($item) {
                    $statusClasses = [
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger'
                    ];
                    $statusLabels = [
                        'pending' => 'Ожидает',
                        'processing' => 'Обрабатывается',
                        'completed' => 'Завершен',
                        'cancelled' => 'Отменен'
                    ];
                    $class = $statusClasses[$item->status] ?? 'secondary';
                    $label = $statusLabels[$item->status] ?? $item->status;
                    return '<span class="badge bg-' . $class . '">' . $label . '</span>';
                }
            ],
            [
                'key' => 'created_at',
                'label' => 'Дата заказа',
                'sortable' => true,
                'format' => 'datetime'
            ]
        ];

        $sortableColumns = ['order_number', 'customer_name', 'total', 'status', 'created_at'];

        $actions = [
            // Actions temporarily disabled - routes not defined yet
            // [
            //     'label' => 'Просмотр',
            //     'icon' => 'eye',
            //     'class' => 'info',
            //     'url' => function($item) {
            //         return route('admin.orders.show', $item->id);
            //     }
            // ],
            // [
            //     'label' => 'Редактировать',
            //     'icon' => 'edit',
            //     'class' => 'primary',
            //     'url' => function($item) {
            //         return route('admin.orders.edit', $item->id);
            //     }
            // ]
        ];

        $bulkActions = [
            [
                'key' => 'mark_processing',
                'label' => 'В обработку'
            ],
            [
                'key' => 'mark_completed',
                'label' => 'Завершить'
            ],
            [
                'key' => 'mark_cancelled',
                'label' => 'Отменить'
            ]
        ];

        return view('admin.orders.index', compact(
            'orders',
            'columns',
            'sortableColumns',
            'actions',
            'bulkActions'
        ));
    }
}
