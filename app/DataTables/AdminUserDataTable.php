<?php

namespace App\DataTables;

use App\Models\AdminUser;
use App\Support\Datatables\Services\DataTable;
use Illuminate\Support\Facades\Auth;

class AdminUserDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @return \Yajra\Datatables\Engines\BaseEngine
     */
    public function dataTable()
    {
        return $this->datatables
            ->eloquent($this->query())
            ->editColumn('avatar', 'datatables.admin-user-avatar')
            ->editColumn('action', function ($user) {
                return view('datatables.admin-user-action', compact('user'))->render();
            })
            ->rawColumns(['avatar', 'action']);
    }

    /**
     * Get the query object to be processed by dataTables.
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Support\Collection
     */
    public function query()
    {
        $query = AdminUser::query();

        return $this->applyScopes($query);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\Datatables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->parameters($this->getBuilderParameters());
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'id' => ['title' => 'ID'],
            'avatar' => $this->staticColumn('avatar', ['title' => '头像']),
            'username' => ['title' => '用户名'],
            'email' => ['title' => '邮箱'],
            'created_at' => ['title' => '创建日期'],
            'action' => $this->staticColumn('action'),
        ];
    }

    /**
     * Get the default builder parameters.
     *
     * @return array
     */
    protected function getBuilderParameters()
    {
        return [
            'order' => [[0, 'asc']],
        ];
    }
}
