<?php


$content .= '
<script type="text/javascript">

function on_click ( id ) {
	
	$("#loading").show();
	
	let uname = $.cookie(\'name\');
	let pass =  $.cookie(\'password\');
	
	if (on_admin_view) return
	
	$.post ( {
		url : "include/inc/get_info.php",
		data : { pid : id, name : uname, password : pass,  },
		success: function ( data ) {
		    setTimeout( function (data) { eval(data) }, 100, data);
			;
		},
		});
	
}
</script>
<!-- Modal -->
<div class="modal fade" id="Details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display:none">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #222222">
        <button type="button" class="close pull-right" data-dismiss="modal" style="color:white" aria-hidden="true">&times;</button>
        <h4 class="modal-title" style="color:whitesmoke" id="myModalLabel">Информация о Бане 
        <span id="adminContent" class="pull-right" style="margin-right: 12px">
            <a id="adminEdit" href="#" ><i class="icon-edit"></i></a>
            <a id="adminDelete" href="#"><i class="icon-ban-circle"></i></a>
        </span></h4>
      </div>
      <div class="modal-body">
       	<table class="table table-bordered table-hover">
       	<tr>
       		<td class="col-lg-1"> <strong>Ник</strong></td>
       		<td class="col-lg-3" id="detail-nick"></td>
       	</tr>
       	<tr>
       		<td class="col-lg-1"> <strong>SteamID</strong></td>
       		<td class="col-lg-3" id="detail-steam"></td>
       	</tr>
       	<tr>
       		<td class="col-lg-1"> <strong>IP</strong></td>
       		<td class="col-lg-3" id="detail-ip"></td>
       	</tr>
       	<tr>
       		<td class="col-lg-1"> <strong>Добавлен</strong></td>
       		<td class="col-lg-3" id="detail-add"></td>
       	</tr>
       	<tr>
       		<td class="col-lg-1"> <strong>Истекает</strong></td>
       		<td class="col-lg-3" id="detail-remove"></td>
       	</tr>
       	<tr>
       		<td class="col-lg-1"> <strong>Админ</strong></td>
       		<td class="col-lg-3" id="detail-admin"></td>
       	</tr>
       	<tr>
       		<td class="col-lg-1"> <strong>Причина</strong></td>
       		<td class="col-lg-3" id="detail-reason"></td>
       	</tr>
       		<tr>
       		<td class="col-lg-1"> <strong>Последний Ник</strong></td>
       		<td class="col-lg-3" id="detail-last-name"></td>
       	</tr>
       	<tr>
       		<td class="col-lg-1"> <strong>Последний визит</strong></td>
       		<td class="col-lg-3" id="detail-last-visit"></td>
       	</tr>
       	<tr>
       		<td class="col-lg-1"> <strong>Кики</strong></td>
       		<td class="col-lg-3" id="detail-kick"></td>
       	</tr>
       	<tr> 
       		<td colspan="2" style="text-align: center"><a href="#" id="viewpid" class="btn btn-sm btn-info">Показать подробности</a></td>
       	</tr>
       	<tr> 
       		<td colspan="2" style="text-align: center"><a href="'.Configuration::$main['m_webtheme'].'" id="viewpid" class="btn btn-sm btn-warning">Заявка на разбан</a></td>
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
