(function($)
{

    $.fn.setValue = function (value) {
        var $e = $(this);
        $e.val(value).attr("value", value);
        return $e;
    };

    $.fn.getValue = function () {
        var $e = $(this);
        return $e.val();
    };

    $.fn.form__parse2String = function(){
        var sParam="";
        var form = $(this);
        form.find("input").each(function(index){

            if(
                ($(this).attr('type')=="checkbox" && $(this).is(':checked') )
                ||
                ($(this).attr('type')!="checkbox" && $(this).attr('type')!="radio")
                ||
                ($(this).attr('type')=="radio" && $(this).is(':checked') )
            ){
                sParam+="&"+$(this).attr('name')+'='+encodeURIComponent($(this).val());
            }
        });

        form.find("textarea").each(function(index){
            sParam+="&"+$(this).attr('name')+'='+encodeURIComponent($(this).val());

        });
        form.find("select").each(function(index){
            sParam+="&"+$(this).attr('name')+'='+encodeURIComponent($(this).val());

        });
        return sParam;
    };

    $.fn.parseForm = function()
    {
        return $(this).form__parse2String();
    };

    $.fn.parseFormObject = function()
    {
        return $(this).form__parse2Object();
    };

    $.fn.form__parse2Object = function(){
        var sParam="",
            form = $(this),
            oDatas = {};

        form.find("input").each(function(index)
        {
            if( ($(this).attr('type')=="checkbox" && $(this).is(':checked') )
                ||
                ($(this).attr('type')!="checkbox" && $(this).attr('type')!="radio")
                ||
                ($(this).attr('type')=="radio" && $(this).is(':checked') ) )
            {
                var t = $(this);
                oDatas[ t.attr('name') ] = t.val();
            }
        });

        form.find("textarea, select").each(function(index)
        {
            var t = $(this);
            oDatas[ t.attr('name') ] = t.val();
        });

        return oDatas;
    };

    $.fn.getFormDatas = function()
    {
        return $(this).form__getDatasInObject();
    };

    $.fn.serializeObject = function()
    {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    $.fn.form__getDatasInObject = function()
    {
        var $form = $(this);
        var aDatas = {};
        var formdata = (window.FormData) ? new FormData($form[0]) : null;
        var data = (formdata !== null) ? formdata : $form.serialize();
        return data;
        /*if( $form.is("form") || true )
         {
         var $candidates, $checkeds, nb_candidates, nb_checkeds;

         $("[name]", $form).each( function(index, element)
         {
         var $that = $(element),
         val = $that.val();

         var name = $that.attr("name");

         if( $that.is(":checkbox") )
         {
         $candidates = $form.find("[name='"+ name +"']");
         $checkeds = $candidates.filter(":checked");

         nb_candidates = $candidates.length;
         nb_checkeds = $checkeds.length;

         // Option de coche
         if( nb_candidates==1 )
         {
         val = $that.is(":checked") ? 1 : 0;
         }
         else // Option de choix
         {
         var arr = [];

         $checkeds.each(function(i, e)
         {
         arr.push( $(e).val() );
         });

         val = arr;
         }
         }

         if( $that.is(":radio") )
         {
         $candidates = $form.find("[name='"+ name +"']");
         $checkeds = $candidates.filter(":checked");

         nb_candidates = $candidates.length;
         nb_checkeds = $checkeds.length;

         if( nb_checkeds==1 )
         {
         val = $checkeds.val();
         }
         else
         {
         val = []; // car ajax envoit pas la clÃ© si array vide
         }
         }

         aDatas[ name ] = val;


         });
         }

         return aDatas;*/
    };



})(jQuery);