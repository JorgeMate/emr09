

$(document).ready(function(){

    // Selección del 1er dr. dixponible y día actual para el tratamiento

    var selector = document.getElementById('js-select-medics');
    var value = selector[selector.selectedIndex].text;
    document.getElementById("js-selected-doctor").innerHTML = value;

    document.getElementById("js-selected-treatment").innerHTML = '';
    document.getElementById("js-selected-place").innerHTML = '';

    let inputValue = document.getElementById("js-datepicker").value;
    document.getElementById("js-selected-date").innerHTML = inputValue;


    // Recuperación de los tratamientos de un tipo

    $("#js-select-types").change(function(){
        var typeid = $(this).val();
        var autocompleteUrl = $("#js-select-types").data('url');
        autocompleteUrl = autocompleteUrl+'?query='+typeid;
    
        //alert(autocompleteUrl);
    
        $.ajax({
            url: autocompleteUrl,
            type: 'post',
            dataType: 'json',
            success:function(response){
    
                var len = response.length;
    
                $("#js-select-treatments").empty();

                for( var i = 0; i<len; i++){
                    var id = response[i]['id'];
                    var name = response[i]['name'];
            
                    
                    $("#js-select-treatments").append('<option value="'+id+'">'+name+'</option>');            
                }

                var selector = document.getElementById('js-select-treatments');
                var value = selector[selector.selectedIndex].text;
                document.getElementById("js-selected-treatment").innerHTML = value;

            }
        });
    });

    $("#js-select-treatments").change(function(){
        var selector = document.getElementById('js-select-treatments');
        var value = selector[selector.selectedIndex].text;
        document.getElementById("js-selected-treatment").innerHTML = value;

        if((document.getElementById("js-selected-treatment").innerHTML).length
            && (document.getElementById("js-selected-place").innerHTML).length)
        {
            $("#collapseNewTrat").collapse({'show':true});
        }

    });

    $("#js-select-medics").change(function(){
        var selector = document.getElementById('js-select-medics');
        var value = selector[selector.selectedIndex].text;
        document.getElementById("js-selected-doctor").innerHTML = value;
    });

    $("#js-select-places").change(function(){
        var selector = document.getElementById('js-select-places');
        var value = selector[selector.selectedIndex].text;
        document.getElementById("js-selected-place").innerHTML = value;

        if((document.getElementById("js-selected-treatment").innerHTML).length
            && (document.getElementById("js-selected-place").innerHTML).length)
        {
            $("#collapseNewTrat").collapse({'show':true});
        }
        
    });

    $("#js-datepicker").change(function(){
        let inputValue = document.getElementById("js-datepicker").value;
        document.getElementById("js-selected-date").innerHTML = inputValue;
    });

    
});
    
    