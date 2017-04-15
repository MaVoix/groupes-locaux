var isSendingForm = false;
var bAnimSlide = false;
var bIsSwipping = false;

$(document).ready(function () {


    //toastr default option
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-center",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "3000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };


    var $body = $('body');

    $body.on("click",".jsLink",function(){
        window.location.href=$(this).data("url");

    });


    $body.on('dblclick', '.error', function (e)
    {
        $(this).hide();
    });


    //click link modal
    $body.on("click", ".click-send-ajax-confirm", function (e)
    {
        var $title = $('#modal-title');
        var $message = $('#modal-body');
        var $btOk = $('#modal-confirm');
        var $btCancel = $('#modal-cancel');

        var $element = $(this);

        if( $element.data("modal-title") )
        {
            $title.html($element.data("modal-title"));
        }
        else
        {
            $title.html("Confirmation");
        }

        if( $element.data("modal-body") )
        {
            $message.html($element.data("modal-body"));
        }
        else
        {
            $message.html("Êtes-vous sûr de vouloir continuer ?");
        }

        if( $element.data("modal-confirm") )
        {
            $btOk.html($element.data("modal-confirm"));
            $btOk.data("url", $element.data("url"));
            $btOk.data("param", $element.data("param"));
        }
        else
        {
            $btOk.html("Ok");
            $btOk.data("url", "/");
            $btOk.data("param", "");
        }
        $btOk.off("click.confirm").on("click.confirm", function (e)
        {
            if (!isSendingForm)
            {
                var url = $(this).data("url");
                var data = $(this).data("param");
                sendAjaxRequest($(this), url, data);
            }
        });

        if( $element.data("modal-confirm") )
        {
            $btCancel.html( $element.data("modal-cancel"));
        }
        else
        {
            $btCancel.html("Annuler");
        }


        $('#modalConfirm').modal('toggle');
    });

    //click link
    $body.on("click", ".click-send-ajax", function (e)
    {
        e.preventDefault();

        var $element = $(this);

        if (!isSendingForm)
        {
            var url = $element.data("url");
            var data = $element.data("param");

            sendAjaxRequest($element, url, data);
        }
    });

    //click form
    $body.on('submit', 'form[data-ajax="true"]', function (e) {
        e.preventDefault();
        if (!isSendingForm) {
            var $form = $(this);
            var aData = $form.getFormDatas();
            sendAjaxRequest($form, $form.attr("action"), aData);
        }

    });

    //toastr message
    $body.on('click', '[data-toastr]', function (e) {
        if ($(this).data("toastr-timeout")) {
            toastr.options.timeOut = $(this).data("toastr-timeout");
        } else {
            toastr.options.timeOut = 3000;
        }
        toastr[$(this).data("toastr")]($(this).data("toastr-message"), $(this).data("toastr-title"));
    });

    //keep-awake
    setInterval(function () {
        if (!isSendingForm) {
            $.ajax('/home/keep-awake.html');
        }
    }, 1000 * 60 * 2);

});


function sendAjaxRequest($obj, url, aData, onSuccess, bFadeLoading)
{
    var $body = $('body');

    toastr.clear();

    if(typeof bFadeLoading !== "boolean"){
        bFadeLoading=true;
    }
    if(bFadeLoading){ $("#loading").fadeIn(); }

    isSendingForm = true;

    if( typeof onSuccess!=="function" )
    {
        onSuccess = function() {};
    }

    aData.noHistoryTrack = 1;

    var paramAjax = {
        "type": "post",
        "data": aData,
        xhr: function(){
            //upload Progress
            var xhr = $.ajaxSettings.xhr();
            if (xhr.upload) {
                xhr.upload.addEventListener('progress', function(event) {
                    progress_bar_id='#progress-wrp';
                    var percent = 0;
                    var position = event.loaded || event.position;
                    var total = event.total;
                    if (event.lengthComputable) {
                        percent = Math.ceil(position / total * 100);
                    }
                    //update progressbar
                    $(progress_bar_id +" .progress-bar").css("width", + percent +"%");
                    $(progress_bar_id + " .status").text(percent +"%");
                }, true);
            }
            return xhr;
        }
    };

    if( aData instanceof FormData )
    {
        paramAjax.contentType = false;
        paramAjax.processData = false;
    }

    $.ajax(url, paramAjax).done(function (response)
    {
        isSendingForm = false;

        var nTimeFade = 1;
        if (response.hasOwnProperty("durationFade"))
        {
            nTimeFade = response.durationFade;
        }

        var nTimeMessage = 3000;
        if (response.hasOwnProperty("durationMessage"))
        {
            nTimeMessage = response.durationMessage;
        }

        var nTimeRedirect = 1;
        if (response.hasOwnProperty("durationRedirect"))
        {
            nTimeRedirect = response.durationRedirect;
        }

        if(bFadeLoading){setTimeout(function () {
            $("#loading").fadeOut();
        }, nTimeFade);}

        if (response.hasOwnProperty("type"))
        {
            if (response.type == "message")
            {

                toastr.options.timeOut = nTimeMessage;
                if(response.message.type=="error"){
                    toastr.options.timeOut = 10000; //force click to close
                }
                toastr[response.message.type](response.message.text, response.message.title);
            }

        }

        if (response.hasOwnProperty("required"))
        {
            if (response.required.length)
            {
                $.each(response.required, function (i)
                {
                    var $field = $('[name="' + response.required[i].field + '"]');
                    $field.closest(".form-group").addClass("has-error");
                    $field.off("click.required").on("click.required", function ()
                    {
                        $(this).off("click.required");
                        $(this).closest(".form-group").removeClass("has-error");
                    })
                });
            }
        }


            if (response.hasOwnProperty("redirect")) {
                if (response.redirect) {
                    isSendingForm = true;
                    setTimeout(function () {
                        document.location.href = response.redirect;
                    }, nTimeRedirect);
                }
            }

        onSuccess(response);

    }).fail( function(response)
    {
        isSendingForm = false;
        var div = $("<div>").html(JSON.stringify(response));
        div.addClass("error");
        $body.append(div);
        if(bFadeLoading){setTimeout(function () {
            $("#loading").fadeOut();
        }, 200)}
    });

}

function ajaxUpdateContent(params, parts)
{
    if( typeof params==="undefined" )
    {
        params = {};
    }
    if( typeof parts==="undefined" )
    {
        parts = [];
    }

    sendAjaxRequest({}, window.location.href, params, function (r)
    {
        var $updatedPage = jQuery(r);

        // console.log( $updatedPage );

        $(".updatableContent[data-updateIndex]").each(function (i, content)
        {
            var $currentContent = $(content);
            var updateIndex =  $currentContent.attr("data-updateIndex") ;

            if( parts.length===0 || parts.indexOf(updateIndex)!==-1 )
            {
                var $updatedContent = $updatedPage.find(".updatableContent[data-updateIndex='"+ updateIndex +"']");
                if( $updatedContent.length===0 )
                {
                    $updatedContent = $updatedPage.filter(".updatableContent[data-updateIndex='"+ updateIndex +"']");
                }

                if( $updatedContent.length>0 )
                {
                    if(updateIndex=="zoom"){
                        $currentContent.removeClass("updatableContent");
                        $('body').append($updatedContent);
                    }else{
                        $currentContent.replaceWith($updatedContent);
                    }

                    initRating();
                }
            }
        });
    });


}