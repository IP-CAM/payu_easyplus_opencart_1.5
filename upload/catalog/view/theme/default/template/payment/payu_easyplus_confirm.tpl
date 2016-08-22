<div class="buttons">
	<div class="right">
		<a id="payu-confirm" href="#" class="button"><?php echo $button_pay; ?></a>
	</div>
</div>
<script type="text/javascript"><!--
$('#confirm').bind('click', function(e) {
	e.preventDefault();
	$.ajax({
		url: 'index.php?route=payment/payu_easyplus/send',
		type: 'post',
        dataType: 'json',	
        cache: false,	
		beforeSend: function() {
			$('#payu-confirm').hide();
			$('#payu-confirm').after('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> Contacting PayU secure payments gateway... </div>');
		},
		complete: function() {

		},				
		success: function(json) {
			if (json['redirect']) {
				location.replace(json['redirect']);
			} else {
				alert(json['error']);
			}
		}
	});
});
//--></script>

	
	