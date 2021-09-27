

{literal}
<script type="text/javascript">
$(document).ready(function(){
    // Simple call
    {/literal}{if $submitsc =='true'}{literal}
        $('.submitMessage, #submitMessage,.contact-form-box .submit,.contact-form .form-footer').css('display','none');
        {/literal}{/if}{literal}
    // More complex call
    $('.QapTcha').QapTcha({
    	autoSubmit : {/literal}{$submitsc}{literal},        
        disabledSubmit : true,
    	autoRevert : true,
		txtLock    : "{/literal}{l s='Form Locked, slide to unlock' mod='slidecaptcha'|strip_tags:'UTF-8'}{literal}",
		txtUnlock  : "{/literal}{l s='Form Unlocked' mod='slidecaptcha'|strip_tags:'UTF-8'}{literal}",
    	PHPfile    : "{/literal}{$module_dir}{literal}php/Qaptcha.jquery.php"
    });
  });
</script>
{/literal}
