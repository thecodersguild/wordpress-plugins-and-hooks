jQuery(document).ready(function ($) {

    $('.epr-event-registration').each(function (i, el) {
        var eventId = $(el).attr('data-event-id');
        $(el).find('button').on('click', function () {
            $.post(
                window.EPR.ajaxurl,
                {
                    action:   'epr-registration',
                    nonce:    window.EPR.nonce,
                    event_id: eventId,
	                  email:    $(el).find('input').val()
                },
                function (response) {
                    alert( response.success ? 'Registered!' : 'An Error Occurred.' );
                    console.log(response);
                }
            );
        });
    });

});
