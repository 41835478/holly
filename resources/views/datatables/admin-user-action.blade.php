<div class="btn-group" role="group">
@if (Auth::user()->can('update', $user))
<button type="button" class="btn btn-info admin-user-action" data-action="edit"><i class="fa fa-edit"></i></button>
@endif
@if (Auth::user()->can('delete', $user))
<button type="button" class="btn btn-danger admin-user-action" data-action="delete"><i class="fa fa-trash"></i></button>
@endif
</div>
