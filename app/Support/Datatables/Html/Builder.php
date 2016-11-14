<?php

namespace App\Support\Datatables\Html;

use Yajra\Datatables\Html\Builder as BaseBuilder;
use Yajra\Datatables\Html\Column;

class Builder extends BaseBuilder
{
    /**
     * Table attributes.
     *
     * @var array
     */
    protected $tableAttributes = [
        'id' => 'dataTable',
        'class' => 'table table-bordered table-striped dt-responsive',
        'width' => '100%',
    ];

    /**
     * Set table "id" attribute.
     *
     * @param  string  $id
     * @return $this
     */
    public function tableId($id)
    {
        return $this->setTableAttribute('id', $id);
    }

    /**
     * Add a "static" column that can not be ordered, searched, or exported.
     *
     * @param  string  $name
     * @param  array  $attributes
     * @return $this
     */
    public function addStatic($name, array $attributes = [])
    {
        $attributes = array_merge([
            'defaultContent' => '',
            'data'           => $name,
            'name'           => $name,
            'title'          => $this->getQualifiedTitle($name),
            'render'         => null,
            'orderable'      => false,
            'searchable'     => false,
            'exportable'     => false,
            'printable'      => true,
            'footer'         => '',
        ], $attributes);

        return $this->addColumn($attributes);
    }
}
