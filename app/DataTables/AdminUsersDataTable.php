<?php

namespace App\DataTables;

use App\Models\AdminUser;
use App\Support\Datatables\Services\DataTable;
use Illuminate\Support\Facades\Auth;

class AdminUsersDataTable extends DataTable
{
    /**
     * Display ajax response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax()
    {
        return $this->datatables
            ->eloquent($this->query())
            ->editColumn('avatar', function ($user) {
                return $this->getAvatarColumnData($user);
            })
            ->editColumn('action', function ($user) {
                return $this->getActionColumnData($user);
            })
            ->make(true);
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
     * Get the "avatar" column data.
     *
     * @param  AdminUser  $user
     * @return mixed
     */
    public function getAvatarColumnData($user)
    {
        return <<<HTML
<a href='{$user->avatar}' data-lightbox='admin-user-avatar-{$user->id}'>
    <img src='{$user->avatar}' class='img-circle' style='width:28px;height:28px'>
</a>
HTML;
    }

    /**
     * Get the "action" column data.
     *
     * @param  AdminUser  $user
     * @return mixed
     */
    public function getActionColumnData($user)
    {
        $html = '<div class="btn-group" role="group">';

        if (Auth::user()->can('update', $user)) {
            $html .= '<button type="button" class="btn btn-info admin-user-action-edit"><i class="fa fa-edit"></i></button>';
        }

        if (Auth::user()->can('delete', $user)) {
            $html .= '<button type="button" class="btn btn-danger admin-user-action-delete"><i class="fa fa-trash"></i></button>';
        }

        $html .= '</div>';

        return $html;
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
            ->addAction(['title' => '操作'])
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
            'avatar' => ['title' => '头像', 'orderable' => false, 'searchable' => false, 'exportable' => false],
            'username' => ['title' => '用户名'],
            'email' => ['title' => '邮箱'],
            'created_at' => ['title' => '创建日期'],
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
            'searching' => false,
            'ordering' => false,
        ];
    }
}
