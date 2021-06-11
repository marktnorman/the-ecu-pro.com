jQuery('#ign-subscribe-btn').on('click', function () {
    subscribe();
});
jQuery('#cs_email, #cs_name').on('keypress', function (e) {
    if (e.which == 13) {
        subscribe();
    }
});

function subscribe() {
    if (!validEmail(jQuery("#cs_email").val())) {
        hideMsg();
        showMsg("Please enter a valid email address!");
        setTimeout(hideMsg, 4000);        
        return;
    }


    jQuery('#ign-subscribe-btn').addClass('disabled');
    jQuery.ajax({
        url: igniteup_ajaxurl,
        data: { action: 'subscribe_email', cs_email: jQuery("#cs_email").val(), cs_name: jQuery("#cs_name").val() },
        dataType: 'json',
        success: function (data) {
            if (data['error']) {
                hideMsg();
                showMsg(data['message']);
                setTimeout(hideMsg, 4000);
            } else {
                jQuery('.subscribe-form').slideUp();
                jQuery('#ign-notifications .thankyou').slideDown();
            }
            jQuery('#ign-subscribe-btn').removeClass('disabled');
        }
    });
}

function hideMsg() {
    jQuery('#ign-notifications #error-msg-text').slideUp();
}
function showMsg(message) {
    jQuery('#ign-notifications #error-msg-text').html(message);
    jQuery('#ign-notifications #error-msg-text').fadeIn();
}

function validEmail(email) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return re.test(email);
}