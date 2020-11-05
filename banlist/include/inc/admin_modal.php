<?php

$content .= '
<script type="text/javascript">

var on_admin_view = false;

function on_click_admin ( id ) {
	let uname = $.cookie(\'name\');
	let pass =  $.cookie(\'password\');
	
	on_admin_view = true;
	$.post ( {
		url : "include/inc/get_info_admin.php",
		data : { pid : id, name : uname, password : pass,  },
		success: function ( data ) {
		    setTimeout( function (data) { eval(data) }, 100, data);
		    on_admin_view = false
		},
		});
	
}
</script>
<!-- Modal -->
<div class="modal fade" id="admin_modal" role="dialog" aria-labelledby="label">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #222222">
        <button type="button" class="close pull-right" data-dismiss="modal" style="color:white" aria-hidden="true">&times;</button>
        <h4 class="modal-title" style="color:whitesmoke" id="myModalLabel">Информация об Администраторе 
        <span id="adminContentAdmin" class="pull-right" style="margin-right: 12px">
            <a id="adminEditAdmin" href="#" ><i class="icon-edit"></i></a>
            <a id="adminDeleteAdmin" href="#"><i class="icon-ban-circle"></i></a>
        </span></h4>
      </div>
      <div class="modal-body">
       	<table class="table table-bordered table-hover">
       	<tr>
       		<td class="col-lg-1"> <strong>Имя</strong></td>
       		<td class="col-lg-3" id="admin-name"></td>
       	</tr>
       	<tr>
       		<td class="col-lg-1"> <strong>Ник</strong></td>
       		<td class="col-lg-3" id="admin-nick"></td>
       	</tr>
       	<tr>
       		<td class="col-lg-1"> <strong>SteamID</strong></td>
       		<td class="col-lg-3" id="admin-steam"></td>
       	</tr>
       	<tr>
       		<td class="col-lg-1"> <strong>Должность</strong></td>
       		<td class="col-lg-3" id="admin-admin"></td>
       	</tr>
       	<tr>
       		<td class="col-lg-1"> <strong>Флаги доступа</strong></td>
       		<td class="col-lg-3" id="admin-flags"></td>
       	</tr>
       	<tr>
       		<td class="col-lg-1"> <strong>Дата покупки</strong></td>
       		<td class="col-lg-3" id="admin-date-start"></td>
       	</tr>
       	<tr>
       		<td class="col-lg-1"> <strong>Дата окончания</strong></td>
       		<td class="col-lg-3" id="admin-date-end"></td>
       	</tr>
       	<tr>
       		<td class="col-lg-1"> <strong>Сервера</strong></td>
       		<td class="col-lg-3" id="admin-servers"></td>
       	</tr>
		</table>
      </div>
      <div class="modal-footer" >
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
';
