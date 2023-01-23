<?php

class console_ig
{
	public static function RUN()
	{
		?>
		<div class="CONSOLE" style="box-shadow: 0px 5px 30px black;border: 1px solid white;display:none;position: fixed;left: calc(50% - 411px);border-radius: 15px;bottom: 23px;background: black;margin-top: 20px;width: 822px;height: 57px;z-index: 999;padding: 14px 0 0 10px;">
			<div class="option" style="color: white;font-size: 20px;">
				<span id="CONSOLE_option" style="font-size: 20px;font-weight: bold;color: #00c2cc;cursor:pointer;">
					Option: 
				</span>
				<input id="CONSOLE_option_val" style="color: white;text-align:center;font-size: 20px;height:30px;background:#252525;border:unset;border-radius:5px;" placeholder="option" value="<?php echo Request::getVar( 'option' ); ?>"/>
				<span id="CONSOLE_command" style="font-size: 20px;font-weight: bold;color: #00db18;cursor:default;">
					Command: 
				</span>
				<input id="CONSOLE_command_val" style="width:38.5%;color: white;text-align:center;font-size: 20px;height:30px;background:#252525;border:unset;border-radius:5px;" placeholder="table(), cron(...)" value=""/>
			</div>
		</div>
		<span id="CONSOLE_close" style="z-index: 1000;border-radius: 10px 10px 0px 0px;background: black;left: calc(50% - 30px);width: 60px;height: 20px;position: fixed;bottom:0px;color: white;cursor:pointer;"><i class="bi-chevron-compact-up" style="font-size: 30px;display: flex;margin: -4px 0px 0px 14px;"></i></span>
		<script>
		  if ($.cookie('CONSOLE') == 1)
		  {
		    $('#CONSOLE_close').children('i').removeClass('bi-chevron-compact-up');
		    $('#CONSOLE_close').children('i').addClass('bi-chevron-compact-down');
		    $('.CONSOLE').show();
		    $.cookie('CONSOLE', 1);
		  }

		  $(document).keypress(function (event) {
		    if (event.ctrlKey && event.keyCode === 13) {
		      if ($('.CONSOLE').css('display', 'none'))
		      {
		        $('#CONSOLE_close').children('i').removeClass('bi-chevron-compact-up');
		        $('#CONSOLE_close').children('i').addClass('bi-chevron-compact-down');
		        $('.CONSOLE').show();
		        $.cookie('CONSOLE', 1);
		      }
		      $('#CONSOLE_option_val').focus().select();
		    }
		  });
		</script>
		<?php

	}

}
